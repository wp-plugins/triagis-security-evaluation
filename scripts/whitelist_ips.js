jQuery(document).ready(function(){
	jQuery( 'input[name=ip_restriction]' ).change(function(){w4sl_showhide_whitelist_ips_input();});
	w4sl_showhide_whitelist_ips_input();
});
function w4sl_showhide_whitelist_ips_input(){
	var ip_restriction = jQuery('input[name=ip_restriction]:checked', '#w4sl_whitelist_ip_form').val();
	if( ip_restriction == 1 ){
		jQuery('.w4sl_ff_unknown_ip_notification').hide();
		jQuery('.w4sl_ff_domain2ip').show();
		jQuery('.w4sl_ff_whitelist_ips').show();
	}
	else{
		jQuery('.w4sl_ff_whitelist_ips').hide();
		jQuery('.w4sl_ff_domain2ip').hide();
		jQuery('.w4sl_ff_unknown_ip_notification').show();
	}
}