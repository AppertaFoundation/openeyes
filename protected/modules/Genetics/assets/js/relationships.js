$(document).ready(function () {
  OpenEyes.Genetics.Relationships.init(geneticsRelationships, []);
  var search = OpenEyes.UI.Search.init($('#genetics_patient_lookup'));


    OpenEyes.UI.Search.setSourceURL('/Genetics/subject/patientSearch');

    search.getElement().autocomplete('option', 'select', function (event, uid) {
        $('#relationships_list').append(OpenEyes.Genetics.Relationships.newRelationshipForm(uid.item));
    });


  search.getElement().autocomplete('option', 'select', function (event, uid) {
    $('#relationships_list').append(OpenEyes.Genetics.Relationships.newRelationshipForm(uid.item));
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
});