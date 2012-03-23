
function callbackAddProcedure(procedure_id) {
	$.ajax({
		'type': 'GET',
		'url': '/OphTrOperationnote/Default/loadElementByProcedure?procedure_id='+procedure_id,
		'success': function(html) {
			if (html.length >0) {
				$('div.elements').append(html);
			}
		}
	});
}

function callbackRemoveProcedure(procedure_id) {
	$.ajax({
		'type': 'GET',
		'url': '/OphTrOperationnote/Default/getElementsByProcedure?procedure_id='+procedure_id,
		'dataType': 'json',
		'success': function(data) {
			$.each(data, function(key, val) {
				$('div.'+val).remove();
			});
		}
	});
}
