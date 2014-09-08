
$(document).ready(function() {
	handleButton($('#run-report'),function(e) {
		e.preventDefault();
		run_report('report-form','report/runReport');
	});

	handleButton($('#run-module-report'),function(e) {
		e.preventDefault();
		run_report('module-report-form',OE_module_name + '/report/runReport');
	});

	$('#download-report').die('click').live('click',function(e) {
		e.preventDefault();
		$('#current_report').submit();
	});
});

function run_report(form, method)
{
	$('div.reportSummary').hide();

	$.ajax({
		'type': 'POST',
		'data': $('#'+form).serialize() + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
		'dataType': 'json',
		'url': baseUrl + '/' + method,
		'success': function(errors) {
			if (typeof(errors['_report']) != 'undefined') {
				enableButtons();
				$('div.reportSummary').html(errors['_report']).show();
			} else {
				$('.errors').children('ul').html('');

				for (var i in errors) {
					$('.errors').children('ul').append('<li>' + errors[i][0] + '</li>');
				}

				$('.errors').show();
				enableButtons();
			}
		}
	});
}
