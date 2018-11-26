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

(function(exports) {
    function FamilyHistoryController(options) {
        this.options = $.extend(true, {}, FamilyHistoryController._defaultOptions, options);

        this.$noHistoryWrapper = $('#' + this.options.modelName + '_no_family_history_wrapper');
        this.$noHistoryFld = $('#' + this.options.modelName + '_no_family_history');
        this.$entryFormWrapper = $('#' + this.options.modelName + '_form_wrapper');
        this.tableSelector = '#' + this.options.modelName + '_entry_table';
        this.$table = $(this.tableSelector);
        this.$popupSelector = $('#add-family-history-button');
        this.templateText = $('#' + this.options.modelName + '_entry_template').text();
        this.initialiseTriggers();
    }

    FamilyHistoryController._defaultOptions = {
        modelName: 'OEModule_OphCiExamination_models_FamilyHistory'
    };

    FamilyHistoryController.prototype.initialiseTriggers = function()
    {
        var controller = this;
        this.$table.on('click', 'i.trash', function(e) {
            e.preventDefault();
            $(this).closest('tr').remove();
            controller.showNoHistory();
        });

        this.$noHistoryFld.on('click', function() {
            if (controller.$noHistoryFld.prop('checked')) {
                controller.$table.hide();
                controller.$popupSelector.hide();
            }
            else {
              controller.$popupSelector.show();
            }
        });

      this.$popupSelector.on('click', function(e) {
        e.preventDefault();
        controller.hideNoHistory();
        if (controller.$table.hasClass('hidden')){
          controller.$table.removeClass('hidden');
        }
        controller.$table.show();
      });
    };

    /**
     * hide the no family history section of the form.
     */
    FamilyHistoryController.prototype.hideNoHistory = function()
    {
        this.$noHistoryWrapper.hide();
        this.$noHistoryFld.prop('checked', false);
        this.$entryFormWrapper.show();
    };

    /**
     * Only show no family history form section if there no entries in the history table.
     */
    FamilyHistoryController.prototype.showNoHistory = function()
    {
        if (this.$table.find('tbody tr').length === 0) {
            this.$noHistoryWrapper.show();
            this.$table.hide();
        } else {
            this.hideNoHistory();
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
