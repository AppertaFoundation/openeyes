$(document).ready(function() {
    $('#OEModule_OphCiExamination_models_SocialHistory_occupation_id').on('change', function() {
        if ($('#OEModule_OphCiExamination_models_SocialHistory_occupation_id option:selected').text() == 'Other (specify)') {
            $('#div_OEModule_OphCiExamination_models_SocialHistory_type_of_job').show();
        } else {
            $('#div_OEModule_OphCiExamination_models_SocialHistory_type_of_job').hide();
            $('#OEModule_OphCiExamination_models_SocialHistory_type_of_job').val('');
        }
    });
});
