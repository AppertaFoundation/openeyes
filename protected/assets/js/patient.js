// change the function name from addItem to addItemP
function addItemPatientForm(wrapper_id, ui) {
  var $wrapper = $('#' + wrapper_id);
  $wrapper.find('.js-name').text(ui.item.label);
  $wrapper.show();
  $wrapper.find('.hidden_id').val(ui.item.value);
}

function removeSelectedGP() {
  $('#no_gp_result').hide();
  $('.js-selected_gp .js-name').text('');
  $('#selected_gp_wrapper').hide();
  $('#Patient_gp_id').val('');
}

function removeSelectedPractice() {
  $('#no_practice_result').hide();
  $('.js-selected_practice .js-name').text('');
  $('#selected_practice_wrapper').hide();
  $('#Patient_practice_id').val('');

}

function removeSelectedReferredto(){
  $('#no_referred_to_result').hide();
  $('.js-selected_referral_to .js-name').text('');
  $('#selected_referred_to_wrapper').hide();
  $('#PatientUserReferral_user_id').val('-1');

}

$(function () {

  pickmeup("#Patient_dob", {
    format: "d/m/Y",
    hide_on_select: true,
    default_date: false,
    max: new Date(),
  });
  pickmeup("#Patient_date_of_death", {
    format: "d/m/Y",
    hide_on_select: true,
    default_date: false,
    max: new Date(),
  });

  $('#patient-form').on('keyup', '#Patient_nhs_num', function () {
    var selector = $(this).data('child_row');
    $(selector).toggle($(this).val().length > 0);
  });

  var submitted = false;
  $(document).ready(function () {
    $("#patient-form").on('submit', function (e) {
        if (!submitted) {
          $('#patient-form-submit-button').attr('disabled', true);
          $('#patient-form-submit-button').addClass('disabled');
          $('#form-submit-loader').show();
          submitted = true;
        } else {
          e.preventDefault();
        }
      }
    );
  });

  $('#Patient_is_deceased').on('change', function () {
    var selector = $(this).data('child_row');

    $(selector).find('input').val('');
    $(selector).toggle($(this).is(':checked'));
  });

  $('#selected_gp_wrapper').on('click', '.js-remove-gp', function () {
    removeSelectedGP();
  });

  $('#selected_practice_wrapper').on('click', '.js-remove-practice', function () {
    removeSelectedPractice();
  });

  $('#selected_referred_to_wrapper').on('click', '.js-remove-referral-to', function(){
    removeSelectedReferredto();
  });

});

function addGpItem(wrapper_id, ui){
    var $wrapper = $('#' + wrapper_id);
    var JsonObj = JSON.parse(ui);
    $wrapper.find('span.js-name').text(JsonObj.label);
    $wrapper.show();
    $wrapper.find('.hidden_id').val(JsonObj.id);
}

$(document).ready(function ()
{
  highLightError("Patient_gp_id_em_","Practitioner cannot be blank",'#autocomplete_gp_id');
  highLightError("Patient_practice_id_em_","Practice cannot be blank",'#autocomplete_practice_id');
});

function highLightError(elementId, containText,highLightFiled){
  if(document.getElementById(elementId).innerHTML.includes(containText)){
    $(highLightFiled).addClass("error");
  }
}