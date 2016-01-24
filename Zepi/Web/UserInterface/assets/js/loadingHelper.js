function showLoadingMessage(message, icon, position)
{
	if (icon === undefined) {
		icon = 'glyphicon-off';
	}
	
	if (position === undefined) {
		position = 'left';
	}
	
	var iconClass = icon;
	if (icon.substring(0, 3) == 'mdi') {
		iconClass = 'mdi ' + icon;
	} else if (icon.substring(0, 9) == 'glyphicon') {
		iconClass = 'glyphicon ' + icon;
	}

	var iconLeft = '';
	var iconRight = '';
	
	if (position === 'left') {
		iconLeft = '<i class="icon %class% icon-spin"></i>';
	} else if (position === 'right') {
		iconRight = '<i class="icon %class% icon-spin"></i>';
	}
	
	var tpl = '<span class="isloading-wrapper %wrapper%">' + iconLeft + '<span class="isloading-text">%text%</span>' + iconRight + '</span>';
	
	jQuery.isLoading({
		'text': message,
		'class': iconClass,
		'tpl': tpl
	})
}

function hideLoadingMessage()
{
	jQuery.isLoading('hide');
}