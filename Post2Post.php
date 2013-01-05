<?php

class Post2Post {
    private $autoLoader;
    private $functionsFacade;
    private $dbFacade;
    private $shortcode;
    private $linkUrl;
    private $title;
    private $linkText;
    private $linkAnchor;
    private $p2pLink;

    public function __construct(ToppaAutoLoader $autoLoader, ToppaFunctionsFacade $functionsFacade, ToppaDatabaseFacade $dbFacade) {
        $this->autoLoader = $autoLoader;
        $this->functionsFacade = $functionsFacade;
        $this->dbFacade = $dbFacade;
    }

    public function run() {
        add_action('init', array($this, 'addTinyMceButton'));
        add_shortcode('p2p', array($this, 'handleShortcode'));
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
        $plugin_array['p2p'] = $this->functionsFacade->getPluginsUrl('/Display/tinyMceButton.js', __FILE__);
        return $plugin_array;
    }

    public function handleShortcode($userShortcode, $text) {
        try {
            $this->setShortcode($userShortcode, $text);

            if ($this->shortcode['slug']) {
                $this->setTitleAndLinkUrlFromPostSlug();
            }

            elseif ($this->shortcode['id']) {
                $this->setTitleAndLinkUrlFromPostId();
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
                case 'cat_slug':
                    $userShortcode['cat_slug'] = $userShortcode['value'];
                    break;
                case 'cat_id':
                    $userShortcode['cat_id'] = $userShortcode['value'];
                    break;
                case 'tag_slug':
                    $userShortcode['tag_slug'] = $userShortcode['value'];
                    break;
                case 'tag_id':
                    $userShortcode['tag_id'] = $userShortcode['value'];
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
        array_walk($this->shortcode, array('ToppaFunctions', 'trimCallback'));
        return $this->shortcode;
    }


    public function setTitleAndLinkUrlFromPostSlug() {
        if (!strlen($this->shortcode['slug'])) {
            throw New Exception(__('You must provide a post slug', 'p2p'));
        }

        $posts_table = $this->dbFacade->executeDbFunction('posts');
        $fields = array('ID', 'post_title');
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
    }

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

    public function setLinkText($text = null) {
        if ($text) {
            $this->linkText = $text;
        }

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
        $this->p2pLink = "<a href='{$this->linkUrl}{$this->linkAnchor}' title='{$this->title}'";

        if (is_string($this->shortcode['attributes'])) {
            #$this->p2pLink .= ' ' . $this->functionsFacade->escHtml($this->shortcode['attributes']);
            $this->p2pLink .= ' ' . $this->shortcode['attributes'];
        }

        $this->p2pLink .= ">{$this->linkText}</a>";
        return $this->p2pLink;
    }

    public function getLinkUrl() {
        return $this->linkUrl;
    }

    public function getTitle() {
        return $this->title;
    }

    /*



    public function setTitleAndLinkByCategory() {
        if ($this->shortcode['type'] != 'cat_id' && $this->shortcode['type'] != 'cat_slug') {
            return null;
        }

        elseif ($this->shortcode['type'] != 'cat_id' && !is_numeric($this->shortcode['value'])) {
            throw New Exception('You must provide a numeric category ID for type="cat_id"', 'p2p');
        }

        elseif ($this->shortcode['type'] != 'cat_slug' && !strlen($this->shortcode['value'])) {
            throw New Exception(__('You must provide a category slug for type="cat_slug"', 'p2p'));
        }

        elseif ($this->shortcode['type'] != 'cat_id' && is_numeric($this->shortcode['value'])) {
            $categorySlug = get_cat_name($this->shortcode['value']);
            $categoryId = $this->shortcode['value'];
        }

        elseif ($this->shortcode['type'] != 'cat_slug' && strlen($this->shortcode['value'])) {
            $categorySlug = $this->shortcode['value'];
            $categoryId = get_cat_ID($this->shortcode['value']);
        }

        // we shouldn't get here
        else {
            throw New Exception('Unknown error', 'p2p');
        }

        $category = get_category_by_slug($categorySlug);

        if (!is_object($category)) {
            throw New Exception(
                __('There seems to not be a category with slug', 'p2p')
                    . ' "'
                    . $this->shortcode['value']
                    . '"'
            );
        }

        $this->link = get_category_link($categoryId);
        return array($this->title, $this->link);
    }


elseif (($type == 'tag_id' && is_numeric($value)) || ($type == 'tag_slug' && is_string($value))) {
			if ($type == 'tag_slug') {
                $sql = $wpdb->prepare("select term_id from {$wpdb->terms} where slug = %s", $value);
                $tag_id = $wpdb->get_var($sql);
				
            }else{
				$tag_id = $value;
			}
			
			$tag_obj = &get_tag($tag_id);
			
			if ( empty($tag_obj) ) {
				if($type == 'tag_slug'){
					return Post2Post::reportError('There seems to not be a tag with slug "'.$value.'"');
				}else{
					return Post2Post::reportError('There seems to not be a tag with id "'.$tag_id.'"');
				}
			}
			
			$title = $tag_obj->name;

            $permalink = get_tag_link($tag_id);
        }

        else {
            return Post2Post::reportError(__("Invalid post-to-post links tag", P2P_L10N_NAME));
        }

*/
    public function formatExceptionMessage($e) {
        return '<p><strong>'
            . __('Post to Post Links II error', 'p2p')
            . ':</strong></p><pre>'
            . $e->getMessage()
            . '</pre>';
    }
}
