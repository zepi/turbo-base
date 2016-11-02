jQuery(document).ready(function () {
	jQuery('.userinterface-tabs').each(function () {
		$('ul.nav-tabs a').click(function (e) {
			e.preventDefault();
		    jQuery(this).tab('show');
		});
	});
	
	fakewaffle.responsiveTabs(['xs', 'sm']);
});