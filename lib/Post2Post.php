<?php

class Post2Post {
    private $functionsFacade;
    private $dbFacade;
    private $shortcode;
    private $linkUrl;
    private $title;
    private $linkText;
    private $linkAnchor;
    private $status = 'publish';
    private $p2pLink;

    public function __construct(p2pFunctionsFacade $functionsFacade, p2pDatabaseFacade $dbFacade) {
        $this->functionsFacade = $functionsFacade;
        $this->dbFacade = $dbFacade;
    }

    public function run() {
        add_action('admin_enqueue_scripts', array($this, 'addAdminScripts'));
        add_action('init', array($this, 'addTinyMceButton'));
        add_shortcode('p2p', array($this, 'handleShortcode'));
        add_action('wp_ajax_p2p', array($this, 'ajaxGetMatches'));
    }

    public function addAdminScripts($hook) {
        if ($hook == 'post-new.php' || $hook == 'post.php') {
            wp_enqueue_script('jquery-ui-autocomplete');
        }
    }

    public function addTinyMceButton() {
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages'))
            return;

        if (get_user_option('rich_editing') == 'true') {
            add_filter('mce_buttons', array($this, 'registerTinyMceButton'));
            add_filter('mce_external_plugins', array($this, 'addTinyMcePlugin'));
        }
    }

    public function registerTinyMceButton($buttons) {
        array_push($buttons, "|", 'p2p');
        return $buttons;
    }

    public function addTinyMcePlugin($plugin_array) {
        $plugin_array['p2p'] = $this->functionsFacade->getPluginsUrl('/display/tinyMceButton.js', dirname(__FILE__));
        return $plugin_array;
    }

    public function handleShortcode($userShortcode, $text = null) {
        try {
            $this->setShortcode($userShortcode);

            if ($this->shortcode['slug']) {
                $this->setTitleAndLinkUrlFromPostSlug();
            }

            elseif ($this->shortcode['id']) {
                $this->setTitleAndLinkUrlFromPostId();
            }

            elseif ($this->shortcode['cat_slug']) {
                $this->setTitleAndLinkUrlFromCatSlug();
            }

            elseif ($this->shortcode['tag_slug']) {
                $this->setTitleAndLinkUrlFromTagSlug();
            }

            else {
                $e = new Exception(__('No valid type provided', 'p2p'));
                return $this->formatExceptionMessage($e);
            }

            $this->setLinkText($text);
            $this->setLinkAnchor();
            $this->setP2pLink();
        }

        catch (Exception $e) {
            return $this->formatExceptionMessage($e);
        }

        return $this->p2pLink;
    }

    public function setShortcode($userShortcode) {
        // if the shortcode has no attributes specified, WP passes
        // an empty string instead of an array
        if (!is_array($userShortcode)) {
            throw New Exception(__('No shortcode attributes found', 'p2p'));
        }

        if ($userShortcode['type']) {
            switch ($userShortcode['type']) {
                case 'slug':
                    $userShortcode['slug'] = $userShortcode['value'];
                    break;
                case 'id':
                    $userShortcode['id'] = $userShortcode['value'];
                    break;
                case 'category':
                    $userShortcode['cat_slug'] = $userShortcode['value'];
                    break;
                case 'post_tag':
                    $userShortcode['tag_slug'] = $userShortcode['value'];
                    break;
                default:
                    throw New Exception(
                        __('Unrecognized type:', 'p2p')
                        . ' '
                        . $this->functionsFacade->escHtml($userShortcode['type'])
                    );
            }
        }

        $this->shortcode = $userShortcode;
        array_walk($this->shortcode, array($this, 'trimCallback'));
        return $this->shortcode;
    }

    public function trimCallback(&$string, $key = null) {
        $string = trim($string);
    }

    public function setTitleAndLinkUrlFromPostSlug() {
        if (!strlen($this->shortcode['slug'])) {
            throw New Exception(__('You must provide a post slug', 'p2p'));
        }

        $posts_table = $this->dbFacade->executeDbFunction('posts');
        $fields = array('ID', 'post_title', 'post_status');
        $where = array('post_name' => $this->shortcode['slug']);
        $post = $this->dbFacade->sqlSelectRow($posts_table, $fields, $where);

        if (!is_numeric($post['ID'])) {
            throw New Exception(
                __('No post found with slug', 'p2p')
                . ' "'
                . $this->functionsFacade->escHtml($this->shortcode['slug'])
                . '"'
            );
        }

        $this->linkUrl = $this->functionsFacade->getPermalink($post['ID']);
        $this->title = $post['post_title'];
        $this->status = $post['post_status'];
    }

    /* @deprecated */
    public function setTitleAndLinkUrlFromPostId() {
        if (!is_numeric($this->shortcode['id'])) {
            throw New Exception(__('You must provide a numeric post ID', 'p2p'));
        }

        $post = $this->functionsFacade->getPost($this->shortcode['id'], ARRAY_A);

        if (!is_array($post)) {
            throw New Exception(
                __('No post found with id', 'p2p')
                . ' "'
                . $this->functionsFacade->escHtml($this->shortcode['id'])
                . '"'
            );
        }

        $this->linkUrl = $this->functionsFacade->getPermalink($this->shortcode['id']);
        $this->title = $post['post_title'];
    }


    public function setTitleAndLinkUrlFromCatSlug() {
        if (!strlen($this->shortcode['cat_slug'])) {
            throw New Exception(__('You must provide a category slug', 'p2p'));
        }

        return $this->setTitleAndLinkUrlFromTermSlug($this->shortcode['cat_slug'], 'category');
    }

    public function setTitleAndLinkUrlFromTagSlug() {
        if (!strlen($this->shortcode['tag_slug'])) {
            throw New Exception(__('You must provide a tag slug', 'p2p'));
        }

        return $this->setTitleAndLinkUrlFromTermSlug($this->shortcode['tag_slug'], 'post_tag');
    }

    private function setTitleAndLinkUrlFromTermSlug($slug, $type) {
        if (!$slug) {
            throw New Exception(__('You must provide a term slug', 'p2p'));
        }

        if (!$type) {
            throw New Exception(__('You must provide a term type', 'p2p'));
        }

        $term = $this->functionsFacade->getTermBy('slug', $slug, $type);

        if (!is_a($term, 'stdClass')) {
            throw New Exception(
                __('No term found with slug', 'p2p')
                . ' "'
                . $this->functionsFacade->escHtml($slug)
                . '"'
            );
        }

        $this->linkUrl = $this->functionsFacade->getTermLink($term);

        if (!is_string($this->linkUrl)) {
            throw New Exception(
                __('No link found for term slug', 'p2p')
                    . ' "'
                    . $this->functionsFacade->escHtml($slug)
                    . '"'
            );
        }

        $this->title = $term->name;
        return array($this->title, $this->linkUrl);
    }

    public function setLinkText($text = null) {
        if ($text) {
            $this->linkText = $text;
        }

        // for backwards compatibility with older versions
        elseif ($this->shortcode['text']) {
            $this->linkText = $this->shortcode['text'];
        }

        else {
            $this->linkText = $this->title;
        }

        return $this->linkText;
    }

    public function setLinkAnchor() {
        if ($this->shortcode['anchor']) {
            $this->linkAnchor = '#' . $this->shortcode['anchor'];
        }

        return $this->linkAnchor;
    }

    public function setP2pLink() {
        if ($this->status == 'publish') {
            $this->p2pLink = "<a href='{$this->linkUrl}{$this->linkAnchor}' title='{$this->title}'";

            if (is_string($this->shortcode['attributes'])) {
                $this->p2pLink .= ' ' . $this->shortcode['attributes'];
            }

            $this->p2pLink .= ">{$this->linkText}</a>";
        }

        else {
            $note = __('[the linked post is not published yet]', 'p2p');
            $this->p2pLink = "<i class='p2p-pending-link'>{$this->linkText}</i> <i class='p2p-pending-note'>$note</i>";
        }

        return $this->p2pLink;
    }

    public function getLinkUrl() {
        return $this->linkUrl;
    }

    public function getTitle() {
        return $this->title;
    }

    public function ajaxGetMatches() {
        try {
            $title = $this->dbFacade->checkIsStringAndEscape($_REQUEST['term']);

            switch ($_REQUEST['type']) {
                case 'slug':
                    $posts_table = $this->dbFacade->executeDbFunction('posts');
                    $sql = "
                        select post_title as label, post_name as value
                        from $posts_table
                        where post_title like '%{$title}%'
                        and post_type not in ('revision', 'attachment')
                    ";
                    break;
                case 'category':
                case 'post_tag':
                    $type = $this->dbFacade->checkIsStringAndEscape($_REQUEST['type']);
                    $terms_table = $this->dbFacade->executeDbFunction('terms');
                    $taxonomy_table = $this->dbFacade->executeDbFunction('term_taxonomy');
                    $sql = "
                        SELECT t.name AS label, t.slug AS value
                        FROM $taxonomy_table tt
                        INNER JOIN $terms_table t ON t.term_id = tt.term_id
                        WHERE LOWER(t.name) LIKE LOWER('%{$title}%')
                        AND tt.taxonomy = '$type'
                    ";
                    break;
            }

            $results = $this->dbFacade->executeQuery($sql, 'get_results');
        }

        catch (Exception $e) {
            echo $this->formatExceptionMessage($e);
        }

        if (is_array($results)) {
            echo json_encode($results);
        }

        die();
    }

    public function formatExceptionMessage($e) {
        return '<i class="p2p-error">'
            . __('Post to Post Links II error', 'p2p')
            . ': '
            . $e->getMessage()
            . '</i>';
    }
}
