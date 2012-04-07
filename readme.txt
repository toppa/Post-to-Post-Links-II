=== Post-to-Post Links II ===
Contributors: toppa
Donate link: http://www.toppa.com/post-to-post-links-wordpress-plugin
Tags: post, posts, admin, categories, category, editor, links, pages, page, permalink, shortcode, tags, tag
Requires at least: 3.0
Tested up to: 3.3.1
Stable tag: 0.3

Using a shortcode, easily link to another post, page, or category in your WordPress blog.

== Description ==

Using a shortcode, you can create a link to another post, page, tag, or category. You use the ID number or the slug to identify what you are linking to. The link will automatically use the title of the post, page, tag, or category as the link text, or you can supply your own link text.

It makes linking within your site more convenient, and it means your links won't break if you ever change your permalink structure or change the parent-child relationships of your categories.

If you also install the Koumpounophobia plugin, you will get a Post-to-Post Links II button added to your HTML Editor. You can use that button to add the Post-to-Post Links II shortcode to your post. If you don't have Koumpounophobia, or you're using the Visual Editor, here are a couple examples of the shortcode:

[p2p type="id" value="53"]

[p2p type="slug" value="hello-world" text="read my Hello World post" attributes="target='_blank'"]

Possible values for "type" are:

* slug: a post or page slug
* id: a post or page ID number
* cat_slug: a category slug
* cat_id: a category ID number
* tag_slug: a tag slug
* tag_id: a tag ID number

The "value" attribute is either the slug or ID number of the linked post, page, category, or tag.

The "text" attribute provides the text for the link. It's optional - if you leave it out, the title of the post, page, tag, or category will be used.

"attributes" is optional, and allows you to provide any other attributes you'd like for the link, such as a target, title, onclick, etc. Note that you need to use single quotes here, since the value itself is delimited by double quotes.

This plugin is inspired by the "Post-to-Post Links" plugin written (but no longer maintained by) Scott Reilly. Unfortunately though, it cannot interpret the old tags. Post-to-Post Links II uses WordPress' robust shortcode functionality, which is not compatible with the syntax Scott used in Easy Post-to-Post Links.

== Installation ==

**Installation Instructions**

Download the zip file, unzip it, and copy the "post-to-post-links-ii" folder to your plugins directory. Then activate it from your plugin panel. After successful activation, you can start using the shortcode. Post-to-Post Links II will appear in your "Settings" menu (but the only thing there is an uninstall option).

If you have Koumpounophobia installed, the Post-to-Post Links button will automatically appear in your HTML Editor after Post-to-Post Links II is activated.

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

