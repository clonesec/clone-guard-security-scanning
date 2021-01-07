jQuery(document).ready(function($) {
	var $form;

    // Listener for form submit.
	function submit() {
		$form = $(this);
		var $button = $form.find('.button-primary');
        var $spinner = $form.find('.spinner:not(.inline)');
		var data = $(this).serialize();

		$button.addClass('disabled');
		$button.addClass('button-disabled');
		$button.addClass('button-primary-disabled');
		$spinner.addClass('is-active');

		$.ajax({
			data: data,
			dataType: 'json',
			method: $form.attr('method'),
			url: $form.attr('action'),
			success: responseSuccess.bind(null, $form),
			error: responseError.bind(null, $form)
		});
		return false;
	} 

    // Handle ajax errors.
	function responseError($form, jqXHR, textStatus, error) {
		var $button = $form.find('.button-primary');
		var $spinner = $form.find('.spinner');

		$button.removeClass('disabled');
		$button.removeClass('button-disabled');
		$button.removeClass('button-primary-disabled');
		$spinner.removeClass('is-active');

		var data = jqXHR.responseJSON;  
		if(data && data.message) {      
			$('#ajax_message').addClass('notice');
			$('#ajax_message').addClass('notice-error');
			$('#ajax_message').removeClass('notice-success');
			$('#ajax_message').html('<p>' + data.message + '</p>');
		} else {
			$('#ajax_message').addClass('notice');
			$('#ajax_message').addClass('notice-error');
			$('#ajax_message').removeClass('notice-success');
			$('#ajax_message').html('<p>There was a problem with the submission.</p>');
		}
		$('html, body').animate({
			scrollTop: 0
		}, 300);
	}

    // Handle ajax success.
	function responseSuccess($form, data) {
		var $button = $form.find('.button-primary');
		var $spinner = $form.find('.spinner');
		var msg = '';

		$button.removeClass('disabled');
		$button.removeClass('button-disabled');
		$button.removeClass('button-primary-disabled');

        if(data.status && data.status == 'success' && data.redirect) {
            window.location.href = $('.button_cancel').attr('href');
        } else if(data.status && data.status == 'success' && data.messages) {
			$('#ajax_message').addClass('notice');
			$('#ajax_message').removeClass('notice-error');
			$('#ajax_message').addClass('notice-success');
			$spinner.removeClass('is-active');
			for(i = 0; i < data.messages.length; i++) {
				if(i > 0) {
					msg += '<br>';
				}
				msg += data.messages[i];
			}
			$('#ajax_message').html('<p>' + msg + '</p>');

			if(data.clear) {
				$('input[type=text]').val('');
				$('input[type=password]').val('');
			}

			$('html, body').animate({
				scrollTop: 0
			}, 300);
		} else if(data.status && data.status == 'success') {
			window.location.href = window.location.href;
		} else {
			if(data.messages) {
				$('#ajax_message').addClass('notice');
				$('#ajax_message').addClass('notice-error');
				$('#ajax_message').removeClass('notice-success');
				for(i = 0; i < data.messages.length; i++) {
					if(i > 0) {
						msg += '<br>';
					}
					msg += data.messages[i];
				}
				$('#ajax_message').html('<p>' + msg + '</p>');
			} else {
				$('#ajax_message').addClass('error');
				$('#ajax_message').removeClass('success');
				$('#ajax_message').html('<p>There was a problem with the submission.</p>');
			}
			$spinner.removeClass('is-active');

			$('html, body').animate({
				scrollTop: 0
			}, 300);
		}
	}

	// Listener for Settings Page password type inputs.
	function toggleInputPassword() {
		var settingsInput = $('.settings-form-input');

		settingsInput.focus(function(){
			$(this).prop('type', 'text')
		  });
		  
		settingsInput.blur(function(){
			$(this).prop('type', 'password')
		});
	}

    // Initialize listeners. 
	function init() {
		$(document).on('submit', '.ajax_form', submit);

		toggleInputPassword();
	}

    // Get everything started.
	init();
});
