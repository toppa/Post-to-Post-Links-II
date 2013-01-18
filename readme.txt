=== Post-to-Post Links II ===
Contributors: toppa
Donate link: http://www.toppa.com/post-to-post-links-wordpress-plugin
Tags: post, posts, admin, categories, category, editor, links, pages, page, permalink, shortcode, tags, tag
Requires at least: 3.0
Tested up to: 3.4.1
Stable tag: 1.0
License: GPLv2 or later

Using a shortcode, easily link to another post, page, tag, or category in your WordPress blog.

== Description ==

Post-to-Post Links II lets you easily create a link to another post, page, tag, or category on your site. With a dialog form that uses autocomplete to look up posts by title, it's very easy to find your link destination. The link will automatically use the title of the post, page, tag, or category as the link text, or you can supply your own link text.

It generates a shortcode instead of an HTML link. This means your links won't break if you ever change your permalink structure, or change the organization of your categories.

You can also specify a named anchor target for the link, and enter any additional attributes you need (such as an "id" or "name" for use with javascript or css).

It includes a button for the visual editor to launch the dialog form for looking up posts. If you prefer the HTML editor, you can install my Extensible HTML Editor Buttons plugin, and Post-to-Post Links II will use it to register a button in the HTML editor button bar.

**Examples**

Here are some example of the shortcode:

A simple link to the "Hello World" post:
[p2p type="slug" value="hello-world"]

A link to a category page, with custom link text, and an ID:
[p2p type="category" value="around-the-world" text="Read about my trip around the world" attributes="id='#world-travel'"]

A link to a named anchor on a page:
[p2p type="slug" value="hello-world" anchor="more"]

**Shortcode Attributes**

* type: can be "slug" (for a post or page), "category", or "post_tag" (for tags)
* value: the slug of the linked post, page, category, or tag (with the dialog form this is entered for you automatically, after you look up the post, category, or tag by title)
* text (optional): provides text for the link (if you leave it out, the title of the post, tag, or category will be used).
* attributes (optional): allows you to provide any other attributes you'd like for the link, such as a target, title, onclick, etc. Note that you need to use single quotes here, since the value itself is delimited by double quotes.
* anchor (optional): a named anchor on the page you want to link to.

If for any reason you prefer to type the shortcode by hand instead of using the dialog form, there is a shorthand for it.  Instead of using "type" and "value", you can use any of the types as an attribute, for example [p2p slug="hello-world"].

This plugin was inspired by the "Post-to-Post Links" plugin written (but no longer maintained by) Scott Reilly.

== Installation ==

Upload to your plugin folder just like any other plugin, and activate.

If you install the Extensible HTML Editor Buttons plugin, you will need to de-activate and re-active Post-to-Post Links II to enable its HTML editor button.

== Changelog ==

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
== Frequently Asked Questions ==

Please go to <a href="http://www.toppa.com/post-to-post-links-wordpress-plugin">the Post-to-Post Links II page for more information</a>.

