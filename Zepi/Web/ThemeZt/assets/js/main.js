jQuery(document).ready(function () {
    jQuery('.responsive-nav-button').on('click', function () {
        var target = jQuery(this).closest('nav').find(jQuery(this).data('target'));
        
        if (target.hasClass('active')) {
            target.removeClass('active');
            jQuery(this).removeClass('active');
        } else {
            jQuery(this).closest('nav').find('.active').removeClass('active');
            target.addClass('active');
            jQuery(this).addClass('active');
        }
    });
    
    jQuery('.responsive-submenu-button').on('click', function (ev) {
        ev.preventDefault(false);
        var submenuHolder = jQuery(this).closest('li').children('.submenu-holder');
        
        if (submenuHolder.is(':visible')) {
            submenuHolder.hide();
            jQuery(this).find('span').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
        } else {
            submenuHolder.show();
            jQuery(this).find('span').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
        }
    });
});
