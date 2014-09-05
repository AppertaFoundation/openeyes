
$(document).ready(function() {
	$('#add_letter_phrase').click(function(e) {
		e.preventDefault();

		$('.phraseList').append('<div><input type="text" name="phrases[]" value="" /> <a href="#" class="removePhrase">remove</a></div>');
		$('.phraseList').find('input[name="phrases[]"]:last').focus();
	});

	$('.removePhrase').die('click').live('click',function(e) {
		e.preventDefault();

		$(this).closest('div').remove();
	});

	handleButton($('#letters_report'),function(e) {
		$('div.reportSummary').hide();

		$.ajax({
			'type': 'POST',
			'data': $('#report-letters').serialize() + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
			'dataType': 'json',
			'url': baseUrl+'/report/letters',
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

	$('#letters_report_download').die('click').live('click',function(e) {
		e.preventDefault();

		$('#current_report').submit();
	});
});
