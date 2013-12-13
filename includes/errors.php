<?php
/**
 * W4 Error Class & Functions
 * @package W4 WordPress Error Class
 * @subpackage WordPress Security Plugin
 * @since 1.0
 */

if( !defined( 'ABSPATH' ))
	require '../plugin.php';


if( !class_exists( 'w4_notices' )):
// W4 Error Class
class w4_notices {
	function w4_notices(){
		$this->notices = array( 
			'error' => array(),
			'msg' => array(),
			'note' => array()
		);
	}

	function add_notice( $type = 'error', $text, $code = '' ){
		if( !empty( $text )){
			$code = !empty( $code ) ? $code : $type;
			$code = $this->unique_code( $type, $code );
			$this->notices[$type][$code] = $text;
		}
	}

	function add_msg( $text, $code = '' ){
		$this->add_notice( 'msg', $text, $code );
	}

	function add_error( $text, $code = '' ){
		$this->add_notice( 'error', $text, $code );
	}

	function add_note( $text, $code = '' ){
		$this->add_notice( 'note', $text, $code );
	}
	
	function unique_code( $type, $code = '' ){
		$codes = isset( $this->notices[$type] ) ? array_keys( $this->notices[$type] ) : array();

		if( !empty( $codes )){
#			echo $code;
			$check = in_array( $code, $codes );
			if ( $check ){
				$suffix = 2;
				do {
					$alt_code = $code . "-$suffix";
					$check = in_array( $alt_code, $codes );
					$suffix++;
				} while ( $check );
				$code = $alt_code;
			}
		}
		return $code;
	}
	
	function preview(){
		if( empty( $this->notices ))
			return;

		$notes = $this->notices['note'];
		if( !empty( $notes )){
			if( !is_array( $notes ))
				$notes = array( $notes );
		
			echo "\t<div class=\"notes\">\n";
			foreach( $notes as $note ){
				echo "<span>$note</span>";
			}
			echo "</div>\n";
		}

		$errors = $this->notices['error'];
		if( !empty( $errors )){
			if( !is_array( $errors ))
				$errors = array( $errors );
		
			echo "\t<div class=\"errors\">\n";
			foreach( $errors as $error ){
				echo "<span>$error</span>";
			}
			echo "</div>\n";
		}
		
		$messages = $this->notices['msg'];
		if( !empty( $messages )){
			if( !is_array( $messages ))
				$messages = array( $messages );
		
			echo "\t<div class=\"messages\">\n";
			foreach( $messages as $message ){
				echo "<span>$message</span>";
			}
			echo "</div>\n";
		}
	}
}
endif;

if( !function_exists( 'w4_add_error' )):
function w4_add_error( $error, $code = '' ){
	global $w4_notices;

	if ( empty( $w4_notices ))
		$w4_notices = new w4_notices();

	return $w4_notices->add_error( $error, $code );
}
endif;

if( !function_exists( 'w4_add_message' )):
function w4_add_message( $message, $code = '' ){
	global $w4_notices;

	if ( empty( $w4_notices ))
		$w4_notices = new w4_notices();

	return $w4_notices->add_msg( $message, $code );
}
endif;

if( !function_exists( 'w4_add_note' )):
function w4_add_note( $note, $code = '' ){
	global $w4_notices;

	if ( empty( $w4_notices ))
		$w4_notices = new w4_notices();

	return $w4_notices->add_note( $note, $code );
}
endif;

if( !function_exists( 'w4_preview_errors' )):
function w4_preview_errors(){
	global $w4_notices, $w4pgn_loaded;

	if ( empty( $w4_notices ))
		$w4_notices = new w4_notices();

	if( !isset( $w4pgn_loaded ))
		w4_global_notices();

	$w4_notices->preview();
	
	if( !isset( $w4pgn_loaded ))
		echo "<div id=\"w4_ajax_notice\"></div>\n";

	$w4pgn_loaded = true;
}
endif;


if( !function_exists( 'w4_global_notices' )):
function w4_global_notices(){
	$notices = apply_filters( 'w4_global_notices', array( 'ms' => array( 'u' => 'Updated' ), 'es' => array(), 'ns' => array()));
	$m_key = isset( $_REQUEST['m'] ) ? $_REQUEST['m'] : '';
	$e_key = isset( $_REQUEST['e'] ) ? $_REQUEST['e'] : '';
	$n_key = isset( $_REQUEST['n'] ) ? $_REQUEST['n'] : '';


	extract( $notices );
	if( !empty( $m_key ) && isset( $ms ) && isset( $ms[$m_key] ))
		w4_add_message( $ms[$m_key], $m_key );

	if( !empty( $e_key ) && isset( $es ) && isset( $es[$e_key] ))
		w4_add_error( $es[$e_key], $e_key );

	if( !empty( $n_key ) && isset( $ns ) && isset( $ns[$n_key] ))
		w4_add_note( $ns[$n_key], $n_key );
}
endif;


function w4sl_global_notices( $notices ){
	return array_merge( $notices, array(
		'ms' => array( 
			'cu' 					=> '<strong>Theme Files Checker Cron</strong> Fired. If any files has changed within last 6 hours, you will receive email notifications shortly.',
			'su' 					=> 'Spam List Data Updated.',
			'ld' 					=> 'Log deleted.',
			'u'						=> 'Updated..'
		),
		'es' => array(
			'fd' 					=> 'Failed Deletion.'
		),
		'ns' => array()
	));
}
add_filter( 'w4_global_notices', 'w4sl_global_notices' );
?>