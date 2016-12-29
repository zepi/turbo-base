jQuery(document).ready(function () {
    // Price fields
	jQuery('.price-field').maskMoney();
	
	// Extended select fields
	jQuery('.extended-select').each(function () {
	    var maxNumberOfSelection = 1;
	    var plugins = [];

	    if (jQuery(this).data('max') != '') {
	        maxNumberOfSelection = jQuery(this).data('max');
	        plugins.push('remove_button');
	    }
	    
	    jQuery(this).selectize({
	        plugins: plugins,
	        create: false,
	        maxItems: maxNumberOfSelection
	    });
	});
});