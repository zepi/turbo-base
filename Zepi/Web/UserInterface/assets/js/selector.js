/**
 * zepi Turbo
 * 
 * UserInterface Selector Field Type
 */
;(function ( $, window, document, undefined ) {
    var pluginName = "ztSelector",
        defaults = {
            propertyName: "value"
        };

    /**
     * Plugin constructor
     */
    function ztSelector( element, options ) {
        this.element = $(element);
        this.availableArea = this.element.find('.selector-available-area');
        this.selectedArea = this.element.find('.selector-selected-area');

        this.options = $.extend( {}, defaults, options) ;
        
        this.selectedItems = new Array();
        this.selectedSide = '';

        this._defaults = defaults;
        this._name = pluginName;

        this.init();
    }

	/**
	 * Plugin functions
	 */
    ztSelector.prototype = {
        init: function() {
        	var _selector = this;
        	
        	this.element.find('.item-search-field').keyup(function (event) { _selector.searchItem(event); });
        	this.element.find('.item-button-add').click(function (event) { _selector.moveToArea($(this).closest('.item'), event, 'selected'); });
        	this.element.find('.item-button-remove').click(function (event) { _selector.moveToArea($(this).closest('.item'), event, 'available'); });
        	
        	this.saveSelectedItems();
        },
        
        /**
         * Moves an item from one to the other area
         */
        moveToArea: function (item, event, targetArea) {
            console.log(event);
        	var selector = this.element;
        	var parent = this;
        	
        	if (targetArea == 'selected') {
        		var area = this.selectedArea; 
        	} else if (targetArea == 'available') {
        		var area = this.availableArea;
        	}
        	
			item.appendTo(area.find('ul'));

        	this.saveSelectedItems();
        	this.sortItems(targetArea);        	
        	this.executeSearch(area);
        },
        
        /**
         * Saves the selected items in an serialized array
         */
        saveSelectedItems: function () {
        	var items = new Array();
        	this.selectedArea.find('ul').children().each(function () {
        		items.push($(this).data('hash'));
        	});

        	this.element.find('.serialized-selected-items').val(JSON.stringify(items));
        },
        
        /**
         * Sort the items alphabetically by the item name
         */
        sortItems: function (targetArea) {
        	if (targetArea == 'available') {
    			var area = this.availableArea.find('ul');
    		} else if (targetArea == 'selected') {
    			var area = this.selectedArea.find('ul');
    		}

        	var items = area.children();
        	
        	items.sort(function (a, b) {
        		var keyA = $(a).find('.item-name').text().toUpperCase();
        		var keyB = $(b).find('.item-name').text().toUpperCase();
        		if (keyA < keyB) {
        			return -1;
        		} else {
        			return 1;
        		}
        	});
        	
        	$.each(items, function (index, row) {
        	    area.append(row);
        	});
        },
        
        /**
         * Event handler when someone changes the value in the search fields
         * 
         * @param Event event
         */
        searchItem: function (event) {
        	var target = $(event.target);
        	var area = target.closest('.selector-area');
        	
        	this.executeSearch(area);
        },
        
        /**
         * Executes the search
         * 
         * @param jQuery area
         */
        executeSearch: function (area) {
        	var query = area.find('.item-search-field').val().toLowerCase();
        	
        	area.find('ul > li').each(function () {
        		var itemText = $(this).find('.item-name, .item-data-content').text();
        		
        		if (itemText.toLowerCase().indexOf(query) < 0) {
        			$(this).hide();
        		} else {
        			$(this).show();
        		}
        	});
        }
    };

    /**
     * Plugin wrapper
     */
    $.fn[pluginName] = function ( options ) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName,
                new ztSelector( this, options ));
            }
        });
    };
    
    /**
     * Initialize every zepi Turbo selectors
     */
    $(document).ready(function () {
	    jQuery('.selector').ztSelector();
    });
})( jQuery, window, document );

