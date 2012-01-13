function getQueryVariable(url, variable) {
	var query = url.search.substring(1);
	var vars = query.split("&");
	for (var i=0;i<vars.length;i++) {
		var pair = vars[i].split("=");
		if (pair[0] == variable) {
		  return pair[1];
		}
	} 
	return false;
}

function getData(post_id, module_id) {
	jQuery.ajax({
	   type: "POST",
	   url: "/wp-admin/admin-ajax.php",
	   data: 'action=wp_cb_wysiwyg&operation=get' +
			 '&post_id=' + post_id +	
			 '&module_id=' + module_id,			 
       dataType: 'json',	   
	   success: function(data){
			switchEditors.go('content', 'html');
			jQuery('#content').val(data.result);
			jQuery('#postdivrich').show();
	   },
		error: function(XMLHttpRequest, textStatus, errorThrown){
			alert(textStatus);		
		}	   
	 });			
}

	function load() {
		cfct_build = jQuery('#cfct-build');
		if (cfct_build.length > 0) {
			var cfct_wysiwyg = jQuery('.cfct-module-cfct-wysiwyg', cfct_build);
			if (cfct_wysiwyg.length > 0) {
				jQuery('<input type="hidden" value="" name="module_id" id="module_id">').appendTo(jQuery('form#post'));
				jQuery('.cfct-module-cfct-wysiwyg', cfct_build).each(function(index) {
					var module_id = jQuery(this).attr('id');	
					var text = jQuery.trim(jQuery('.cfct-module-content-title', jQuery(this)).text());
					var text = text.replace("WP Wysiwyg", "");
					jQuery('<li><a href="#' + module_id + '" title="Carrington wysiwyg editing mode">' + text + '</a></li>').appendTo(jQuery('#cfct-build-tabs'));

					jQuery('#cfct-build-tabs li a:[href=#' + module_id + ']',cfct_build).click(function() {
						//cfct_build = jQuery('#cfct-build');				
						jQuery('#cfct-build-tabs li a:[href=#cfct-build-data]', cfct_build).unbind('click');
						jQuery('#cfct-build-tabs li a:[href=#cfct-build-data]', cfct_build).click(function() {
							location.reload(true);
						});
						jQuery('#module_id').val(module_id);				
						jQuery('#cfct-build-tabs li').removeClass('active');
						jQuery(this).parent('li').addClass('active');
						jQuery('#cfct-build-data').hide();
						cfctPrepMediaGallery('wordpress');
						var post_id = getQueryVariable(window.location, 'post');
						if (post_id) {
							getData(post_id, module_id);
						}				
						return false;
					});
				});
			}
		}
	}


jQuery(function(){
	setTimeout("load()",1000);
});