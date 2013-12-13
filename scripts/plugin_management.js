jQuery(document).ready(function(){
	jQuery( 'input[name=plugin_restriction]' ).change(function(){w4sl_showhide_plugin_moderators();});
	w4sl_showhide_plugin_moderators();
});
function w4sl_showhide_plugin_moderators(){
	var plugin_restriction = jQuery('input[name=plugin_restriction]:checked', '#w4sl_plugin_management').val();
	if( plugin_restriction == 1 )
		jQuery('.w4sl_ff_plugin_moderators').show();
	else
		jQuery('.w4sl_ff_plugin_moderators').hide();
}