
function Reports_AddDiagnosis(disorder_id, name) {
	$('#Reports_diagnoses').append('<tr><td>'+name+'</td><td><input type="checkbox" class="principalCheckbox" name="principal[]" value="'+disorder_id+'" /></td><td><a href="#" class="small removeDiagnosis" rel="'+disorder_id+'"><strong>Remove</strong></a></td></tr>');
	$('#selected_diagnoses').append('<input type="hidden" name="secondary[]" value="'+disorder_id+'" />');
}

$(document).ready(function() {
	$('a.removeDiagnosis').die('click').live('click',function() {
		var disorder_id = $(this).attr('rel');

		$('#selected_diagnoses').children('input').map(function() {
			if ($(this).val() == disorder_id) {
				$(this).remove();
			}
		});

		$(this).parent().parent().remove();

		$.ajax({
			'type': 'GET',
			'url': baseUrl+'/disorder/iscommonophthalmic/'+disorder_id,
			'success': function(html) {
				if (html.length >0) {
					$('#DiagnosisSelection_disorder_id').append(html);
					sort_selectbox($('#DiagnosisSelection_disorder_id'));
				}
			}
		});

		return false;
	});

	handleButton($('#diagnoses_report'),function(e) {
		$('div.reportSummary').hide();

		$.ajax({
			'type': 'POST',
			'data': $('#report-diagnoses').serialize() + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
			'dataType': 'json',
			'url': baseUrl+'/report/validateDiagnoses',
			'success': function(errors) {
				if (errors.length == 0) {
					$('.errors').hide();
					$.ajax({
						'type': 'POST',
						'data': $('#report-diagnoses').serialize() + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
						'url': baseUrl+'/report/diagnoses',
						'success': function(html) {
							enableButtons();
							$('div.reportSummary').html(html).show();
						}
					});
				} else {
					$('.errors').children('ul').html('');

					for (var i in errors) {
						$('.errors').children('ul').append('<li>' + errors[i] + '</li>');
					}

					$('.errors').show();
					enableButtons();
				}
			}
		});

		e.preventDefault();
	});

	$('#diagnoses_report_download').die('click').live('click',function(e) {
		e.preventDefault();

		$('#current_report').submit();
	});

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
			'url': baseUrl+'/report/validateLetters',
			'success': function(errors) {
				if (errors.length == 0) {
					$('.errors').hide();
					$.ajax({
						'type': 'POST',
						'data': $('#report-letters').serialize() + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
						'url': baseUrl+'/report/letters',
						'success': function(html) {
							enableButtons();
							$('div.reportSummary').html(html).show();
						}
					});
				} else {
					$('.errors').children('ul').html('');

					for (var i in errors) {
						$('.errors').children('ul').append('<li>' + errors[i] + '</li>');
					}

					$('.errors').show();
					enableButtons();
				}
			}
		});

		e.preventDefault();
	});
});
