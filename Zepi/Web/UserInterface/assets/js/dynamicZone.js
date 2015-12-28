jQuery(document).ready(function () {
	jQuery('.dynamic-zone').each(function () {
		var dynamicZone = jQuery(this);
		var triggerId = jQuery(this).data('trigger-id'); 
		jQuery('#' + triggerId).change(function () {
			var form = jQuery(this).parents('form');
			
			var dataArray = {};
			jQuery.map(form.serializeArray(), function (n, i) {
				dataArray[n['name']] = n['value'];
			});
			
			dataArray[dynamicZone.data('name')] = true;
			
			jQuery.post(form.prop('action'), dataArray, function (response) {
				var htmlContent = jQuery(response).find('#' + dynamicZone.prop('id')).html();
				jQuery('#' + dynamicZone.prop('id')).html(htmlContent);
				
				jQuery('input[name=csrf-key]').val(jQuery(response).find('input[name=csrf-key]').val());
				jQuery('input[name=csrf-token]').val(jQuery(response).find('input[name=csrf-token]').val());
			});
		});
	});
});