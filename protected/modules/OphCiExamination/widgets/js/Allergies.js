/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

(function (exports) {
    function AllergiesController(options) {
        this.options = $.extend(true, {}, AllergiesController._defaultOptions, options);
        this.$element = this.options.element;
        this.$noAllergiesWrapper = $('#' + this.options.modelName + '_no_allergies_wrapper');
        this.$noAllergiesFld = $('#' + this.options.modelName + '_no_allergies');
        this.allergySelector = '[name$="[allergy_id]"]';
        this.$other = $('#' + this.options.modelName + '_other');
        this.otherWrapperSelector = '.' + this.options.modelName + '_other_wrapper';
        this.$popupSelector = $('#history-allergy-popup');
        this.tableSelector = '#' + this.options.modelName + '_entry_table';
        this.$table = $(this.tableSelector);
        this.templateText = $('#' + this.options.modelName + '_entry_template').text();
        this.allergyNotCheckedValue = this.options.allergyNotCheckedValue;
        this.allergyNoValue = this.options.allergyNoValue;
        this.allergyYesValue = this.options.allergyYesValue;

        this.initialiseTriggers();
        this.dedupeAllergySelectors();

    }

    AllergiesController._defaultOptions = {
        modelName: 'OEModule_OphCiExamination_models_Allergies',
        element: undefined,
        allergyNotCheckedValue: "-9",
        allergyNoValue: "0",
        allergyYesValue: "1"
    };

    AllergiesController.prototype.initialiseTriggers = function () {
        var controller = this;

        controller.$table.on('change', 'input[type=radio]', function () {
            controller.updateNoAllergiesState();
        });
        controller.$table.on('change', controller.allergySelector, function (e) {
            var $selected = $(this).find('option:selected');


            if ($selected.data('other')) {
                $(this).closest('td').find(controller.otherWrapperSelector).show();
            }
            else {
                $(this).closest('td').find(controller.otherWrapperSelector).hide();
                controller.$other.val('');
            }
            controller.dedupeAllergySelectors();
        });

        this.$popupSelector.on('click', '.add-icon-btn', function (e) {
            e.preventDefault();
            if (controller.$table.hasClass('hidden')) {
                controller.$table.removeClass('hidden');
            }
            controller.$table.show();
            if ($('#history-allergy-option').find('.selected').length) {
                controller.addEntry();
            }
        });

        this.$table.on('click', 'i.trash', function (e) {

            e.preventDefault();
            $(this).closest('tr').remove();
            controller.updateNoAllergiesState();
            controller.dedupeAllergySelectors();
        });

        this.$noAllergiesFld.on('click', function () {
            if (controller.$noAllergiesFld.prop('checked')) {
                controller.$table.find('tr:not(:first-child)').hide();
                controller.$popupSelector.hide();
                //in case of mandatory allegies are present
                controller.setRadioButtonsToNo();
            }
            else {
                controller.$popupSelector.show();
                controller.$table.find('tr:not(:first-child)').show();
                //when we ticked the 'no allergies' checkbox all allergies were set to No(value 0)
                //now when we un-tick the box we do not want allergies marked No by default - user must select something
                controller.$table.find('input[type=radio]:checked').prop('checked', false);
            }
        });
    };

    AllergiesController.prototype.isAllergiesChecked = function (value) {
        var valueChecked = false;
        this.$table.find('input[type=radio]:checked').each(function (i) {
            if ($(this).val() === value) {
                valueChecked = true;
                return false;
            }
        });
        if (valueChecked) {
            return true;
        } else {
            return false;
        }
    };

    AllergiesController.prototype.setRadioButtonsToNo = function () {
        this.$table.find('input[type=radio]').each(function (i) {
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
    AllergiesController.prototype.createRows = function (allergies) {
        if (allergies === undefined)
            allergies = {};
        var newRows = [];
        var template = this.templateText;
        var tableSelector = this.tableSelector;
        $(allergies).each(function (e) {
            var data = {};
            data['row_count'] = OpenEyes.Util.getNextDataKey(tableSelector + ' tbody tr', 'key') + newRows.length;
            data['allergy_id'] = this['id'];
            data['allergy_display'] = this['label'];
            newRows.push(Mustache.render(
                template,
                data));
        });
        return newRows;
    };

    AllergiesController.prototype.updateNoAllergiesState = function () {
        if (this.$table.find('tbody tr').length === 0) {
            this.$noAllergiesWrapper.show();
            this.$table.hide();
        } else {
            this.$popupSelector.show();
            this.$noAllergiesWrapper.hide();
            this.$noAllergiesFld.prop('checked', false);
        }
    };

    /**
     * Add a family history section if its valid.
     */
    AllergiesController.prototype.addEntry = function (allergies) {
        this.$table.find('tbody').append(this.createRows(allergies));
        $('.flex-item-bottom').find('.selected').removeClass('selected');
        this.dedupeAllergySelectors();
        this.updateNoAllergiesState();
    };

    /**
     * Show the table. (useful for when adding a row to an empty and thus hidden table)
     */
    AllergiesController.prototype.showTable = function () {
        this.$table.show();
    };

    /**
     * Run through each Allergies drop down and ensure selected options are not
     * available in other rows.
     */
    AllergiesController.prototype.dedupeAllergySelectors = function () {
        var self = this;
        var selectedAllergies = [];

        self.$element.find(self.allergySelector).each(function () {
            var value = this.getAttribute('value');
            /* can someone explain the hardcoded 17, please ? And shouldn't operator !== be used ? */
            if (value && !(value == 17)) {
                selectedAllergies.push(value);
            }
        });

        self.$element.find('li').each(function () {
            if (inArray(this.getAttribute('data-id'), selectedAllergies)) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
    };
    exports.AllergiesController = AllergiesController;

})(OpenEyes.OphCiExamination);

