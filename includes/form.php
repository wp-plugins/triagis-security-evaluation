<?php
if( !defined( 'ABSPATH' ))
	require '../plugin.php';

# Nothing Important Here.
# Adin Page FrameWork by w4dev.com
# Do Not Edit Below, Please..
function w4sl_parse_form_fields( $form_fields, $defaults = array()){
	global $w4sl_pagenow, $w4sl_action, $w4sl_form_url;

	if( !is_array( $form_fields ))
		$form_fields = array();

	if( !is_array( $defaults ))
		$defaults = array();

	if( empty( $defaults['form_id'] ))
		$defaults['form_id'] = '';

	if( empty( $defaults['form_after_start'] ))
		$defaults['form_after_start'] = '';

	if( empty( $defaults['form_before_close'] ))
		$defaults['form_before_close'] = '';

	if( empty( $defaults['button_text'] ))
		$defaults['button_text'] = 'Update';

	if( empty( $defaults['action'] )){
		$query_vars = array();

		if( !$w4sl_form_url )
			$defaults['action'] = w4sl_guess_url();
		else
			$defaults['action'] = $w4sl_form_url;

		if( isset( $_GET['id'] ))
			$query_vars['id'] = $_GET['id'];

		if( !empty( $w4sl_pagenow ))
			$query_vars['subpage'] = $w4sl_pagenow;

		if( !empty( $w4sl_action ))
			$query_vars['action'] = $w4sl_action;

		if( !empty( $query_vars ))
			$defaults['action'] = add_query_arg( $query_vars, $defaults['action'] );
	}
	echo '<div class="w4sl_form_wrapper">';
	echo '<form class="form-wrap w4-form" method="POST" enctype="multipart/form-data" id="'. $defaults['form_id']. '" action="'. $defaults['action']. '">';

	if( !isset( $defaults['form_btn_top'] ))
		echo "<p class='submit' style='text-align:right; margin:0 0 10px;'><input type='submit' value='". $defaults['button_text'] ."' class='button-primary'></p>";

	echo $defaults['form_after_start'];

	foreach( $form_fields as $key => $option ):
		$option['val'] = isset( $defaults[$key] ) ? $defaults[$key] : "";
		if( !$option['val'] && isset( $option['default'] ))
			$option['val'] = $option['default'];

		w4sl_parse_form_field( $key, $option );
	endforeach;

	if( !isset( $defaults['form_btn_bottom'] ))
		echo "<p class='submit' style='text-align:left; margin:10px 0 0;'><input type='submit' value='". $defaults['button_text'] ."' class='button-primary'></p>";
	echo $defaults['form_before_close'];
	echo '</form>';
	echo '</div>';
}

function w4sl_parse_form_field( $key = '', $field = array()){
	if( !is_array( $field ) || empty( $key ))
		return;

	#print_r( $field );
	$f_value = $field['val'];
	$f_title =  isset( $field['title'] ) ? $field['title'] : '';

	$tip_class = "";
	$f_tip = "";
	if( isset( $field['help'] )){
		$f_tip = "tip-title='".$field['help']."'";
		$tip_class = " has_tip";
	}
	$f_attr = !empty( $field['style'] ) ? "style='". $field['style'] ."'" : "";

	if( isset( $field['html_before'] ))
		echo $field['html_before'];

	$f_type = isset( $field['type'] ) ? $field['type'] : '';

	$f_class = " w4sl_ff_{$key}";
	if( isset( $field['class'] ))
		$f_class .= " ". $field['class'];

	switch( $f_type ):
	case "text":
		echo "<div class='form-field{$f_class}' {$f_attr}>";
		if( !empty( $f_title ))
			echo "<label class='form-head{$tip_class}' for='$key' {$f_tip}>{$f_title}</label>";
	
			echo "<input type='text' value=\"$f_value\" id='{$key}' name='{$key}' />";
		echo "</div>";
	break;

	case "hidden":
		echo "<input type='hidden' value=\"$f_value\" id='$key' name='$key' />";
	break;

	case "textarea":
		echo "<div class='form-field{$f_class}' {$f_attr}>";
		if( !empty( $f_title ))
			echo "<label class='form-head{$tip_class}' for='$key' {$f_tip}>{$f_title}</label>";

		$rows = isset( $field['rows'] ) ? $field['rows'] : 5;
		echo "<textarea rows='$rows' id='$key' name='$key'>".$f_value."</textarea>";
		echo "</div>";
	break;

	case "radio":
		echo "<div class='form-field{$f_class}' {$f_attr}>";
		if( !empty( $f_title ))
			echo "<span class='form-head{$tip_class}' for='$key' {$f_tip}>{$f_title}</span>";
					
		foreach( $field['option'] as $value => $name ){
			$checked = $f_value == $value ? " checked='checked'" : '';
			echo "<label><input name='$key' class='radio' id='{$key}_{$value}' type='radio' value='$value'{$checked} /> $name</label>\n";
		}
		echo "</div>";
	break;

	case "select":
		echo "<div class='form-field{$f_class}' {$f_attr}>";
		if( !empty( $f_title ))
			echo "<label class='form-head{$tip_class}' for='$key' {$f_tip}>{$f_title}</label>";

		echo "<select name='$key' id='$key'>";
		foreach( $field['option'] as $value => $name ){
			$selected = $f_value == $value ? " selected='true'" : '';
			echo "<option value='$value'{$selected}>$name</option>";
		}
		echo "</select>";
		echo "</div>";
	break;

	case "checkbox":
		$f_value = !is_array( $f_value ) ? explode( ',', $f_value ) : $f_value;
		echo "<div class='form-field{$f_class}' {$f_attr}><span class='form-head{$tip_class}' for='$key' {$f_tip}>{$f_title}</span>";
		foreach( $field['option'] as $value => $name ){
			$extra = in_array( $value, $f_value ) ? " checked='checked'" : '';
			echo "<label><input name='{$key}[]' class='checkbox' id='{$key}_{$value}' type='checkbox' value='$value' $extra /> $name</label>\n";
		}
		echo "</div>";
	break;

	case "timezone":
		echo "<div class='form-field{$f_class}' {$f_attr}><span class='form-head{$tip_class}' for='$key' {$f_tip}>{$f_title}</span>";
		echo "<select name='{$key}' id='{$key}'>";

		$f_value = get_option( 'gmt_offset' );

		$offset_range = array( -12, -11.5, -11, -10.5, -10, -9.5, -9, -8.5, -8, -7.5, -7, -6.5, -6, -5.5, -5, -4.5, -4, -3.5, -3, -2.5, -2, -1.5, -1, -0.5,
		0, 0.5, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5, 5.5, 5.75, 6, 6.5, 7, 7.5, 8, 8.5, 8.75, 9, 9.5, 10, 10.5, 11, 11.5, 12, 12.75, 13, 13.75, 14);
		foreach ( $offset_range as $offset ){
			if ( 0 <= $offset )
				$offset_name = '+' . $offset;
			else
				$offset_name = (string) $offset;

			$offset_name = str_replace( array('.25','.5','.75'), array(':15',':30',':45'), $offset_name );
			$offset_name = 'UTC' . $offset_name;

			$selected = '';
			if ( $offset == $f_value ) $selected = 'selected="selected" ';
			echo '<option ' . $selected . 'value="' . $offset . '">' . esc_html( $offset_name ) . "</option>";
		}

		echo "</select>";
		echo "<br /><br />Curent UTC time: <em>". date_i18n( 'Y-m-d G:i:s a', false, 'gmt' )."</em><br />Local time: <em>". date_i18n( 'Y-m-d G:i:s a' ) . "</em>";
		echo "</div>";
	break;

	default:
		if( isset( $field['html'] ))
			echo $field['html'];
	break;

	endswitch;

	if( isset( $field['html_after'] ))
		echo $field['html_after'];
}
?>