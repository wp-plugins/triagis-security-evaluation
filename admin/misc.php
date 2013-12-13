<?php
/**
 * Function Admin Misc
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 */

if( !defined( 'ABSPATH' ))
	require '../plugin.php';


/**
 * Load the Theme files data and save upon new theme activation
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 */

add_action( 'after_switch_theme', 'w4sl_update_theme_files_info' );


/**
 * Load the Theme files data and save, schedule crons, install database upon this plugin activation
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 */

add_action( 'activate_' . W4SL_BASENAME, 'w4sl_update_theme_files_info' );
add_action( 'activate_' . W4SL_BASENAME, '_w4sl_install_db' );
add_action( 'activate_' . W4SL_BASENAME, '_w4sl_schedule_crons' );


/**
 * Clear the cron if plugin deactivated
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 */

add_action( 'deactivate_' . W4SL_BASENAME, '_w4sl_clear_crons' );


/**
 * Restrict WordPress Admin Plugin Page Access.
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 */

function w4sl_restrict_plugin_page(){
	global $menu, $submenu, $pagenow;
	
	$plugin_pages = array( 'plugins.php', 'plugin-install.php', 'plugin-editor.php' );
	if( !is_admin() || !in_array( $pagenow, $plugin_pages ))
		return;

	$plugin_restriction = (bool) w4sl_get_options( 'plugin_restriction' );
	if( !$plugin_restriction )
		return;

	$current_user_id = get_current_user_id();
	$plugin_moderators = w4sl_get_options( 'plugin_moderators' );
	if( !is_array( $plugin_moderators ))
		$plugin_moderators = array();

	if( in_array( $current_user_id, $plugin_moderators ))
		return;
	
	wp_die( sprintf( 'You do not have sufficient permissions to edit plugins for this site. Back to <a href="%s">admin</a>', admin_url( '/' )));
}
add_action( 'plugins_loaded', 'w4sl_restrict_plugin_page',2 );


/**
 * Remove WordPress Admin Plugin Page Menu.
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 */

function _w4sl_restricted_admin_menu(){
	global $menu, $submenu;

	$plugin_restriction = (bool) w4sl_get_options( 'plugin_restriction' );
	if( !$plugin_restriction )
		return;

	$current_user_id = get_current_user_id();
	$plugin_moderators = w4sl_get_options( 'plugin_moderators' );
	if( !is_array( $plugin_moderators ))
		$plugin_moderators = array();

	if( in_array( $current_user_id, $plugin_moderators ))
		return;
	
	unset( $menu['65'] ); #plugin
}
add_action( 'admin_menu', '_w4sl_restricted_admin_menu', 13 );
?>