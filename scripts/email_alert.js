jQuery(document).ready(function() {
	jQuery('#w4sl_themefilechange_btn').click(function(){
		var container = jQuery('#w4sl_themefilechange_content');
		var th = jQuery(this);
		
		if( !container.hasClass( 'loaded' )){
			container.html( '<img src="images/loading.gif" /> loading info...' ).addClass('w4sl_loading_big');
			jQuery.post( ajaxurl, 'action=w4sl_theme_files_changes', function(r){
				if( r.html ){container.html(r.html);}
				container.addClass( 'loaded' ).removeClass('w4sl_loading_big');
			});
		}

		if( container.is(":hidden")){
			th.html('Hide info');
			container.show();
			jQuery(this).addClass('active');
		}else{
			th.html('Show info');
			container.hide();
			jQuery(this).removeClass('active');
		}
		return false;
	});
});