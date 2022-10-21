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

        this.discharge_status_options_selector = '#discharge-status-options li';
        this.destination_options_selector = '#discharge-destination-options li';
        this.$destination_options = $(this.destination_options_selector);
        this.transfer_to_search_selector = '#transfer-to-search';
        this.transfer_to_options_selector = '#discharge-transfer-to-options li';
        this.period_options_selector = '#follow-up-period-options li';
        this.$period_options = $(this.period_options_selector);
        this.role_options_selector = '#follow-up-role-options li';
        this.$role_options = $(this.role_options_selector);
        this.quantity_options_selector = '#follow-up-quantity-options li';
        this.$quantity_options = $(this.quantity_options_selector);
        // risk status column items selector
        this.risk_status_options_selector = '#follow-up-risk-status-options li';
        this.$risk_status_options = $(this.risk_status_options_selector);

        this.subspecialty_options_selector = '#followup-outcome-subspecialty_options li';
        this.site_options_selector = '#follow-up-site-options li';
        this.context_options_selector = '#follow-up-context-options li';


        this.initialiseTriggers();
    }

    ClinicOutcomeController._defaultOptions = {
        model_name: 'OEModule_OphCiExamination_models_Element_OphCiExamination_ClinicOutcome',
    };

    ClinicOutcomeController.prototype.initialiseTriggers = function () {
        let controller = this;

        $(this.transfer_to_search_selector).on('keyup', function () {
            // Get all institutions matching the specified search term.
            let search_term = $(this).val();
            if (this.institutionRequest !== null && this.institutionRequest !== undefined) {
                this.institutionRequest.abort();
                this.institutionRequest = null;
            }
            $('#discharge-transfer-to-options').html('');
            this.institutionRequest = $.get('/' + OE_module_name + '/default/searchInstitutions?term=' + search_term, {}, function (response) {
                $('#discharge-transfer-to-options').html(response);
            });
        });

        this.$status_options.on('click', function () {
            let is_followup_entry = !!$(this).data('followup');
            $('.follow-up-options-follow-up-only').toggle(is_followup_entry);
            let is_discharge_entry = !!$(this).data('discharge');
            $('.follow-up-options-discharge-only').toggle(is_discharge_entry);
            $('.follow-up-options-discharge-transfer-only').hide();
        });


        this.$destination_options.on('click', function () {
            let institution_required = !!$(this).data('institution-required');
            $('.follow-up-options-discharge-transfer-only').toggle(institution_required);
        });

        $(this.entry_table_selector).on('click', 'i.trash', function (e) {
            e.preventDefault();
            controller.deleteRow($(this).closest('tr'));
        });

        $(this.subspecialty_options_selector).on('click', (e) => {
            e.preventDefault();
            let context_options_selector = '#follow-up-context-options';

            let subspecialty_id = $(e.target).data('subspecialty_id');
            let contexts = (this.getContextFromService(subspecialty_id));
            let html = '';

            if(contexts){
                $(contexts).each( (i,obj) => {
                    html += `<li data-context_id="${obj.id}" data-label="${obj.name}" ${obj.id == 0 ? 'class="selected"' : ''}>${obj.name}</li>`;
                });
            } else {
                html = '<li class="disabled">- empty set -</li>';
            }

            $(context_options_selector).html(html);
        });
    };

    ClinicOutcomeController.prototype.getContextFromService = function(subspecialty_id) {
        let contexts = $(jQuery.parseJSON( this.options.contexts ));
        let result;
        contexts.each( (idx, i) => {
            if (i.id == subspecialty_id) {
                result = i.contexts;
                return;
            }
        });
        result.unshift({id: 0, name: "N/A", selected: true});
        return result;
    };

    ClinicOutcomeController.prototype.onAdderDialogReturn = function () {
        let $selected_status = $(this.status_options_selector + '.selected');
        let $selected_discharge_status = $(this.discharge_status_options_selector + '.selected');
        let $selected_discharge_destination = $(this.destination_options_selector + '.selected');
        let $selected_transfer_to = $(this.transfer_to_options_selector + '.selected');
        let $selected_period = $(this.period_options_selector + '.selected');
        let $selected_role = $(this.role_options_selector + '.selected');
        let selected_quantity = $(this.quantity_options_selector + '.selected').data('quantity');

        let $selected_site = $(this.site_options_selector + '.selected');
        let $selected_subspecialty = $(this.subspecialty_options_selector + '.selected');
        let $selected_context = $(this.context_options_selector + '.selected');

        if (this.validateInputs(
            $selected_status,
            $selected_period,
            $selected_role,
            selected_quantity,
            $selected_discharge_status,
            $selected_discharge_destination,
            $selected_transfer_to,
            $selected_site,
            $selected_subspecialty,
            $selected_context
        )) {
            this.createRow(
                $selected_status,
                $selected_period,
                $selected_role,
                selected_quantity,
                $selected_discharge_status,
                $selected_discharge_destination,
                $selected_transfer_to,
                $selected_site,
                $selected_subspecialty,
                $selected_context
            );
            $('#followup_comments').val('');
        }
    };

    ClinicOutcomeController.prototype.validateInputs = function (
        $selected_status,
        $selected_period,
        $selected_role,
        selected_quantity,
        $selected_discharge_status,
        $selected_discharge_destination,
        $selected_transfer_to,

        $selected_site,
        $selected_subspecialty,
        $selected_context
    ) {
        let alert_message = '';
        let validation_passed = true;
        if (!$selected_status.length) {
            alert_message = "Please select a status";
            validation_passed = false;
        }

        if ($selected_status.data('followup') && (!$selected_period.length || !$selected_role.length || typeof selected_quantity === "undefined")) {
            alert_message = "Please select a value for quantity, period and role.";
            validation_passed = false;
        }

        if ($selected_status.data('discharge')) {
            if (!$selected_discharge_destination.length || !$selected_discharge_status.length) {
                alert_message = "Please select a value for discharge status and destination.";
                validation_passed = false;
            }
            if ($selected_discharge_destination.data('institution-required') && !$selected_transfer_to.length) {
                alert_message = "Please select an institution.";
                validation_passed = false;
            }
        }

        if (!validation_passed) {
            $('#add-to-follow-up').show();
            new OpenEyes.UI.Dialog.Alert({
                content: alert_message
            }).open();
            return false;
        }

        return true;
    };

    ClinicOutcomeController.prototype.createRow = function (
        $selected_status,
        $selected_period,
        $selected_role,
        selected_quantity,
        $selected_discharge_status,
        $selected_discharge_destination,
        $selected_transfer_to,
        $selected_site,
        $selected_subspecialty,
        $selected_context
    ) {
        let data = {};

        data.row_count = OpenEyes.Util.getNextDataKey(this.entry_table_selector + ' tbody.entries tr', 'key');
        data.condition_text = data.row_count ? "AND" : "";
        data.status_id = $selected_status.data('id');
        data.status = $selected_status.data('label');

        let template = $selected_status.data('patient-ticket') ? this.patient_ticket_template_text : this.followup_template_text;

        if ($selected_status.data('followup')) {
            let $selected_risk_status = $(this.risk_status_options_selector + '.selected');
            let selected_risk_status_class = $selected_risk_status.find('.js-risk-status-details').attr('class');
            selected_risk_status_class += ' js-has-tooltip';

            data.followup_quantity = selected_quantity;
            data.followup_period_id = $selected_period.data('period-id');
            data.followup_period = $selected_period.data('label');
            let comments = $('#followup_comments').val();
            data.followup_comments = comments;
            data.followup_comments_display = comments !== '' ? ' (' + comments + ')' : null;
            data.role_id = $selected_role.data('role-id');
            data.role = ' with ' + $selected_role.data('label');

            data.risk_status_id = $selected_risk_status.data('risk-status-id');
            data.risk_status_class = selected_risk_status_class;
            data.risk_status_content = $selected_risk_status.data('display');

            data.site_id = $selected_site.data('site_id');
            data.site = $selected_site.data('label');
            data.subspecialty_id = $selected_subspecialty.data('subspecialty_id');
            data.subspecialty = $selected_subspecialty.data('label');
            data.context_id = $selected_context.data('context_id');
            data.context = $selected_context.data('label');

        } else if ($selected_status.data('discharge')) {
            data.discharge_status_id = $selected_discharge_status.data('discharge-status-id');
            data.discharge_status = $selected_discharge_status.data('label');
            data.discharge_destination_id = $selected_discharge_destination.data('discharge-destination-id');
            data.discharge_destination = $selected_discharge_destination.data('label');
            data.transfer_institution_id = $selected_transfer_to.length > 0 ? $selected_transfer_to.data('transfer-institution-id') : null;
            data.transfer_to = $selected_transfer_to.length > 0 ? (' (' + $selected_transfer_to.data('label') + ')') : null;
        }

        this.hideUniqueOptions($selected_status);
        this.addRow(template, data);
    };

    ClinicOutcomeController.prototype.addRow = function (template, data) {
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
                .filter(function () {
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