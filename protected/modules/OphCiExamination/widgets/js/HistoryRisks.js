/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

(function (exports) {
    function HistoryRisksCore() {
        this.elementTypeClass = OE_MODEL_PREFIX + 'HistoryRisks';
        this.controllerElSelector = '#' + OE_MODEL_PREFIX + 'HistoryRisks_element';
    }

    /**
     * Singleton function for any element to call when wanting to add a Risk
     *
     * @param risks [{id: riskId, comments: [comment, ...]}, ...]
     * @param sourceElementName
     */
    HistoryRisksCore.prototype.addRisksForSource = function (risks, sourceElementName) {
        this.callFunctionOnController('addRisks', [risks, sourceElementName]);
    };

    /**
     * Abstraction for calling function on the active history risks controller
     *
     * @param funcName
     * @param args
     */
    HistoryRisksCore.prototype.callFunctionOnController = function (funcName, args) {
        var core = this;

        function controllerFunction() {
            var controller = $(core.controllerElSelector).data('controller');
            controller[funcName].apply(controller, args);
        }

        if (!$.find(core.controllerElSelector).length) {
            let sidebar = $('#episodes-and-events').data('patient-sidebar');
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
        this.addCommentsButtonSelector = ".js-add-comments";
        this.pcrRiskLinks = this.options.pcrRiskLinks;
        this.$risksOptions = $('#history-risks-option');
        this.riskNotCheckedValue = this.options.riskNotCheckedValue;
        this.riskNoValue = this.options.riskNoValue;
        this.riskYesValue = this.options.riskYesValue;

        this.$noRisksWrapper = this.$element.find('.' + this.options.modelName + '_no_risks_wrapper');
        this.$noRisksFld = this.$element.find('.' + this.options.modelName + '_no_risks');
        this.tableSelector = '.' + this.options.modelName + '_entry_table';
        this.$popupSelector = $('#add-history-risk-popup');
        this.$table = this.$element.find(this.tableSelector);
        this.templateText = this.$element.find('.' + this.options.modelName + '_entry_template').text();
        this.riskIdMap = undefined;
        this.riskLabelMap = undefined;
        this.initialiseTriggers();
        this.dedupeRiskSelectors();
    }

    HistoryRisksController._defaultOptions = {
        modelName: 'OEModule_OphCiExamination_models_HistoryRisks',
        element: undefined,
        riskNotCheckedValue: "-9",
        riskNoValue: "0",
        riskYesValue: "1",
        pcrRiskLinks: [
            {
                name: "Cannot Lie Flat",
                selector: "select.pcr_lie_flat",
                "function": function (is_present, selector, is_checked, req_risk_value = undefined) {
                    if (req_risk_value) {
                        HistoryRisksController.prototype.editPcrRiskValues(is_present, selector, req_risk_value);
                    } else if (is_present) {
                        $(selector).val("N");
                    } else if (is_checked) {
                        $(selector).val("Y");
                    } else {
                        $(selector).val("NK");
                    }
                }
            },
            {
                name: "Alpha blockers",
                selector: "select.pcrrisk_arb",
                function: function (is_present, selector, is_checked, req_risk_value = undefined) {
                    if (req_risk_value) {
                        HistoryRisksController.prototype.editPcrRiskValues(is_present, selector, req_risk_value);
                    } else if (is_present) {
                        $(selector).val("Y");
                    } else if (is_checked){
                        $(selector).val("N");
                    } else {
                        $(selector).val("NK");
                    }
                }
            }]
    };

    HistoryRisksController.prototype.initialiseTriggers = function () {
        var controller = this;
        controller.$element.on('change', controller.riskSelector, function (e) {
            var $selected = $(this).find('option:selected');
            var $container = $(this).parents('td');
            var $tr = $(this).closest('tr');
            if ($selected.data('other')) {
                $container.find(controller.otherWrapperSelector).show();
            } else {
                $container.find(controller.otherWrapperSelector).hide();
                $container.find(controller.otherSelector).val('');
            }
            $tr.off('.pcr');
            controller.dedupeRiskSelectors();
        });

        $(document).ready(function () {
            if (controller.$noRisksFld.prop('checked')) {
                controller.$table.find('tr').hide();
                controller.$popupSelector.hide();
            }
            controller.updateNoRisksState();
        });

        controller.$table.on('change', 'input[type=radio]', function () {
            controller.updateNoRisksState();
            controller.setPcrRisk($(this));
        });

        this.$popupSelector.on('click', '.js-add-select-search', function (e) {
            e.preventDefault();
            controller.$table.show();
        });
        controller.$table.on('click', 'i.trash', function (e) {
            e.preventDefault();
            var info = $(e.target).parents('tr').find('.risk-display');
            data = {};
            data['id'] = $(info).data('id');
            data['name'] = $(info).data('label');
            controller.appendSelectList(data);
            $(e.target).parents('tr').remove();

            controller.updateNoRisksState();
            controller.dedupeRiskSelectors();
            controller.setPcrRisk();
        });

        controller.$noRisksFld.on('click', function () {
            if (controller.$noRisksFld.prop('checked')) {
                controller.$table.find('tr').hide();
                controller.$popupSelector.hide();
                controller.setRadioButtonsToNo();
            } else {
                controller.$popupSelector.show();
                controller.$table.find('tr').show();
                controller.$table.find('input[type=radio]:checked').prop('checked', false);
            }
        });
    };

    HistoryRisksController.prototype.setPcrRisk = function ($req_risk_input = undefined) {
        var controller = this;
        let is_present;
        let is_checked;
        let not_checked_risks = [];
        let present_risks = [];
        controller.$table.find('tbody tr .risk-display').each(function () {
            if ($(this).parents('tr').find('.risks-read-only').length) {
                let checked_value = $(this).parents('tr').find('input[type=radio]:checked').val();
                if (checked_value === controller.riskYesValue) {
                    present_risks.push($(this).text().trim());
                } else if (checked_value === controller.riskNotCheckedValue) {
                    not_checked_risks.push($(this).text().trim());
                }
            } else {
                present_risks.push($(this).text().trim());
            }
        }).get();

        if ($req_risk_input) {
            let req_risk_value = $req_risk_input.val();
            let req_risk_display_name = $req_risk_input.parents('tr').find('.risk-display').data('label');
            let pcr_link = controller.pcrRiskLinks.find(function (riskLink) {
                return riskLink.name === req_risk_display_name && typeof riskLink.function === 'function';
            });
            if (pcr_link) {
                is_present = pcr_link.name === req_risk_display_name;
                pcr_link.function(is_present, pcr_link.selector, true, req_risk_value);
            }
        } else { // non-required risk input
            controller.pcrRiskLinks.forEach(function (pcr_link) {
               is_present = $.inArray(pcr_link.name, present_risks) !== -1;
               is_checked = $.inArray(pcr_link.name, not_checked_risks) === -1;
                if (typeof pcr_link.function === 'function') {
                    pcr_link.function(is_present, pcr_link.selector, is_checked);
                }
            });
        }

    };

    HistoryRisksController.prototype.editPcrRiskValues = function (is_present, selector, req_risk_value) {
        let values_converted = {'-9': 'NK', '1': 'Y', '0': 'N'};
        $(selector).val(values_converted[req_risk_value]);
    };


    /**
     * Show the no risks form section if there are no table entries or
     * if any of the values are checked to Yes or Not Checked
     */
    HistoryRisksController.prototype.updateNoRisksState = function () {
        if (this.$noRisksFld.prop('checked') && this.isRisksChecked(this.riskNotCheckedValue)) {
            this.$noRisksFld.prop('checked', false);
            this.$popupSelector.show();
        }
        if (this.isRisksChecked(this.riskYesValue) || this.isRisksChecked(this.riskNotCheckedValue)) {
            this.$noRisksWrapper.hide();
            this.$popupSelector.show();
            this.$noRisksFld.prop('checked', false);
        } else {
            this.$noRisksWrapper.show();
        }
    };

    /**
     * Check checked radio boxes for a given value
     * Return true if the value was found
     * @param value
     * @returns {boolean}
     */
    HistoryRisksController.prototype.isRisksChecked = function (value) {
        var valueChecked = false;

        this.$table.find('input[type=radio]:checked , input[type=hidden]').each(function () {
            if ($(this).val() === value) {
                valueChecked = true;
                return false;
            }
        });
        return valueChecked;

    };

    HistoryRisksController.prototype.setRadioButtonsToNo = function () {
        this.$table.find('input[type=radio]').each(function () {
            if ($(this).val() === "0") {
                $(this).prop('checked', 'checked');
            }
        });
    };

    /**
     *
     * @param data
     * @returns {*}
     */
    HistoryRisksController.prototype.createRow = function (risk_id, risk_name) {

        let template = this.templateText;
        let tableSelector = this.tableSelector;
        let data = {};
        data['row_count'] = OpenEyes.Util.getNextDataKey(tableSelector + ' tbody tr', 'key');
        data['risk_id'] = risk_id;
        data['risk_display'] = risk_name;
        let new_row = Mustache.render(template, data);
        let row_item = $(new_row);
        if (risk_name === 'Other') {
            row_item.find('.js-other-risk').show();
            row_item.find('.js-not-other-risk').hide();
        }
        return row_item;
    };

    /**
     * Add a history risks row
     */
    HistoryRisksController.prototype.addEntry = function (risk_id, risk_name) {
        var row = this.createRow(risk_id, risk_name);
        this.$table.find('tbody').append(row);
        this.updateNoRisksState();

        this.setPcrRisk();
        autosize($('.autosize'));
    };

    /**
     * Run through each Risk drop down and ensure selected options are not
     * available in other rows.
     */
    HistoryRisksController.prototype.dedupeRiskSelectors = function () {
        var self = this;
        var selectedRisks = [];
        self.$element.find(self.riskSelector).each(function () {
            var $selected = $(this).find('option:selected');
            if ($selected.val() && !$selected.data('other')) {
                selectedRisks.push($selected.val());
            }
        });

        self.$element.find(self.riskSelector).each(function () {
            $(this).find('option').each(function () {
                if (!$(this).is(':selected') && ($.inArray($(this).val(), selectedRisks) > -1)) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
        });
    };

    /**
     * Update the risks table with the given list of risks with comments
     */
    HistoryRisksController.prototype.addRisks = function (risks, sourceName) {
        var risk_id;
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
     * Find the table row for the given risk id if it exists.
     *
     * @param risk_id
     * @returns $('tr')|{undefined}
     */
    HistoryRisksController.prototype.findTableRowForRisk = function (risk_id) {
        var self = this;
        var row = undefined;
        self.$table.find('tbody tr').each(function () {
            if ($(this).find(self.riskSelector).val() == risk_id) {
                row = $(this);
                return false;
            }
        });
        return row;
    };

    /**
     * get or create a table row for the given risk
     */
    HistoryRisksController.prototype.getTableRowForRisk = function (risk_id) {
        var self = this;
        var row = self.findTableRowForRisk(risk_id);
        if (row === undefined) {

            let $risk_to_add =  $('ul[data-id="risk_dialog_list"]').find('li[data-id=' + risk_id + ']');
            self.addEntry(null, $risk_to_add.data('label'));

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
    HistoryRisksController.prototype.setHasRiskAndComments = function (row, comments) {
        // select the appropriate radio option
        row.find(this.hasRiskSelector + '[value="1"]').prop('checked', 'checked').trigger('change');

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
        row.find(this.addCommentsButtonSelector).trigger('click');
        row.find(this.commentsSelector).val(finalList.join(', '));
    };

    /**
     * Create an internal map of risk ids to names
     */
    HistoryRisksController.prototype.initialiseRiskIdMap = function () {
        var self = this;
        self.riskIdMap = {};
        var tmpRow = self.createRow(null, null);
        $(tmpRow).find('td:first select option').each(function () {
            self.riskIdMap[$(this).val()] = $(this).text();
        });
        // map out those that are not in the dropdown list.
        self.$table.find('tr td:first').each(function () {
            var text = $(this).text();
            if (text.length) {
                self.riskIdMap[$(this).find(self.riskSelector).val()] = text;
            }
        })
    };

    /**
     * Find a risk id for given label (needs to partial match the risk name)
     * @param riskLabel
     * @returns {*}
     */
    HistoryRisksController.prototype.getRiskIdForLabel = function (riskLabel) {
        var self = this;

        if (self.riskIdMap === undefined) {
            self.initialiseRiskIdMap();
        }
        if (self.riskLabelMap === undefined) {
            self.riskLabelMap = {};
        }

        if (!self.riskLabelMap.hasOwnProperty(riskLabel)) {
            $.each(self.riskIdMap, function (id, name) {
                if (name.toLowerCase().indexOf(riskLabel.toLowerCase()) >= 0) {
                    self.riskLabelMap[riskLabel] = id;
                }
            });
        }
        return self.riskLabelMap[riskLabel];
    };

    /**
     *
     * @param riskLabel
     */
    HistoryRisksController.prototype.getRiskStatus = function (riskLabel) {
        var riskId = this.getRiskIdForLabel(riskLabel);
        var row = this.findTableRowForRisk(riskId);
        if (row === undefined) {
            return undefined;
        }

        var selected = row.find('input:checked');
        return selected ? selected.val() : undefined;
    };

    /**
     *
     */
    HistoryRisksController.prototype.appendSelectList = function (data) {
        var span = "<span class='auto-width'>" + data['name'] + "</span>";
        var list_item = $('<li>')
            .attr('data-str', data['name'])
            .attr('data-id', data['id']);
        list_item.append(span);
        this.$risksOptions.append(list_item);
    };

    exports.HistoryRisksController = HistoryRisksController;
})(OpenEyes.OphCiExamination);