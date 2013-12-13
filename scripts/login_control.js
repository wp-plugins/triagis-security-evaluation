jQuery(document).ready(function(){
	jQuery( 'input[name=status]' ).change(function(){w4sl_showhide_login_control_options();});
	w4sl_showhide_login_control_options();


	jQuery( 'input[name=method]' ).change(function(){w4sl_showhide_login_control_method_options();});
	w4sl_showhide_login_control_method_options();
});
function w4sl_showhide_login_control_options(){
	var status = jQuery('input[name=status]:checked', '#w4sl_login_control').val();
	if( status == 1 ){
		jQuery('#w4sl_lco').show();
	}
	else{
		jQuery('#w4sl_lco').hide();
	}
}

function w4sl_showhide_login_control_method_options(){
	var method = jQuery('input[name=method]:checked', '#w4sl_login_control').val();
	if( method == 'selected' ){
		jQuery('.w4sl_ff_users').show();
		jQuery('.w4sl_ff_roles').hide();
	}
	else if( method == 'byrole' ){
		jQuery('.w4sl_ff_roles').show();
		jQuery('.w4sl_ff_users').hide();
	}
	else{
		jQuery('.w4sl_ff_roles, .w4sl_ff_users').hide();
	}
}