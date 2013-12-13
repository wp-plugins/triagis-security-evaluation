<?php
/**
 * Admin Page - Blacklist
 * @package W4 WordPress Adimn Page FrameWork
 * @subpackage WordPress Security Plugin
 * @since 1.0
 */

if( !defined( 'ABSPATH' ))
	require '../plugin.php';


function w4sl_admin_menu(){
	if( !is_super_admin())
		return;

	global $menu, $submenu, $submenu_file, $plugin_page, $w4sl_subpages, $w4sl_pagenow, $w4sl_action, $w4sl_admin_url, $w4sl_current_page_url;

	$w4sl_admin_url = admin_url( 'admin.php?page=' . W4SL_SLUG );
	$w4sl_current_page_url = w4sl_guess_url();

	$w4sl_subpages = array(
		'security_informations'	=> 'Security',
		'license'				=> 'Premium'
	);

	$capability = 'delete_users';
	add_menu_page( W4SL_NAME, W4SL_MENU_NAME, $capability, W4SL_SLUG , 'w4sl_admin_page',  W4SL_URL.'scripts/w4sl_icon.png', W4SL_MENU_POSITION );

	if( $plugin_page == W4SL_SLUG ){
		$w4sl_action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';
		$w4sl_pagenow = isset( $_REQUEST['subpage'] ) ? $_REQUEST['subpage'] : '';

		// Default Actions
		add_action( "w4sl_admin_action", 	'w4sl_load_scripts', 	1 );
		add_action( 'w4sl_admin_body_top', 	'w4sl_admin_nav_menu', 	1 );
		add_action( 'w4sl_admin_body_top', 	'w4_preview_errors', 	2 );

		if( !empty( $w4sl_pagenow ))
			do_action( 'w4sl_admin_action_'. $w4sl_pagenow );

		do_action( 'w4sl_admin_action' );
	}
	
	// We are doing things a little bit fancy here. Not using the builtin wordpress submenu method.
	// As We have reated submenus within the main menu not a stand alone one, here is the solution.
	$i=0;
	foreach( $w4sl_subpages as $key => $val ){
		$submenu_url = 'admin.php?page='.W4SL_SLUG.'&subpage='. $key;
		
		if( $w4sl_pagenow == $key )
			$submenu_file = $submenu_url;
		
		$submenu[W4SL_SLUG][$i] = array( $val, $capability, $submenu_url );
		$i++;
	}
}
add_action( 'admin_menu', 'w4sl_admin_menu');

// Plugin page script loaders
function w4sl_load_scripts(){
	wp_enqueue_style( 'w4sl-admin-css', W4SL_URL . 'scripts/w4sl.css' );
	wp_enqueue_script( 'w4sl-admin-js', W4SL_URL . 'scripts/w4sl.js', array( 'jquery'),'', true );
}

function w4sl_admin_nav_menu(){
	global $w4sl_pagenow, $w4sl_subpages, $w4sl_action, $w4sl_admin_url;

	$total = count( $w4sl_subpages );
	$i = '1';
	echo "<ul class='subsubsub'>";
	foreach( $w4sl_subpages as $key => $val ){
		$class = $w4sl_pagenow == $key ? 'current' : '';
		if( $i == '1' )
			$class .= ' first-item';
		if( $i == $total )
			$class .= ' last-item';

		$class = trim( $class );
		echo "<li><a class='$class' href='". add_query_arg( 'subpage', $key, $w4sl_admin_url ). "' title='$val'>$val</a></li>";
		$i++;
	}
	echo "</ul>";
	echo "<br class='clear' />";
}

function w4sl_admin_page_title_default( $title, $w4sl_pagenow, $w4sl_action ){
	global $w4sl_subpages, $w4sl_admin_url;

	if( isset( $w4sl_pagenow ) && isset( $w4sl_subpages[$w4sl_pagenow] )){

		$title = W4SL_NAME;
		$title .= " &raquo; " . $w4sl_subpages[$w4sl_pagenow];
		if( $w4sl_pagenow == 'post_scan' ){
			if( $w4sl_action == 'spamlist' )
				$title .= " &raquo; Spam Lists";
			elseif( $w4sl_action == 'spam_scan' )
				$title .= " &raquo; Scanning";
		}
		elseif( $w4sl_pagenow == 'visitors' ){
			$title .= ' <span id="w4sl_log_visitor"> "BETA feature! - Use with caution or keep disabled"';
			$log_visitor = (int) w4sl_get_options('log_visitor');
	
			foreach( array( '0' => 'Inactive', '1' => 'Active' ) as $key => $val ){
				$checked = $log_visitor == $key ? ' checked="checked"' : '';
				$title .= "<input name=\"w4sl_visitor_log\" class=\"radio\" id=\"w4sl_visitor_log{$key}\" type=\"radio\" value=\"$key\"{$checked} /> <label for=\"w4sl_visitor_log{$key}\">$val</label>";
			}
			$title .= '</span>';
		}
	}
	else{
		$title = W4SL_NAME;
	}
	
	return $title;
}
add_filter( 'w4sl_admin_page_title', 'w4sl_admin_page_title_default', 1, 3 );

function w4sl_admin_page(){
	global $w4sl_pagenow, $w4sl_action;
?>
	<div class="wrap">
	<div class="icon32 icon32-w4sl" id="icon-w4sl"><br></div>
    <h2><?php _e( apply_filters( 'w4sl_admin_page_title', W4SL_NAME, $w4sl_pagenow, $w4sl_action )); ?></h2>
	<div id="w4" class="metabox-holder">
		<div id="post-body"><div id="post-body-content">
			<?php do_action( 'w4sl_admin_body_top'); ?>
            <?php if( !empty( $w4sl_pagenow ))
				do_action( 'w4sl_admin_body_'. $w4sl_pagenow );

			else
				do_action( 'w4sl_admin_body_default' );

			do_action( 'w4sl_admin_body_bottom' ); ?>
		</div></div>
	</div><!--#poststuff-->
</div><!--#wrap-->
<?php
}
?>