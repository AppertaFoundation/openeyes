$(document).ready(function () {

  function setStatus($container, pedigreeText, status) {

    $('select[name="GeneticsPatient[pedigrees_through][' + pedigreeText + '][status_id]"]').find('option:contains(' + status + ')').prop('selected', 'selected');
  }

  $('#GeneticsPatient_pedigrees').on('MultiSelectChanged', function () {

    var $container = $(this).parents('.multi-select'),
        pedigreeText = $(this.options[this.selectedIndex]).val();

    setStatus($container, pedigreeText, 'Unknown');

  });

});