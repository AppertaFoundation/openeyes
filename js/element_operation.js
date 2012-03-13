	var removed_stack = [];

	$(document).ready(function() {
		$('select[name=select_procedure_id]').children().map(function() {
			removed_stack[$(this).val()] = $(this).text();
		});
	});

	$(function() {
		$('input[id=autocomplete_procedure_id]').watermark('type the first few characters of a procedure');
		$("#procedure_list tbody").sortable({
			 helper: function(e, tr)
			 {
				 var $originals = tr.children();
				 var $helper = tr.clone();
				 $helper.children().each(function(index)
				 {
					 // Set helper cell sizes to match the original sizes
					 $(this).width($originals.eq(index).width())
				 });
				 return $helper;
			 }
		}).disableSelection();
		$('input[name=schedule_timeframe1]').change(function() {
			var select = $('input[name=schedule_timeframe1]:checked').val();

			if (select == 1) {
				$('select[name=schedule_timeframe2]').attr('disabled', false);
			} else {
				$('select[name=schedule_timeframe2]').attr('disabled', true);
			}
		});

		$('select[id=subsection_id]').change(function() {
			var subsection = $('select[name=subsection_id] option:selected').val();
			if (subsection != 'Select a subsection') {
				var existingProcedures = [];
				$('#procedure_list tbody').children().each(function () {
					var text = $(this).children('td:first').children('span:first').text();
					existingProcedures.push(text.replace(/ remove$/i, ''));
				});
				$.ajax({
					'url': '//procedure/list',
					'type': 'POST',
					'data': {'subsection': subsection, 'existing': existingProcedures},
					'success': function(data) {
						$('select[name=select_procedure_id]').attr('disabled', false);
						$('select[name=select_procedure_id]').html(data);
						$('select[name=select_procedure_id]').show();
					}
				});
			}
		});

		$('#select_procedure_id').change(function() {
			var procedure = $('select[name=select_procedure_id] option:selected').text();
			if (procedure != 'Select a commonly used procedure') {
				$.ajax({
					'url': '//procedure/details',
					'type': 'GET',
					'data': {'name': procedure},
					'success': function(data) {
						// append selection onto procedure list
						$('#procedure_list tbody').append(data);
						$('#procedureDiv').show();
						$('#procedure_list').show();

						// update total duration
						var totalDuration = 0;
						$('#procedure_list tbody').children().children('td:odd').each(function() {
							duration = Number($(this).text());
							if ($('input[name="ElementOperation[eye]"]:checked').val() == 2) {
								duration = duration * 2;
							}
							totalDuration += duration;
						});
						var thisDuration = Number($('#procedure_list tbody').children().children(':last').text());
						if ($('input[name="ElementOperation[eye]"]:checked').val() == 2) {
							thisDuration = thisDuration * 2;
						}
						var operationDuration = Number($('#ElementOperation_total_duration').val());
						$('#projected_duration').text(totalDuration);
						$('#ElementOperation_total_duration').val(operationDuration + thisDuration);

						// clear out text field
						$('#autocomplete_procedure_id').val('');

						// remove the procedure from the options list
						$('select[name=select_procedure_id] option:selected').remove();

						// disable the dropdown if there are no items left to select
						if ($('select[name=select_procedure_id] option').length == 1) {
							$('select[name=select_procedure_id]').attr('disabled', true);
						}
					}
				});
			}
			return false;
		});
	});
	function removeProcedure(row) {
		edited();

		var option_value = $(row).parent().siblings('input').val();

		var duration = $(row).parent().siblings('td').text();
		if ($('input[name="ElementOperation[eye]"]:checked').val() == 2) {
			duration = duration * 2;
		}
		var projectedDuration = Number($('#projected_duration').text()) - duration;
		var totalDuration = Number($('#ElementOperation_total_duration').val()) - duration;

		if (projectedDuration < 0) {
			projectedDuration = 0;
		}
		if (totalDuration < 0) {
			totalDuration = 0;
		}
		$('#projected_duration').text(projectedDuration);
		$('#ElementOperation_total_duration').val(totalDuration);

		$(row).parents('tr').remove();

		var text = removed_stack[option_value];

		$('select[name=select_procedure_id]').append($('<option>',{text : option_value}).text(text));
		$('select[name=select_procedure_id]').attr('disabled',false);
		sortProcedures();

		return false;
	};

	function sortProcedures() {
		var $dd = $('select[name=select_procedure_id]');

		if ($dd.length > 0) { // make sure we found the select we were looking for

			// save the selected value
			var selectedVal = $dd.val();

			// get the options and loop through them
			var $options = $('option', $dd);
			var arrVals = [];
			$options.each(function(){
					// push each option value and text into an array
					arrVals.push({
							val: $(this).val(),
							text: $(this).text()
					});
			});

			// sort the array by the value (change val to text to sort by text instead)
			arrVals.sort(function(a, b){
				if(a.val>b.val){
					return 1;
				}
				else if (a.val==b.val){
					return 0;
				}
				else {
					return -1;
				}
			});

			// loop through the sorted array and set the text/values to the options
			for (var i = 0, l = arrVals.length; i < l; i++) {
					$($options[i]).val(arrVals[i].val).text(arrVals[i].text);
			}

			// set the selected value back
			$dd.val(selectedVal);
		}
	}

	function updateTotalDuration() {
		// update total duration
		var totalDuration = 0;
		$('#procedure_list tbody').children().children('td:odd').each(function() {
			duration = Number($(this).text());
			totalDuration += duration;
		});
		if ($('input[name=\"ElementOperation[eye]\"]:checked').val() == 2) {
		$('#projected_duration').text(totalDuration + ' * 2');
			totalDuration *= 2;
		}
		$('#projected_duration').text(totalDuration);
		$('#ElementOperation_total_duration').val(totalDuration);
	}

	$('input[name="ElementOperation[eye]"]').click(function() {
		updateTotalDuration();
		if ($('input[name="Procedures[]"]').length == 0) {
			$('input[id="autocomplete_procedure_id"]').focus();
		}
	});

	function edited() {
		$('div.action_options').hide();
		$('div.action_options_alt').show();
	}
