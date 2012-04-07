<?php
/*
Plugin Name: Post-to-Post Links II
Plugin URI:http://www.toppa.com/post-to-post-links-wordpress-plugin
Description: Using a shortcode, easily link to another post, page, or category in your WordPress blog.
Author: Michael Toppa
Version: 0.3
Author URI: http://www.toppa.com
*/

$p2pAutoLoaderPath = dirname(__FILE__) . '/../toppa-plugin-libraries-for-wordpress/ToppaAutoLoaderWp.php';
add_action('wpmu_new_blog', 'p2pActivateForNewNetworkSite');
register_activation_hook(__FILE__, 'p2pActivate');
register_deactivation_hook(__FILE__, 'p2pDeactivateForNetworkSites');
load_plugin_textdomain('p2p', false, basename(dirname(__FILE__)) . '/Languages/');

if (file_exists($p2pAutoLoaderPath)) {
    require_once($p2pAutoLoaderPath);
    $p2pToppaAutoLoader = new ToppaAutoLoaderWp('/toppa-plugin-libraries-for-wordpress');
    $p2pAutoLoader = new ToppaAutoLoaderWp('/post-to-post-links-ii');
    $functionFacade = new ToppaFunctionsFacadeWp();
    $p2p = new Post2Post($p2pAutoLoader, $functionFacade);
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
    $autoLoaderPath = dirname(__FILE__) . '/../toppa-plugin-libraries-for-wordpress/ToppaAutoLoaderWp.php';

    if (!file_exists($autoLoaderPath)) {
        $message = __('To activate Post to Post Links II you need to first install', 'p2p')
            . ' <a href="http://wordpress.org/extend/plugins/toppa-plugin-libraries-for-wordpress/">Toppa Plugins Libraries for WordPress</a>';
        p2pCancelActivation($message);
    }

    elseif (!function_exists('spl_autoload_register')) {
        p2pCancelActivation(__('You must have at least PHP 5.1.2 to use Post to Post Links II', 'p2p'));
    }

    elseif (version_compare(get_bloginfo('version'), '3.0', '<')) {
        p2pCancelActivation(__('You must have at least WordPress 3.0 to use Post to Post Links II', 'p2p'));
    }

    // activate
    elseif (method_exists('Buttonable', 'registerButton')) {
        $dialog_path = dirname(__FILE__) . '/Display/buttonDialog.html';
        $status = Buttonable::registerButton('p2p', 'p2p', __('Add Post-to-Post Link', 'p2p'), 'ed_p2p', 'y', 'y', $dialog_path);

        if (is_string($status)) {
            p2pCancelActivation($status);
        }
    }
}

function p2pCancelActivation($message) {
    deactivate_plugins(basename(__FILE__));
    wp_die($message);
}

function p2pDeactivateForNetworkSites() {
    $toppaAutoLoader = new ToppaAutoLoaderWp('/toppa-plugin-libraries-for-wordpress');
    $functionsFacade = new ToppaFunctionsFacadeWp();
    $functionsFacade->callFunctionForNetworkSites('p2pDeactivate');
}

function p2pDeactivate() {
    if (method_exists('Buttonable', 'deregisterButton')) {
        return Buttonable::deregisterButton('p2p');
    }

    return false;
}
