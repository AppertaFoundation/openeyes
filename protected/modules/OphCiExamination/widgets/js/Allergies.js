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

        this.$popupSelector.on('click','.add-icon-btn', function(e) {
            e.preventDefault();
            if (controller.$table.hasClass('hidden')){
                controller.$table.removeClass('hidden');
            }
            controller.$table.show();
          if($('#history-allergy-option').find('.selected').length) {
            controller.addEntry();
          }
        });

        this.$table.on('click', 'i.trash', function(e) {

            e.preventDefault();
            $(this).closest('tr').remove();
            controller.updateNoAllergiesState();
            controller.dedupeAllergySelectors();
        });

        this.$noAllergiesFld.on('click', function () {
            if (controller.$noAllergiesFld.prop('checked')) {
                controller.$table.hide();
                controller.$popupSelector.hide();
            }
            else {
              controller.$popupSelector.show();
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
    AllergiesController.prototype.createRow = function (data) {
        if (data === undefined)
            data = {};
        var selected_option = $('#history-allergy-option').find('.selected');
        data['row_count'] = OpenEyes.Util.getNextDataKey( this.tableSelector + ' tbody tr', 'key');
        data['allergy_id'] = selected_option.data('id');
        data['allergy_display'] = selected_option.data('str');
        return Mustache.render(
            this.templateText,
            data
        );
    };

    /**
     * If the no allergy box is checked and any of the allergies are checked
     * into not checked then uncheck the 'No allergies' box
     * or
     * If any of the allergy boxes is checked yes then hide the 'patient has no allergies' box
     * else show the box
     */
    AllergiesController.prototype.updateNoAllergiesState = function () {
        if (this.$noAllergiesFld.prop('checked') && this.isAllergiesChecked(this.allergyNotCheckedValue)) {
            this.$noAllergiesFld.prop('checked', false);
        }
        if (this.isAllergiesChecked(this.allergyYesValue)) {
            this.$noAllergiesWrapper.hide();
            this.$noAllergiesFld.prop('checked', false);
        } else {
            this.$noAllergiesWrapper.show();
        }
    };

    /**
     * Add a family history section if its valid.
     */
    AllergiesController.prototype.addEntry = function () {
        this.$table.find('tbody').append(this.createRow());
        $('.flex-item-bottom').find('.selected').removeClass('selected');
        this.$table.find('tbody tr:last').on('click', '.js-add-comments', function (e) {
            $(e.target).hide();
            $(e.target).siblings('input').show();
        });
        this.dedupeAllergySelectors();
        this.updateNoAllergiesState();
    };

    /**
     * Run through each Allergies drop down and ensure selected options are not
     * available in other rows.
     */
    AllergiesController.prototype.dedupeAllergySelectors = function () {
        var self = this;
        var selectedAllergies = [];
        self.$element.find(self.allergySelector).each(function () {
            var $selected = $(this).find('option:selected');
            if ($selected.val() && !$selected.data('other')) {
                selectedAllergies.push($selected.val());
            }
        });

        self.$element.find(self.allergySelector).each(function () {
            $(this).find('option').each(function () {
                if (!$(this).is(':selected') && ($.inArray($(this).val(), selectedAllergies) > -1)) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
        });
    }

    exports.AllergiesController = AllergiesController;

})(OpenEyes.OphCiExamination);

