<?php
/*
Plugin Name: Triagis® Security Evaluation
Plugin URI: http://triagis.com 
Description: Liteweight backend solution to quickly scan your Wordpress installation for security vulnerabilities and common exploits
Version: 1.13 
Author: Triagis Ltd. 
Author URI: http://triagis.com
Author Email: support@triagis.com
*/

/*
Change Log 
= 1.13 =
Fixed CSS issues
= 1.12 =
Fixed critical bug, maintenance release
= 1.11 =
Removed clutter 
= 1.10 =
Fixed function error
*/

# Plugins Global Constant
define( 'W4SL_DIR', 			plugin_dir_path(__FILE__));
define( 'W4SL_URL', 			plugin_dir_url(__FILE__));
define( 'W4SL_BASENAME', 		plugin_basename( __FILE__ ));
define( 'W4SL_NAME', 			'Security Evaluation' );
define( 'W4SL_SLUG', 			'w4sl' );

define( 'W4SL_INC', 			W4SL_DIR . 'includes' );
define( 'W4SL_ADMIN', 			W4SL_DIR . 'admin' );

define( 'W4SL_MENU_POSITION',	'47.2' );
define( 'W4SL_MENU_NAME', 		'Triagis Evaluation' ); 


# This is a Fallback email if you are missing any files that belongs to the Plugin.
# Editing is recommended here.
# Do not contact me, please..
define( 'W4SL_EMAIL', 			'contact@triagis.com' );

# Define Plugin Table Global Varible.
global $wpdb;
$wpdb->w4sl_log 		= $wpdb->prefix . 'w4sl_log';
$wpdb->w4sl_spamlist 	= $wpdb->prefix . 'w4sl_spamlist';
$wpdb->w4sl_visitors 	= $wpdb->prefix . 'w4sl_visitors';
 
 


# Check is there any files missing from plugin directory
function w4sl_core_files_exists(){
	$includes = array( 
		W4SL_INC . '/functions.php',
		W4SL_ADMIN . '/admin.php',
		W4SL_ADMIN . '/database.php'
	);

	foreach( $includes as $file ){
		if( !file_exists( $file ))
			return false;
	}
	return true;
}

 

# If any files are missing, then show error in admin page and shut down our plugin process..
function w4sl_fallback_notice(){
	printf( '<div class="error"><p>You have Activated %1$s Plugin, unfortunately some files are missing from the plugin directory. Please <a href="mailto:%2$s">Contact Plugin Author</a> to get a Fresh Copy and Reinstall It.</p></div>', W4SL_NAME, W4SL_EMAIL );
}

# All necessary files exists, lets proceed..
if( w4sl_core_files_exists()){
	include( W4SL_INC .'/form.php' );
	include( W4SL_INC .'/errors.php' );
	include( W4SL_INC .'/functions.php' );
	include( W4SL_INC .'/misc.php' );
	include( W4SL_INC .'/cron.php' );
	include( W4SL_INC .'/query.php' );
 
	include( W4SL_INC .'/function-security-info.php' ); 

	if( is_admin()){
		include( W4SL_ADMIN .'/admin.php' );
		include( W4SL_ADMIN .'/database.php' );
		include( W4SL_ADMIN .'/misc.php' ); 
	
		include( W4SL_ADMIN .'/page-security-informations.php' );
		include( W4SL_ADMIN .'/page-premium.php' );

	 
		
	}
}
else{
	add_action( 'admin_notices', 'w4sl_fallback_notice' );
}
?>