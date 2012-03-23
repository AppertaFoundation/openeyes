$(document).ready(function() {
	$('input[name=schedule_timeframe1]').change(function() {
		var select = $('input[name=schedule_timeframe1]:checked').val();

		if (select == 1) {
			$('select[name=schedule_timeframe2]').attr('disabled', false);
		} else {
			$('select[name=schedule_timeframe2]').attr('disabled', true);
		}
	});

	$('input[name="ElementOperation[eye_id]"]').click(function() {
		updateTotalDuration();
		if ($('input[name="Procedures[]"]').length == 0) {
			$('input[id="autocomplete_procedure_id"]').focus();
		}
	});
});
