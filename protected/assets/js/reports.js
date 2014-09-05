
$(document).ready(function() {
	handleButton($('#run-report'),function(e) {
		$('div.reportSummary').hide();

		$.ajax({
			'type': 'POST',
			'data': $('#report-form').serialize() + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
			'dataType': 'json',
			'url': baseUrl + '/report/runReport',
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

		e.preventDefault();
	});

	$('#download-report').die('click').live('click',function(e) {
		e.preventDefault();

		$('#current_report').submit();
	});
});
