=== ezFlippr ===
Contributors: nuagelab
Tags: flipbook, administration
Requires at least: 3.6
Tested up to: 4.2.2
Stable tag: trunk
License: GPLv2 or later

Allows download and installation of flipbooks created with ezFlippr.com.

== Description ==

The plugin will download a list of the flipbook you created with [ezFlippr](http://ezflippr.com), and will allow you to install them on your WordPress web site.

[ezFlippr](http://ezflippr.com) is a flipbook creation platform that offers free watermarked Flash and mobile-compatible flipbooks, and paid versions without watermarks.

= Features =

* Get the list of your flipbooks from [ezFlippr](http://ezflippr.com)
* Install the flipbooks you want on your web site in one click
* Add them to pages with the [ezflippr] shortcode
* View them full page

= Feedback =
* We are open for your suggestions and feedback - Thank you for using or trying out one of our plugins!
* Drop us a line [@nuagelab](http://twitter.com/#!/nuagelab) on Twitter
* Follow us on [our Facebook page](https://www.facebook.com/pages/NuageLab/150091288388352)
* Drop us a line at [info@ezflippr.com](mailto:info@ezflippr.com)

= More =
* [Also see our other plugins](http://www.nuagelab.com/products/wordpress-plugins/) or see [our WordPress.org profile page](http://profiles.wordpress.org/users/nuagelab/)
* For a limited time, use the <code>WORDPRESS</code> promocode for an instant 10$ off paid versions of your flipbooks.

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

= Adding flip books =

1. Create your flip book on www.ezflippr.com
1. Go to the ezFlippr configuration panel, and enter either your email or your access key (if you enter your email, your access key will be mailed to you)
1. Flip books will be imported and available in the Flip books menu. To install them on your server, simply click on the Install link.
1. To display the flipbook in a page, use the shortcode displayed in the flip books list (ex. [flipbook id="58" width="100%" height="500"])
1. To display the flipbook full page, simply use the "View" link in the flip books list.
1. You may customize the full page template by creating a single-ezflippr_flipbook.php file in your template directory. You may start from the file wp-content/plugins/ezflippr/resources/templates/single-ezflippr_flipbook.php.

= Installing CURL =

*On Ubuntu Linux*:
<code>
sudo apt-get install curl libcurl3 php5-curl
sudo service apache2 restart
</code>

*On Centos/RedHat Linux*:
CURL is installed by default with PHP.

*On WAMP*:
CURL is installed by default with WAMP.

*On MAMP*:
CURL is installed by default with MAMP.

== Frequently Asked Questions ==

= Do you plan to localize this plugin in a near future? =

This plugin is available in English, French and Spanish. If you want to help with translation in other languages, we'll be happy to hear from you.

== Changelog ==
= 1.1.11 =
* Fixed height problem with short code

= 1.1.10 =
* Added uploads directory writability verification and warning messages

= 1.1.9 =
* Added detection for php_openssl, which is required when cURL is not available and we must use the URL wrappers.

= 1.1.8 =
* Added contact form in ezFlippr menu
* Added loading message during refresh and key verification

= 1.1.7 =
* Added flipbook shortcode button to visual editor

= 1.1.6 =
* Added alternate download methods for low memory and allow_url_fopen=off hostings such as 1&1 and GoDaddy.

= 1.1.5 =
* Added Spanish translation

= 1.1.4 =
* Added User Agent when querying API and downloading

= 1.1.3 =
* Fixed problem with access key sometimes not being sent
* Tested with 4.2.2

= 1.1.2 =
* Added AJAX install/reinstall/uninstall

= 1.1.1 =
* Fixed early call to is_admin() causing a bug in the admin once in a while

= 1.1 =
* Added notice when flipbooks are modified/bought
* Moved flush_rewrite_rules to activation hook rather than registration

= 1.0 =
* First stable version.

= 0.0.1 =
* First released version. Tested internally with about 10 sites.

== Upgrade Notice ==
= 1.1.11 =
* Fixed height problem with short code

= 1.1.10 =
* Added uploads directory writability verification and warning messages

= 1.1.8 =
* Added contact form in ezFlippr menu
* Added loading message during refresh and key verification

= 1.1.7 =
* Added flipbook shortcode button to visual editor

= 1.1.6 =
* Added alternate download methods for low memory and allow_url_fopen=off hostings such as 1&1 and GoDaddy.

= 1.1.5 =
* Added Spanish translation

= 1.1.3 =
* Fixed problem with access key sometimes not being sent

= 1.1.2 =
* Added AJAX install/reinstall/uninstall

= 1.1.1 =
* Fixed early call to is_admin() causing a bug in the admin once in a while

= 1.1 =
* Added notice when flipbooks are modified/bought
* Moved flush_rewrite_rules to activation hook rather than registration

== Translations ==

* English
* Français
