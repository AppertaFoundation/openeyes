function setCurrentManagementIOP(side)
{
	if(typeof side == 'object'){
		side = side.data.side;
	}

	cmIopElement = $('#OEModule_OphCiExamination_models_Element_OphCiExamination_CurrentManagementPlan_' + side + '_iop');
	targetIop = getTargetIop(side);
	currentIopAvg = getCurrentIopValue(side);

	if (currentIopAvg) {
		setCurrentManagementIOP_FromValue(cmIopElement,targetIop,currentIopAvg);
	} else {
		setCurrentManagementIOP_FromPreviousExamination(side,cmIopElement,targetIop);
	}
}

function setCurrentManagementIOP_FromValue(cmIopElement,targetIop,currentIopAvg)
{
	var display = currentIopAvg + ' mmHg';

	if (targetIop) {
		if (currentIopAvg <= targetIop) {
			display += ' <span class="iop_notification">*** IOP within target ***</span>';
		} else {
			display += ' <span class="iop_notification error">*** IOP above target ***</span>';
		}
	}

	cmIopElement.html(display);
}

function setCurrentManagementIOP_FromPreviousExamination(side,cmIopElement,targetIop)
{
	$.ajax({
		'type': 'GET',
		'url': baseUrl+'/OphCiExamination/default/getPreviousIOPAverage?patient_id=' + OE_patient_id + '&side=' + side,
		'success': function(value) {
			if (value) {
				setCurrentManagementIOP_FromValue(cmIopElement,targetIop,value);
			}
		}
	});
}

function getTargetIop(side)
{
	targetEl = $('#OEModule_OphCiExamination_models_Element_OphCiExamination_OverallManagementPlan_' + side + '_target_iop_id');

	if (targetEl.length != 0) {
		if (targetEl.text() > 0) {
			return parseInt(targetEl.text());
		}
		else if (targetEl.val() != '') {
			return parseInt(targetEl.val());
		}
	}

	return null;
}

function getCurrentIopValue(side)
{
	if (side =='right' && typeof view_iop_right != 'undefined') {
		return parseInt(view_iop_right);
	} else if (side =='left' && typeof view_iop_left != 'undefined') {
		return parseInt(view_iop_left);
	}
	var result = false;
	sum = 0;
	readings = $('#OEModule_OphCiExamination_models_Element_OphCiExamination_IntraocularPressure_readings_' + side + ' td:nth-child(2) select option:selected');

	for (var i = 0; i < readings.length; i++) {
		reading = parseInt($(readings[i]).text() );
		sum = sum + reading;
	}
	if (sum != 0) {
		result = Math.round(sum/readings.length);
	}

	return result;
}

$(document).ready(function() {
	$('.event.edit').on('change click', [
		'#OEModule_OphCiExamination_models_Element_OphCiExamination_OverallManagementPlan_left_target_iop_id',
		'#OEModule_OphCiExamination_models_Element_OphCiExamination_OverallManagementPlan_right_target_iop_id',
		'.OEModule_OphCiExamination_models_Element_OphCiExamination_IntraocularPressure',
		'#OEModule_OphCiExamination_models_Element_OphCiExamination_IntraocularPressure_readings_right button.delete',
		'#OEModule_OphCiExamination_models_Element_OphCiExamination_IntraocularPressure_readings_left button.delete'
	].join(','), function() {
		setCurrentManagementIOP('left');
		setCurrentManagementIOP('right');
	});

	setCurrentManagementIOP('left');
	setCurrentManagementIOP('right');

	$("#OEModule_OphCiExamination_models_Element_OphCiExamination_IntraocularPressure_readings_right").click({side: "right"}, setCurrentManagementIOP);
	$("#OEModule_OphCiExamination_models_Element_OphCiExamination_IntraocularPressure_readings_left").click({side: "left"}, setCurrentManagementIOP);
});
