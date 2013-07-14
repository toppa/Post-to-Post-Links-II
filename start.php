<?php
/*
Plugin Name: Post-to-Post Links II
Plugin URI:http://www.toppa.com/post-to-post-links-wordpress-plugin
Description: Using a shortcode, easily link to another post, page, or category in your WordPress blog.
Author: Michael Toppa
Version: 1.2.1
Author URI: http://www.toppa.com
*/

$p2pPath = dirname(__FILE__);
$p2pParentDir = basename(dirname(__FILE__));

add_action('wpmu_new_blog', 'p2pActivateForNewNetworkSite');
register_activation_hook(__FILE__, 'p2pActivate');
register_deactivation_hook(__FILE__, 'p2pDeactivateForNetworkSites');
load_plugin_textdomain('p2p', false, $p2pParentDir . '/languages/');

$p2pAutoLoaderPath = $p2pPath . '/lib/P2pAutoLoader.php';

if (file_exists($p2pAutoLoaderPath)) {
    require_once($p2pAutoLoaderPath);
    new P2pAutoLoader('/' . $p2pParentDir . '/lib');
    $functionsFacade = new P2pFunctionsFacade();
    $dbFacade = new P2pDatabaseFacade();
    $p2p = new Post2Post($functionsFacade, $dbFacade);
    $p2p->run();
}

function p2pActivateForNewNetworkSite($blog_id) {
    global $wpdb;

    if (is_plugin_active_for_network(__FILE__)) {
        $old_blog = $wpdb->blogid;
        switch_to_blog($blog_id);
        p2pActivate();
        switch_to_blog($old_blog);
    }
}

function p2pActivate() {
    if (!function_exists('spl_autoload_register')) {
        p2pCancelActivation(__('You must have at least PHP 5.1.2 to use Post to Post Links II', 'p2p'));
    }

    elseif (version_compare(get_bloginfo('version'), '3.0', '<')) {
        p2pCancelActivation(__('You must have at least WordPress 3.0 to use Post to Post Links II', 'p2p'));
    }

    // if the Extensible HTML Editor Buttons plugin  is installed
    // activate our custom button and dialog
    elseif (is_plugin_active('extensible-html-editor-buttons/start.php')) {
        $dialogPath = dirname(__FILE__) . '/display/buttonDialog.html';
        $status = Buttonable::registerButton('p2p', 'p2p', __('Add Post-to-Post Link', 'p2p'), 'ed_p2p', 'n', 'y', $dialogPath);

        if (is_string($status)) {
            p2pCancelActivation($status);
        }
    }
}

function p2pCancelActivation($message) {
    deactivate_plugins(__FILE__);
    wp_die($message);
}

function p2pDeactivateForNetworkSites() {
    new P2pAutoLoader('/' . basename(dirname(__FILE__)) . '/lib');
    $functionsFacade = new P2pFunctionsFacade();
    $functionsFacade->callFunctionForNetworkSites('p2pDeactivate');
}

function p2pDeactivate() {
    if (is_plugin_active('extensible-html-editor-buttons/start.php')) {
        return Buttonable::deregisterButton('p2p');
    }

    return false;
}
