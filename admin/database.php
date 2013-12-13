<?php
if( !defined( 'ABSPATH' ))
	require '../plugin.php';

# Installing Plugin Database.
function _w4sl_install_db(){
	global $wpdb;

	$sql = array();
	$charset_collate = "";

	if( $wpdb->get_var("SHOW TABLES LIKE '$wpdb->w4sl_log'") != $wpdb->w4sl_log ):
		if ( !empty ( $wpdb->charset))
			$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";

		if ( !empty ( $wpdb->collate))
			$charset_collate .= " COLLATE {$wpdb->collate}";

		$sql[] = "CREATE TABLE {$wpdb->w4sl_log}(
		  log_id bigint(20) unsigned NOT NULL auto_increment,
		  user_id bigint(20) unsigned NOT NULL DEFAULT '0',
		  session_id varchar(200) NOT NULL,
		  ip_address varchar(200) NOT NULL,
		  http_referer varchar(200) NOT NULL,
		  log_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		  PRIMARY KEY  log_id (log_id)
		){$charset_collate};";
	endif;

	if( $wpdb->get_var("SHOW TABLES LIKE '$wpdb->w4sl_spamlist'") != $wpdb->w4sl_spamlist ):
		if ( !empty ( $wpdb->charset))
			$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";

		if ( !empty ( $wpdb->collate))
			$charset_collate .= " COLLATE {$wpdb->collate}";

		$sql[] = "CREATE TABLE {$wpdb->w4sl_spamlist}(
		  list_id bigint(20) unsigned NOT NULL auto_increment,
		  data longtext NOT NULL,
		  PRIMARY KEY  list_id (list_id)
		){$charset_collate};";
	endif;
	
	if( $wpdb->get_var("SHOW TABLES LIKE '$wpdb->w4sl_visitors'") != $wpdb->w4sl_visitors ):
		if ( !empty ( $wpdb->charset))
			$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";

		if ( !empty ( $wpdb->collate))
			$charset_collate .= " COLLATE {$wpdb->collate}";

		$sql[] = "CREATE TABLE {$wpdb->w4sl_visitors}(
		  visitor_id bigint(20) unsigned NOT NULL auto_increment,
		  visitor_ip varchar(50) NOT NULL,
		  visitor_url varchar(255) NOT NULL,
		  visitor_agent varchar(255) NOT NULL,
		  visitor_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		  PRIMARY KEY  visitor_id (visitor_id)
		){$charset_collate};";
	endif;
	
	 
	
	 
	 
 
}
?>