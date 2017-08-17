/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

(function(exports) {
  function HistoryMedicationsController(options) {
    this.options = $.extend(true, {}, HistoryMedicationsController._defaultOptions, options);
    this.$element = this.options.element;
    this.$table = this.$element.find('table');
    this.templateText = this.$element.find('.entry-template').text();
    this.initialiseTriggers();
  }

  HistoryMedicationsController._defaultOptions = {
    modelName: 'OEModule_OphCiExamination_models_HistoryMedications',
    element: undefined,
    addButtonSelector: '.add-entry',
    searchSource: '/medication/finddrug',
    routeOptionSource: '/medication/retrieveDrugRouteOptions',
    searchAsTypedPrefix: 'As typed: ',
    drugFieldSelector: 'input[name$="[drug_id]"]',
    medicationFieldSelector: 'input[name$="[medication_drug_id]"]',
    asTypedFieldSelector: 'input[name$="[medication_name]"]',
    medicationSearchSelector: 'input[name$="[medication_search]"]',
    medicationNameSelector: '.medication-name',
    medicationDisplaySelector: '.medication-display',
    stopDateFieldSelector: 'input[name$="[stop_date]"]',
    stopDateButtonSelector: 'button.stop-medication',
    cancelStopDateButtonSelector: 'button.cancel-stop-medication',
    routeFieldSelector: 'select[name$="[route_id]"]',
    routeOptionWrapperSelector: '.admin-route-options'
  };

  HistoryMedicationsController.prototype.initialiseTriggers = function()
  {
    var controller = this;

    controller.$table.on('click', '.button.remove', function(e) {
      e.preventDefault();
      $(e.target).parents('tr').remove();
    });

    controller.$table.find('tbody tr').each(function() {
      controller.initialiseRow($(this));
    });

    controller.$element.on('click', controller.options.addButtonSelector, function(e) {
      e.preventDefault();
      controller.addEntry();
    });
  };

  HistoryMedicationsController.prototype.initialiseRow = function($row)
  {
      var controller = this;
      controller.initialiseSearch($row.find('input.search'));
      $row.on('click', '.medication-rename', function(e) {
          e.preventDefault();
          controller.resetSearchRow($row, true);
      });
      $row.on('click', controller.options.stopDateButtonSelector, function(e) {
          e.preventDefault();
          controller.showStopDate($row);
      });

      $row.on('click', controller.options.cancelStopDateButtonSelector, function(e) {
          e.preventDefault();
          controller.cancelStopDate($row);
      });

      $row.on('change', controller.options.routeFieldSelector, function(e) {
          controller.updateRowRouteOptions($row);
      });

      $row.on('change', '.fuzzy-date select', function(e) {
          var $fuzzyFieldset = $(this).closest('fieldset');
          var date = controller.dateFromFuzzyFieldSet($fuzzyFieldset);
          $fuzzyFieldset.find('input[type="hidden"]').val(date);
      });
      controller.resetSearchRow($row, true);
  };

  HistoryMedicationsController.prototype.initialiseSearch = function($el)
  {
    var controller = this;
    if (!$el.data('search')) {
        $el.autocomplete({
            minLength: 3,
            delay: 300,
            source: function(request, response) {
                $.getJSON(controller.options.searchSource, {
                    term: request.term,
                    ajax: 'ajax'
                }, response);
            },
            focus: function (event, ui) {
                event.preventDefault();
                $el.val(controller.getItemDisplayValue(ui));
            },
            select: function (event, ui) {
                controller.searchSelect($el, event, ui);
            },
            response: function (event, ui) {
                ui.content.push({
                    value: $el.val(),
                    label: controller.options.searchAsTypedPrefix + $el.val(),
                    type: 't'
                });
            }
        });
    }
  };

  HistoryMedicationsController.prototype.getItemDisplayValue = function(ui)
  {
      if (ui.item.type == 't') {
          return ui.item.label.replace(this.options.searchAsTypedPrefix, '');
      }
      return ui.item.label;
  };

  HistoryMedicationsController.prototype.searchSelect = function($el, event, ui)
  {
    event.preventDefault();
    var $container = $el.parents('td');
    var displayText = this.getItemDisplayValue(ui);
    this.resetSearchRow($container, false);

    if (ui.item.type == 't') {
        $container.find(this.options.asTypedFieldSelector).val(ui.item.value);
    }
    if (ui.item.type == 'd') {
        $container.find(this.options.drugFieldSelector).val(ui.item.value);
    } else {
        $container.find(this.options.medicationFieldSelector).val(ui.item.value);
    }
    $container.find(this.options.medicationNameSelector).text(displayText);
    // set the search text box to the full value chosen
    $el.val(displayText);
    $container.find(this.options.medicationDisplaySelector).show();
    $container.find(this.options.medicationSearchSelector).hide();
  };

  HistoryMedicationsController.prototype.resetSearchRow = function($container, showSearch)
  {
      if (showSearch === undefined)
          showSearch = true;

      $container.find(this.options.asTypedFieldSelector).val('');
      $container.find(this.options.drugFieldSelector).val('');
      $container.find(this.options.medicationFieldSelector).val('');
      $container.find(this.options.medicationNameSelector).text('');
      if (showSearch) {
          $container.find(this.options.medicationDisplaySelector).hide();
          $container.find(this.options.medicationSearchSelector).show();
      }
  };

  HistoryMedicationsController.prototype.showStopDate = function($row)
  {
    $row.find('.stop-date-wrapper').show();
    var $fuzzyFieldset = $row.find('.stop-date-wrapper fieldset');
    var date = this.dateFromFuzzyFieldSet($fuzzyFieldset);
    $fuzzyFieldset.find('input[type="hidden"]').val(date);
    $row.find(this.options.stopDateButtonSelector).hide();
  };

  HistoryMedicationsController.prototype.cancelStopDate = function($row)
  {
    $row.find('.stop-date-wrapper').hide();
    $row.find('.stop-date-wrapper').find('input:hidden').val('');
    $row.find(this.options.stopDateButtonSelector).show();
  };


  HistoryMedicationsController.prototype.updateRowRouteOptions = function($row)
  {
      var $routeOptionWrapper = $row.find(this.options.routeOptionWrapperSelector);
      $routeOptionWrapper.hide();
      $routeOptionWrapper.find('option').each(function() {
          if ($(this).val().length) {
              $(this).remove();
          }
      });
      var value = $row.find(this.options.routeFieldSelector + ' option:selected').val();
      if (value != "") {
          $.getJSON(this.options.routeOptionSource, {route_id: value}, function(data) {
              if (data.length) {
                  var $select = $routeOptionWrapper.find('select');
                  $.each(data, function(i, item) {
                      $select.append('<option value="' + item.id +'">' + item.name + '</option>');
                  });
                  $routeOptionWrapper.show();
              }
          })
      }

  };

  HistoryMedicationsController.prototype.createRow = function(data)
  {
    if (data === undefined)
      data = {};

    data['row_count'] = OpenEyes.Util.getNextDataKey( this.$element.find('table tbody tr'), 'key');
    return Mustache.render(
      this.templateText,
      data
    );
  };

  HistoryMedicationsController.prototype.addEntry = function()
  {
    var row = this.createRow();
    this.$table.find('tbody').append(row);
    this.initialiseRow(this.$table.find('tbody tr:last'));
  };

    /**
     * @TODO: should be common function across history elements
     * @param fieldset
     * @returns {*}
     */
    HistoryMedicationsController.prototype.dateFromFuzzyFieldSet = function(fieldset)
    {
        res = fieldset.find('select.fuzzy_year').val();
        var month = parseInt(fieldset.find('select.fuzzy_month option:selected').val());
        res += '-' + ((month < 10) ? '0' + month.toString() : month.toString());
        var day = parseInt(fieldset.find('select.fuzzy_day option:selected').val());
        res += '-' + ((day < 10) ? '0' + day.toString() : day.toString());

        return res;
    };


  exports.HistoryMedicationsController = HistoryMedicationsController;
})(OpenEyes.OphCiExamination);
