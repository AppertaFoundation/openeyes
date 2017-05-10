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
    function FamilyHistoryController(options) {
        this.options = $.extend(true, {}, FamilyHistoryController._defaultOptions, options);

        this.$noAllergiesWrapper = $('#' + this.options.modelName + '_no_family_history_wrapper');
        this.$noHistoryFld = $('#' + this.options.modelName + '_no_family_history');
        this.$entryFormWrapper = $('#' + this.options.modelName + '_form_wrapper');

        this.$relativeSelect = $('#' + this.options.modelName + '_relative_id');
        this.$otherRelative = $('#' + this.options.modelName + '_other_relative');
        this.$otherRelativeWrapper = $('#' + this.options.modelName + '_other_relative_wrapper');
        this.$sideSelect = $('#' + this.options.modelName + '_side_id');
        this.$conditionSelect = $('#' + this.options.modelName + '_condition_id');
        this.$otherCondition = $('#' + this.options.modelName + '_other_condition');
        this.$otherConditionWrapper = $('#' + this.options.modelName + '_other_condition_wrapper');
        this.$commentFld = $('#' + this.options.modelName + '_comments');
        this.$table = $('#' + this.options.modelName + '_entry_table');
        this.templateText = $('#' + this.options.modelName + '_entry_template').text();

        this.initialiseTriggers();

    }

    FamilyHistoryController._defaultOptions = {
        modelName: 'OEModule_OphCiExamination_models_FamilyHistory'
    };

    FamilyHistoryController.prototype.initialiseTriggers = function()
    {
        var controller = this;
        controller.$relativeSelect.on('change', function(e) {
            var $selected = controller.$relativeSelect.find('option:selected');
            if ($selected.data('other')) {
                controller.$otherRelativeWrapper.show();
            }
            else {
                controller.$otherRelativeWrapper.hide();
                controller.$otherRelative.val('');
            }
        });

        controller.$conditionSelect.on('change', function(e) {
            var $selected = controller.$conditionSelect.find('option:selected');
            if ($selected.data('other')) {
                controller.$otherConditionWrapper.show();
            }
            else {
                controller.$otherConditionWrapper.hide();
                controller.$otherCondition.val('');
            }
        });
        
        $('#' + this.options.modelName + '_add_entry').on('click', function(e) {
            e.preventDefault();
            controller.addEntry();
        });

        this.$table.on('click', '.button', function(e) {
            e.preventDefault();
            $(e.target).parents('tr').remove();
            controller.showNoHistory();
        });

        this.$noHistoryFld.on('click', function() {
            if (controller.$noHistoryFld.prop('checked')) {
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
   * Generates a hash of the form data to use in rendering table rows for family history
   *
   * @returns {{}}
   */
  FamilyHistoryController.prototype.generateDataFromForm = function()
    {
        var data = {};
        var $relativeSelected = this.$relativeSelect.find('option:selected');
        if ($relativeSelected.val()) {
            data.relative_id = $relativeSelected.val();
            data.other_relative = this.$otherRelative.val();
            if ($relativeSelected.data('other')) {
                data.relative_display = this.$otherRelative.val();
            } else {
                data.relative_display = $relativeSelected.text();
            }
        }
        
        var $sideSelected = this.$sideSelect.find('option:selected');
            if ($sideSelected.val()) {
            data.side_id = $sideSelected.val();
            data.side_display = $sideSelected.text();
        }

        var $conditionSelected = this.$conditionSelect.find('option:selected');
        if ($conditionSelected.val()) {
            data.condition_id = $conditionSelected.val();
            data.other_condition = this.$otherCondition.val();
            if ($conditionSelected.data('other')) {
                data.condition_display = this.$otherCondition.val();
            } else {
                data.condition_display = $conditionSelected.text();
            }
        }

        data.comments = this.$commentFld.val();

        return data;
    };

    /**
     * Reset the entry form to default values
     */
    FamilyHistoryController.prototype.resetForm = function()
    {
        this.$relativeSelect.find('option:selected').prop('selected', false);
        this.$relativeSelect.trigger('change');
        this.$conditionSelect.find('option:selected').prop('selected', false);
        this.$conditionSelect.trigger('change');
        this.$sideSelect.find('option:selected').prop('selected', false);
        this.$commentFld.val('');
    }

    /**
     * @returns {boolean}
     */
    FamilyHistoryController.prototype.validateData = function(data)
    {
        var errs = [];
        if (!data.relative_display) {
            errs.push('Please choose/enter a relative.');
        }

        if (!data.condition_display) {
            errs.push('Please choose/enter a condition.');
        }

        if (!data.side_display) {
            errs.push('Please choose a family side.');
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
    FamilyHistoryController.prototype.createRow = function(data)
    {
        return Mustache.render(
          template = this.templateText,
          data
        );
    };

    /**
     * hide the no family history section of the form.
     */
    FamilyHistoryController.prototype.hideNoAllergies = function()
    {
        this.$noAllergiesWrapper.hide();
        this.$noHistoryFld.prop('checked', false);
        this.$entryFormWrapper.show();
    };

    /**
     * Only show no family history form section if there no entries in the history table.
     */
    FamilyHistoryController.prototype.showNoHistory = function()
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
    FamilyHistoryController.prototype.addEntry = function()
    {
        var data = this.generateDataFromForm();
        if (this.validateData(data)) {
            this.hideNoAllergies();

            this.$table.append(this.createRow(data));
            this.resetForm();
        }
    };

    exports.FamilyHistoryController = FamilyHistoryController;

})(OpenEyes.OphCiExamination);

(function(exports, Util) {
    'use strict';

    var FamilyHistoryController = exports.FamilyHistoryController;

    /**
     * Controller in the patient summary context
     *
     * @param options
     * @constructor
     */
    function FamilyHistoryPatientController(options) {
        options = $.extend(true, {}, FamilyHistoryPatientController._defaultOptions, options);

        this.$showEditButton = $('#btn-edit-family-history');
        this.$cancelEditButton = $('#btn-cancel-family-history');
        this.$saveEditButton = $('#btn-save-family-history');
        this.$editForm = $('#family-history-form');

        FamilyHistoryController.call(this, options);
        
        this.$editColumn = this.$table.find('.edit-column');
        this.originalTable = this.$table.html();
        this.originalNoHistory = this.$noHistoryFld.prop('checked');
    }

    Util.inherits(FamilyHistoryController, FamilyHistoryPatientController);

    FamilyHistoryPatientController._defaultOptions = {};

    FamilyHistoryPatientController.prototype.initialiseTriggers = function()
    {
        var controller = this;
        controller.$showEditButton.on('click', function(e) {
            e.preventDefault();
            controller.$editForm.slideDown('fast');
            controller.$table.show();
            controller.$editColumn.show();
            controller.$showEditButton.hide();
        });

        controller.$cancelEditButton.on('click', function(e) {
            e.preventDefault();
            controller.$table.html(controller.originalTable);
            controller.$noHistoryFld.prop('checked', controller.originalNoHistory);
            if (controller.originalNoHistory) {
                controller.$entryFormWrapper.hide();
            }
            controller.showNoHistory();
            if (controller.$table.find('tr').length === 1) {
                controller.$table.hide();
            }
            controller.$editForm.slideToggle('fast');
            controller.$showEditButton.show();
        });

        controller.$saveEditButton.on('click', function(e) {
            if (!controller.validateSave()) {
                e.preventDefault();
            }
        });

        FamilyHistoryPatientController._super.prototype.initialiseTriggers.call(controller);
    };

    FamilyHistoryPatientController.prototype.validateSave = function()
    {
        if (this.$table.find('tr').length === 1 && !this.$noHistoryFld.prop('checked')) {
            new OpenEyes.UI.Dialog.Alert({
                content: 'Please confirm there are no family history entries to be recorded.'
            }).open();
            return false;
        }
        return true;
    };

    exports.FamilyHistoryPatientController = FamilyHistoryPatientController;
})(OpenEyes.OphCiExamination, OpenEyes.Util);
