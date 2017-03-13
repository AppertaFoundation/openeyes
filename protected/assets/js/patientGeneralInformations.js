
$(document).ready(function() {
	var patientGeneralInfoToolTip = new OpenEyes.UI.Tooltip({
	    className: 'patient_general_information_tooltip tooltip',
		offset: {
			x: 16,
			y: 16
		},
		viewPortOffset: {
			x: 0,
			y: 32 // height of sticky footer
		}
	});
	
	var patient_info_content = $('#patient_general_informations').html();
	patientGeneralInfoToolTip.setContent(patient_info_content);
    
    $('.icon-patient-panel-info').on('mouseover', function() {
    	var offsets = $(this).offset();
        patientGeneralInfoToolTip.show(offsets.left, offsets.top);
    }).mouseout(function (e) {
		patientGeneralInfoToolTip.hide();
	}); 
});
