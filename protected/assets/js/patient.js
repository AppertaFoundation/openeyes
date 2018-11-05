function addItem(wrapper_id, ui) {
  var $wrapper = $('#' + wrapper_id);

  $wrapper.find('.js-name').text(ui.item.label);
  $wrapper.show();
  $wrapper.find('.hidden_id').val(ui.item.value);
}

function addReferredToItem(wrapper_id, ui){
  var $wrapper = $('#' + wrapper_id);
  $wrapper.find('.js-name').text(ui.item.label);
  $wrapper.show();
  $wrapper.find('.hidden_id').val(ui.item.id);
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
  $('.selected_referred_to span.name').text('');
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

});