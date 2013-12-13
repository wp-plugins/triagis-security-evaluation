<?php
/**
 * Cron Functions
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 */

if( !defined( 'ABSPATH' ))
	require '../plugin.php';


function _w4sl_corn_schedules( $schedules ){
	$schedules['hourly'] = array(
		'interval' 	=> 60 * 60,
		'display' 	=> 'Hourly'
	);

	$schedules['six_hourly'] = array(
		'interval' 	=> 60 * 60 * 6,
		'display' 	=> '6 Hourly'
	);

	$schedules['twicedaily'] = array(
		'interval' 	=> 60 * 60 * 12,
		'display' 	=> '12 Hourly'
	);

	$schedules['daily'] = array(
		'interval' 	=> 60 * 60 * 24,
		'display' 	=> 'Daily'
	);

	$schedules['weekly'] = array(
		'interval' 	=> 60 * 60 * 24 * 7,
		'display' 	=> 'Weekly'
	);

	$schedules['monthly'] = array(
		'interval' 	=> 60 * 60 * 24 * 30,
		'display' 	=> 'Monthly'
	);

	return $schedules;
}
add_filter( 'cron_schedules', '_w4sl_corn_schedules' );

function w4sl_reschedule_crons(){
	_w4sl_clear_crons();
	_w4sl_schedule_crons();
}
function _w4sl_schedule_crons(){
	if( !wp_next_scheduled( 'w4sl_theme_file_check_cron' )){
		wp_schedule_event(( time() + 1 ), 'six_hourly', 'w4sl_theme_file_check_cron' );
	}

	if( !wp_next_scheduled( 'w4sl_spam_post_scan_cron' )){
		$occurence = w4sl_get_options( 'spam_post_scan_occurence' );
		if( !empty( $occurence ) && $occurence != 'nocron' ){
			wp_schedule_event(( time() + 1 ), $occurence, 'w4sl_spam_post_scan_cron' );
		}
	}

	if( !wp_next_scheduled( 'w4sl_spam_file_download_cron' )){
		$occurence = w4sl_get_options( 'spam_file_download_occurence' );
		if( !empty( $occurence ) && $occurence != 'nocron' ){
			wp_schedule_event(( time() + 1 ), $occurence, 'w4sl_spam_file_download_cron' );
		}
	}
}
function _w4sl_clear_crons(){
	$hooks = array( 'w4sl_theme_file_check_cron', 'w4sl_spam_post_scan_cron', 'w4sl_spam_file_download_cron' );
	foreach( $hooks as $hook )
		w4sl_clear_cron( $hook );
}
function w4sl_reschedule_cron( $hook, $occurence = '' ){
	w4sl_clear_cron( $hook );
	if( !empty( $occurence ) && $occurence != 'nocron' )
		wp_schedule_event(( time() + 1 ), $occurence, $hook );
}
function w4sl_clear_cron( $hook ){
	$timestamp = wp_next_scheduled( $hook );
	wp_unschedule_event( $timestamp, $hook );

	if( wp_next_scheduled( $hook ))
		wp_clear_scheduled_hook( $hook );
}
?>