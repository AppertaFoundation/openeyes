/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

(function (exports) {
    function ClinicOutcomeController(options) {
        this.options = $.extend(true, {}, ClinicOutcomeController._defaultOptions, options);
        this.$element = $('.' + this.options.model_name);
        this.followup_template_text = $('#' + this.options.model_name + '_followup_entry_template').text();
        this.patient_ticket_template_text = $('#' + this.options.model_name + '_patient_ticket_entry_template').text();
        this.entry_table_selector = '#' + this.options.model_name + '_entry_table';
        this.status_options_selector = '#followup-outcome-options li';
        this.$status_options = $(this.status_options_selector);
        this.period_options_selector = '#follow-up-period-options li';
        this.$period_options = $(this.period_options_selector);
        this.role_options_selector = '#follow-up-role-options li';
        this.$role_options = $(this.role_options_selector);
        this.quantity_options_selector = '#follow-up-quantity-options li';
        this.$quantity_options = $(this.quantity_options_selector);

        this.initialiseTriggers();
    }

    ClinicOutcomeController._defaultOptions = {
        model_name: 'OEModule_OphCiExamination_models_Element_OphCiExamination_ClinicOutcome',
    };

    ClinicOutcomeController.prototype.initialiseTriggers = function () {
        let controller = this;

        this.$status_options.on('click', function () {
            let is_followup_entry = !!$(this).data('followup');
            $('.follow-up-options-follow-up-only').toggle(is_followup_entry);
        });

        $(this.entry_table_selector).on('click', 'i.trash', function (e) {
            e.preventDefault();
            controller.deleteRow($(this).closest('tr'));
        });
    };

    ClinicOutcomeController.prototype.onAdderDialogReturn = function () {
        let $selected_status = $(this.status_options_selector + '.selected');
        let $selected_period = $(this.period_options_selector + '.selected');
        let $selected_role = $(this.role_options_selector + '.selected');
        let selected_quantity = $(this.quantity_options_selector + '.selected').data('quantity');

        if (this.validateInputs($selected_status, $selected_period, $selected_role, selected_quantity)) {
            this.createRow($selected_status, $selected_period, $selected_role, selected_quantity);
            $('#followup_comments').val('');
        }
    };

    ClinicOutcomeController.prototype.validateInputs = function ($selected_status, $selected_period, $selected_role, selected_quantity) {
        let alert_message = '';
        let validation_passed = true;
        if(!$selected_status.length) {
            alert_message = "Please select a status";
            validation_passed = false;
        }
        if ($selected_status.data('followup') && (!$selected_period.length || !$selected_role.length || typeof selected_quantity === "undefined")) {
           alert_message = "Please select a value for quantity, period and role.";
            validation_passed = false;
        }

        if(!validation_passed) {
            $('#add-to-follow-up').show();
            new OpenEyes.UI.Dialog.Alert({
                content: alert_message
            }).open();
            return false;
        }

        return true;
    };

    ClinicOutcomeController.prototype.createRow = function ($selected_status, $selected_period, $selected_role, selected_quantity) {
        let data = {};

        data.row_count = OpenEyes.Util.getNextDataKey(this.entry_table_selector + ' tbody.entries tr', 'key');
        data.condition_text = data.row_count ? "AND" : "";
        data.status_id = $selected_status.data('id');
        data.status = $selected_status.data('label');
        let template = $selected_status.data('patient-ticket') ? this.patient_ticket_template_text : this.followup_template_text;
        if ($selected_status.data('followup')){
            data.followup_quantity = selected_quantity;
            data.followup_period_id = $selected_period.data('period-id');
            data.followup_period = $selected_period.data('label');
            let comments = $('#followup_comments').val();
            data.followup_comments = comments;
            data.followup_comments_display = comments !== '' ? ' ('+ comments + ')' : null;
            data.role_id = $selected_role.data('role-id');
            data.role = ' with ' + $selected_role.data('label');
        }

        this.hideUniqueOptions($selected_status);
        this.addRow(template, data);
    };

    ClinicOutcomeController.prototype.addRow = function(template, data) {
        $(this.entry_table_selector + ' tbody.entries').append(Mustache.render(template, data));
        this.resetAdderDialog();
    };

    ClinicOutcomeController.prototype.deleteRow = function ($row) {
        if (inArray($row.data('status'), $('#pt_status_list').data('statuses'))) {
            this.$status_options.each(function () {
                $(this).show();
            });
        }

        let next_row = $(this.entry_table_selector + ' tr:nth-child(2) td:first-child');
        // if the row to be deleted is the first row and there is a second row
        // then remove the 'AND' text at the front of the second row
        if ($row.is('tr:first-child') && next_row.length > 0) {
            next_row
            .contents()
            .filter(function(){
                // only the text needs to be removed
                return this.nodeType === Node.TEXT_NODE;
            }).remove();
        }

        $row.remove();
    };

    ClinicOutcomeController.prototype.hideUniqueOptions = function ($selected_status) {
        if (inArray($selected_status.data('id'), $('#pt_status_list').data('statuses'))) {
            $selected_status.hide();
        }
    };

    ClinicOutcomeController.prototype.resetAdderDialog = function () {
        this.$status_options.add(this.$quantity_options).add(this.$period_options).add(this.$role_options).each(function () {
            $(this).removeClass('selected');
        });
        $('.follow-up-options-follow-up-only').each(function () {
            $(this).hide();
        });
    };

    exports.ClinicOutcomeController = ClinicOutcomeController;

})(OpenEyes.OphCiExamination);

$(document).ready(function () {
    let controller = new OpenEyes.OphCiExamination.ClinicOutcomeController();
    $(controller.$element).data('controller', controller);
});