<?php

 

if( !defined( 'ABSPATH' ))
	require '../plugin.php';

# Add Log of the visitor, on load wp_footer, not required in lite
#add_action( 'wp_footer', 'w4sl_insert_visitor', 1 );

 
 
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

   
?>
