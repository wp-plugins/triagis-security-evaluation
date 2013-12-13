<?php
# Triagis FrameWork
# Url : http://triagis.com

function w4sl_admin_body_license(){
	global $wpdb, $w4sl_pagenow, $w4sl_action, $w4sl_admin_url, $license_options;
 
 
 
 	if( !isset( $license_options ) || !is_array( $license_options ))
		$license_options = array();

	$form_fields = array( 
		/*'timezone' => array(
			'type' 			=> 'timezone',
			'title'			=> 'Your Timezone <small>(please setup your timezone properly)</small>'
		),*/ 
		'license' => array(
			'type' 			=> 'text',
			'title'			=> 'Would you like to secure your Wordpress site? Go Pro at www.triagis.com/pro',
			'class'			=> 'med_size'  
		)
		  
	);

	$license_options['form_id'] = 'w4sl_license_form';
	//$license_options['button_text'] = 'Update Preference';
	$license_options['w4sl_license_options_update'] = '1';
	
	$mytext = '<h2>Premium: 24/7 Email Alerts + Admin Logs + DynDNS Whitelist For Only $39.99</h2><br/>Would you like to fully secure your Wordpress installation with a single plugin? Then get our premium plugin which includes
	24/7 Email Alerts, DynDNS WhiteList, IP Blacklists, Admin Log On Your Dashboard And Much More - <br/> <a style="font-size:22px;font-weight:bold" href="https://triagis.com/take-the-tour-the-best-features">Take The Tour</a> ';
	echo $mytext;
	//w4sl_parse_form_fields( $form_fields, $license_options );
}
add_action( 'w4sl_admin_body_license', 'w4sl_admin_body_license' );

function w4sl_admin_action_license(){
	global $wpdb, $w4sl_pagenow, $w4sl_action, $w4sl_admin_url, $license_options;
 
}
add_action( 'w4sl_admin_action_license', 'w4sl_admin_action_license' );
 
?>