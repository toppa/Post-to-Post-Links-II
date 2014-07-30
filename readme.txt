=== Post-to-Post Links II ===
Contributors: toppa
Donate link: http://www.toppa.com/post-to-post-links-wordpress-plugin
Tags: post, posts, admin, categories, category, editor, links, pages, page, permalink, shortcode, tags, tag
Requires at least: 3.0
Tested up to: 3.5.1
Stable tag: 1.2.1
License: GPLv2 or later

Using a modal dialog window in the post editor, easily link to another post, page, tag, or category in your WordPress site.

== Description ==

**I am currently no longer developing or supporting this plugin. I may resume development and support in the future, but I'm not sure when.**

*New in version 1.2:* Support for custom post types

[youtube http://www.youtube.com/watch?v=KTwM5P6TohI]

Post-to-Post Links II lets you easily create a link to another post, page, tag, or category on your site. With a dialog form that uses autocomplete to look up posts by title, it's very easy to find your link destination. The link will automatically use the title of the post, page, tag, or category as the link text, or you can supply your own link text.

It generates a shortcode instead of an HTML link. This means your links won't break if you ever change your permalink structure, or change the organization of your categories.

You can also specify a named anchor for the link, and enter any additional attributes you need (such as an "id" or "name" for use with javascript or css).

It includes a button for the visual editor to launch the dialog form for looking up posts. If you prefer the HTML editor, you can install my [Extensible HTML Editor Buttons](http://wordpress.org/extend/plugins/extensible-html-editor-buttons/) plugin, and Post-to-Post Links II will use it to register a button in the HTML editor button bar.

For posts not yet published (pending, future, or draft) Post-to-Post Links II won't try to link to them. Instead it shows the link text along with a note saying the post is not published yet (with both inside css classes if you want to style the appearance).

It is multi-site compatible.

**Examples**

A simple link to the "Hello World" post:
`[p2p type="slug" value="hello-world"]My first post[/p2p]`

A link to a category page that opens in a new window:
`[p2p type="category" value="around-the-world" attributes="target='_blank'"]Read about my trip around the world[/p2p]`

A link to a named anchor on a page:
`[p2p type="slug" value="hello-world" anchor="more"]My first post[/p2p]`

**Get Help**

Enter a post in [the wordpress.org support forum for Post to Post Links II](http://wordpress.org/support/plugin/post-to-post-links-ii), and I'll respond there.

**Give Help**

* Provide a language translation - [here's how](http://weblogtoolscollection.com/archives/2007/08/27/localizing-a-wordpress-plugin-using-poedit/)
* Fork [the Post to Post Links II repository on github](https://github.com/toppa/Post-to-Post-Links-II) and make a code contribution
* If you're savvy user of the plugin, [answer questions in the support forum](http://wordpress.org/support/plugin/post-to-post-links-ii)
* If you tip your pizza delivery guy, tip your plugin developer - [make a donation](http://www.toppa.com/post-to-post-links-wordpress-plugin/)

This plugin was inspired by the "Post-to-Post Links" plugin written (but no longer maintained by) Scott Reilly.

== Installation ==

Upload to your plugin folder just like any other plugin, and activate.

If you install the [Extensible HTML Editor Buttons](http://wordpress.org/extend/plugins/extensible-html-editor-buttons/) plugin, you will need to de-activate and re-active Post-to-Post Links II to enable its HTML editor button.

== Frequently Asked Questions ==

Please go to [the Post-to-Post Links II page](http://www.toppa.com/post-to-post-links-wordpress-plugin/) for more information</a>.

== Screenshots ==

1. The modal dialog for selecting your link destination

== Changelog ==

= 1.2.1 = Bug fix: get tag links working!

= 1.2 = Added support for custom post types

= 1.1.1 = Bug fix: the admin-ajax.php url resolution fix in 1.0.3 only worked when in the HTML editor; now also fixed for the Visual editor

= 1.1 = For posts not yet published (pending, future, or draft) don't link to them. Instead show the link text along with a note saying the post is not published yet (with both inside a css class for custom styling)

= 1.0.3 = Bug fix: infer admin-ajax.php url from current url (the post edit page)

= 1.0.2 = Bug fix: removed remaining calls to Toppa Libraries plugin

= 1.0.1 =

* bug fix: provide relative URL for ajax request
* bug fix: on activation, use is_plugin_active() to check whether we should register the p2p button with Extensible HTML Editor Buttons (the method check was throwing an error in some environments)

= 1.0 =
* added visual editor button and dialog for finding posts
* added support for highlighting text in the editor to make link text
* added support for named anchors
* refactored and added unit tests

= 0.3 =
* made multi-site compatible

= 0.2 =
* changed setLink setLink($atts, $content=null), adding $content parameter
* overwrite $text=$content if $content was used
* load element (post, category, tag)'s title everytime
* for $type == 'tag_id', replaced database select by 'get_term_by'
* BUGFIX: $tag_id was being used outside of its scope, code was removed because it's not needed anymore
* created reportError, to report bad parameters
* replace $wpdb queries for correspondent API functions in $type=='id' and $type=='tag_id'
* added some validation checks for when passed ip or slug is not found in database
* added apply_filters() with tag 'p2p_error_msg', so that the message can be filtered as needed before it is sent to page

= 0.1 = First version
