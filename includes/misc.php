<?php

 

if( !defined( 'ABSPATH' ))
	require '../plugin.php';

# Add Log of the visitor, on load wp_footer
add_action( 'wp_footer', 'w4sl_insert_visitor', 1 );


 
# Clear Our Log Session ID upon Logout
function w4sl_logout(){
	if( !session_id())
		session_start();

	if( isset( $_SESSION['w4sl_sid'] )){
		if( version_compare( '4.0.6', phpversion(), '>=' )){
			session_unset( 'w4sl_sid' );
		}
		else{
			unset( $_SESSION['w4sl_sid'] );
		}
	}
}
add_action( 'wp_logout', 'w4sl_logout' );

# WordPress PLugable Function for Catching Login Page Errors and Mailing To an admin if Enabled From the Plugin Page.
if ( !function_exists('wp_authenticate')):
function wp_authenticate( $username, $password ){
	$username = sanitize_user($username);
	$password = trim($password);
	$user = apply_filters( 'authenticate', null, $username, $password);
	if( $user == null ){
		$user = new WP_Error( 'authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.'));
	}
	$ignore_codes = array( 'empty_username', 'empty_password', 'ip_restricted' );

	if( is_wp_error( $user ) && !in_array( $user->get_error_code(), $ignore_codes )){

		$ip = w4sl_get_ip();
		$http_referer = $_SERVER['HTTP_REFERER'];

		do_action( 'wp_login_errors', $user, $username, $password, $ip, $http_referer );
		do_action( 'wp_login_failed', $username );
	}
	return $user;
}
endif;

function w4sl_get_whitelist_ips(){
	$whitelist_ips = w4sl_get_options( 'whitelist_ips' );
	if( empty( $whitelist_ips )){
		$array = array();
	}
	else{
		$array = explode( "\n", $whitelist_ips );
		$array = array_map( 'trim', $array );
		$array = array_unique( $array );

		// Convert Domain Name Into Ip addresses.
		foreach( $array as $k => $v ){
			if( !w4sl_is_ip( $v )){
				$array[$k] = w4sl_domain_ip( $v );
			}
		}
	}

	return $array;
}

function w4sl_get_blacklist_ips(){
	$blacklist_ips = w4sl_get_options( 'blacklist_ips' );
	if( empty( $blacklist_ips )){
		$array = array();
	}
	else{
		$array = explode( "\n", $blacklist_ips );
		$array = array_map( 'trim', $array );
		$array = array_unique( $array );
	}

	return $array;
}

# LOGIN ERRORS NOTIFICATIONS
function w4sl_login_error_checks( $wp_error, $username, $password, $ip_address, $http_referer ){
	$login_error_blacklist = w4sl_get_options( 'login_error_blacklist' );
	$login_error_blacklist = intval( $login_error_blacklist );

	if( !$login_error_blacklist || $login_error_blacklist < 1 )
		return;

	foreach ( $wp_error->get_error_codes() as $code ){
		$severity = $wp_error->get_error_data( $code );
		if( 'incorrect_password' == $code )
			$errors[] = sprintf( 'Incorrect Password[%2$s] Entered For Username[%1$s]', $username, $password );
		elseif( 'invalid_username' == $code )
			$errors[] = sprintf( 'Incorrect/Invalid Username[%s]', $username );
		else{
			foreach( $wp_error->get_error_messages( $code ) as $error ){
				if ( 'message' != $severity ){
					$error = strip_tags( $error );
					$errors[] = wp_specialchars_decode( $error, ENT_QUOTES );
				}
			}
		}
	}

	if( empty( $errors ))
		return;

	if( !session_id())
		session_start();

	if( !isset( $_SESSION['w4sl_le'] )){
		$_SESSION['w4sl_le'] = '1';
	}
	else{
		$error_occured = intval( $_SESSION['w4sl_le'] );

		if( $login_error_blacklist <= $error_occured ){
			$blacklist_ips = w4sl_get_options( 'blacklist_ips' );
			
			if( empty( $blacklist_ips ))
				$blacklist_ips = $ip_address;
			else{
				$blacklist_ips = $ip_address ."\n". $blacklist_ips;
			}

			# Update the options
			w4sl_update_options( compact( array( 'blacklist_ips' )), true );
			
			if( version_compare( '4.0.6', phpversion(), '>=' )){session_unset( 'w4sl_le' );}
			else{unset( $_SESSION['w4sl_le'] );}
		}
		else{
			$_SESSION['w4sl_le'] = $error_occured + 1;
		}
	}
}
add_action( 'wp_login_errors', 'w4sl_login_error_checks', 2, 5 );


/*
 * Restrict User From Login From Blacklisted IP Address.
 * @param $userdata array userdata array
 * @hooked @ wp_authenticate_user
 */

function w4sl_authenticate_blacklisted_ips( $userdata ){
	$blacklist_ips = w4sl_get_blacklist_ips();
	$ip = w4sl_get_ip();

	if( in_array( $ip, $blacklist_ips )){
		# If the Ip is blacklisted, then remove all other hooks.
		remove_filter( 'wp_authenticate_user', 'w4sl_authenticate_whitelist_ips' );
		remove_all_actions( 'wp_login_errors' );

		return new WP_Error( 'ip_blacklisted' , 'You Ip has been Blacklisted. You Wont be able to login from it.' );
	}

	return $userdata;
}
add_filter( 'wp_authenticate_user', 'w4sl_authenticate_blacklisted_ips', 1 );


/*
 * Restrict User To Login Only From Whitelisted IP Address, If enabled.
 * @param $userdata array userdata array
 * @hooked @ wp_authenticate_user
 */

function w4sl_authenticate_whitelist_ips( $userdata ){
	$ip_restriction = (bool) w4sl_get_options( 'ip_restriction' );
	if( !$ip_restriction )
		return $userdata;

	$whitelist_ips = w4sl_get_whitelist_ips();
	$ip = w4sl_get_ip();

	if( !in_array( $ip, $whitelist_ips )){
		return new WP_Error( 'ip_restricted', 'You Can not Login From The IP Address You Are Using At Present.' );
	}

	return $userdata;
}
add_filter( 'wp_authenticate_user', 'w4sl_authenticate_whitelist_ips' );


/*
 * Restrict User From Reseting PassWord From An Blacklisted IP Address.
 * @param $allow bool true/false allow.
 * @param $user_ID interger user id
 * @hooked @ allow_password_reset
 */

function w4sl_disallow_password_reset_blacklisted_ips( $allow, $user_ID ){
	$blacklist_ips = w4sl_get_blacklist_ips();
	$ip = w4sl_get_ip();

	if( in_array( $ip, $blacklist_ips ))
		return new WP_Error( 'ip_blacklisted' , 'You Ip has been Blacklisted. You Wont be able to reset your password.' );

	return $allow;
}
add_filter( 'allow_password_reset', 'w4sl_disallow_password_reset_blacklisted_ips', 10, 2 );



/*
 * Grant User To Reset PassWord Only From Whitelisted IP Address.
 * @param $allow bool true/false allow.
 * @param $user_ID interger user id
 * @hooked @ allow_password_reset
 */

function w4sl_allow_password_reset_whitelisted_ips( $allow, $user_ID ){
	$ip_restriction = (bool) w4sl_get_options( 'ip_restriction' );
	if( !$ip_restriction )
		return $allow;

	$whitelist_ips = w4sl_get_whitelist_ips();
	$ip = w4sl_get_ip();

	if( !in_array( $ip, $whitelist_ips ))
		return new WP_Error( 'ip_restricted', 'You Wont be able to reset your password From The IP Address You Are Using At Present.' );

	return $allow;
}
add_filter( 'allow_password_reset', 'w4sl_allow_password_reset_whitelisted_ips', 10, 2 );


/*
 * Fire notification action for login from unknown IP.
 * @param $user_login string user login name
 * @param $user object user data object
 * @hooked @ wp_login
 */
function w4sl_wp_login( $user_login, $user ){
	$unknown_ip_notification = (bool) w4sl_get_options( 'unknown_ip_notification' );
	if( !$unknown_ip_notification )
		return;

	$whitelist_ips = w4sl_get_whitelist_ips();
	$ip = w4sl_get_ip();

	if( !in_array( $ip, $whitelist_ips )){
		$http_referer = $_SERVER['HTTP_REFERER'];
		do_action( 'wp_unknown_ip_login', $ip, $http_referer, $user );
	}
}
add_action( 'wp_login', 'w4sl_wp_login', 10, 2 );
?>