$(document).ready(function () {
    OpenEyes.Genetics.Relationships.init(geneticsRelationships, []);
    var search = OpenEyes.UI.Search.init($('#genetics_patient_lookup'));

    search.setSourceURL('/Genetics/subject/patientSearch');
    search.setLoader('.loader-relation');

    search.getElement().autocomplete('option', 'select', function (event, uid) {
        $('#relationships_list').append(OpenEyes.Genetics.Relationships.newRelationshipForm(uid.item));
        $('#genetics_patient_lookup').blur();
    });

    $('input[name="GeneticsPatient\[patient_lookup_gender\]"]').on('change', function () {
        var genderValue = this.value;
        $('#GeneticsPatient_gender_id option').filter(function () {
            return $(this).html() === genderValue;
        }).prop('selected', true);
    });

    $('input[name="GeneticsPatient\[patient_lookup_deceased\]"]').on('change', function () {
        $('#GeneticsPatient_is_deceased').prop('checked', (this.value === '1'));
    });

    $('#genetics_patient_lookup').on('focus',function(){
        $('.ui-autocomplete.patient-ajax-list').show();
    });
});