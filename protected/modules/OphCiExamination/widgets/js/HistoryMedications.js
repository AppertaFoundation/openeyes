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
    drugFieldSelector: 'input[name$="[drug_id]"]',
    medicationFieldSelector: 'input[name$="[medication_id]"]'
  };

  HistoryMedicationsController.prototype.initialiseTriggers = function()
  {
    var controller = this;

    controller.$table.on('click', '.button.remove', function(e) {
      e.preventDefault();
      $(e.target).parents('tr').remove();
    });

    controller.$table.find('.medication-search').each(function() {
      controller.initialiseSearch($(this));
    });

    controller.$element.on('click', controller.options.addButtonSelector, function(e) {
      e.preventDefault();
      controller.addEntry();
    });
  };

  HistoryMedicationsController.prototype.initialiseSearch = function($el)
  {
    var controller = this;
    if (!$el.data('search')) {
        $el.autocomplete({
            minLength: 3,
            delay: 700,
            source: function(request, response) {
                $.getJSON(controller.options.searchSource, {
                    term: request.term,
                    ajax: 'ajax'
                }, response);
            },
            select: function (event, ui) {
                controller.searchSelect($el, event, ui);
            }
        });
    }
  };

  HistoryMedicationsController.prototype.searchSelect = function($el, event, ui)
  {
    event.preventDefault();
    var $container = $el.parents('td');
    $container.find('.medication-display').text(ui.item.label);
    if (ui.item.type == 'd') {
        $container.find(this.options.drugFieldSelector).val(ui.item.value);
        $container.find(this.options.medicationFieldSelector).val('');
    } else {
        $container.find(this.options.drugFieldSelector).val('');
        $container.find(this.options.medicationFieldSelector).val(ui.item.value);
    }
    $el.val('').blur();
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
    this.initialiseSearch(this.$table.find('tbody tr:last input.search'));
  };

  exports.HistoryMedicationsController = HistoryMedicationsController;
})(OpenEyes.OphCiExamination);
