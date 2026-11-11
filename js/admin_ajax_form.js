jQuery(document).ready(function($) {
	var $form;

    // Listener for form submit.
	function submit() {
		$form = $(this);
		console.log($form);
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
		var settingsInput = $('.settings-input-password');

		settingsInput.focus(function(){
			$(this).prop('type', 'text')
		  });
		  
		settingsInput.blur(function(){
			$(this).prop('type', 'password')
		});
	}

	function toggleScheduleRepeatInput() {
		var scheduleFrequencyValue = $('.schedule-frequency input[name="frequency"]:checked').val();

		if (scheduleFrequencyValue === 'one_time') {
			$('.schedule-repeat-input').hide()
		} else {
			$('.schedule-repeat-input').show()
		}
		
	}

	// Listener for Add Exception Page functionalities.
	function addExceptionFunctionalities() {

		var denialTextRadio = $('input[type=radio][name=denialTextIsEnabled]');
		var scanOrHostRadio = $('input[type=radio][name=scanOrHost]');

		denialTextRadio.change(function() {
			if (this.value == 'false') {
				$('#add-exception-justification-text').val('');
				$('#add-exception-justification-text').prop('readonly', false);
			}
			else if (this.value == 'true') {
				$('#add-exception-justification-text').val('Denial-of-Service-only vulnerability marked as compliant');
				$('#add-exception-justification-text').prop('readonly', true);
			}
		});

		scanOrHostRadio.change(function() {
			if (this.value == 'scan') {
				$('.add-exception-scan-tr').removeClass("hide");
				$('.add-exception-host-tr').addClass("hide");
			}
			else if (this.value == 'host') {
				$('.add-exception-host-tr').removeClass("hide");
				$('.add-exception-scan-tr').addClass("hide");
			}
		});

	}

	// Listener for Edit Exception Page functionalities.
	function editExceptionFunctionalities() {

		var denialTextRadioEdit = $('input[type=radio][name=editDenialTextIsEnabled]');
		var dosTextradioFalse = $('#dos-text-radio-false-edit');
		var dosTextradioTrue = $('#dos-text-radio-true-edit');
		var justificationText = $('#edit-exception-justification-text').val();
		var DoSText = 'Denial-of-Service-only vulnerability marked as compliant';


		if (justificationText === DoSText) {
			dosTextradioTrue.prop('checked', true);
		} else {
			dosTextradioFalse.prop('checked', true);
		}

		denialTextRadioEdit.change(function() {
			if (this.value == 'false') {
				$('#edit-exception-justification-text').val('');
				$('#edit-exception-justification-text').prop('readonly', false);
			}
			else if (this.value == 'true') {
				$('#edit-exception-justification-text').val('Denial-of-Service-only vulnerability marked as compliant');
				$('#edit-exception-justification-text').prop('readonly', true);
			}
		});

	}

	// Listener for Bulk Action Add Exception on Vulnerabilities page. 
	function addExceptionModal() {
		var modal = $('#newExceptionModal');
		var closeElement = $('.close');	

		$(".select-bulk-add-exception").change(function() {
			if ($(this).val() === 'add-exception') {
				modal.css("display", "block");
				$('#newExceptionModal textarea').css('border','1px solid #8c8f94');
				$('.modal-validation-text').addClass('hide');
			}
		});
		
		$('.modal-footer button').on('click', function() {
			if($("#newExceptionModal textarea").val() == '') {
				$("#newExceptionModal textarea").css('border','1px solid red');
				$('.modal-validation-text').removeClass('hide');
				return false;
			} else {
				modal.css("display", "none");
			}
		});

		closeElement.on('click', function() {
			modal.css("display", "none");
		});

		$('#newExceptionModal textarea').blur(function(){
			if($(this).val() == '') {
				$(this).css('border','1px solid red');
				$('.modal-validation-text').removeClass('hide');
			} else {
				$(this).css('border','1px solid green');
				$('.modal-validation-text').addClass('hide');
			}
		});

		// Not working.
		$(window).click(function(e) {
			if (e.target === modal) {
				modal.css("display", "none");
			}
		});
	}

    // Initialize listeners. 
	function init() {
		$(document).on('submit', '.ajax_form', submit);

		toggleInputPassword();

		toggleScheduleRepeatInput();

		addExceptionFunctionalities();

		editExceptionFunctionalities();
		
		$('.schedule-frequency input[name="frequency"]').change(function() {
			toggleScheduleRepeatInput()	
		})

		addExceptionModal();
	}

	init();
});
