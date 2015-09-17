=== Simple Instant Search ===
Contributors: bainternet 
Donate link:http://en.bainternet.info/donations
Tags: Instant search, ajax search, search
Requires at least: 2.9.2
Tested up to: 4.0.0
Stable tag: 1.4

With This Plugin you can  eaily add instant search functionalty to your site or blog.

== Description ==

Not long a go google implemented an instant search functionallty that show resluts as you type even before you hit the search button.
With This Plugin you can  eaily add instant search functionalty to your site or blog. 

Main features:

*	Very easy to install.
*	Easy configuration screen.
*	simple options to style the search results.
*	extendable by hooks and filters all around.
*	Use as shortcode.
*	Use as template tag for easy integration with your theme or plugin.
	
any Feedback is Welcome.

check out my [other plugins][1]

[1]: http://en.bainternet.info/category/plugins

== Installation ==

1.  Upload the plugin directory to the /wp-content/plugins/ directory
1.  Activate the plugin through the \'Plugins\' menu in WordPress
1.  Go to \"Instant Search\" option to configure the plugin
== Frequently Asked Questions ==

= It's not working, whats wrong? =
Could be a miolion thing but the main reason is you simply need to add the shortcode to a page or a post,
so simply create a page/post and enter `[IS]`

= I have Found a Bug, Now what? =

Simply use the <a href=\"http://wordpress.org/tags/simple-instant-search?forum_id=10\">Support Forum</a> and thanks a head for doing that.


= How to use in template files? =

`<?php echo do_shortcode('[IS]'); ?>`


== Screenshots ==
1. Options panel

2. Example search(well you need to see it live...)

== Changelog ==
= 1.4 =
* removed admin code under init hook.
* added filter hook to no results found.

= 1.3 =
* Cleand wp_debug bugs.

= 1.2 =
* changed CSS to fix front end submit hidden.

= 1.1 =
*Fixed content phrasing issue thanks to Omer Goshen

= 1.0 =
* initial release.
