
function callbackAddProcedure(procedure_id) {
	var eye = ($('input[name="ElementProcedureList\[eye_id\]"]:checked').val() == 2) ? 'R' : 'L';

	$.ajax({
		'type': 'GET',
		'url': '/OphTrOperationnote/Default/loadElementByProcedure?procedure_id='+procedure_id+'&eye='+eye,
		'success': function(html) {
			if (html.length >0) {
				var m = html.match(/<div class="(Element.*?)"/);
				if (m) {
					m[1] = m[1].replace(/ ondemand$/,'');

					if ($('div.'+m[1]).length <1) {
						$('#procedureSpecificElements').append(html);
						$('div.'+m[1]).slideToggle('fast');
					}
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

		var text = window["ed_drawing_edit_"+eyeDrawName].report().replace(/, +$/, '');

		if ($('#'+buttonClass+'_report').text().length >0) {
			text += ', '+text;
		}

		$('#'+buttonClass+'_report').text($('#'+buttonClass+'_report').text() + text);

		return false;
	});

	$('#ElementCataract_incision_site_id').unbind('change').change(function(e) {
		e.preventDefault();

		ed_drawing_edit_Cataract.setParameterForDoodleOfClass('PhakoIncision', 'incisionSite', $(this).children('option:selected').text());

		return false;
	});

	$('#ElementCataract_incision_type_id').unbind('change').change(function(e) {
		e.preventDefault();

		ed_drawing_edit_Cataract.setParameterForDoodleOfClass('PhakoIncision', 'incisionType', $(this).children('option:selected').text());

		return false;
	});

	$('input[name="ElementProcedureList\[eye_id\]"]').unbind('change').change(function() {

		if ($('#typeProcedure').is(':hidden')) {
			$('#typeProcedure').slideToggle('fast');
		}

		if (window.ed_drawing_edit_Cataract !== undefined) {
			if ($(this).val() == 2) {
				if (parseInt(ed_drawing_edit_Cataract.doodleArray[3].rotation * (180/Math.PI)) == 90) {
					et_operationnote_hookDoodle = ed_drawing_edit_Cataract.doodleArray[3];
					et_operationnote_hookTarget = -90;
					et_operationnote_hookDirection = 1;
					opnote_move_eyedraw_element_to_position();
				}
			} else if ($(this).val() == 1) {
				if (parseInt(ed_drawing_edit_Cataract.doodleArray[3].rotation * (180/Math.PI)) == -90) {
					et_operationnote_hookDoodle = ed_drawing_edit_Cataract.doodleArray[3];
					et_operationnote_hookTarget = 90;
					et_operationnote_hookDirection = 0;
					opnote_move_eyedraw_element_to_position();
				}
			}
		}
	});

	$('input[name="ElementAnaesthetic\[anaesthetic_type_id\]"]').unbind('click').click(function() {
		if ($(this).val() == 5) {
			if (!$('#anaesthetist_id').is(':hidden')) {
				$('#anaesthetist_id').slideToggle('fast');
				$('#anaesthetic_delivery_id').slideToggle('fast');
				$('#anaesthetic_delivery_id').next().slideToggle('fast');
			}
		} else {
			if ($('#anaesthetist_id').is(':hidden')) {
				$('#anaesthetist_id').slideToggle('fast');
				$('#anaesthetic_delivery_id').slideToggle('fast');
				$('#anaesthetic_delivery_id').next().slideToggle('fast');
			}
		}
	});
});

var et_operationnote_hookDoodle = null;
var et_operationnote_hookTarget = 0;
var et_operationnote_hookDirection = 0;

function opnote_move_eyedraw_element_to_position() {
	var target = et_operationnote_hookTarget;
	var doodle = et_operationnote_hookDoodle;
	var pos = parseInt(doodle.rotation * (180/Math.PI));

	if (et_operationnote_hookDirection == 0) {
		if (pos < target) {
			pos += 10;
			if (pos > target) {
				doodle.rotation = target * (Math.PI/180);
				ed_drawing_edit_Cataract.repaint();
				ed_drawing_edit_Cataract.modified = false;
			} else {
				doodle.rotation = pos * (Math.PI/180);
				ed_drawing_edit_Cataract.repaint();
				setTimeout('opnote_move_eyedraw_element_to_position();', 20);
			}
		}
	} else {
		if (pos > target) {
			pos -= 10;
			if (pos < target) {
				doodle.rotation = target * (Math.PI/180);
				ed_drawing_edit_Cataract.repaint();
				ed_drawing_edit_Cataract.modified = false;
			} else {
				doodle.rotation = pos * (Math.PI/180);
				ed_drawing_edit_Cataract.repaint();
				setTimeout('opnote_move_eyedraw_element_to_position();', 20);
			}
		}
	}
}
