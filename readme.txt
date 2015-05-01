=== ezFlippr ===
Contributors: nuagelab
Tags: flipbook
Requires at least: 3.0
Tested up to: 4.2.1
Stable tag: trunk
License: GPLv2 or later

Allows download and installation of flipbooks created with ezFlippr.com.

== Description ==

The plugin will download a list of the flipbook you created and allow you to install them on your WordPress web site.

= Features =

* Get the list of your flipbooks from ezFlippr.com
* Install the flipbooks you want on your web site in one click
* Add them to pages with the [ezflippr] shortcode
* View them full page

== Installation ==

This section describes how to install the plugin and get it working.

= Installing the Plugin =

*(using the Wordpress Admin Console)*

1. From your dashboard, click on "Plugins" in the left sidebar
1. Add a new plugin
1. Search for "ezFlippr"
1. Install "ezFlippr"
1. Once Installed, go to the ezFlippr and enter/request your access key
1. Go to the flipbooks section to install flipbooks

*(manually via FTP)*

1. Delete any existing 'ezflippr' folder from the '/wp-content/plugins/' directory
1. Upload the 'ezflippr' folder to the '/wp-content/plugins/' directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Once Installed, go to the ezFlippr and enter/request your access key
1. Go to the flipbooks section to install flipbooks

== Adding flip books ==

1. Create your flip book on www.ezflippr.com
1. Go to the ezFlippr configuration panel, and enter either your email or your access key (if you enter your email, your access key will be mailed to you)
1. Flip books will be imported and available in the Flip books menu. To install them on your server, simply click on the Install link.
1. To display the flipbook in a page, use the shortcode displayed in the flip books list (ex. [flipbook id="58" width="100%" height="500"])
1. To display the flipbook full page, simply use the "View" link in the flip books list.
1. You may customize the full page template by creating a single-ezflippr_flipbook.php file in your template directory. You may start from the file wp-content/plugins/ezflippr/resources/templates/single-ezflippr_flipbook.php.

== Frequently Asked Questions ==

= Do you plan to localize this plugin in a near future? =

Yes, this plugin will be translated to french and spanish shortly. If you want to help with translation in other languages, we'll be happy to hear from you.

== Changelog ==
= 1.0 =
* First stable version.

= 0.0.1 =
* First released version. Tested internally with about 10 sites.

== Upgrade Notice ==