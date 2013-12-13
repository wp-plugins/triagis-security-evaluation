=== Triagis Security Evaluation ===
Contributors: soliver, triagis-ltd
Donate link: http://triagis.com
Tags: security, permissions, wp-config, wp_head, wp_generator, database, php errors, server, ssl, prefix, mod_security, timthumb, spam
Requires at least: 3.0.1
Tested up to: 3.7.1
Stable tag: 1.11
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

Triagis Security Evaluation is a simple lite-weight plugin to analyze your current Wordpress installation and server config for common security flaws. 

== Description ==

Wordpress can be easily secured by following a few best security practices. We check your server and Wordpress installation for common security pittfalls. 

Some of the things we check for: 

* Location of your wp-config.php 
* Is mod_security enabled
* Is SSL for backend enabled
* What information do you expose 
* Do you currently allow PHP to display errors?
* What permission does your wp-config file, folders and other files on the server have - are they secure?
* Is your server software up to date (MySQL,PHP,OS)
* What database prefix do you use?
* What is the username of the admin account

= Don't Expose Wordpress Version =

A default Wordpress installation will expose your version. Hackers scan sites for exploits and always look for older versions that are still vulnerable. Use our suggestion to remove it

= Check Folder Permissions =

Most Wordpress installations get hacked due to insecure folder permissions. World-writable (777) permissions invite other users to upload and access files to your server, making it highly vulnerable.


= Further Reading =
For more info, check out the following articles and videos:

* [Secure Your Site Using HTACCESS - HTTP Authentication](http://www.youtube.com/watch?v=adl35wXv850).
* [Secure Passwords](http://www.webmaster.net/video-guide-secure-passwords-and-gaining-access-wordpress-when-you-forget-your-password). 
* [Triagis Security Plugin - Take The Tour](https://triagis.com/take-the-tour-the-best-features).


== Installation ==

This will help you to correctly install the plugin

1. Upload `triagis-security-evaluation` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Verify that your server has the correct permissions. Usually nobody:nobody or www-data:www-data need to own the public_html folder in order to move the wp-config.php to a more secure location


== Frequently Asked Questions ==

No questions yet - send questions to support@triagis.com
 
 

== Screenshots ==

1. Displays Urgent Warnings In Red / Yellow
2. Various security options you can check and modify directly on the plugin page e.g. table prefix, admin account name, etc. 

== Changelog ==

= 1.11 =
* Cleaned up files 

= 1.10 =
* Fixed various functions

= 1.0 =
* Initial stable version

 