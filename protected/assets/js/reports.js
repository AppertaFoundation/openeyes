
$(document).ready(function() {
	handleButton($('.display-report'),function(e) {
		e.preventDefault();
		display_report('report-form');
	});

	handleButton($('.download-report'),function(e) {
		e.preventDefault();
		download_report('report-form');
	});

	handleButton($('.display-module-report'),function(e) {
		e.preventDefault();
		display_report('module-report-form');
	});

	handleButton($('.download-module-report'),function(e) {
		e.preventDefault();
		download_report('module-report-form');
	});
  pickmeup(".start-date", {
    format: "d-m-Y",
    hide_on_select: true,
    default_date: false,
		max: new Date(),
  });
  pickmeup(".end-date", {
    format: "d-m-Y",
    hide_on_select: true,
    default_date: false,
		max: new Date(),
  });
});

function display_report(form)
{
	$('div.js-report-summary').hide();

	$('.errors').hide();

	$.ajax({
		'type': 'POST',
		'data': $('#'+form).serialize() + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
		'dataType': 'json',
		'url': $('#'+form).attr('action').replace(/downloadReport/,'runReport'),
		'success': function(errors) {
			if (typeof(errors['_report']) != 'undefined') {
				enableButtons();
				$('div.js-report-summary').html(errors['_report']).show();
			} else {
				$('.errors').children('ul').html('');

				for (var i in errors) {
					$('.errors').children('ul').append('<li>' + errors[i][0] + '</li>');
				}

				$('.errors').show();
				enableButtons();
			}
		},
		'error': function(a,b,c) {
			enableButtons();
		}
	});
}

function download_report(form)
{
	$('.errors').hide();

	$.ajax({
		'type': 'POST',
		'data': $('#'+form).serialize() + "&validate_only=1" + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
		'dataType': 'json',
		'url': $('#'+form).attr('action'),
		'success': function(errors) {
			if (errors.length == 0) {
				enableButtons();
				$('#'+form).submit();
			} else {
				$('.errors').children('ul').html('');

				for (var i in errors) {
					$('.errors').children('ul').append('<li>' + errors[i][0] + '</li>');
				}

				$('.errors').show();
				enableButtons();
			}
		},
		'error': function(a,b,c) {
			enableButtons();
		}
	});
}
