<?php



if( !defined( 'ABSPATH' ))
	require '../plugin.php';

/**
 * Get Plugin Options.
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 * @param $key string - Option key
 * @return array|string key value or all key => value array
 */

function w4sl_get_options( $key = '', $option_name = 'w4sl_options' ){
	$options = get_option( $option_name );

	if( !$options )
		return false;

	if( !$key )
		return $options;

	if( !is_array( $key ) && !array_key_exists( $key, $options ))
		return false;

	if( is_array( $key )){
		$return = array();
		foreach( $key as $k ){
			$return[$k] = array_key_exists( $k, $options ) ? apply_filters( 'w4sl_option_'. $k, $options[$k] ) : false;
		}
	}
	else{
		$return = apply_filters( 'w4sl_option_'. $key, $options[$key] );
	}

	if( $return == '' || !isset( $return ) || empty( $return ) || false == $return ){
		if( is_array( $key ))
			return array();
		
		else
			return '';
	}

	return $return;
}


/**
 * Update our plugin options.
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 * @param $update array - Update array key => value pair
 * @param $override bool - save only this or update with existing
 * @return array all options
 */

function w4sl_update_options( $update = array(), $option_name = 'w4sl_options', $override = true ){
	if( empty( $update ))
		return;

	if( !is_array( $update ))
		$update = (array) $update;

	if( $override ){
		$old_update = w4sl_get_options('', $option_name);
		$update = wp_parse_args( $update, $old_update );
	}

	update_option( $option_name, $update );

	return $update;
}


/**
 * Hook fire after plugin options have been update
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 * @param $oldvalue array - Old option values
 * @param $newvalue array - New option values
 */

function w4sl_after_options_updated( $oldvalue, $newvalue ){
	if( 
		( !empty( $newvalue['spam_file_download_occurence'] ) && $newvalue['spam_file_download_occurence'] != $oldvalue['spam_file_download_occurence'] )
		||
		( empty( $oldvalue['spam_file_download_occurence'] ) && !empty( $newvalue['spam_file_download_occurence'] ) )
	){
		w4sl_reschedule_cron( 'w4sl_spam_file_download_cron', $newvalue['spam_file_download_occurence'] );
	}
	
	if( 
		( !empty( $newvalue['spam_post_scan_occurence'] ) && $newvalue['spam_post_scan_occurence'] != $oldvalue['spam_post_scan_occurence'] )
		||
		( empty( $oldvalue['spam_post_scan_occurence'] ) && !empty( $newvalue['spam_post_scan_occurence'] ) )
	){
		w4sl_reschedule_cron( 'w4sl_spam_post_scan_cron', $newvalue['spam_post_scan_occurence'] );
	}
	
}
add_action( 'update_option_w4sl_options', 'w4sl_after_options_updated', 10, 2 );


/**
 * Get current visitor ip address
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @return string ip address
 * @since 1.0
 */

function w4sl_guess_url( $query = '' ){
	$pageURL = '';

	if( is_admin()){
		$pageURL = remove_query_arg( array( 'm', 'e', 'n', 'lpage' ));

		if ( !empty( $query )){
			$r = wp_parse_args( $query );
			foreach ( $r as $k => $v ){
				if ( strpos( $v, ' ' ) !== false )
					$r[$k] = rawurlencode( $v );
			}
			$pageURL = add_query_arg( $r, $url );
		}
	}

	if ( !$pageURL || $pageURL=="" || !is_string( $pageURL )){
		$pageURL = "";
		$pageURL = ( isset( $_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
		
		if ($_SERVER["SERVER_PORT"] != "80" )
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		else
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

		if ( !strstr( get_bloginfo('url'),'www.'))
			$pageURL = str_replace( 'www.','', $pageURL );
	}

	if ( force_ssl_login() || force_ssl_admin()){
		$pageURL = str_replace( 'http://', 'https://', $pageURL );
	}

	return $pageURL;
}


/**
 * WordPress wp_main email from
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 */

function w4sl_mail_from( $from_email ){
	$sitename = strtolower( $_SERVER['SERVER_NAME'] );
	if ( substr( $sitename, 0, 4 ) == 'www.' ) {
		$sitename = substr( $sitename, 4 );
	}
	$from_email = 'no-reply@' . $sitename;
	return $from_email ;
}


/**
 * WordPress wp_main email from name
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 */

function w4sl_mail_from_name( $from_name ){
	$from_name = 'Security Lockdown';
	return $from_name;
}


# This is a Debug Helper for array or Object Values.
function w4sl($a){
	echo '<pre>';print_r($a);echo '</pre>';
}
 
 
?>