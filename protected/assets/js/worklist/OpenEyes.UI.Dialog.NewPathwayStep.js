/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

(function (exports, Util) {
    'use strict';

    // Base Dialog.
    const Dialog = exports;

    function NewPathwayStep(options)
    {
        options = $.extend(true, {}, NewPathwayStep._defaultOptions, options);

        Dialog.call(this, options);
    }

    Util.inherits(Dialog, NewPathwayStep);

    NewPathwayStep._defaultOptions = {
        destroyOnClose: false,
        onReturn: null,
        workflow_steps: [],
        custom_options: [],
        title: '',
        popupContentClass: 'oe-popup-content popup-new-path-step',
        modal: true,
        width: null,
        minHeight: 400,
        maxHeight: 400,
        dialogClass: 'dialog oe-new-path-step-popup',
        selector: '#new-path-step-template',
    };

    /**
     * Manage all the provided option data into required internal data structures for initialisation.
     */
    NewPathwayStep.prototype.create = function () {
        const self = this;

        // parent initialisation
        NewPathwayStep._super.prototype.create.call(self);

        self.servicesBySubspecialtyId = {};
        self.subspecialtiesById = {};
        self.contextsBySubspecialtyId = {};

        if (self.options.custom_options.length > 0 && self.options.custom_options[0].id === 'subspecialty') {
            for (const i in self.options.custom_options[0].option_values) {
                const subspecialty = self.options.custom_options[0].option_values[i];
                self.subspecialtiesById[subspecialty.id] = subspecialty;
                self.contextsBySubspecialtyId[subspecialty.id] = subspecialty.contexts;
                self.servicesBySubspecialtyId[subspecialty.id] = subspecialty.services;
            }
        }

        if (self.options.current_subspecialty) {
            self.content.find('.js-custom-option[name="custom_option_subspecialty"] option[value="' + this.options.current_subspecialty + '"]').prop('selected', true);
            self.updateContextList();
            if (self.options.current_firm) {
                self.content.find('.js-custom-option[name="custom_option_context"] option[value="' + this.options.current_firm + '"]').prop('selected', true);
                self.updateWorkflowStepList();
            }
        }

        $(document).off('click', '.js-add-pathway').on('click', '.js-add-pathway', this.returnValues.bind(this));
        $(document).off('click', '.js-cancel-popup-steps').on('click', '.js-cancel-popup-steps', this.cancelAdd.bind(this));
        $(document).off('change', '.js-custom-option[name="custom_option_subspecialty"]')
            .on('change', '.js-custom-option[name="custom_option_subspecialty"]', this.updateContextList.bind(this));
        $(document).off('change', '.js-custom-option[name="custom_option_context"]')
            .on('change', '.js-custom-option[name="custom_option_context"]', this.updateWorkflowStepList.bind(this));
    };

    /**
     *
     * @param options
     * @returns {string}
     */
    NewPathwayStep.prototype.getContent = function (options) {
        // Display the screen using the specified template.
        return this.compileTemplate({
            selector: options.selector,
            data: {
                display_custom_options: options.display_custom_options,
                custom_options: options.custom_options
            }
        });
    };

    NewPathwayStep.prototype.updateContextList = function () {
        const self = this;
        let list = '';
        // get selected subspecialty
        let subspecialtyId = self.content.find('.js-custom-option[name="custom_option_subspecialty"]').val();
        if (subspecialtyId) {
            for (const i in self.contextsBySubspecialtyId[subspecialtyId]) {
                const context = self.contextsBySubspecialtyId[subspecialtyId][i];
                list += '<option value="' + context.id + '">' + context.name + '</option>';
            }
        }

        self.content.find('.js-custom-option[name="custom_option_context"]').html(list);
    };

    NewPathwayStep.prototype.updateWorkflowStepList = function () {
        const self = this;
        const selected = self.content.find('.js-custom-option[name="custom_option_context"]').val();
        if (selected) {
            let list = '';
            const workflowSteps = self.options.workflow_steps[selected];
            if (workflowSteps !== undefined) {
                list += '<option value="">None</li>';
                for (let i = 0; i < workflowSteps.length; i++) {
                    list += '<option value="' + workflowSteps[i].id + '">' + workflowSteps[i].name + '</li>';
                }
            } else {
                list += '<option value="">None</li>';
            }
            self.content.find('.js-custom-option[name="custom_option_workflow_step"]').html(list);
        }
    };

    NewPathwayStep.prototype.returnValues = function () {
        if (this.options.onReturn) {
            let long_name = $('input[name="taskname"]').val();
            let short_name = $('input[name="shortname"]').val();
            let selected_custom_options = $('select[class="js-custom-option"]').map(function () { return $(this).val(); });
            this.options.onReturn(this, long_name, short_name, selected_custom_options);
        }
    };

    NewPathwayStep.prototype.cancelAdd = function () {
        this.close();
    };

    exports.NewPathwayStep = NewPathwayStep;
}(OpenEyes.UI.Dialog, OpenEyes.Util));
