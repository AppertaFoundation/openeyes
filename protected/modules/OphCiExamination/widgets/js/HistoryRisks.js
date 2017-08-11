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
    function HistoryRisksController(options) {
        this.options = $.extend(true, {}, HistoryRisksController._defaultOptions, options);
        this.$element = this.options.element;
        this.riskSelector = '.' + this.options.modelName + '_risk_id';
        this.otherSelector = '.' + this.options.modelName + '_other_risk';
        this.otherWrapperSelector = '.' + this.options.modelName + '_other_wrapper';

        this.$noRisksWrapper = this.$element.find('.' + this.options.modelName + '_no_risks_wrapper');
        this.$noRisksFld = this.$element.find('.' + this.options.modelName + '_no_risks');

        this.$table = this.$element.find('.' + this.options.modelName + '_entry_table');
        this.templateText = this.$element.find('.' + this.options.modelName + '_entry_template').text();
        console.log(this.templateText);
        this.initialiseTriggers();

    }

    HistoryRisksController._defaultOptions = {
      modelName: 'OEModule_OphCiExamination_models_HistoryRisks',
      element: undefined
    };

    HistoryRisksController.prototype.initialiseTriggers = function()
    {
        var controller = this;
        controller.$element.on('change', controller.riskSelector, function(e) {
            var $selected = $(this).find('option:selected');
            var $container = $(this).parents('td');
            if ($selected.data('other')) {
                $container.find(controller.otherWrapperSelector).show();
            }
            else {
                $container.find(controller.otherWrapperSelector).hide();
                $container.find(controller.otherSelector).val('');
            }
        });

        controller.$element.on('click', '.' + controller.options.modelName + '_add_entry', function(e) {
            e.preventDefault();
            controller.addEntry();
        });

      controller.$table.on('click', '.button.remove', function(e) {
            e.preventDefault();
            $(e.target).parents('tr').remove();
            controller.updateNoRisksState();
        });

      controller.$noRisksFld.on('click', function() {
            if (controller.$noRisksFld.prop('checked')) {
                controller.$table.hide();
            }
            else {
                controller.$table.show();
            }
        })
    };

    HistoryRisksController.prototype.updateNoRisksState = function()
    {
        if (this.$table.find('tbody tr').length === 0) {
            this.$noRisksWrapper.show();
        } else {
            this.$noRisksWrapper.hide();
            this.$noRisksFld.prop('checked', false);
        }
    }


    /**
     *
     * @param data
     * @returns {*}
     */
    HistoryRisksController.prototype.createRow = function(data)
    {
        if (data === undefined)
            data = {};
        data['row_count'] = this.$table.find('tbody tr').length;
        return Mustache.render(
            template = this.templateText,
            data
        );
    };

    /**
     * Add a family history section if its valid.
     */
    HistoryRisksController.prototype.addEntry = function()
    {
        var row = this.createRow();
        console.log(row);
        this.$table.find('tbody').append(row);
        this.updateNoRisksState();
    };

    exports.HistoryRisksController = HistoryRisksController;

})(OpenEyes.OphCiExamination);

