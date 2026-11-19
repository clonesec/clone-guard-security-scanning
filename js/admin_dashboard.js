// CloneGuard Security Dashboard widget: loads the scan status after the Dashboard has rendered.
jQuery(function($) {
	var $widget = $('#cgss-dashboard-widget');
	if(!$widget.length) {
		return;
	}

	function fail() {
		$widget.html('<p>The status could not be loaded. Please reload the page.</p>');
	}

	$.ajax({
		url: window.ajaxurl,
		method: 'post',
		dataType: 'json',
		data: {
			action: $widget.data('action'),
			_wpnonce: $widget.data('nonce')
		}
	}).done(function(data) {
		if(data && data.html) {
			$widget.html(data.html);
		} else {
			fail();
		}
	}).fail(fail);
});
