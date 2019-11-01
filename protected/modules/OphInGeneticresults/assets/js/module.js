/* Module-specific javascript can be placed here */

$(document).ready(function () {

    $('#search_genetics-patient-disorder-id_0').keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });

  $(this).on('click','#et_cancel',function(e) {
        if (m = window.location.href.match(/\/update\/[0-9]+/)) {
          window.location.href = window.location.href.replace('/update/', '/view/');
        } else {
          window.location.href = baseUrl + '/patient/summary/' + et_patient_id;
        }
        e.preventDefault();
      });

  $(this).on('click','#et_canceldelete',function(e) {
        if (m = window.location.href.match(/\/delete\/[0-9]+/)) {
          window.location.href = window.location.href.replace('/delete/', '/view/');
        } else {
          window.location.href = baseUrl + '/patient/summary/' + et_patient_id;
        }
        e.preventDefault();
      });

      $('select.populate_textarea').unbind('change').change(function () {
        if ($(this).val() != '') {
          var cLass = $(this).parent().parent().parent().attr('class').match(/Element.*/);
          var el = $('#' + cLass + '_' + $(this).attr('id'));
          var currentText = el.text();
          var newText = $(this).children('option:selected').text();

          if (currentText.length == 0) {
            el.text(ucfirst(newText));
          } else {
            el.text(currentText + ', ' + newText);
          }
        }
      });

      $('#search_tests').click(function (e) {
        e.preventDefault();
        window.location.href = baseUrl + '/OphInGeneticresults/search/geneticResults?' + 'genetics-patient-disorder-id=' + $('#savedDiagnosis').val() + '&genetics-pedigree-id=' + $('#genetics-pedigree-id').val() +'&genetics-patient-id='+ $('#genetics-patient-id').val() + '&gene-id=' + $('#gene-id').val() + '&method-id=' + $('#method-id').val() + '&homo=' + $('#homo').val() + '&effect-id=' + $('#effect-id').val() + '&date-from=' + $('#date-from').val() + '&date-to=' + $('#date-to').val() + '&query=' + $('#query').val() + '&search=search';
      });


      $('#genetics_result').on('click', 'tr.clickable', function(){
          window.location.href = baseUrl + $(this).data('uri');
      });

});

function ucfirst(str) {
  str += '';
  var f = str.charAt(0).toUpperCase();
  return f + str.substr(1);
}

function eDparameterListener(_drawing) {
  if (_drawing.selectedDoodle != null) {
    // handle event
  }
}
