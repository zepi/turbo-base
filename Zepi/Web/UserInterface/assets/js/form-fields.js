jQuery(document).ready(function () {
    // Price fields
	jQuery('.price-field').maskMoney();
	
	// Extended select fields
	jQuery('.extended-select').each(function () {
	    var maxNumberOfSelection = 1;
	    console.log(jQuery(this).data('max'));
	    if (jQuery(this).data('max') != '') {
	        maxNumberOfSelection = jQuery(this).data('max');
	    }
	    
	    jQuery(this).selectize({
	        create: false,
	        maxItems: maxNumberOfSelection
	    });
	});
});