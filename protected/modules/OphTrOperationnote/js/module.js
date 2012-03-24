
function callbackAddProcedure(procedure_id) {
	$.ajax({
		'type': 'GET',
		'url': '/OphTrOperationnote/Default/loadElementByProcedure?procedure_id='+procedure_id,
		'success': function(html) {
			if (html.length >0) {
				var m = html.match(/<div class="(Element.*?)"/)
				if ($('div.'+m[1]).map().length <1) {
					$('div.elements').append(html);
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
				$('div.'+val).remove();
			});
		}
	});
}

/* If this function doesn't exist eyedraw click events default and cause bad behaviour */

function eDparameterListener(_drawing) {
	if (_drawing.selectedDoodle != null) {
		//console.log(_drawing.IDSuffix);
	}
}

