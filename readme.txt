=== CloudFlare Rocket Loader Ignore ===
Tags: cloudflare, rocket loader, script, javascript, ignore
Requires at least: 3.5
Tested up to: 3.9
Contributors: jp2112
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7EX9NB9TLFHVW
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Instruct CloudFlare's Rocket Loader to ignore specific scripts.

== Description ==

This plugin tells CloudFlare's Rocket Loader not to process the given script by adding an attribute to the script tag. Per https://support.cloudflare.com/hc/en-us/articles/200169436--How-can-I-have-Rocket-Loader-ignore-my-script-s-in-Automatic-Mode-

Disclaimer: This plugin is not affiliated with or endorsed by CloudFlare.

<h3>If you need help with this plugin</h3>

If this plugin breaks your site or just flat out does not work, please go to <a href="http://wordpress.org/plugins/cloudflare-rocket-loader-ignore/#compatibility">Compatibility</a> and click "Broken" after verifying your WordPress version and the version of the plugin you are using.

Then, create a thread in the <a href="http://wordpress.org/support/plugin/cloudflare-rocket-loader-ignore">Support</a> forum with a description of the issue. Make sure you are using the latest version of WordPress and the plugin before reporting issues, to be sure that the issue is with the current version and not with an older version where the issue may have already been fixed.

<strong>Please do not use the <a href="http://wordpress.org/support/view/plugin-reviews/cloudflare-rocket-loader-ignore">Reviews</a> section to report issues or request new features.</strong>

== Installation ==

1. Upload plugin file through the WordPress interface.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Settings &raquo; CloudFlare Rocket Loader Ignore, configure plugin.
4. View a page that contains a script tag with one of the scripts you configured, it should contain a "data-cfasync=false" attribute. Other scripts should not.

== Frequently Asked Questions ==

= How do I use the plugin? =

Go to Settings &raquo; CloudFlare Rocket Loader Ignore and insert filenames of .js files you want the Rocket Loader to not process. Make sure the "enabled" checkbox is checked. One filename per line.

To exclude specific files, use the full filename including the .js file extension. You can also match patterns by using only the filename or parts of the filename. For example, to exclude any .js file containing the name "jquery" (ex: jquery, jquery-migrate, jquery-ui, etc), simply enter <strong>jquery</strong>. Any .js file that contains the word "jquery" will be ignored by Rocket Loader.

= I entered some filenames but don't see any changes on the page. =

Are you caching your pages?

= I don't want the admin CSS. How do I remove it? =

Add this to your functions.php:

`remove_action('admin_head', 'insert_cfrli_admin_css');`

== Screenshots ==

1. Plugin settings page
2. HTML source of a webpage showing attribute added to W3TC combined script

== Changelog ==

= 0.0.6 =
- updated .pot file and readme
- fixed error with empty needle in textarea array
- code to add data-cfasync attribute to combined scripts has been added but not implemented yet

= 0.0.5 =
- fixed issue with breaking admin functionality (added is_admin() check)
- added prelim code to add cfa attribute to inline and combined JS, pending response from people in forum (http://wordpress.org/support/topic/not-working-with-multiple-script-filenames)

= 0.0.4 =
- added option to switch plugin sentiment from false to true
- footer scripts also included

= 0.0.3 =
- fixed issue with plugin not handling multiple scripts
- adjusted code placement per CloudFlare guidelines

= 0.0.2 =
- fixed validation code
- pattern matching is more precise: code only checks filename for match instead of whole string

= 0.0.1 =
- created

== Upgrade Notice ==

= 0.0.6 =
- updated .pot file and readme; fixed error with empty needle in textarea array; code to add data-cfasync attribute to combined scripts has been added but not implemented yet

= 0.0.5 =
- fixed issue with breaking admin functionality (added is_admin() check); added prelim code to add cfa attribute to inline and combined JS

= 0.0.4 =
- added option to switch plugin sentiment from false to true; footer scripts also included

= 0.0.3 =
- fixed issue with plugin not handling multiple scripts; adjusted code placement per CloudFlare guidelines

= 0.0.2 =
- fixed validation code; pattern matching is more precise: code only checks filename for match instead of whole string

= 0.0.1 =
- created