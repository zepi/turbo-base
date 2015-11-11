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
        this.leftSide = this.element.find('.selector-left-side');
        this.rightSide = this.element.find('.selector-right-side');

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
        	
        	this.element.find('.item').click(function (event) { _selector.clickItem(event); });
        	this.element.find('.item-search-field').keyup(function (event) { _selector.searchItem(event); });
        	this.element.find('.to-right').click(function (event) { _selector.moveToSide(event, 'right'); });
        	this.element.find('.to-left').click(function (event) { _selector.moveToSide(event, 'left'); });
        	
        	this.saveSelectedItems();
        },
        
        /**
         * Enables and disables the buttons
         */
    	updateButtons: function () {
        	var buttonHolder = this.element.find('.selector-center-buttons');
        	
        	buttonHolder.find('button').prop('disabled', true);
        	
        	if (this.selectedSide == 'left') {
        		buttonHolder.find('.to-right').prop('disabled', false);
        	} else if (this.selectedSide == 'right') {
        		buttonHolder.find('.to-left').prop('disabled', false);
        	}
        },
        
        /**
         * Moves an item from one to the other side
         */
        moveToSide: function (event, targetSide) {
        	var selector = this.element;
        	var parent = this;
        	
        	if (targetSide == 'left') {
        		var side = this.leftSide; 
        	} else if (targetSide == 'right') {
        		var side = this.rightSide;
        	}
        	
        	$.each(this.selectedItems, function (index, value) {
        		var item = selector.find('.item[data-hash=' + value + ']');
        		
        		if (targetSide == 'left') {
        			item.appendTo(side.find('ul'));
        		} else if (targetSide == 'right') {
        			item.appendTo(side.find('ul'));
        		}
        	});

        	this.saveSelectedItems();
        	this.sortItems(targetSide);        	
        	this.unmarkItems();
        	this.executeSearch(side);
        },
        
        /**
         * Saves the selected items in an serialized array
         */
        saveSelectedItems: function () {
        	var items = new Array();
        	this.rightSide.find('ul').children().each(function () {
        		items.push($(this).data('hash'));
        	});

        	this.element.find('.serialized-selected-items').val(JSON.stringify(items));
        },
        
        /**
         * Sort the items alphabetically by the item name
         */
        sortItems: function (targetSide) {
        	if (targetSide == 'left') {
    			var side = this.leftSide.find('ul');
    		} else if (targetSide == 'right') {
    			var side = this.rightSide.find('ul');
    		}

        	var items = side.children();
        	
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
        		side.append(row);
        	});
        },
        
        /**
         * Unmarks all items
         */
        unmarkItems: function () {
        	this.element.find('.active-item').removeClass('active-item');
        	this.selectedItems = new Array();
        	this.selectedSide = '';
        	
        	this.updateButtons();
        },

        /**
         * Event handler when someone clicks an item
         * 
         * @param Event event
         */
        clickItem: function (event) {
        	var target = $(event.target);
        	var item = target.closest('.item');
        	var side = item.closest('.selector-side');
        	var selector = this.element;
        	
        	/**
        	 * If the item is disabled nothing should happen
        	 */
        	if (item.hasClass('item-disabled')) {
        		return;
        	}
        	
        	/**
        	 * If the selected side is the same and the ctrl key is pressed
        	 * we add the item to a list to move the whole list
        	 */
        	if (event.ctrlKey && side.data('side') === this.selectedSide) {
        		target.closest('.item').addClass('active-item');
        		
        		this.selectedItems.push(item.data('hash'));
        	} else {
        		selector.find('.active-item').removeClass('active-item');
            	target.closest('.item').addClass('active-item');
            	
            	this.selectedItems = new Array(item.data('hash'));
            	this.selectedSide = side.data('side');
        	}
        	
        	/**
        	 * Update the buttons
        	 */
        	this.updateButtons();
        },
        
        /**
         * Event handler when someone changes the value in the search fields
         * 
         * @param Event event
         */
        searchItem: function (event) {
        	var target = $(event.target);
        	var side = target.closest('.selector-side');
        	
        	this.executeSearch(side);
        },
        
        /**
         * Executes the search
         * 
         * @param jQuery side
         */
        executeSearch: function (side) {
        	var query = side.find('.item-search-field').val().toLowerCase();
        	
        	side.find('ul > li').each(function () {
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

