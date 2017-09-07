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
    function HistoryRisksCore()
    {
        this.elementTypeClass = OE_MODEL_PREFIX + 'HistoryRisks';
        this.controllerElSelector = '#' + OE_MODEL_PREFIX + 'HistoryRisks_element';
    }

    /**
     * Singleton function for any element to call when wanting to add a Risk
     *
     * @param risks [{id: riskId, comments: [comment, ...]}, ...]
     * @param sourceElementName
     */
    HistoryRisksCore.prototype.addRisksForSource = function(risks, sourceElementName)
    {
        this.callFunctionOnController('addRisks', [risks, sourceElementName]);
    };

    /**
     * Abstraction for calling function on the active history risks controller
     *
     * @param funcName
     * @param args
     */
    HistoryRisksCore.prototype.callFunctionOnController = function(funcName, args)
    {
        var core = this;
        function controllerFunction() {
            var controller = $(core.controllerElSelector).data('controller');
            controller[funcName].apply(controller, args);
        }

        if (!$.find(core.controllerElSelector).length) {
            var sidebar = $('aside.episodes-and-events').data('patient-sidebar');
            sidebar.addElementByTypeClass(core.elementTypeClass, undefined, controllerFunction);
        } else {
            controllerFunction();
        }
    };

    if (exports.HistoryRisks === undefined) {
        exports.HistoryRisks = new HistoryRisksCore();
    }

    function HistoryRisksController(options) {
        this.options = $.extend(true, {}, HistoryRisksController._defaultOptions, options);
        this.$element = this.options.element;
        this.$element.data('controller', this);
        // this.riskSelector = '.' + this.options.modelName + '_risk_id';
        this.riskSelector = '[name$="[risk_id]"]';
        this.hasRiskSelector = '[name$="[has_risk]"]';
        this.otherSelector = '.' + this.options.modelName + '_other_risk';
        this.otherWrapperSelector = '.' + this.options.modelName + '_other_wrapper';
        this.commentsSelector = '[name$="[comments]"]';

        this.$noRisksWrapper = this.$element.find('.' + this.options.modelName + '_no_risks_wrapper');
        this.$noRisksFld = this.$element.find('.' + this.options.modelName + '_no_risks');
        this.tableSelector = '.' + this.options.modelName + '_entry_table';
        this.$table = this.$element.find(this.tableSelector);
        this.templateText = this.$element.find('.' + this.options.modelName + '_entry_template').text();
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

    /**
     * Show the no risks form section if there are no table entries
     */
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

        data['row_count'] = OpenEyes.Util.getNextDataKey( this.tableSelector + ' tbody tr', 'key');
        return Mustache.render(
            this.templateText,
            data
        );
    };

    /**
     * Add a history risks row
     */
    HistoryRisksController.prototype.addEntry = function()
    {
        var row = this.createRow();
        this.$table.find('tbody').append(row);
        this.updateNoRisksState();
    };

    /**
     * Update the risks table with the given list of risks with comments
     */
    HistoryRisksController.prototype.addRisks = function(risks, sourceName)
    {
        for (var idx in risks) {
            if (risks.hasOwnProperty(idx)) {
                risk_id = risks[idx].id;
                // add to table if not present
                var rowEntry = this.getTableRowForRisk(risk_id);
                // set the risk and comment
                this.setHasRiskAndComments(rowEntry, risks[idx].comments);
            }

        }
    };

    /**
     * get or create a table row for the given risk
     */
    HistoryRisksController.prototype.getTableRowForRisk = function(risk_id)
    {
        var self = this;
        var row = undefined;
        self.$table.find('tbody tr').each(function() {
            if ($(this).find(self.riskSelector).val() == risk_id) {
                row = $(this);
                return false;
            }
        });
        if (row === undefined) {
            self.addEntry();
            row = self.$table.find('tbody tr:last');
            row.find(self.riskSelector).val(risk_id);
        }
        return row;
    };

    /**
     *
     * @param row
     * @param comments
     */
    HistoryRisksController.prototype.setHasRiskAndComments = function(row, comments)
    {
        // select the appropriate radio option
        row.find(this.hasRiskSelector + '[value="1"]').prop('checked', 'checked');

        // now munge comments together with current content
        var current = row.find(this.commentsSelector).val();
        var finalList = [];
        if (current && current.replace(/\s/g, '').length) {
            finalList.push(current);
        }
        for (var i in comments) {
            var comment = comments[i];
            if (!current || current.indexOf(comment) === -1) {
                finalList.push(comment);
            }
        }
        row.find(this.commentsSelector).val(finalList.join(', '));
    };

    exports.HistoryRisksController = HistoryRisksController;
})(OpenEyes.OphCiExamination);

