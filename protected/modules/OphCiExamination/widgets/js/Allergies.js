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
        this.$popupSelector = $('#history-allergy-popup');
        this.tableSelector = '#' + this.options.modelName + '_entry_table';
        this.$table = $(this.tableSelector);
        this.templateText = $('#' + this.options.modelName + '_entry_template').text();
        this.allergyNotCheckedValue = this.options.allergyNotCheckedValue;
        this.allergyNoValue = this.options.allergyNoValue;
        this.allergyYesValue = this.options.allergyYesValue;

        this.registerController();
        this.initialiseTriggers();
        this.dedupeAllergySelectors();
        this.showEditableIfOther();

    }

    AllergiesController._defaultOptions = {
        modelName: 'OEModule_OphCiExamination_models_Allergies',
        element: undefined,
        allergyNotCheckedValue: "-9",
        allergyNoValue: "0",
        allergyYesValue: "1",
        allergyOtherValue: 17,
        allAllergies: {}
    };

    AllergiesController.prototype.initialiseTriggers = function () {
        var controller = this;

        $(document).ready(function(){
            if (controller.$noAllergiesFld.prop('checked') && controller.$noAllergiesWrapper.prop('style').display !== "none"){
                controller.$table.find('tr:not(:first-child)').hide();
                controller.$popupSelector.hide();
            }
        });

        controller.$table.on('change', 'input[type=radio]', function () {
            controller.updateNoAllergiesState();
            controller.notifyMedicationManagementController();
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
            controller.notifyMedicationManagementController();
            controller.notifyHistoryMedicationsController();
        });

        this.$noAllergiesFld.on('click', function () {
            if (controller.$noAllergiesFld.prop('checked')) {
                controller.$table.find('tr').hide();
                controller.$popupSelector.hide();
                //in case of mandatory allegies are present
                controller.setRadioButtonsToNo();
            }
            else {
                controller.$popupSelector.show();
                controller.$table.find('tr').show();
                //when we ticked the 'no allergies' checkbox all allergies were set to No(value 0)
                //now when we un-tick the box we do not want allergies marked No by default - user must select something
                controller.$table.find('input[type=radio]:checked').prop('checked', false);
            }

            controller.notifyMedicationManagementController();
        });
    };

    AllergiesController.prototype.registerController = function () {
        this.$element.data("controller", this);
    };

    AllergiesController.prototype.notifyMedicationManagementController = function(new_allergy_ids) {
        var $element = $("#OEModule_OphCiExamination_models_MedicationManagement_element");
        if($element.length > 0) {
            let medicationManagementController = new OpenEyes.OphCiExamination.HistoryMedicationsController({
                element: $element,
                allAllergies: this.options.allAllergies
            });
            medicationManagementController.updateAllergyStatuses(new_allergy_ids);
        }
    };

    AllergiesController.prototype.notifyHistoryMedicationsController = function(new_allergy_ids) {
        let $historyMedicationsElement = $('#OEModule_OphCiExamination_models_HistoryMedications_element');
        if ($historyMedicationsElement.length > 0) {
            let historyMedicationController = new OpenEyes.OphCiExamination.HistoryMedicationsController({
                element: $historyMedicationsElement,
                allAllergies: this.options.allAllergies
            });
            historyMedicationController.updateAllergyStatuses(new_allergy_ids);
        }
    };


    AllergiesController.prototype.isAllergiesChecked = function (value) {
        var valueChecked = false;
        this.$table.find('input[type=radio]:checked , input[type=hidden][id$="has_allergy"]').each(function () {
            if ($(this).val() === value) {
                valueChecked = true;
                return false;
            }
        });
        return valueChecked;
    };

    AllergiesController.prototype.setRadioButtonsToNo = function () {
        this.$table.find('input[type=radio]').each(function () {
            if ($(this).val() === "0") {
                $(this).prop('checked', 'checked');
            }
        });
    };

    /**
     *
     * @param allergies
     * @returns {*}
     */
    AllergiesController.prototype.createRows = function (allergies = {}) {
        var newRows = [];
        var template = this.templateText;
        var tableSelector = this.tableSelector;
        $(allergies).each(function () {
          var data = {};
          data.row_count = OpenEyes.Util.getNextDataKey( tableSelector + ' tbody tr', 'key')+ newRows.length;
          data.allergy_id = this.id;
          data.allergy_display = this.label;
          newRows.push(Mustache.render(
              template,
              data ));
        });
        return newRows;
    };

    AllergiesController.prototype.showEditableIfOther = function () {
        var controller = this;
        $(this.allergySelector).each(function () {
            let $tr = $(this).closest('tr');
            let row_other_allergy = $tr.find('.js-other-allergy');
            let other_allergy_value = row_other_allergy.find('input').val();

            if (other_allergy_value === '') {
                let show_other_input = (parseInt(this.value) === controller.options.allergyOtherValue);
                $tr.find('.js-not-other-allergy').toggle(!show_other_input);
                row_other_allergy.toggle(show_other_input);
            }
        });
    };

    AllergiesController.prototype.updateNoAllergiesState = function () {
        if (this.$noAllergiesFld.prop('checked')) {
            this.$noAllergiesFld.prop('checked', false);
            this.$popupSelector.show();
        }
        if(this.isAllergiesChecked(this.allergyYesValue)){
            this.$noAllergiesWrapper.hide();
            this.$popupSelector.show();
            this.$noAllergiesFld.prop('checked', false);
        } else {
            this.$noAllergiesWrapper.show();
        }
    };

    /**
     * API method used by other elements
     * to get current allergy ids
     */

    AllergiesController.prototype.getAllergyIds = function ()
    {
        var allergy_ids = [];
        $.each(this.$element.find('input[name*="[allergy_id]"]'), function (i, e) {
            allergy_ids.push($(e).val());
        });

        return allergy_ids;
    };

    /**
     * Add a family history section if its valid.
     */
    AllergiesController.prototype.addEntry = function (allergies) {
        this.$table.find('tbody').append(this.createRows(allergies));
        $('.flex-item-bottom').find('.selected').removeClass('selected');
        this.dedupeAllergySelectors();
        this.updateNoAllergiesState();
        this.showEditableIfOther();
        var new_allergy_ids = [];
        $.each(allergies, function(i,e){
            new_allergy_ids.push(e.id);
        });

        this.notifyMedicationManagementController(new_allergy_ids);
        this.notifyHistoryMedicationsController(new_allergy_ids);

        autosize($('.autosize'));
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
            if (value && (value != self.options.allergyOtherValue)) {
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
