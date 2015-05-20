=== Plugin Name ===
Contributors: winterpk
Tags: registration, verification, user, auth
Requires at least: 3.0.1
Tested up to: 4.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds email verification to registration

== Description ==

When activated, this plugin will override the default user registration function in pluggable.php to send an email verification link 
instead of the generated password email. It will create a new role type "Unverified" and autmatically set the "New User Default Role" to "Unverified" on activation. 

== Installation ==

1. Upload `email-verification` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

== Screenshots ==

== Changelog ==

= 1.0.0 =
* Beta release
