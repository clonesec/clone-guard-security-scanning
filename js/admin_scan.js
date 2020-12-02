jQuery(document).ready(function($) {
    var scans_list_page = false;
    var generate = {};
    var $spinner;
    var $td;

    var generate_check_delay = 20000;
    var scans_list_page_delay = 30000;

	function checkGenerate() {
        var action = 'cgss_report_generate_check';
        var msg = '';

        generate.action = action;

		$.ajax({
			data: generate,
			dataType: 'json',
			method: 'POST',
			url: ajaxurl,
			success: function(data) {
                if(data.status && data.status == 'success' && data.still_processing) {
                    setTimeout(checkGenerate, generate_check_delay);
                } else if(data.status && data.status == 'success') {
                    $td.addClass('generated');
                    $td.removeClass('ungenerated');
                    $spinner.removeClass('is-active');
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
            },
			error: function(jqXHR, textStatus, error) {
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
		});
        return false;
    }

	function clickDelete() {
        if(!confirm('Are you sure you want to delete this item?')) {
            return false;
        }
        var action = $(this).attr('data-action');
        var id = $(this).attr('data-id');
        var data = {};
        var nonce = $(this).attr('data-nonce');
        var $form = $(this).closest('form');

        var msg = '';

        // Show the spinner.
        var $spinner = $(this).next();
        $spinner.addClass('is-active');

        data._wpnonce = nonce;
        data.action = action;
        data.id = id;

		$.ajax({
			data: data,
			dataType: 'json',
			method: 'POST',
			url: ajaxurl,
			success: function(data) {
                if(data.status && data.status == 'success' && data.reload) {
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
            },
			error: function(jqXHR, textStatus, error) {
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
		});
        return false;
    }

	function clickDownload() {
        var action = $(this).closest('[data-action]').attr('data-action');
        var id = $(this).closest('[data-id]').attr('data-id');
        var type = $(this).closest('[data-type]').attr('data-type');
        var data = {};
        var nonce = $(this).closest('[data-nonce]').attr('data-nonce');

        var url = ajaxurl;
        url += '?_wpnonce=' + nonce;
        url += '&action=' + action;
        url += '&id=' + id;
        url += '&type=' + type;

        window.location.href = url;
        return false;
    }

	function clickGenerate() {
        var action = 'cgss_report_generate';
        var id = $(this).closest('[data-id]').attr('data-id');
        var type = $(this).closest('[data-type]').attr('data-type');
        var data = {};
        var nonce = $(this).closest('[data-nonce]').attr('data-nonce');

        var msg = '';

        // Save the td.
        $td = $(this).closest('td');

        // Show the spinner.
        $spinner = $(this).closest('td').find('.spinner');
        $spinner.addClass('is-active');

        generate._wpnonce = nonce;
        generate.action = action;
        generate.id = id;
        generate.type = type;

		$.ajax({
			data: generate,
			dataType: 'json',
			method: 'POST',
			url: ajaxurl,
			success: function(data) {
                if(data.status && data.status == 'success') {
                    setTimeout(checkGenerate, generate_check_delay);
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
            },
			error: function(jqXHR, textStatus, error) {
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
		});
        return false;
    }

	function clickReturn() {
        var action = $(this).attr('data-action');
        var data = {};
        var href = $(this).attr('href');
        var nonce = $(this).attr('data-nonce');
        var $form = $(this).closest('form');

        // Show the spinner.
        $(this).next().addClass('is-active');

        data._wpnonce = nonce;
        data.action = action;
        data.key = $form.find('[name=key]').val();
        data.name = $form.find('[name=name]').val();
        data.schedule = $form.find('[name=schedule]').val();
        data.target = $form.find('[name=target]').val();
        data.notifications = '';
        $form.find('[name="notifications[]"]').each(function() {
            if($(this).is(':checked')) {
                if(data.notifications) {
                    data.notifications += ',' + $(this).val();
                } else {
                    data.notifications = $(this).val();
                }
            }
        });
        data.comment = $form.find('[name=comment]').val();


        // Whether it is successful or not, proceed to the next page.
        // This is just an attempt to save state but is not required.
		$.ajax({
			data: data,
			dataType: 'json',
			method: 'POST',
			url: ajaxurl,
			success: function() {
                window.location.href = href;
            },
			error: function() {
                window.location.href = href;
            }
		});
        return false;
    }

	function clickStart() {
        var $td = $(this).closest('td');
        var action = 'cgss_scan_start';
        var id = $td.attr('data-id');
        var data = {};
        var nonce = $td.attr('data-nonce');

        data._wpnonce = nonce;
        data.action = action;
        data.id = id;

        $td.attr('class', 'basic_processing');

		$.ajax({
			data: data,
			dataType: 'json',
			method: 'POST',
			url: ajaxurl,
			success: function(data) {
                if(data.status && data.status == 'success') {
                    // Do nothing on success
                } else {
                    alert('There was a problem processing the request.');
                    $td.attr('class', 'scheduled');
                }
            },
			error: function(jqXHR, textStatus, error) {
                alert('There was a problem processing the request.');
                $td.attr('class', 'scheduled');
            }
		});
        return false;
    }

	function clickStartScheduled() {
        var $td = $(this).closest('td');
        var action = 'cgss_scan_scheduled_start';
        var id = $td.attr('data-id');
        var data = {};
        var nonce = $td.attr('data-nonce');

        data._wpnonce = nonce;
        data.action = action;
        data.id = id;

        $td.attr('class', 'scheduled_processing');

		$.ajax({
			data: data,
			dataType: 'json',
			method: 'POST',
			url: ajaxurl,
			success: function(data) {
                if(data.status && data.status == 'success') {
                    // Do nothing on success
                } else {
                    alert('There was a problem processing the request.');
                    $td.attr('class', 'scheduled');
                }
            },
			error: function(jqXHR, textStatus, error) {
                alert('There was a problem processing the request.');
                $td.attr('class', 'scheduled');
            }
		});
        return false;
    }

	function clickStop() {
        var $td = $(this).closest('td');
        var action = 'cgss_scan_stop';
        var id = $td.attr('data-id');
        var data = {};
        var nonce = $td.attr('data-nonce');

        data._wpnonce = nonce;
        data.action = action;
        data.id = id;

        $td.attr('class', 'basic');

		$.ajax({
			data: data,
			dataType: 'json',
			method: 'POST',
			url: ajaxurl,
			success: function(data) {
                if(data.status && data.status == 'success') {
                    // Do nothing on success
                } else {
                    alert('There was a problem processing the request.');
                    $td.attr('class', 'scheduled');
                }
            },
			error: function(jqXHR, textStatus, error) {
                alert('There was a problem processing the request.');
                $td.attr('class', 'scheduled');
            }
		});
        return false;
    }

	function clickStopScheduled() {
        var $td = $(this).closest('td');
        var action = 'cgss_scan_stop';
        var id = $td.attr('data-id');
        var data = {};
        var nonce = $td.attr('data-nonce');

        data._wpnonce = nonce;
        data.action = action;
        data.id = id;

        $td.attr('class', 'scheduled');

		$.ajax({
			data: data,
			dataType: 'json',
			method: 'POST',
			url: ajaxurl,
			success: function(data) {
                if(data.status && data.status == 'success') {
                    // Do nothing on success
                } else {
                    alert('There was a problem processing the request.');
                    $td.attr('class', 'scheduled');
                }
            },
			error: function(jqXHR, textStatus, error) {
                alert('There was a problem processing the request.');
                $td.attr('class', 'scheduled');
            }
		});
        return false;
    }

    // https://stackoverflow.com/a/901144
    // var user_id = getParameterByName('user_id');
    function getParameterByName(name, url) {
            if (!url) url = window.location.href;
            name = name.replace(/[\[\]]/g, '\\$&');
            var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
                    results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }

	function reloadScans() {
        var data = {};
        data.action = 'cgss_scans_reload';
        data.paged = getParameterByName('paged');
		$.ajax({
			data: data,
			dataType: 'html',
			method: 'GET',
			url: ajaxurl,
			success: function(htm) {
                $('.scans_wrap').replaceWith(htm);
                setTimeout(reloadScans, scans_list_page_delay);
            },
			error: function(jqXHR, textStatus, error) {
                setTimeout(reloadScans, scans_list_page_delay);
            }
		});

    }

    // Initialize listeners and set up page. 
	function init() {
        $('.datetimepicker').datetimepicker({
            format: 'Y-m-d H:i:s'
        });

        $('.select2').select2();

        $(document).on('click', '.delete', clickDelete);
        $(document).on('click', '.return', clickReturn);

        $(document).on('click', '.download', clickDownload);
        $(document).on('click', '.generate', clickGenerate);
        $(document).on('click', '.regenerate', clickGenerate);

        $(document).on('click', '.basic .action_play', clickStart);
        $(document).on('click', '.scheduled .action_play', clickStartScheduled);
        $(document).on('click', '.basic_processing .action_stop', clickStop);
        $(document).on('click', '.scheduled_processing .action_stop', clickStopScheduled);

        if($('.scans_wrap').length) {
            scans_list_page = true;
            // Reload every 30 seconds
            setTimeout(reloadScans, scans_list_page_delay);
        }
	}

    // Get everything started.
	init();
});
