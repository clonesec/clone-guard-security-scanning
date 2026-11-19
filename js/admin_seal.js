// Overview page seal picker: live preview, one-click site-wide seal, shortcode for inline placement.
jQuery(function($) {
	var $form = $('#cgss-seal-picker');
	if(!$form.length) {
		return;
	}

	var image = String($form.data('image'));
	var pixels = $form.data('pixels') || {};
	var $preview = $form.find('.cgss-seal-preview');
	var $img = $form.find('.cgss-seal-preview-img');
	var $status = $form.find('.cgss-seal-status');
	var $save = $form.find('.cgss-seal-save');
	var $remove = $form.find('.cgss-seal-remove');
	var $view = $form.find('.cgss-seal-view');

	function value(name) {
		return $form.find('input[name="' + name + '"]:checked').val();
	}

	function update() {
		var icon = value('icon');
		var size = value('size');
		var position = value('position');
		var px = pixels[size] || [150, 54];

		$img.attr({ src: image.replace('{icon}', icon), width: px[0], height: px[1] });
		$preview.removeClass('is-left is-right is-relative').addClass('is-' + position);

		var inline = position === 'relative';
		$form.find('.cgss-seal-corner-actions').prop('hidden', inline);
		$form.find('.cgss-seal-inline-actions').prop('hidden', !inline);
		$form.find('.cgss-seal-shortcode').text('[cloneguard_seal icon="' + icon + '" size="' + size + '" position="relative"]');
		$form.find('.cgss-seal-copy').text('Copy');
	}

	function setEnabled(enabled) {
		$form.data('enabled', enabled ? 1 : 0);
		$save.text(enabled ? 'Update the seal' : 'Show on my site');
		$remove.prop('hidden', !enabled);
		$view.prop('hidden', !enabled);
	}

	function send(data) {
		$save.prop('disabled', true);
		$remove.prop('disabled', true);
		$.ajax({
			url: window.ajaxurl,
			method: 'post',
			dataType: 'json',
			data: $.extend({ action: $form.data('action'), _wpnonce: $form.data('nonce') }, data)
		}).done(function(res) {
			$status.text(res && res.message ? res.message : 'The seal could not be saved. Please reload the page and try again.');
			$status.toggleClass('is-error', !(res && res.status === 'success'));
			if(res && res.status === 'success') {
				setEnabled(res.enabled);
			}
		}).fail(function() {
			$status.text('The seal could not be saved. Please reload the page and try again.').addClass('is-error');
		}).always(function() {
			$save.prop('disabled', false);
			$remove.prop('disabled', false);
		});
	}

	$form.on('change', 'input', update);

	$form.on('submit', function(e) {
		e.preventDefault();
		send({ op: 'save', icon: value('icon'), size: value('size'), position: value('position') });
	});

	$remove.on('click', function() {
		send({ op: 'remove' });
	});

	$form.on('click', '.cgss-seal-copy', function() {
		var $button = $(this);
		var text = $form.find('.cgss-seal-shortcode').text();
		function done() {
			$button.text('Copied');
		}
		if(navigator.clipboard && window.isSecureContext) {
			navigator.clipboard.writeText(text).then(done);
		} else {
			var $tmp = $('<textarea>').val(text).css({ position: 'fixed', opacity: 0 }).appendTo('body');
			$tmp[0].select();
			document.execCommand('copy');
			$tmp.remove();
			done();
		}
	});

	update();
});
