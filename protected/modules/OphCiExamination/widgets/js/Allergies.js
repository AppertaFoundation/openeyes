/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

(function(exports) {
    function AllergiesController(options) {
        this.options = $.extend(true, {}, AllergiesController._defaultOptions, options);

        this.$noAllergiesWrapper = $('#' + this.options.modelName + '_no_allergies_wrapper');
        this.$noAllergiesFld = $('#' + this.options.modelName + '_no_allergies');
        this.$entryFormWrapper = $('#' + this.options.modelName + '_form_wrapper');

        this.$allergySelect = $('#' + this.options.modelName + '_allergy_id');
        this.$other = $('#' + this.options.modelName + '_other');
        this.$otherWrapper = $('#' + this.options.modelName + '_other_wrapper');
        this.$commentFld = $('#' + this.options.modelName + '_comments');
        this.$table = $('#' + this.options.modelName + '_entry_table');
        this.templateText = $('#' + this.options.modelName + '_entry_template').text();

        this.initialiseTriggers();

    }

    AllergiesController._defaultOptions = {
        modelName: 'OEModule_OphCiExamination_models_Allergies'
    };

    AllergiesController.prototype.initialiseTriggers = function()
    {
        var controller = this;
        controller.$allergySelect.on('change', function(e) {
            var $selected = controller.$allergySelect.find('option:selected');
            if ($selected.data('other')) {
                controller.$otherWrapper.show();
            }
            else {
                controller.$otherWrapper.hide();
                controller.$other.val('');
            }
        });

        $('#' + this.options.modelName + '_add_entry').on('click', function(e) {
            e.preventDefault();
            controller.addEntry();
        });

        this.$table.on('click', '.button', function(e) {
            e.preventDefault();
            $(e.target).parents('tr').remove();
            controller.showNoAllergies();
        });

        this.$noAllergiesFld.on('click', function() {
            if (controller.$noAllergiesFld.prop('checked')) {
                controller.$entryFormWrapper.hide();
                controller.$table.hide();
            }
            else {
                controller.$entryFormWrapper.show();
                controller.$table.show();
            }
        })
    };

    /**
     * Generates a json object of the form data to use in rendering table rows for family history
     *
     * @returns {{}}
     */
    AllergiesController.prototype.generateDataFromForm = function()
    {
        var data = {};
        var $allergySelected = this.$allergySelect.find('option:selected');
        if ($allergySelected.val()) {
            data.allergy_id = $allergySelected.val();
            data.other_allergy = this.$other.val();
            if ($allergySelected.data('other')) {
                data.allergy_display = this.$other.val();
            } else {
                data.allergy_display = $allergySelected.text();
            }
        }

        data.comments = this.$commentFld.val();

        return data;
    };

    /**
     * Reset the entry form to default values
     */
    AllergiesController.prototype.resetForm = function()
    {
        this.$allergySelect.find('option:selected').prop('selected', false);
        this.$allergySelect.trigger('change');
        this.$commentFld.val('');
    }

    /**
     * @returns {boolean}
     */
    AllergiesController.prototype.validateData = function(data)
    {
        var errs = [];
        if (!data.allergy_display) {
            errs.push('Please choose/enter an allergy.');
        }

        if (errs.length) {
            new OpenEyes.UI.Dialog.Alert({
                content: errs.join('<br />')
            }).open();
            return false;
        }
        return true;
    };

    /**
     *
     * @param data
     * @returns {*}
     */
    AllergiesController.prototype.createRow = function(data)
    {
        return Mustache.render(
            template = this.templateText,
            data
        );
    };

    /**
     * hide the no family history section of the form.
     */
    AllergiesController.prototype.hideNoAllergies = function()
    {
        this.$noAllergiesWrapper.hide();
        this.$noAllergiesFld.prop('checked', false);
        this.$entryFormWrapper.show();
    };

    /**
     * Only show no family history form section if there no entries in the history table.
     */
    AllergiesController.prototype.showNoAllergies = function()
    {
        if (this.$table.find('tr').length === 1) {
            this.$noAllergiesWrapper.show();
        } else {
            this.hideNoAllergies();
        }
    };

    /**
     * Add a family history section if its valid.
     */
    AllergiesController.prototype.addEntry = function()
    {
        var data = this.generateDataFromForm();
        if (this.validateData(data)) {
            this.hideNoAllergies();

            this.$table.append(this.createRow(data));
            this.resetForm();
        }
    };

    exports.AllergiesController = AllergiesController;

})(OpenEyes.OphCiExamination);

