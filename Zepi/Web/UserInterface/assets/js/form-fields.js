jQuery(document).ready(function () {
    // Price fields
	jQuery('.price-field').maskMoney();
	
	// Extended select fields
	jQuery('.extended-select').each(function () {
	    var extendedField = jQuery(this);
	    var options = {
            plugins: [],
            create: false,
            maxItems: 1
        };

	    if (jQuery(this).data('max') != '') {
	        options.maxItems = jQuery(this).data('max');
	        options.plugins.push('remove_button');
	    }
	    
	    var extendedOptions = JSON.parse(decodeURIComponent(jQuery(this).data('extended-options')));
	    if (typeof extendedOptions == 'object') {
	        options = jQuery.extend(true, options, extendedOptions);
	    }
	    
	    if (options.load == '__ztExtendedSelectAjaxSearch') {
	        options.load = function (query, callback) {
	            ztFormUpdateExtendedSelectSearch(extendedField, query, callback);
	        };
	    }
	    
	    jQuery(this).selectize(options);
	});
});

function ztFormUpdateExtendedSelectSearch(field, query, callback)
{
    if (!query.length) {
        return callback();
    }
    
    var form = field.parents('form');
    
    var dataArray = {};
    jQuery.map(form.serializeArray(), function (n, i) {
        dataArray[n['name']] = n['value'];
    });
    
    dataArray['form-update-request'] = field.attr('id');
    dataArray['form-extended-entity-select-query'] = query;

    $.ajax({
        url: form.prop('action'),
        type: 'POST',
        data: dataArray,
        dataType: 'json',
        error: function() {
            callback();
        },
        success: function(response) {
            if (jQuery(response).find('#login-user-data').length > 0) {
                window.location.reload();
                return;
            }
            
            callback(response.data);
            
            form.find('input[name=csrf-key]').val(response.csrf.key);
            form.find('input[name=csrf-token]').val(response.csrf.token);
        }
    });
}