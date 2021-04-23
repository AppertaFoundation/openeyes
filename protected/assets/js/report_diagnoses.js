
function Reports_AddDiagnosis(disorder_id, name) {
	$('#Reports_diagnoses').append('<tr>' +
		'<td>'+name+'</td>' +
		'<td><input type="checkbox" class="principalCheckbox" name="principal[]" value="'+disorder_id+'" />' +
		'</td>' +
		'<td>' +
		'<a href="#" class="small removeDiagnosis" rel="'+disorder_id+'"><i class="oe-i trash"></i></a>' +
		'</td></tr>');
	$('#selected_diagnoses').append('<input type="hidden" name="all[]" value="'+disorder_id+'" />');
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
});
