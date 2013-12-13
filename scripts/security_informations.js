jQuery(document).ready(function() {
	jQuery('#w4sl_wp_content_location_change_form_btn').click(function(){
		var container = jQuery('#w4sl_wp_content_location_change_form_content');
		
		if( container.is(":hidden")){
			container.html( '<img src="images/loading.gif" /> loading form...' ).addClass('w4sl_loading_big').show();
			jQuery.post( ajaxurl, 'action=w4sl_wp_content_location_change_form', function(r){
				if( r.html ){container.html(r.html);}
				container.addClass( 'loaded' ).removeClass('w4sl_loading_big');
			});
		}
		else{
			container.hide();
		}

		return false;
	});
	
	jQuery('#w4sl_change_wp_content_location_act_btn').live('click', function(){
		var up = jQuery(this).next('span');
		var th = jQuery(this);
		
		var wp_content_dir =  jQuery( '#wp_content_dir' ).val();
		var wp_content_url =  jQuery( '#wp_content_url' ).val();
		if( wp_content_dir === undefined || wp_content_dir == '' ){
			up.html( 'New directory value empty.' );
			return;
		}
		if( wp_content_url === undefined || wp_content_url == '' ){
			up.html( 'Content url value empty.' );
			return;
		}

		up.html( '<img src="images/loading.gif" class="loading_img" /> changing...' );
		th.hide();

		jQuery.post( ajaxurl, 'action=w4sl_change_wp_content_location&content_dir='+ wp_content_dir + '&content_url='+ wp_content_url, function(r){
			if( r.error ){
				up.html(r.error);
				th.show();
			}
			else if( r.sucess ){
				up.html('Directory changed..' );
				setTimeout(function(){  jQuery( '#w4sl_wp_content_location_change_form_content' ).empty().hide();},5000);
			}
		});

		return false;
	});

	
	jQuery('#w4sl_unappropriate_entries_table_btn').click(function(){
		var container = jQuery('#w4sl_unappropriate_entries_table_content');

		if( !container.hasClass( 'loaded' )){
			container.html( '<img src="images/loading.gif" /> loading files and folders having wrong permission...' ).addClass('w4sl_loading_big').show();
			jQuery.post( ajaxurl, 'action=w4sl_unappropriate_entries_table', function(r){
				if( r.html ){container.html(r.html);}
			});
		}
		else{
			container.toggle();
		}
		return false;
	});
	
	
	jQuery('a.w4sl_cp').live('click', function(){
		var up = jQuery(this).next('span');
		var th = jQuery(this);
		var file = jQuery(this).attr('alt');

		th.hide();
		up.html( '<img src="images/loading.gif" class="loading_img" /> changing permission...' );

		jQuery.post( ajaxurl, 'action=w4sl_change_permission&file='+file, function(r){
			if( r.error ){
				up.html(r.error);
				th.show();
			}
			else if( r.sucess ){
				up.html('permission changed.');
				th.parent('td').parent('tr').children('td.w4ls_cip').html(r.perm);
			}
		});

		return false;
	});
	
	jQuery('#w4sl_checkthimbthumb_btn').click(function(){
		var container = jQuery('#w4sl_checkthimbthumb_content');

		if( container.is(":hidden")){
			container.html( '<img src="images/loading.gif" /> loading "thimbthumb.php" files...' ).addClass('w4sl_loading_big').show();
			jQuery.post( ajaxurl, 'action=w4sl_thimbthumb_files_table', function(r){
				if( r.html ){container.html(r.html);}
				container.removeClass('w4sl_loading_big');
			});
		}
		else{
			container.hide();
		}
		return false;
	});

	jQuery('a.w4sl_delete_file').live('click', function(){
		var up = jQuery(this).next('span');
		var th = jQuery(this);
		var file = jQuery(this).attr('alt');

		th.hide();
		up.html( '<img src="images/loading.gif" class="loading_img" /> deleting file...' );

		jQuery.post( ajaxurl, 'action=w4sl_delete_file&file='+file, function(r){
			if( r.error ){
				up.html(r.error);
				th.show();
			}
			else if( r.sucess ){
				up.html('File has been deleted.');
			}
		});

		return false;
	});

	jQuery('#w4sl_username_change_form_btn').click(function(){
		var container = jQuery('#w4sl_username_change_form_content');
		
		if( container.is(":hidden")){
			container.html( '<img src="images/loading.gif" /> loading user using "admin" as username...' ).addClass('w4sl_loading_big').show();
			jQuery.post( ajaxurl, 'action=w4sl_username_change_form', function(r){
				if( r.html ){container.html(r.html);}
				container.removeClass('w4sl_loading_big');
			});
		}
		else{
			container.hide();
		}

		return false;
	});
	
	jQuery('a.w4sl_rename_username').live('click', function(){
		var up = jQuery(this).next('span');
		var th = jQuery(this);
		
		var u_id = jQuery(this).attr('alt');
		var uname =  jQuery( '#w4sl_adminusername_' + u_id ).val();

		if( uname === undefined || uname == '' ){
			up.html( 'You havent entered anything.' );
			return false;
		}

		up.html( '<img src="images/loading.gif" class="loading_img" /> deleting file...' );
		th.hide();

		jQuery.post( ajaxurl, 'action=w4sl_rename_username&u_id='+u_id+'&uname='+uname, function(r){
			if( r.error ){
				up.html(r.error);
				th.show();
			}
			else if( r.new_login ){
				up.html('Username changed to <b>'+ r.new_login + '</b>.' );
				setTimeout(function(){  jQuery( '#w4sl_username_change_form_content' ).empty().hide();},5000);
			}
		});

		return false;
	});
	
	jQuery('#w4sl_osmpinfo_btn').click(function(){
		var container = jQuery('#w4sl_osmpinfo_content');
		
		if( !container.hasClass( 'loaded' )){
			container.html( '<img src="images/loading.gif" /> loading informations...' ).addClass('w4sl_loading_big').show();
			jQuery.post( ajaxurl, 'action=w4sl_osmp_info_table', function(r){
				if( r.html ){container.html(r.html);}
				container.addClass( 'loaded' ).removeClass('w4sl_loading_big');
			});
		}
		else{
			container.toggle();
		}
		return false;
	});

	jQuery('#w4sl_change_dbprefix_btn').click(function(){
		var container = jQuery('#w4sl_change_dbprefix_content');
		var th = jQuery(this);

		if( container.is(":hidden")){
			container.html( '<img src="images/loading.gif" /> loading form...' ).addClass('w4sl_loading_big').show();
			jQuery.post( ajaxurl, 'action=w4sl_change_dbprefix_form', function(r){
				if( r.html ){container.html(r.html);}
				container.removeClass('w4sl_loading_big');
			});
		}
		else{
			container.hide();
		}
		return false;
	});

	jQuery('#w4sl_change_dbprefix_act_btn').live('click', function(){
		var up = jQuery(this).next('span');
		var th = jQuery(this);
		
		var new_prefix =  jQuery( '#w4sl_dbprefix' ).val();
		if( new_prefix === undefined || new_prefix == '' ){
			up.html( 'You havent entered anything' );
			return;
		}

		up.html( '<img src="images/loading.gif" class="loading_img" /> changing prefix...' );
		th.hide();

		jQuery.post( ajaxurl, 'action=w4sl_change_dbprefix&new_prefix='+ new_prefix, function(r){
			if( r.error ){
				up.html(r.error);
				th.show();
			}
			else if( r.sucess ){
				up.html('Database prefix changed. New prefix is <b>'+ r.new_prefix + '</b>.' );
				jQuery('#w4sl_dbprefix_notice').remove();
				jQuery('#w4sl_cprefix').html( r.new_prefix );
				setTimeout(function(){  jQuery( '#w4sl_change_dbprefix_content' ).empty().hide();},5000);
			}
		});

		return false;
	});

	jQuery('#w4sl_move_config_file_btn').click(function(){
		var up = jQuery(this).next('span');
		var th = jQuery(this);
		
		up.html( '<img src="images/loading.gif" class="loading_img" /> moving file...' );
		th.hide();

		jQuery.post( ajaxurl, 'action=w4sl_move_config_file', function(r){
			if( r.error ){
				up.html(r.error);
				th.show();
			}
			else if( r.sucess ){
				jQuery( '#w4sl_config_notice' ).html( 'Your config.php file is on a secure place.' ).removeClass('errors').addClass('notes');
			}
		});

		return false;
	});
});