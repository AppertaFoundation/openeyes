
function callbackAddProcedure(procedure_id) {
	$.ajax({
		'type': 'GET',
		'url': '/OphTrOperationnote/Default/loadElementByProcedure?procedure_id='+procedure_id,
		'success': function(html) {
			if (html.length >0) {
				var m = html.match(/<div class="(Element.*?)"/)
				if ($('div.'+m[1]).map().length <1) {
					$('div.elements').append(html);
					$('div.'+m[1]).slideToggle('fast');
				}
			}
		}
	});
}

/*
 * Post the removed operation_id and an array of ElementType class names currently in the DOM
 * This should return any ElementType classes that we should remove.
 */

function callbackRemoveProcedure(procedure_id) {
	var procedures = '';

	$('div.procedureItem').children('input[type="hidden"]').map(function() {
		if (procedures.length >0) {
			procedures += ',';
		}
		procedures += $(this).val();
	});

	$.ajax({
		'type': 'POST',
		'url': '/OphTrOperationnote/Default/getElementsToDelete',
		'data': "remaining_procedures="+procedures+"&procedure_id="+procedure_id,
		'dataType': 'json',
		'success': function(data) {
			$.each(data, function(key, val) {
				$('div.'+val).slideToggle('fast',function() {
					$('div.'+val).remove();
				});
			});
		}
	});
}

/* If this function doesn't exist eyedraw click events default and cause bad behaviour */

function eDparameterListener(_drawing) {
	if (_drawing.selectedDoodle != null) {
		if (_drawing.selectedDoodle != null && _drawing.selectedDoodle.className == 'PhakoIncision') {
			setCataractSelectInput('incision_site',_drawing.selectedDoodle.getParameter('incisionSite'));
			setCataractSelectInput('incision_type',_drawing.selectedDoodle.getParameter('incisionType'));
			setCataractInput('length',_drawing.selectedDoodle.getParameter('incisionLength'));
			setCataractInput('meridian',_drawing.selectedDoodle.getParameter('incisionMeridian'));
		}
	}
}

function setCataractSelectInput(key, value) {
	$('#ElementCataract_'+key+'_id').children('option').map(function() {
		if ($(this).text() == value) {
			$('#ElementCataract_'+key+'_id').val($(this).val());
		}
	});
}

function setCataractInput(key, value) {
	$('#ElementCataract_'+key).val(value);
}

$(document).ready(function() {
	$("button[id$='_generate_report']").unbind('click').click(function(e) {
		e.preventDefault();

		var buttonClass = $(this).attr('id').replace(/_generate_report$/,'');
		var eyeDrawName = buttonClass.replace(/Element/,'');

		var text = window["ed_drawing_edit_R"+eyeDrawName].report().replace(/, +$/, '');

		if ($('#'+buttonClass+'_report').text().length >0) {
			text += ', '+text;
		}

		$('#'+buttonClass+'_report').text($('#'+buttonClass+'_report').text() + text);

		return false;
	});

	$('#ElementCataract_incision_site_id').unbind('change').change(function(e) {
		e.preventDefault();

		ed_drawing_edit_RCataract.setParameterForDoodleOfClass('PhakoIncision', 'incisionSite', $(this).children('option:selected').text());

		return false;
	});
});
