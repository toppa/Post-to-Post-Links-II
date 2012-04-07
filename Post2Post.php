<?php

class Post2Post {
    private $autoLoader;
    private $functionsFacade;
    private $shortcode = array(
        'type' => null,
        'value' => null,
        'text' => null,
        'attributes' => null,
        'anchor' => null
    );

    private $postId;
    private $linkUrl;
    private $linkText;
    private $linkTag;

    public function __construct(ToppaAutoLoader $autoLoader, ToppaFunctionsFacade $functionsFacade) {
        $this->autoLoader = $autoLoader;
        $this->functionsFacade = $functionsFacade;
    }

    public function run() {
        add_shortcode('p2p', array($this, 'handleShortcode'));
    }

    public function handleShortcode($userShortcode) {
        try {
            $this->setShortcode($userShortcode);
            $this->setLinkTextFromShortcodeIfProvided();

            switch ($this->shortcode['type']) {
                case 'id':
                    $this->setPostId();
                    $this->setLinkTextByPostIdIfNeeded();
                    $this->setLinkUrlByPostId();
                    break;
                case 'slug':
                    $this->setLinkByPostSlug();
                    break;
                case 'cat_id':
                    $this->setLinkByCategoryId();
                    break;
                case 'cat_slug':
                    $this->setLinkByCategorySlug();
                    break;
                case 'tag_id':
                    $this->setLinkByTagId();
                    break;
                case 'tag_slug':
                    $this->setLinkByTagSlug();
                    break;
                default:
                    $e = new Exception(__('Unrecognized shortcode "type"', 'p2p'));
                    return $this->formatExceptionMessage($e);
            }
        }

        catch (Exception $e) {
            return $this->formatExceptionMessage($e);
        }

        return $this->setLink();
    }

    public function setShortcode($userShortcode) {
        // if the shortcode has no attributes specified, WP passes
        // an empty string instead of an array
        if (!is_array($userShortcode)) {
            throw New Exception(__('Invalid shortcode arguments provided', 'p2p'));
        }

        $this->shortcode = $userShortcode;
        array_walk($this->shortcode, array('ToppaFunctions', 'trimCallback'));
        return $this->shortcode;
    }

    public function setLinkTextFromShortcodeIfProvided() {
        if (strlen($this->shortcode['text'])) {
            $this->linkText = $this->shortcode['text'];
        }

        return $this->linkText;
    }

    public function setPostId() {
        if ($this->shortcode['type'] != 'id') {
            return null;
        }

        if (!is_numeric($this->shortcode['value'])) {
            throw New Exception(__('You must provide a numeric post ID for type="id"', 'p2p'));
        }

        $this->postId = $this->shortcode['value'];
        return $this->postId;
    }

    public function setLinkTextByPostIdIfNeeded() {
        if (!$this->linkText) {
            $post = $this->functionsFacade->getPost($this->postId);

            if (is_object($post)) {
                $this->linkText = $post->post_title;
            }

            else {
                throw New Exception(__('There is no post with that ID', 'p2p'));
            }
        }

        return $this->linkText;
    }
/*
    public function setTitleAndLinkByPost() {
        if ($this->shortcode['type'] != 'id' && $this->shortcode['type'] != 'slug') {
            return null;
        }

        elseif ($this->shortcode['type'] == 'id' && !is_numeric($this->shortcode['value'])) {
            throw New Exception('You must provide a numeric post ID for type="id"', 'p2p');
        }

        elseif ($this->shortcode['type'] == 'slug' && !strlen($this->shortcode['value'])) {
            throw New Exception(__('You must provide a post slug for type="slug"', 'p2p'));
        }

        elseif ($this->shortcode['type'] != 'id' && is_numeric($this->shortcode['value'])) {
            $post = get_post($this->shortcode['value']);
        }

        elseif ($this->shortcode['type'] != 'cat_slug' && strlen($this->shortcode['value'])) {
            $categorySlug = $this->shortcode['value'];
            $categoryId = get_cat_ID($this->shortcode['value']);
        }


        elseif (!is_numeric($this->shortcode['value'])) {
            throw New Exception('You must provide a numeric post ID for type="id"', 'p2p');
        }

        elseif (is_numeric($this->shortcode['value'])) {
            $post = get_post($this->shortcode['value'], ARRAY_A);

            if (!is_array($post)) {
                throw New Exception(
                    __('There seems to not be a post with id', 'p2p')
                    . ' "'
                    . htmlentities($this->shortcode['value'])
                    . '"'
                );
            }

            $this->link = get_permalink($this->shortcode['value']);
            $this->title = $post['post_title'];
        }

        // we shouldn't get here
        else {
            throw New Exception('Unknown error', 'p2p');
        }

        return array($this->title, $this->link);
    }

    public function setTitleAndLinkByPostSlug() {
        global $wpdb;

        if ($this->shortcode['type'] != 'slug') {
            return null;
        }

        elseif (!strlen($this->shortcode['value'])) {
            throw New Exception(__('You must provide a post slug for type="slug"', 'p2p'));
        }

        elseif (strlen($this->shortcode['value'])) {
            $sql = $wpdb->prepare("select ID, post_title from {$wpdb->posts} where post_name = %s", $this->shortcode['value']);
            list($id, $this->title) = $wpdb->get_row($sql, ARRAY_N);

            if (!is_numeric($id)) {
                throw New Exception(
                    __('There seems to not be a post with slug', 'p2p')
                    . ' "'
                    . htmlentities($this->shortcode['value'])
                    . '"'
                );
            }

            $this->link = get_permalink($id);
        }

        // we shouldn't get here
        else {
            throw New Exception('Unknown error', 'p2p');
        }

        return array($this->title, $this->link);
    }

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

		
		
		
		
		if(is_wp_error($permalink)){
			return Post2Post::reportError('An unespected error happened in Post2Post plugin. $permalink returned with error "'. $permalink->get_error_message() .'".');
		}
		
		if( !empty($section)  &&  $section[0]!='#' ){
			$section='#'.$section;
		}
		
		if (empty($text)) {
			$text = $title;
		}
		
		
        $replace = '<a href="' . $permalink . $section .'" title="'. $title .'"';

        if (is_string($attributes)) {
            $replace .= " $attributes";
        }

        $replace .= ">$text</a>";
        return $replace;
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


