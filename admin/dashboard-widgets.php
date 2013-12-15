<?php
/**
 * Dashboard Widget Function
 * @package W4 WordPress Adimn Page FrameWork
 * @subpackage WordPress Security Plugin
 * @since 1.0
 */

if( !defined( 'ABSPATH' ))
	require '../plugin.php';

	
function w4sl_dashboard_scripts(){
	if( !is_super_admin())
		return;

	wp_enqueue_style( 'w4sl-dashboard-css', W4SL_URL . 'scripts/dashboard.css' );
	wp_enqueue_script( 'w4sl-dashboard-js', W4SL_URL . 'scripts/dashboard.js', array( 'jquery'),'', true );

	global $w4sl_options;
	$w4sl_options = w4sl_get_options();
	
	if( isset( $_POST['w4sl_options_update'] ) && '1' == $_POST['w4sl_options_update'] ){

		$fields = array( 
		'admin_email', 'mail_if_failed_login', 'mail_if_theme_changed',
		'login_error_blacklist', 'blacklist_ips',
		'whitelist_ips', 'ip_restriction', 'unknown_ip_notification', 'domain2ip',
		'plugin_moderators', 'plugin_restriction', 'plugin_activating_notification'
		);

		foreach( $fields as $field )
			$w4sl_options[$field] = !empty( $_POST[$field] ) ? $_POST[$field] : '';

		update_option( 'gmt_offset', $_POST['timezone'] );

		if( !is_array( $w4sl_options['plugin_moderators'] ))
			$w4sl_options['plugin_moderators'] = array();

		$uid = get_current_user_id();
		if( empty( $w4sl_options['plugin_moderators'] ))
			$w4sl_options['plugin_moderators'] = array( $uid );
		elseif( !in_array( $uid, $w4sl_options['plugin_moderators'] ))
			$w4sl_options['plugin_moderators'] = array_merge( array( $uid ), $w4sl_options['plugin_moderators'] );
		

		$w4sl_options['whitelist_ips'] = w4sl_sanitize_ips_string( $w4sl_options['whitelist_ips'],
		(bool) $w4sl_options['domain2ip'] );

		# Force Adding the current user ip into whitelish.
		$ip = w4sl_get_ip();
		if( empty( $w4sl_options['whitelist_ips'] ))
			$w4sl_options['whitelist_ips'] = $ip;
		elseif( !preg_match( '|'.$ip.'|', $w4sl_options['whitelist_ips'] )){
			$w4sl_options['whitelist_ips'] = $ip ."\n". $w4sl_options['whitelist_ips'];
		}

		$w4sl_options['blacklist_ips'] = w4sl_sanitize_ips_string( $w4sl_options['blacklist_ips'] );

		# Force Adding the current user ip into whitelish.
		if( !empty( $w4sl_options['login_error_blacklist'] ))
			$w4sl_options['login_error_blacklist'] = preg_replace( '/[^0-9]/', '', $w4sl_options['login_error_blacklist'] );

		$update = w4sl_update_options( $w4sl_options );

		if( is_wp_error( $update  ))
			return w4_add_error( $update->get_error_message());

		wp_redirect( add_query_arg( array( 'm' => 'w4slu' ), admin_url( '/index.php' )));
		return;
	}
}
add_action( 'wp_dashboard_setup', 'w4sl_dashboard_scripts' );

function w4sl_dashboard_widget(){
	if( !is_super_admin())
		return;

	global $wpdb, $w4sl_admin_url, $w4sl_options;




	if( !isset( $w4sl_options ) || !is_array( $w4sl_options ))
		$w4sl_options = w4sl_get_options();

	$us = get_users( array( 'role' => 'administrator' ));
	$uso = array();
	foreach( $us as $u )
		$uso[$u->ID] = $u->display_name;

	if( isset( $_GET['m'] ) && $_GET['m'] == 'w4slu' ){
		echo '<div class="messages"><span>Updated.</span></div>';
	}

	$form_fields = array(
		'w4sl_options_update' => array(
			'type' 			=> 'hidden'
		),
		'admin_email' => array(
			'type' 			=> 'text',
			'title'			=> 'Where to send email alert, enter an email address',
			'class'			=> 'med_size',
			'default'		=> get_option( 'admin_email' )
		),
		'timezone' => array(
			'type' 			=> 'timezone',
			'title'			=> 'Your Timezone'
		),
		'mail_if_failed_login' => array(
			'type' 			=> 'radio',
			'title'			=> 'Email when failed login ?',
			'option'		=> array( '0' => 'No', '1' => 'Yes' )
		),
		'mail_if_theme_changed' => array(
			'type' 			=> 'radio',
			'title'			=> 'Email whenever theme files change ?',
			'option'		=> array( '0' => 'No', '1' => 'Yes' )
		),
		'login_error_blacklist' => array(
			'type' 			=> 'text',
			'title'			=> 'Add ip to blacklist if visitor fails to login for given number of times, leave empty to disable this.',
			'class'			=> 'small_size'
		),
		'blacklist_ips' => array(
			'type' 			=> 'textarea',
			'title'			=> 'Blacklist IPs <small>(insert one ip per line, no comma)</small><br /><small>Rember, blacklisted ips are completely forbidden to login !!! Add them very carefully</small>.',
		),
		'ip_restriction' => array(
			'type' 			=> 'radio',
			'title'			=> 'Enable Login with only whitelisted ips ?</small>',
			'option'		=> array( '0' => 'No', '1' => 'Yes' )
		),
		'unknown_ip_notification' => array(
			'type' 			=> 'radio',
			'title'			=> 'Email when log in from unknown ip ?',
			'option'		=> array( '0' => 'No', '1' => 'Yes' )
		),
		'domain2ip' => array(
			'type' 			=> 'radio',
			'title'			=> 'Convert Domain Names to IP address <small>(for whitelisted Ips)</small>?',
			'option'		=> array( '0' => 'No', '1' => 'Yes' )
		),
		'whitelist_ips' => array(
			'type' 			=> 'textarea',
			'title'			=> 'Whitelist IPs <small>(insert one ip per line, no comma)</small><br />',
			'rows'			=> '10'
		),
		'plugin_activating_notification' => array(
			'type' 			=> 'radio',
			'title'			=> 'Email when plugin activated ?',
			'option'		=> array( '0' => 'No', '1' => 'Yes' )
		),
		'plugin_restriction' => array(
			'type' 			=> 'radio',
			'title'			=> 'Enable Plugin management features only from Defined users ?</small>',
			'option'		=> array( '0' => 'No', '1' => 'Yes' )
		),
		'plugin_moderators' => array(
			'type' 			=> 'checkbox',
			'title'			=> 'Select User who can manage plugins',
			'option'		=> $uso
		)
	);

	$w4sl_options['action'] = admin_url( '/index.php' );
	$w4sl_options['form_id'] = 'w4sl_dashboard_form';
	$w4sl_options['button_text'] = 'Update Preference';
	$w4sl_options['form_btn_top'] = false;
	$w4sl_options['w4sl_options_update'] = '1';

	w4sl_parse_form_fields( $form_fields, $w4sl_options );
}

function w4sl_dashboard_widget_log(){

	$w4sl_logs_args = array( 'limit' => 10, 'orderby' => 'log_time', 'order' => 'desc', 'join_name' => true );
	$w4sl_logs = w4sl_log_query( $w4sl_logs_args );

	echo '<table class="widefat" cellspacing="0" cellpadding="0" border="0" width="100%">';
	echo '<tr>';
	
	$header_columns = array(
		'user_id' => array( 'lbl' => "User id", 'sortable' => false ),
		'ip_address' => array( 'lbl' => "IP", 'sortable' => false ),
		'log_time' => array( 'lbl' => "Time", 'sortable' => false )
	);
	$header_columns_count = count( $header_columns );
	
	foreach( $header_columns as $key => $header_column ){
		$sortable = false;

		$class = 'manage-column';
		$lbl = $header_column['lbl'];
		echo "<th class='$class'>$lbl</th>";
	}

	echo '</tr><tbody>';

	$class = 'alternate';

	if( count( $w4sl_logs ) < 1 ):
		echo "<tr class='alternate'><td align='center' colspan='$header_columns_count'>No available logs</td></tr>";
	
	else:
		foreach( $w4sl_logs as $log ){
			$class = ( $class != 'alternate') ? 'alternate' : '';

			echo "<tr class='$class'>";
			echo "<td><abbr title='$log->name'>{$log->user_id}</abbr></td>";
			echo "<td>{$log->ip_address}</td>";

			echo "<td>";
				$log_time = $log->log_time;
				
				if( !empty( $log_time ) || '0000-00-00 00:00:00' != $log_time ){
					echo "<abbr title='" . mysql2date( 'l, d-M-y @ g:i:s a', $log_time ) . "'>". mysql2date( 'd-m-y g:i:s a', $log_time ) . "<abbr>";
				}
			echo "</td>";
			echo "</tr>";
		}
	endif;

	echo '</tbody></table>';
}
function w4sl_dashboard_widgets(){
	if( is_super_admin()){
		wp_add_dashboard_widget( 'w4sl_dashboard_widget', W4SL_NAME . " Settings", 'w4sl_dashboard_widget');
		wp_add_dashboard_widget( 'w4sl_dashboard_widget_log', W4SL_NAME . " Admin Log", 'w4sl_dashboard_widget_log');
	}
}
add_action( 'wp_dashboard_setup', 'w4sl_dashboard_widgets' );

function w4sl_admin_bar_menu( $wp_admin_bar ){
	if( !is_super_admin())
		return;

	global $w4sl_subpages, $w4sl_admin_url;
	if( isset( $w4sl_subpages )){
		$parent = 'w4sl';
		$wp_admin_bar->add_menu( array( 'id' => $parent, 'title' => W4SL_NAME, 'href' => "{$w4sl_admin_url}&subpage=email_alert" ));

		foreach( $w4sl_subpages as $_pagenow => $name ){
			$id = 'w4sl-' . $_pagenow;
			$wp_admin_bar->add_menu( array( 'id' => $id, 'parent' => $parent, 'title' => $name, 'href' => "{$w4sl_admin_url}&subpage={$_pagenow}" ));
		}
	}
}
add_action( 'admin_bar_menu', 'w4sl_admin_bar_menu', 999 );

?>