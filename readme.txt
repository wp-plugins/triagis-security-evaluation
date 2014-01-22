=== Triagis® Security Evaluation - Check Folder Permissions, Fix For Common Security Vulnerabilities ===
Contributors: soliver, triagis-ltd, webmaster-net 
Donate link: http://triagis.com
Tags: security, permissions, wp-config, wp_head, wp_generator, database, php errors, server, ssl, prefix, mod_security, timthumb, spam
Requires at least: 3.0.1
Tested up to: 3.8 
Stable tag: 1.14  
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

Triagis® Security Evaluation is a simple lite-weight plugin to analyze your current Wordpress installation, server for security vulnerabilities. 

== Description == 

Wordpress can be easily secured by following a few best security practices. We check your server and Wordpress installation for common security vulnerabilities, which you can then address right on the plugin page itself!

= Some of the things we check for, which you can fix with a few clicks! =

* Check if thumthumb.php or other scripts exist that are easily exploited 
* Location of your wp-config.php 
* Is mod_security enabled 
* Is SSL for backend enabled
* What information do you expose 
* Do you currently allow PHP to display errors?
* What permission does your wp-config file, folders and other files on the server have - are they secure?
* Is your server software up to date (MySQL,PHP,OS)
* What database prefix do you use? (1-click Fix available)
* What is the username of the admin account (1-click Fix available)


= Don't Expose Wordpress Version Using Our Suggestions =

A default Wordpress installation will expose your version. Hackers scan sites for exploits and always look for older versions that are still vulnerable. Use our suggestion to remove it

= Check Folder Permissions With 1-Click =

Most Wordpress installations get hacked due to insecure folder permissions. World-writable (777) permissions invite other users to upload files to your server, making it highly vulnerable.


= Move Wp-Content Directory =

Most Wordpress installations use a folder called wp-content and a subdirectory "uploads". If you want to make it a little more difficult for possible automated
attacks to succeed you might want to consider changing your wp-content directory name. With our plugin you can do that with a few clicks. NOTE: This is intended for development
environments and not production sites. We do not recommend to try this on your live sites. 

= Why TimThumb Poses A Security Threat = 

On all servers that host Wordpress sites you will have automated scans for a file called timthumb.php or a variation of other names that are targeting exactly this file. Why?
Because timthumb.php is very easy to exploit if you set the wrong file and folder permissions. If your server is mis-configured, timthumb.php poses a significant threat 
to your site and server. That's why we recommend that beginners try to locate plugins that make use of this script and try to find alternatives. An alternative approach is to move the timthumb.php outside
the public folders. 


= Future Versions =

Planned for future versions is a dashboard widget with important information at a glance and additional security checks

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

= 1.14 = 
* Fixed issue for some lite users 

= 1.13 =
* Minor update, fixed some CSS issues

= 1.12 =
* Fixed critical bug, further cleanup
* Added disclaimer before moving wp-content directory 

= 1.11 =
* Cleaned up files 

= 1.10 =
* Fixed various functions

= 1.0 =
* Initial stable version

 
