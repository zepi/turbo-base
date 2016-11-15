/**
 * zepi Turbo
 * 
 * UserInterface Selector Field Type
 */
;(function ( $, window, document, undefined ) {
    var pluginName = "ztIpAddress",
        element,
        input,
        family = 'IP',
        ipType = 'ip-address',
        valid = false,
        defaults = {
            propertyName: "value"
        };

    /**
     * Plugin constructor
     */
    function ztIpAddress( element, options ) {
        this.element = $(element);
        this.input = this.element.find('input');
        this.ipType = this.input.data('type');

        this.options = $.extend( {}, defaults, options) ;

        this._defaults = defaults;
        this._name = pluginName;

        this.init();
    }

	/**
	 * Plugin functions
	 */
    ztIpAddress.prototype = {
        init: function() {
        	var _ipField = this;
        	
        	this.input.keypress(function (event) { _ipField.validateInput(event); });
        	this.input.keyup(function (event) { _ipField.validateIp(); });
            this.input.change(function (event) { _ipField.validateIp(); });
        	
        	this.validateType();
        	this.validateIp();
        },
        
        validateIp: function () {
            this.validateType();
            this.element.find('.ip-type').text(this.family);
            
            if (this.family == 'IPv6' && /^([a-f0-9]{0,4}\:){1,7}([a-f0-9]{0,4})(\/([0-9]{1,3}))?$/.test(this.input.val().toLowerCase())) {
                this.valid = true;
            } else if (this.family == 'IPv4' && /^((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\/([0-9]{1,3}))?$/.test(this.input.val())) {
                this.valid = true;
            } else {
                this.valid = false;
            }

            if (this.ipType == 'subnet' && this.valid && !/(\/[0-9]{1,3})$/.test(this.input.val())) {
                this.valid = false;
            } else if (this.ipType != 'subnet' && this.valid && /(\/[0-9]{1,3})$/.test(this.input.val())) {
                this.valid = false;
            }
            
            var formGroup = this.element.closest('.form-group');
            var hasInput = (this.input.val().length > 0);
            if (this.valid && hasInput && !formGroup.hasClass('has-success')) {
                formGroup.addClass('has-feedback').addClass('has-success').removeClass('has-error');
                formGroup.children('div').find('span.glyphicon').remove();
                formGroup.children('div').append(jQuery('<span></span>').addClass('glyphicon glyphicon-ok form-control-feedback'));
            } else if (!this.valid && hasInput && !formGroup.hasClass('has-error')) {
                formGroup.addClass('has-feedback').removeClass('has-success').addClass('has-error');
                formGroup.children('div').find('span.glyphicon').remove();
                formGroup.children('div').append(jQuery('<span></span>').addClass('glyphicon glyphicon-remove form-control-feedback'));
            } else if (!hasInput) {
                formGroup.removeClass('has-feedback').removeClass('has-success').removeClass('has-error');
                formGroup.find('span.form-control-feedback').remove();
            }
        },
        
        validateInput: function (event) {
            this.validateType();
            
            var allowedCharacters = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.', '/'];
            if (this.family == 'IPv6' || this.family == 'IP') {
                allowedCharacters.push('a', 'b', 'c', 'd', 'e', 'f', ':');
            }
            
            if (allowedCharacters.indexOf(event.key.toLowerCase()) == -1) {
                event.preventDefault(false);
            }
        },
        
        validateType: function () {
            var inputValue = this.input.val().toLowerCase();

            if (inputValue.indexOf(':') > -1 && /^([0-9a-f\:\/\.]*)$/.test(inputValue)) {
                this.family = 'IPv6';
            } else if (inputValue.indexOf('.') > -1 && /^([0-9\.\/]*)$/.test(inputValue)) {
                this.family = 'IPv4';
            } else {
                this.family = 'IP';
            }
        }
    };

    /**
     * Plugin wrapper
     */
    $.fn[pluginName] = function ( options ) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName,
                new ztIpAddress( this, options ));
            }
        });
    };
    
    /**
     * Initialize every zepi Turbo IP Field
     */
    $(document).ready(function () {
	    jQuery('.ip-field').ztIpAddress();
    });
})( jQuery, window, document );

