<?php
if( !defined( 'ABSPATH' ))
	require '../plugin.php';

/**
 * Delete directory and all files init
 * @param string $dir, directory path
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 */

function w4sl_delete_dir( $dir = '' ){
	if( !is_dir( $dir ) || !is_writable( $dir ))
		return false;

	$dir = untrailingslashit( $dir );
	$dh = opendir( $dir );
	while(( $file = readdir( $dh )) !== false ){
		if ( in_array( $file, array( '.', '..' )))
			continue;

		if( is_dir( $dir . '/' . $file ))
			w4sl_delete_dir( $dir . '/' . $file );
		else
			@unlink( $dir . '/' . $file );		
	}
	closedir( $dh );
	@rmdir( $dir );

	return true;
}

/**
 * Check if current database user can ALTER table
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 * @return bool true|false
 */

function w4sl_dbuser_can_alter_table(){
	global $wpdb;

	$grants = $wpdb->get_results( "SHOW GRANTS FOR CURRENT_USER()", ARRAY_N );
	if( empty( $grants ))
		return false;

	foreach( $grants as $grant ){
		if( !empty( $grant[0] )){
			$grant_str = strtoupper($grant[0]);
            if( preg_match( "/GRANT ALL PRIVILEGES/i", $grant_str ) || preg_match( "/ALTER\s*[,|ON]/i", $grant_str ))
				return true;
        }
    }
	return false;
}


function w4sl_sanitize_dbprefix( $prefix ){
	$prefix = preg_replace( '/[^0-9a-zA-Z_]/', '', $prefix );
	if( substr( $prefix, -1 ) != '_' )
		$prefix .= '_';
	
	return $prefix;
}

/**
 * Get Operating Sytem, Server Software, Mysql, PHP info
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 * @return array key => value array
 */

function w4sl_osmp_info(){
	global $is_apache, $is_IIS, $is_iis7,
	$wpdb,
	$wp_version, $wp_db_version, $tinymce_version;

	$info = array();

	# SERVER
	if( isset( $_SERVER["SERVER_SOFTWARE"] )){
		$sse = explode( ' ', $_SERVER["SERVER_SOFTWARE"] );
		if( !empty( $sse['0'] ))
			$info['server_software'] .= $sse['0'];
		else
			$info['server_software'] .= $_SERVER["SERVER_SOFTWARE"];
	}
	elseif( $is_apache )
		$info['server_software'] = "Apache";
	elseif( $is_IIS )
		$info['server_software'] = "Microsoft-IIS";
	elseif( $is_iis7 )
		$info['server_software'] = "Microsoft-IIS/7";
	else
		$info['server_software'] = "Unknown";


	# OPERATING SYSTEM
	if( strtoupper( substr( PHP_OS, 0, 3 )) === 'WIN' )
		$info['operating_system'] = "Windows";
	elseif( strtoupper( substr( PHP_OS, 0, 5 )) === 'LINUX' )
		$info['operating_system'] = "Linux";
	else
		$info['operating_system'] = PHP_OS;


	# PHP
	$info['php_version'] = phpversion();
	$info['php_is_safe_mode_enable'] = ini_get('safe_mode' ) ? 'Yes' : 'No';
	$info['php_memory_limit'] = ini_get( 'memory_limit' ) ? ini_get( 'memory_limit' ). 'b' : 'N/A';
	$info['php_maximum_execution_time'] = ini_get( 'max_execution_time' ) ? ini_get( 'max_execution_time' ) . ' seconds' : 'N/A';
	$info['php_maximum_post_size'] = ini_get( 'post_max_size' ) ? ini_get( 'post_max_size' ) . 'b' : 'N/A';
	
	if( ini_get('safe_mode' ) || in_array( 'shell_exec', array_map( 'trim', explode( ',', ini_get( 'disable_functions' )))) || !@shell_exec( 'echo WordPress' ))
		$info['php_shell_execution_enable'] = 'No';
	else
		$info['php_shell_execution_enable'] = 'Yes';
	

	# MYSQL
	$info['mysql_version'] = $wpdb->get_var( "SELECT VERSION() AS version" );

	# DATABASE
	$info['database_host'] = DB_HOST;
	$info['database_name'] = DB_NAME;
	$info['database_user'] = DB_USER;

	$dbsize = 0;
   	$dbres = $wpdb->get_results( 'SHOW TABLE STATUS FROM ' . DB_NAME, ARRAY_A );
   	foreach ( $dbres as $dbr )
		$dbsize += (float) $dbr['Data_length'];

	$info['database_total_table'] = count( $dbres );
	$info['database_size'] = w4sl_format_size( $dbsize );

	# WORDPRESS
	$info['wordpress_version'] = $wp_version;
	$info['wordpress_database_version'] = $wp_db_version;
	$info['wordpress_tinymce_version'] = $tinymce_version;

	return $info;
}


/**
 * Format bytes in readable format
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 * @return string formatted size
 */

function w4sl_format_size( $rawSize ){
	if( $rawSize / 1073741824 > 1 ) 
		return number_format_i18n( $rawSize / 1073741824, 1 ) . 'Gb';
	elseif( $rawSize / 1048576 > 1 )
		return number_format_i18n( $rawSize / 1048576, 1 ) . 'Mb';
	elseif( $rawSize / 1024 > 1 )
		return number_format_i18n( $rawSize / 1024, 1 ) . 'Kb';
	else
		return number_format_i18n( $rawSize, 0 ) . 'Bt';
}


/**
 * Get files and folders recursively with permission
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 * @return array of files and folder
 */

function w4sl_get_entries( $folder = '', $attr = array()){
	if ( !empty( $folder )){
		$folder = untrailingslashit( $folder );
		$prefix = $folder . '/';
	}
	else{
		$folder = '.';
		$prefix = '';
	}

	$files = array();
	$folders = array();

	if( !$levels || $levels < 1 )
		$levels = 1;

	$files = array();
	if ( $dir = @opendir( $folder )){
		while (( $file = readdir( $dir )) !== false ){
			if ( in_array( $file, array( '.', '..' )))
				continue;

			$entry_path = w4sl_sanitize_path( $prefix . $file );
			$entry_perm = substr( sprintf( '%o', fileperms( $entry_path )), '-4' );

			if ( is_dir( $entry_path )){
				$entries = w4sl_get_entries( $entry_path, $attr );

				if( !isset( $attr['skip_folders'] )){
					if( isset( $attr['folder_perm_not_equal'] ) && $attr['folder_perm_not_equal'] != '' ){
						if( $attr['folder_perm_not_equal'] != $entry_perm )
							$folders[] = array(
								'perm' => $entry_perm,
								'path' => $entry_path
							);
					}
					else{
						$folders[] = array(
							'perm' => $entry_perm,
							'path' => $entry_path
						);
					}

					if ( $entries['folders'] )
						$folders = array_merge( $folders, $entries['folders'] );
				}

				if( !isset( $attr['skip_files'] )){
					if ( $entries['files'] )
						$files = array_merge( $files, $entries['files'] );
				}
			}
			else{
				if( isset( $attr['file_perm_not_equal'] ) && $attr['file_perm_not_equal'] != '' ){
					if( $attr['file_perm_not_equal'] != $entry_perm )
						$files[] = array(
							'perm' => $entry_perm,
							'path' => $entry_path
						);
				}
				elseif( isset( $attr['filename'] ) && !empty( $attr['filename'] )){
					if( $attr['filename'] == $file )
						$files[] = array(
							'perm' => $entry_perm,
							'path' => $entry_path
						);
				}
				elseif( !isset( $attr['skip_files'] )){
					$files[] = array(
						'perm' => $entry_perm,
						'path' => $entry_path
					);
				}
			}
		}
	}

	@closedir( $dir );
	return array( 'files' => $files, 'folders' => $folders );
}


/**
 * Sanitize folder / file path
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 * @return array of files and folder
 */

function w4sl_sanitize_path( $path = '/' ){
	if ( ! $path )
		$path = '/';

	$path = str_replace( '\\', '/', $path );
	$path = str_replace( '//', '/', $path );

	return (string) $path;
}

?>