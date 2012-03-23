	var removed_stack = [];

	$(document).ready(function() {
		$('select[name=select_procedure_id]').children().map(function() {
			removed_stack[$(this).val()] = $(this).text();
		});
	});

	$(function() {
		$('input[id=autocomplete_procedure_id]').watermark('type the first few characters of a procedure');
		$('input[name=schedule_timeframe1]').change(function() {
			var select = $('input[name=schedule_timeframe1]:checked').val();

			if (select == 1) {
				$('select[name=schedule_timeframe2]').attr('disabled', false);
			} else {
				$('select[name=schedule_timeframe2]').attr('disabled', true);
			}
		});

	$('input[name="ElementOperation[eye]"]').click(function() {
		updateTotalDuration();
		if ($('input[name="Procedures[]"]').length == 0) {
			$('input[id="autocomplete_procedure_id"]').focus();
		}
	});
