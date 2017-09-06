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
        this.risksBySource = {};
        this.registeredControllers = [];
        this.sourceIndex = 0;
    }

    HistoryRisksCore.prototype.registerForSync = function(controller)
    {
        this.registeredControllers.push(controller);
        this.sync();
    };

    /**
     * Look for elements that should be notified of diagnoses, and provide the latest set of diagnoses to them.
     */
    HistoryRisksCore.prototype.sync = function()
    {
        for (var idx in this.registeredControllers) {
            var controller = this.registeredControllers[idx];
            controller.setExternalRisks(this.risksBySource);
        }
    };

    /**
     * Track element sources with a unique id
     *
     * @param element
     * @returns {*|jQuery}
     */
    HistoryRisksCore.prototype.getSourceId = function(element)
    {
        var id = $(element).data('historyrisks-core-id');
        if (id === undefined) {
            id = this.sourceIndex++;
            $(element).data('historyrisks-core-id', id);
        }
        return id;
    };

    /**
     * Singleton function for any element to call when wanting to update diagnoses that have been
     * found from user interaction.
     *
     * @param diagnoses
     * @param sourceElement
     */
    HistoryRisksCore.prototype.setForSource = function(risks, sourceElement)
    {
        var source = this.getSourceId(sourceElement);
        this.risksBySource[source] = risks;
        // run a sync
        this.sync();
    };

    exports.HistoryRisks = new HistoryRisksCore();

    function HistoryRisksController(options) {
        this.options = $.extend(true, {}, HistoryRisksController._defaultOptions, options);
        this.$element = this.options.element;
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

        this.externalRisks = {};
        exports.HistoryRisks.registerForSync(this);
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
     * Add a family history section if its valid.
     */
    HistoryRisksController.prototype.addEntry = function()
    {
        var row = this.createRow();
        this.$table.find('tbody').append(row);
        this.updateNoRisksState();
    };

    /**
     * Syncing functions below this point - possibly ripe for refactoring if another element
     * needs similar functionality (see Diagnosis for original example)
     */

    /**
     * risksBySource is expected to be of the format:
     * {source_id: [[risk_id, [comment, comment ...] ...], ... }
     *
     * @param risksBySource
     */
    HistoryRisksController.prototype.setExternalRisks = function(risksBySource) {
        // reformat to controller structure:
        // { risk_id: [comment, ...], ... }
        var newExternalRisks = {};
        for (var source in risksBySource) {
            if (risksBySource.hasOwnProperty(source)) {
                for (var i = 0; i < risksBySource[source].length; i++) {
                    var risk_id = risksBySource[source][i][0];
                    if (risksBySource[source][i][0] in newExternalRisks) {
                        // already exists, so append comments not yet stored for this risk
                        for (var j in risksBySource[source][i][1]) {
                            var comment = risksBySource[source][i][1][j];
                            if (!comment in newExternalRisks[risk_id].comments) {
                                newExternalRisks[risk_id].comments.push(comment);
                            }
                        }
                    } else {
                        // simply clone the comments list for the risk
                        newExternalRisks[risk_id] = {comments: risksBySource[source][i][1]}
                    }
                }
            }
        }

        this.externalRisks = newExternalRisks;

        this.renderExternalRisks();
    };

    /**
     * Update the risks table with all the external risks that have been set on the controller
     */
    HistoryRisksController.prototype.renderExternalRisks = function()
    {
        // iterate through external risks
        for (var risk_id in this.externalRisks) {
            // add to table if not present
            var rowEntry = this.getTableRowForRisk(risk_id);
            // ensure the comments include the relevant items of text
            this.setHasRiskAndComments(rowEntry, this.externalRisks[risk_id].comments);
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

