$(document).ready(function() {
    clinic_inteval = 'OEModule_OphCiExamination_models_Element_OphCiExamination_OverallManagementPlan_clinic_interval_id';
	photo = 'OEModule_OphCiExamination_models_Element_OphCiExamination_OverallManagementPlan_photo_id';
    oct = 'OEModule_OphCiExamination_models_Element_OphCiExamination_OverallManagementPlan_oct_id';
    visual_fields = 'OEModule_OphCiExamination_models_Element_OphCiExamination_OverallManagementPlan_hfa_id';
    hrt = 'OEModule_OphCiExamination_models_Element_OphCiExamination_OverallManagementPlan_hrt_id';

    clinic_inteval_val = function(){ return $('#' + clinic_inteval).find(":selected").val() };

    $('.event.edit').on('change click', [
		'#' + clinic_inteval,
	].join(','), function() {
        $('#' + photo).val(clinic_inteval_val());
        $('#' + oct).val(clinic_inteval_val());
        $('#' + visual_fields).val(clinic_inteval_val());
        $('#' + hrt).val(clinic_inteval_val());
	});
});
