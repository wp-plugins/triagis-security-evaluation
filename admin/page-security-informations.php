<?php
/**
 * Admin Page - Security Informations
 * @package W4 WordPress Adimn Page FrameWork
 * @subpackage WordPress Security Plugin
 * @since 1.0
 */

if( !defined( 'ABSPATH' ))
	require '../plugin.php';
 
	
function w4sl_admin_body_security_informations(){
	global $wpdb, $w4sl_pagenow, $w4sl_action, $w4sl_admin_url;

	if( !empty( $w4sl_action ))
		return;

	$notices = '';
	$security_informations_options = array();

	if( $wpdb->prefix == 'wp_' )
		$notices .= '<div class="errors" id="w4sl_dbprefix_notice">
					Your database prefix is <b>wp_</b> which is very <b>Insecure</b>.
					<a href="#w4sl_change_dbprefix_btn" class="button">Change your database prefix to a secure one</a>
					</div>';
	
	#suggestion to move wp-config.php outside the accessible folders
	$config_notice = '';
	if( file_exists( ABSPATH . 'wp-config.php' )){
		if( file_exists( dirname( ABSPATH ) . '/wp-settings.php' ))
			$config_notice = sprintf(
			'You should move the wp-config.php file to this location <b>%s</b>, but it seems another WordPress installation already exists.', dirname( ABSPATH ));
		else
			$config_notice = sprintf(
			'You should move the wp-config.php file one folder up to this location <b>%s</b>. <!--<a class="button" id="w4sl_move_config_file_btn" href="javascript:void(0);">Move it</a>--> <span></span>',
			dirname( ABSPATH ));

		$notices .= "<div class='errors' id='w4sl_config_notice'>$config_notice</div>";
	}
	else{
		$notices .= "<div class='notes'>Your wp-config.php file is in a secure location now.</div>";
	}


	#mod_security existences
	$apache_modules = function_exists( 'apache_get_modules' ) ? apache_get_modules() : array();
	if( !empty( $apache_modules ) && in_array( 'mod_security', $apache_modules )){
		$notices .= "<div class='notes'>Apache module <b>mod_security</b> is installed.</div>";
	}
	else{
		$notices .= "<div class='errors'>Apache module <b>mod_security</b> is not installed or at least we were unable to locate it.</div>";
	}


	#check if Wordpress SSL is turned on or at least admin login over SSL
	$ssl_notice = '';
	if( !defined( 'FORCE_SSL_LOGIN' )){
		$ssl_notice .= "SSL login is not defined, means it is turned off,";
	}
	else{
		if( FORCE_SSL_LOGIN === false )
			$ssl_notice .= "SSL login is turned off,";
		elseif( FORCE_SSL_LOGIN === true )
			$ssl_notice .= "SSL login is turned on,";
	}

	if( !defined( 'FORCE_SSL_ADMIN' )){
		$ssl_notice .= " SSL for admin pages is not defined, it is turned off.";
	}
	else{
		if( FORCE_SSL_ADMIN === false )
			$ssl_notice .= " SSL for admin pages is turned off.";
		elseif( FORCE_SSL_ADMIN === true )
			$ssl_notice .= " SSL for admin pages is turned on.";
	}

	if( !empty( $ssl_notice ))
		$notices .= "<div class='notes'>$ssl_notice</div>";


	#check if the followings function exist
	$wp_header_active_metas = array();
	foreach( array( 'wp_generator', 'wlwmanifest_link', 'rsd_link', 'wp_shortlink_wp_head' ) as $f ){
		if( has_action( 'wp_head', $f ))
			$wp_header_active_metas[] = $f;
	}
	
	$wp_header_meta_notice = '';
	if( !empty( $wp_header_active_metas )){
		$themepath = defined( TEMPLATEPATH ) ? TEMPLATEPATH : get_template_directory();
		$functionpath = w4sl_sanitize_path( untrailingslashit( $themepath )) . '/functions.php';

		$wp_header_meta_notice = "Remove <b>" . implode( '</b>, <b>', $wp_header_active_metas ) . "</b> meta generator functions from your site html header.<br />Place the follwing code in your themes functions.php file - <b>$functionpath</b>.<br />";

		$wp_header_meta_notice .= "<br />#------------------------------------------------------------------<br /><br />";
		foreach( $wp_header_active_metas as $f ){
			if( $f == 'wp_shortlink_wp_head' )
				$wp_header_meta_notice .= "remove_action( 'wp_head', '$f', 10, 0);<br />";
			else
				$wp_header_meta_notice .= "remove_action( 'wp_head', '$f');<br />";
		}
		$wp_header_meta_notice .= "<br />#------------------------------------------------------------------<br />";
	}

	if( !empty( $wp_header_meta_notice ))
		$notices .= "<div class='notes'>$wp_header_meta_notice</div>";


	#check if wordpress errors are logged and check if they are displayed
	$error_log_notice = '';
	if( !defined( 'WP_DEBUG' ) || !defined( 'WP_DEBUG_LOG' )){
		$error_log_notice = "WordPress error logging is not defined, means it is turned off,";
	}
	else{
		if( WP_DEBUG === false || WP_DEBUG_LOG === false )
			$error_log_notice = "WordPress error logging is turned off,";
		elseif( WP_DEBUG === true && WP_DEBUG_LOG === true )
			$error_log_notice = "WordPress error logging is turned on,";
	}

	if( !defined( 'WP_DEBUG' ) || !defined( 'WP_DEBUG_DISPLAY' )){
		$error_log_notice .= " errors display is not defined, means it is turned off.";
	}
	else{
		if( WP_DEBUG === false || WP_DEBUG_DISPLAY === false )
			$error_log_notice .= " errors display is turned off.";
		elseif( WP_DEBUG === true && WP_DEBUG_DISPLAY === true )
			$error_log_notice .= " errors display is turned on.";
	}

	if( !empty( $error_log_notice ))
		$notices .= "<div class='notes'>$error_log_notice</div>";


	#w4sl( w4sl_change_wp_content_dir( ' D:/server/www/wp/wp-content/s/nc', 'http://localhost/nc' ));

	#echo w4sl_sanitize_path( ABSPATH . 'content2' );
	#w4sl_delete_dir( w4sl_sanitize_path( ABSPATH . 'content2' ));

		# Execute the file
	#$request = wp_remote_request( admin_url( 'admin-ajax.php?action=w4sl_clear_old_content' ));
	#$body = wp_remote_retrieve_body( $request );

	#w4sl($body);

	echo $notices;
?>
	<div class="w4sl_form_wrapper">
		<div class="form-wrap">
 
			<div class="form-field w4ls_half_left">
				<span class="form-head"><strong>WARNING: This may break your website, use on development server ONLY</strong> Change WordPress wp-content folder location:<br><small>Moves your content folder outside of your WordPress installation.</small></span>
			</div>
			<a class="f_btn" id="w4sl_wp_content_location_change_form_btn" href="javascript:void(0);">Change</a><br class="clear"/>
			<div id="w4sl_wp_content_location_change_form_content" class="hidden"></div>

			<div class="form-field w4ls_half_left">
				<span class="form-head">Checks all files and folders for wrong permissions:<br><small>Directories should be 755, files should be 644.</small></span>
			</div>
			<a class="f_btn" id="w4sl_unappropriate_entries_table_btn" href="javascript:void(0);">Check permission</a><br class="clear"/>
			<div id="w4sl_unappropriate_entries_table_content"></div>
	
			<div class="form-field w4ls_half_left">
				<span class="form-head">Check if any thimbthumb.php file exists within your WordPress setup (Careful!, hackers will exploit this file immediately):<br><small>delete thimbthumb.php file for security reason.</small></span>
			</div>
			<a class="f_btn" id="w4sl_checkthimbthumb_btn" href="javascript:void(0);">Checks for existing thimbthumb.php files</a><br class="clear"/>
			<div id="w4sl_checkthimbthumb_content"></div>
			
			<div class="form-field w4ls_half_left">
				<span class="form-head">Checks if any users use the username "admin":<br><small>Users should NEVER use the username "admin" because it is easily explotable.</small></span>
			</div>
			<a class="f_btn" id="w4sl_username_change_form_btn" href="javascript:void(0);">Checks admin username</a><br class="clear"/>
			<div id="w4sl_username_change_form_content"></div>
							
			<div class="form-field w4ls_half_left">
			<span class="form-head">System Info:<br /><small>Operating sytem, Server Software, MySQL, PHP Version, Wordpress</small></span>
			</div>
			<a class="f_btn" id="w4sl_osmpinfo_btn" href="javascript:void(0);">Show System Info</a><br class="clear"/>
			<div id="w4sl_osmpinfo_content"></div>
							
			<div class="form-field w4ls_half_left">
				<span class="form-head">Change database table prefix<br /><small>Prefix can contain only letters, numbers and underscore "_" and should end with an underscore "_". if you do not use an underscore after the prefix name, we will automatically add one.</small></span>
			</div>
			<a class="f_btn" id="w4sl_change_dbprefix_btn" href="javascript:void(0);">Change Database table prefix</a><br class="clear"/>
			<div id="w4sl_change_dbprefix_content"></div>

		</div>
	</div>
<?php
}
add_action( 'w4sl_admin_body_security_informations', 'w4sl_admin_body_security_informations' );

function w4sl_clear_old_content_ajax(){
	if( get_option( 'w4sl_old_content_path' )){
		w4sl_delete_dir( get_option( 'w4sl_old_content_path' ));
		if( !@is_dir( get_option( 'w4sl_old_content_path' )))
			delete_option( 'w4sl_old_content_path' );

		die('1');
	}
	die('2');
}
add_action( 'wp_ajax_w4sl_clear_old_content', 'w4sl_clear_old_content_ajax' );
add_action( 'wp_ajax_nopriv_w4sl_clear_old_content', 'w4sl_clear_old_content_ajax' );


/**
 * Display wp-content change form
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 */

function w4sl_wp_content_location_change_form_ajax(){
	@error_reporting( 0 );
	header( 'Content-type: application/json' );

	if( !is_super_admin())
		die( json_encode( array( 'error' => 'Unauthorized access !!' )));

	$html = '';
	$html .= '<div class="form-field full_size" style="padding: 20px 3.5%; margin-bottom:50px;background-color:#EEE; border:1px solid #DDD;">';
	$html .= '<form method="POST" id="w4sl_content_location_change_form" action="">';

	$html .= '<div class="alignleft" style="width:40%;margin: 0 10% 20px 0;">';
	$html .= '<label for="wp_content_dir" class="form-head">New <code>wp-content</code> folder</label>';
	$html .= '<input type="text" id="wp_content_dir" value="'. WP_CONTENT_DIR .'" size="60" />';
	$html .= '<label for="wp_content_url" class="form-head">New <code>wp-content</code> Url</label>';
	$html .= '<input type="text" id="wp_content_url" value="'. WP_CONTENT_URL .'" size="60" />';
	$html .= '</div>';

	$html .= '<table class="alignleft" style="width:40%;margin:0;">';
	$html .= '<tr><td align="left">WordPress Installation:</td><td><code>'. ABSPATH .'</code></td></tr>';
	$html .= '<tr><td align="left">Content Folder:</td><td><code>'. WP_CONTENT_DIR .'</code></td></tr>';
	$html .= '<tr><td align="left">Content Url:</td><td><code>'. WP_CONTENT_URL .'</code></td></tr>';
	$html .= '</table>';

	$html .= '<div class="clear"><input type="submit" id="w4sl_change_wp_content_location_act_btn" class="button-primary" value="Change" /> <span></span></div>';
	$html .= '</form>';
	$html .= '</div>';

	die( json_encode( array( 'html' => $html )));
}
add_action( 'wp_ajax_w4sl_wp_content_location_change_form', 'w4sl_wp_content_location_change_form_ajax' );



/**
 * Change wp-content
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 */

function w4sl_change_wp_content_location_ajax(){
	@error_reporting( 0 );
	header( 'Content-type: application/json' );

	if( !is_super_admin())
		die( json_encode( array( 'error' => 'Unauthorized access !!' )));

	$content_dir = $_POST['content_dir'];
	$content_url = $_POST['content_url'];

	if( empty( $content_dir ))
		die( json_encode( array( 'error' => 'New directory value empty.' )));

	if( empty( $content_url ))
		die( json_encode( array( 'error' => 'Content url value empty.' )));

	
	$change = w4sl_change_wp_content_dir( $content_dir, $content_url );
	if( is_wp_error( $change ))
		die( json_encode( array( 'error' => $change->get_error_message())));

	die( json_encode( array( 'sucess' => true )));
}
add_action( 'wp_ajax_w4sl_change_wp_content_location', 'w4sl_change_wp_content_location_ajax' );


function w4sl_recursive_copy( $src, $dst ){
/*
	$files = w4sl_get_entries( WP_CONTENT_DIR );
	$nfiles = array();
	$npath = w4sl_sanitize_path( 'D:/server/www/nc' );


	@mkdir( $npath );
	foreach( $files['folders'] as $folder ){
		$nfolder = str_replace( w4sl_sanitize_path( WP_CONTENT_DIR ), $npath, $folder['path'] );
		@mkdir( $nfolder );
	}
	foreach( $files['files'] as $file ){
		$nfile = str_replace( w4sl_sanitize_path( WP_CONTENT_DIR ), $npath, $file['path'] );
		copy( $file['path'], $nfile );
	}
*/
	$dir = opendir( $src );
	@mkdir( $dst );
	while( false !== ( $file = readdir( $dir ))){
		if (( $file != '.' ) && ( $file != '..' )){
			if ( is_dir( $src . '/' . $file )){
				w4sl_recursive_copy( $src . '/' . $file, $dst . '/' . $file );
			}
			else{
				copy($src . '/' . $file,$dst . '/' . $file);
			}
		}
	}
	closedir( $dir );
}

/*
 * Change WP_CONTENT_DIR and WP_CONTENT_URL to a new one and write on wp-config.php  
 * @param string $new_dir, New dir name.
 * @param string $new_url, New url name.
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 */

function w4sl_change_wp_content_dir( $content_dir = '', $content_url = '' ){
	if( empty( $content_dir ) || empty( $content_url ))
		return new WP_Error( 'arg_error', 'Folder or Url empty.' );

	$old_content_dir = w4sl_sanitize_path( untrailingslashit( WP_CONTENT_DIR ));

	#update_option( 'w4sl_old_content_path', $old_content_dir );
	#return true;

	$content_dir = w4sl_sanitize_path( untrailingslashit( $content_dir ));
	$content_url = untrailingslashit( $content_url );

	if( $content_dir == $old_content_dir )
		return new WP_Error( 'dir_error', 'Provided folder already being used.' );

	if( strpos( $content_dir . '/', $old_content_dir . '/' ) !== false )
		return new WP_Error( 'dir_error', 'New folder shouldn\'t be within current <code>wp-content</code> folder.' );

	# Create directory if not exists.
	if( !wp_mkdir_p( $content_dir ))
		return new WP_Error( 'dir_error', 'Invalid folder location.' );

	# Test directory and url combination
	# Create a new php file through the path, and we will execute it with http.
	$test_file = time(). ".php";
	$test_file_path = $content_dir .'/'. $test_file;
	$fh = @fopen( $test_file_path, 'w' );

	# Couldn't Create the file or the directory isnt exists 
	if( !$fh || !is_dir( $content_dir ))
		return new WP_Error( 'dir_error', 'Invalid directory information.' );

	# Put header status on the file
	fwrite( $fh, '<?php header( "HTTP/1.0 200 OK", true, 200 ); ?>' );
	fclose( $fh );
	chmod( $test_file_path, 0666 );

	# Execute the file
	$request = wp_remote_request( $content_url .'/'. $test_file, array( 'timeout' => 90 ));
	if( is_wp_error( $request )){
		@unlink( $test_file_path );
		die( json_encode( array( 'error' => 'Couldn\'t locate new WP-Content url. Url address should be relative based on the directory provided. That means, the folder should be accessible with the Url provided.' )));
	}

	$header = wp_remote_retrieve_response_code( $request );
	if( $header != '200' ){
		@unlink( $test_file_path );
		die( json_encode( array( 'error' => 'Couldn\'t locate new WP-Content url. Url address should be relative based on the directory provided. That means, the folder should be accessible with the Url provided.' )));
	}

	# Clear the test file.
	@unlink( $test_file_path );

	# Check if wp-config.php file is accessible.
	$config_path = '';
	if( file_exists( ABSPATH . 'wp-config.php' )){
		$config_path = ABSPATH . 'wp-config.php';
	}
	elseif( file_exists( dirname( ABSPATH ) . '/wp-config.php' ) && ! file_exists( dirname( ABSPATH ) . '/wp-settings.php' )){
		$config_path = dirname( ABSPATH ) . '/wp-config.php';
	}

	$config_path = w4sl_sanitize_path( $config_path );
	if( !is_readable( $config_path ) || !function_exists( 'file' ) || !function_exists( 'file_put_contents' ))
		return new WP_Error( 'config_error', 'Config file is not accessible.' );


	# Copy complete wp-content to the new location.
	w4sl_recursive_copy( $old_content_dir, $content_dir );

	$constants = array(
		'WP_CONTENT_DIR' => $content_dir,
		'WP_CONTENT_URL' => $content_url
	);

	$pattern = "/(define\('". join( '|', array_keys( $constants )) ."')/i";

	$lines = file( $config_path );
	$config = '';
	foreach( $lines as $line ){
		$line = ltrim( $line );

		# Skip Existing
		if( preg_match( $pattern, $line ))
			continue;

		$config .= $line;

		# Place after the WP_DEBUG constant line;
		if( preg_match( "/(define\('WP_DEBUG')/i", $line )){
			#$config .= "/** WordPress Content Directory Connstants.*/";
			#$config .= "\r\n";
			foreach( $constants as $constant => $value ){
				$config .= "define('" . $constant . "', '" . $value . "');\r\n";
			}
		}
	}

	if( empty( $config ))
		return new WP_Error( 'config_error', 'Some error occurs.' );

	if( !is_writable( $config_path ))
		return new WP_Error( 'config_error', 'Sorry, but I can\'t write the <code>wp-config.php</code> file. You have to paste the following text into your <code>wp-config.php</code> file manually. <textarea cols="98" rows="15" class="code">'. htmlentities( $config, ENT_COMPAT, 'UTF-8') . '</textarea>' );

	@file_put_contents( $config_path, $config );

	# Save Old Directory location
	update_option( 'w4sl_old_content_path', $old_content_dir );
	
	# Delete Old Directory files..
	wp_remote_request( admin_url( 'admin-ajax.php?action=w4sl_clear_old_content' ));

	return true;
}

/**
 * Wrong permitted files and folder table
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 */

function w4sl_unappropriate_entries_table_ajax(){
	@error_reporting( 0 );
	header( 'Content-type: application/json' );

	if( !is_super_admin())
		die( json_encode( array( 'error' => 'Unauthorized access !!' )));

	$entries = w4sl_get_entries( ABSPATH, array( 'file_perm_not_equal' => '0644', 'folder_perm_not_equal' => '755' ));

	$files = isset( $entries['files'] ) && !empty( $entries['files'] ) ? $entries['files'] : array();
	$folders = isset( $entries['folders'] ) && !empty( $entries['folders'] ) ? $entries['folders'] : array();

	#$files = array_slice( $files, 0, 10 );
	#$folders = array_slice( $folders, 0, 10 );

	if( empty( $files ) && empty( $folders ))
		die( json_encode( array( 'html' => '<div class="notes">All files and folder have the right permission.</div>' )));

	$html = '<div class="notes">While changing file permission if you see a message like this "couldn\'t change permission", then try to change permission by connecting to the server with a FTP Client or login to your cPanel.</div>';
	$html .= '<table class="widefat w4sl_table"><thead><tr><th>File path</th><th>Type</th><th>Current Permission</th><th>Proper Permission</th><tr><tbody>';
	$row_class = 'row_even';

	if( empty( $folders )){
		$html .= "<tr><td colspan=\"4\" align=\"center\">All folder have the right permission.</td></tr>";
	}
	else{
		foreach( $folders as $folder ){
			$html .= "<tr class=\"{$row_class}\">";
			$html .= "<td>{$folder['path']}</td>";
			$html .= "<td>folder</td>";
			$html .= "<td class='w4ls_cip'>{$folder['perm']}</td>";
			$html .= "<td>0755 | <a href=\"javascript:void(0);\" alt=\"{$folder['path']}\" class=\"w4sl_cp\">change</a> <span></span></td>";
			$html .= "</tr>";
		
			$row_class = $row_class == 'row_even' ? 'row_odd' : 'row_even';
		}
	}

	if( empty( $files )){
		$html .= "<tr><td colspan=\"4\" align=\"center\">All files have the right permission.</td></tr>";
	}
	else{
		foreach( $files as $file ){
			$html .= "<tr class=\"{$row_class}\">";
			$html .= "<td>{$file['path']}</td>";
			$html .= "<td>file</td>";
			$html .= "<td>{$file['perm']}</td>";
			$html .= "<td>0644 | <a href=\"javascript:void(0);\" alt=\"{$folder['path']}\" class=\"w4sl_cp\">change</a> <span></span></td>";
			$html .= "</tr>";

			$row_class = $row_class == 'row_even' ? 'row_odd' : 'row_even';
		}
	}

	$html .= "</tbody></table>";

	die( json_encode( array( 'html' => $html )));
}
add_action( 'wp_ajax_w4sl_unappropriate_entries_table', 'w4sl_unappropriate_entries_table_ajax' );


function w4sl_change_permission_ajax(){
	@error_reporting( 0 );
	header( 'Content-type: application/json' );

	if( !is_super_admin())
		die( json_encode( array( 'error' => 'Unauthorized access !!' )));

	$file = w4sl_sanitize_path( $_POST['file'] );

	if( empty( $file ) || !file_exists( $file ))
		die( json_encode( array( 'error' => 'file not found !!' )));

	if( is_dir( $file )){
		if( chmod( $file, 0755 ))
			die( json_encode( array( 'sucess' => true, 'perm' =>'0755' )));
		else
			die( json_encode( array( 'error' => 'couldn\'t change permission.' )));
	}
	else{
		if( chmod( $file, 0644 ))
			die( json_encode( array( 'sucess' => true, 'perm' => '0644' )));
		else
			die( json_encode( array( 'error' => 'couldn\'t change permission.' )));
	}
}
add_action( 'wp_ajax_w4sl_change_permission', 'w4sl_change_permission_ajax' );
/**
 * List found thimbthumb.php files table
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 */

function w4sl_thimbthumb_files_table_ajax(){
	@error_reporting( 0 );
	header( 'Content-type: application/json' );

	if( !is_super_admin())
		die( json_encode( array( 'error' => 'Unauthorized access !!' )));

	
	$entries = w4sl_get_entries( ABSPATH, array( 'skip_folders' => true, 'filename' => 'timthumb.php' ));
	$files = !empty( $entries['files'] ) ? $entries['files'] : array();
	
	if( empty( $files ))
		die( json_encode( array( 'html' => '<div class="messages">No <b>thimbthumb.php</b> file found within your WordPress installation.</div>' )));


	$html = '<table class="widefat w4sl_table"><thead><tr><th>File</th><tr><tbody>';
	$row_class = 'row_even';
	foreach( $files as $file ){
		$html .= "<tr class=\"{$row_class}\">";
		$html .= "<td>{$file['path']}";
		$html .= " | <a href=\"javascript:void(0);\" alt=\"{$file['path']}\" class=\"w4sl_delete_file\">delete</a> <span></span></td>";
		$html .= "</tr>";
	
		$row_class = $row_class == 'row_even' ? 'row_odd' : 'row_even';
	}
	$html .= "</tbody></table>";

	die( json_encode( array( 'html' => $html )));
}
add_action( 'wp_ajax_w4sl_thimbthumb_files_table', 'w4sl_thimbthumb_files_table_ajax' );


function w4sl_delete_file_ajax(){
	@error_reporting( 0 );
	header( 'Content-type: application/json' );

	if( !is_super_admin())
		die( json_encode( array( 'error' => 'Unauthorized access !!' )));

	$file = w4sl_sanitize_path( $_POST['file'] );
	if( empty( $file ) || !file_exists( $file ))
		die( json_encode( array( 'error' => 'File not found !!' )));

	if( !is_readable( $file ))
		die( json_encode( array( 'error' => 'File not readable !!' )));

	@unlink( $file );
	if( file_exists( $file ))
		die( json_encode( array( 'error' => 'Unable to delete file.' )));

	die( json_encode( array( 'sucess' => true )));
}
add_action( 'wp_ajax_w4sl_delete_file', 'w4sl_delete_file_ajax' );

/**
 * Display username change form
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 */

function w4sl_username_change_form_ajax(){
	@error_reporting( 0 );
	header( 'Content-type: application/json' );

	if( !is_super_admin())
		die( json_encode( array( 'error' => 'Unauthorized access !!' )));

	global $wpdb;
	$user = $wpdb->get_row( "SELECT * FROM $wpdb->users WHERE user_login = 'admin'" );

	if( !$user ){
		die( json_encode( array( 
		'html' => '<div class="messages">No user found with the username as "admin".</div>' )));
	}

	$html = '<table class="widefat">';
	$html .= '<thead><tr><th>User ID</th><th>User</th><th>Rename to</th><tr>';
	$html .= '<tbody><tr>';
	$html .= "<td>$user->ID</td>";
	$html .= "<td>";
	$html .= $user->display_name ? $user->display_name : $user->user_login;
	$html .= "</td>";
	$html .= "<td><input type='text' id='w4sl_adminusername_{$user->ID}' />";
	$html .= " <a href='javascript:void(0);' alt='$user->ID' class='button-primary w4sl_rename_username'>rename</a> <span></span></td>";
	$html .= "</tr></tbody></table>";

	die( json_encode( array( 'html' => $html )));
}
add_action( 'wp_ajax_w4sl_username_change_form', 'w4sl_username_change_form_ajax' );


/**
 * Change username
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 */

function w4sl_rename_username_ajax(){
	@error_reporting( 0 );
	header( 'Content-type: application/json' );

	if( !is_super_admin())
		die( json_encode( array( 'error' => 'Unauthorized access !!' )));

	$ID = $_POST['u_id'];
	$user_login = $_POST['uname'];

	if( empty( $ID ) || empty( $user_login ))
		die( json_encode( array( 'error' => 'Unable to change Username. Unappropriate information provided.' )));

	global $wpdb;
	if( !get_userdata( $ID ))
		die( json_encode( array( 'error' => 'User not found' )));

	$user_login = trim( str_replace( array( ' ', '-', '.', '?', '|' ), '_', $user_login ), '_' );
	$user_login = trim( $user_login, '_' );
	$user_login = sanitize_title( $user_login, true );

	if( $user_login == 'admin' )
		die( json_encode( array( 'error' => 'Can\'t change username as "admin".' )));

	if( username_exists( $user_login ))
		die( json_encode( array( 'error' => 'There\'s already a user with the given username.' )));

	$user_nicename = $user_login;
	$update = $wpdb->update( $wpdb->users, compact( 'user_login', 'user_nicename' ), compact( 'ID' ));

	if( is_wp_error( $update ))
		die( json_encode( array( 'error' => $update->get_error_message() )));
	else{
		$new_login = $wpdb->get_var( "SELECT user_login FROM $wpdb->users WHERE ID = '$ID'" );
		die( json_encode( array( 'new_login' => $new_login )));
	}
}
add_action( 'wp_ajax_w4sl_rename_username', 'w4sl_rename_username_ajax' );


/**
 * Display database prefix change form
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 */

function w4sl_change_dbprefix_form_ajax(){
	@error_reporting( 0 );
	header( 'Content-type: application/json' );

	if( !is_super_admin())
		die( json_encode( array( 'error' => 'Unauthorized access !!' )));

	global $wpdb;

	$html = '';
	$config_path = '';
	if( file_exists( ABSPATH . 'wp-config.php' )){
		$config_path = ABSPATH . 'wp-config.php';
	}
	elseif( file_exists( dirname( ABSPATH ) . '/wp-config.php' ) && ! file_exists( dirname(ABSPATH) . '/wp-settings.php' )){
		$config_path = dirname(ABSPATH) . '/wp-config.php';
	}

	$config_path = w4sl_sanitize_path( $config_path );

	if( $config_path == '' || !is_writable( $config_path ) || !function_exists( 'file' ) || !function_exists( 'file_get_contents' ) || !function_exists( 'file_put_contents' )){
		die( json_encode( array( 
		'html' => sprintf( '<div class="errors">We are unable to change Database prefix as the file <b>%s</b> is not writeable or accessible.</div>', $config_path ))));
	}

	$html .= '<table class="widefat w4sl_table"><thead><tr><th>Current Prefix</th><th>New prefix</th></tr></thead><tbody><tr>';
	$html .= '<td id="w4sl_cprefix">'. $wpdb->prefix .'</td>';
	$html .= '<td><input type="text" id="w4sl_dbprefix" />';
	$html .= ' <a href="javascript:void(0);" id="w4sl_change_dbprefix_act_btn" class="button-primary">Change</a> <span></span></td>';
	$html .= "</tr></tbody></table>";

	die( json_encode( array( 'html' => $html )));
}
add_action( 'wp_ajax_w4sl_change_dbprefix_form', 'w4sl_change_dbprefix_form_ajax' );


/**
 * Change database prefix
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 */

function w4sl_change_dbprefix_ajax(){
	@error_reporting( 0 );
	header( 'Content-type: application/json' );

	if( !is_super_admin())
		die( json_encode( array( 'error' => 'Unauthorized access !!' )));

	$new_prefix = $_POST['new_prefix'];
	if( empty( $new_prefix ))
		die( json_encode( array( 'error' => 'Unable to change database prefix, empty prefix name.' )));

	$new_prefix = w4sl_sanitize_dbprefix( $new_prefix );
	if( empty( $new_prefix ))
		die( json_encode( array( 'error' => 'Unable to change database prefix, invalid prefix name.' )));

	if( $new_prefix == 'wp_' )
		die( json_encode( array( 'error' => 'You can not use wp_ as table prefix name as it is insecure..' )));

	global $wpdb;
	# Change table prefix
	$prefix_change = w4sl_change_table_prefix( $new_prefix );
	if( is_wp_error( $prefix_change ))
		die( json_encode( array( 'error' => $prefix_change->get_error_message())));

	# Change table prefix variable on the config file
	$config_change = w4sl_change_config_prefix( $new_prefix );
	if( is_wp_error( $config_change ))
		die( json_encode( array( 'error' => $config_change->get_error_message())));

	die( json_encode( array( 'new_prefix' => $new_prefix, 'sucess' => true )));
}
add_action( 'wp_ajax_w4sl_change_dbprefix', 'w4sl_change_dbprefix_ajax' );


/*
 * Change WordPress database table prefix name
 * @param string $new_prefix, New prefix name.
 * @param string $old_prefix, Old prefix name. Optional.
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 */

function w4sl_change_table_prefix( $new_prefix, $old_prefix = '' ){
	global $wpdb;

	if( !w4sl_dbuser_can_alter_table())
		return new WP_Error( 'db_error', 'Unable to change prefix, current database user doesn\'t have enough permission. The user should have <b>ALTER</b> rights.' );

	if( empty( $old_prefix ))
		$old_prefix = $wpdb->prefix;

	if( $old_prefix == $new_prefix )
		return new WP_Error( 'db_error', 'Requested prefix name already beign used.' );

	$old_prefix_length = strlen( $old_prefix );

	# Check table names
	$changeable_tables = $wpdb->get_results( "SHOW TABLES LIKE '{$old_prefix}%'", ARRAY_N );
	if( empty( $changeable_tables ))
		return new WP_Error( 'db_error', sprintf( 'Errors occured, no table found with the prefix name %s.', $old_prefix ));

	foreach( $changeable_tables as $table ){
		$wpdb->hide_errors();

		$old_name = $table[0];
		if( empty( $old_name ))
			continue;

		$new_name =  $new_prefix . substr( $old_name, $old_prefix_length );
		$wpdb->query( "RENAME TABLE {$old_name} TO {$new_name}" );
	}

	# Change usermeta table meta_key column values which use table prefix
	$usermetas = $wpdb->get_results( "SELECT umeta_id, meta_key FROM {$new_prefix}usermeta WHERE meta_key LIKE '{$old_prefix}%'" );
	$usermeta_updates = array();

	foreach( $usermetas as $usermeta ){
		$absolute_name = substr( $usermeta->meta_key, $old_prefix_length );
		if( !empty( $absolute_name ))
			$usermeta_updates[$usermeta->umeta_id] = $new_prefix . $absolute_name;
	}

	if( !empty( $usermeta_updates )):
		$query = "UPDATE {$new_prefix}usermeta SET meta_key = CASE umeta_id ";
		foreach( $usermeta_updates as $umeta_id => $meta_key ):
			$query .= "WHEN $umeta_id THEN '$meta_key' ";
			$all_umeta_ids[] = $umeta_id;
		endforeach;
		$query .= "END WHERE umeta_id IN (" . implode( ',', $all_umeta_ids ) .")";
		$wpdb->query( $query );
	endif;


	# Change options table option_name column values
	$options = $wpdb->get_results( "SELECT option_id, option_name FROM {$new_prefix}options WHERE option_name LIKE '{$old_prefix}%'" );
	$options_updates = array();

	foreach( $options as $option ){
		$absolute_name = substr( $option->option_name, $old_prefix_length );
		if( !empty( $absolute_name ))
			$options_updates[$option->option_id] = $new_prefix . $absolute_name;
	}

	if( !empty( $options_updates )):
		$query = "UPDATE {$new_prefix}options SET option_name = CASE option_id ";
		foreach( $options_updates as $option_id => $option_name ):
			$query .= "WHEN $option_id THEN '$option_name' ";
			$all_option_ids[] = $option_id;
		endforeach;
		$query .= "END WHERE option_id IN (" . implode( ',', $all_option_ids ) .")";
		$wpdb->query( $query );
	endif;
}


/*
 * Change $db_prefix var to a new one on wp-config.php  
 * @param string $new_prefix, New prefix name.
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 */

function w4sl_change_config_prefix( $new_prefix ){
	$config_path = '';
	if( file_exists( ABSPATH . 'wp-config.php' )){
		$config_path = ABSPATH . 'wp-config.php';
	}
	elseif( file_exists( dirname( ABSPATH ) . '/wp-config.php' ) && ! file_exists( dirname( ABSPATH ) . '/wp-settings.php' )){
		$config_path = dirname( ABSPATH ) . '/wp-config.php';
	}

	if( empty( $config_path ))
		return new WP_Error( 'config_error', 'We are unable to locate config file.' );

	$config_path = w4sl_sanitize_path( $config_path );

	if( $config_path == '' || !is_writable( $config_path ) || !function_exists( 'file' ) || !function_exists( 'file_get_contents' ) || !function_exists( 'file_put_contents' ))
		return new WP_Error( 'config_error', 'Config file is not writeable or accessible.' );

	$lines = file( $config_path );
	$config = '';
	foreach( $lines as $line ){
		$line = ltrim( $line );
		if( !empty( $line )){
			if( strpos( $line, '$table_prefix' ) !== false ){
				$line = preg_replace( "/=(.*)\;/", "= '". $new_prefix ."';", $line );
			}
		}
		$config .= $line;
	}

	if( empty( $config ))
		return new WP_Error( 'config_error', 'Some error occurs.' );

	@file_put_contents( $config_path, $config );
	return true;
}


/*
 * Ajax | Move WordPress config.php file to one directory top of the wordpress installation  
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 */

function w4sl_move_config_file_ajax(){
	@error_reporting( 0 );
	header( 'Content-type: application/json' );

	if( !is_super_admin())
		die( json_encode( array( 'error' => 'Unauthorized access !!' )));

	if( !file_exists( ABSPATH . 'wp-config.php' ))
		die( json_encode( array( 'error' => 'Config file is already on a secure location..' )));

	if( file_exists( dirname( ABSPATH ) . '/wp-settings.php' ))
		die( json_encode( array( 'error' => 'Can\'t move the wp-config.php file as there\'s another WordPress Installation found..' )));

	@rename( ABSPATH . 'wp-config.php', dirname( ABSPATH ) . '/wp-config.php' );

	die( json_encode( array( 'sucess' => true )));
}
add_action( 'wp_ajax_w4sl_move_config_file', 'w4sl_move_config_file_ajax' );


/**
 * Ajax | system information table
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 */

function w4sl_osmp_info_table_ajax(){
	@error_reporting( 0 );
	header( 'Content-type: application/json' );

	if( !is_super_admin())
		die( json_encode( array( 'error' => 'Unauthorized access !!' )));

	$osmp_info = w4sl_osmp_info();

	$html = '<table class="widefat w4sl_table"><thead><tr><th>Name</th><th>Value</th><tr><tbody>';
	$row_class = 'row_even';
	foreach( $osmp_info as $name => $value ){
		$name = ucwords( str_replace( '_', ' ', $name ));
		$html .= "<tr class=\"{$row_class}\">";
		$html .= "<td>{$name}</td>";
		$html .= "<td>{$value}</td>";
		$html .= "</tr>";

		$row_class = $row_class == 'row_even' ? 'row_odd' : 'row_even';
	}

	$html .= "</tbody></table>";

	die( json_encode( array( 'html' => $html )));
}
add_action( 'wp_ajax_w4sl_osmp_info_table', 'w4sl_osmp_info_table_ajax' );


/**
 * Security Informations Page Action handler
 * @package WordPress
 * @subpackage WordPress Security Plugin
 * @since 1.0
 */

function w4sl_admin_action_security_informations(){
	global $wpdb, $w4sl_pagenow, $w4sl_action, $w4sl_admin_url;
	wp_enqueue_script( 'w4sl-security-informations', W4SL_URL . 'scripts/security_informations.js', array( 'jquery'),'', true );
}
add_action( 'w4sl_admin_action_security_informations', 'w4sl_admin_action_security_informations' );
 
?>