jQuery(document).ready(function(){
	jQuery( 'input[name=plugin_restriction]' ).change(function(){w4sl_showhide_plugin_moderators();});
	w4sl_showhide_plugin_moderators();

	jQuery( 'input[name=ip_restriction]' ).change(function(){w4sl_showhide_whitelist_ips_input();});
	w4sl_showhide_whitelist_ips_input();
});
function w4sl_showhide_plugin_moderators(){
	var plugin_restriction = jQuery('input[name=plugin_restriction]:checked').val();
	if( plugin_restriction == 1 )
		jQuery('.w4sl_ff_plugin_moderators').show();
	else
		jQuery('.w4sl_ff_plugin_moderators').hide();
}
function w4sl_showhide_whitelist_ips_input(){
	var ip_restriction = jQuery('input[name=ip_restriction]:checked').val();
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