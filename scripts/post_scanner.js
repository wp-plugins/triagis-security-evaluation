jQuery(document).ready(function(){
	jQuery('#w4sl_spamlistdata_btn').click(function(){
		var container = jQuery('#w4sl_spamlistdata_content');
		
		if( container.is(":hidden")){
			container.html( '<img src="images/loading.gif" /> loading info...' ).addClass('w4sl_loading_big').show();
			jQuery.post( ajaxurl, 'action=w4sl_spamlist_list', function(r){
				if( r.html ){container.html(r.html);}
				container.removeClass('w4sl_loading_big');
			});
		}
		else{
			container.hide();
		}
		return false;
	});
	
	jQuery('#w4sl_spamlist li a').live('click', function(){
		var d = jQuery(this);
		var id = d.attr('href').split("#")[1];
		var u = jQuery('#w4sl_spamlist_urls_'+id);
		var p = d.parent('li');
		
		if( p.hasClass( 'data_loading' )){
			return false;
		}
		
		if( !u.html() || !p.hasClass( 'data_loaded' )){
			p.addClass( 'data_loading' );
			d.append( ' <span class="w4ls_loading"><img src="images/loading.gif" style="position:relative; top:2px;" /> Loading...</span>' );
			//return false;
			
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: { action: "w4sl_spamlist_data", id: id },
				success: function( response ){
					if( response.html ){
						u.html( response.html );
						p.addClass( 'data_loaded' );
					}
					jQuery('.w4ls_loading').remove();
					p.removeClass( 'data_loading' );
				}
			});
		}
		
		if( p.hasClass( 'panel_open' )){
			u.hide();
			p.removeClass( 'panel_open' );
		}else{
			u.show();
			p.addClass( 'panel_open' );
		}
	
		return false;
	});
	
	
	jQuery('#w4sl_updatespamlist_btn').click(function(){
		var container = jQuery('#w4sl_updatespamlist_content' );
	
		container.html( '<img src="images/loading.gif" /> updating spam list data...' ).addClass('w4sl_loading_big').show();
		jQuery.post( ajaxurl, 'action=w4sl_update_spamlist_data', function(r){
			if( r.html ){container.html(r.html).show();}
			setTimeout(function(){container.hide();},3000);
		});

		return false;
	});
	
	
	jQuery('#w4sl_spam_postlist li a').live('click', function(){
		var d = jQuery(this);
		var id = d.attr('href').split("#")[1];
		var u = jQuery('#w4sl_spam_postlist_'+id);
		var p = d.parent('li');
	
		if( p.hasClass( 'panel_open' )){
			u.hide();
			p.removeClass( 'panel_open' );
		}else{
			u.show();
			p.addClass( 'panel_open' );
		}
	
		return false;
	});
});