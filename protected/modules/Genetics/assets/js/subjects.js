/**
 * Created by petergallagher on 14/12/2016.
 */
$(document).ready(function () {

  function setStatus($container, pedigreeText, status) {
    $container.find('.MultiSelectRemove[data-text="' + pedigreeText + '"]').siblings('select').find('option').each(function () {
      if ($(this).text() === status) {
        $(this).attr('selected', 'selected');
      }
    });
  }

  $('#GeneticsPatient_pedigrees').on('MultiSelectChanged', function () {
    var $container = $(this).parents('.multi-select'),
      pedigreeText = this.options[this.selectedIndex].text.trim();
    $.getJSON('/Genetics/pedigree/pedigreeDisorder/' + $(this).val(), function (disorder) {
      var found = false;
      $('.multiDiagnosis').each(function () {
        if ($(this).val() === disorder.id) {
          setStatus($container, pedigreeText, 'Affected');
          found = true;
          return;
        }
      });

      if (!found) {
        setStatus($container, pedigreeText, 'Unknown');
      }
    }).fail(function () {
      setStatus($container, pedigreeText, 'Unknown');
    });
  });
});