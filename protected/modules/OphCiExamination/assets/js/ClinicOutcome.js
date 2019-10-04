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
        this.$element = this.options.element;
        this.followup_template_text = $('#' + this.options.model_name + '_followup_entry_template').text();
        this.patient_ticket_template_text = $('#' + this.options.model_name + '_patient_ticket_entry_template').text();
        this.entry_table_selector = '#' + this.options.model_name + '_entry_table';

        this.initialiseTriggers();
    }

    ClinicOutcomeController._defaultOptions = {
        model_name: 'OEModule_OphCiExamination_models_Element_OphCiExamination_ClinicOutcome',
        element: undefined,
    };

    ClinicOutcomeController.prototype.initialiseTriggers = function () {
        let controller = this;

        $('#followup-outcome-options li').on('click', function () {
            let followup = !!$(this).data('followup');
            $('.follow-up-options-follow-up-only').toggle(followup);
        });

        $(this.entry_table_selector).on('click', 'i.trash', function (e) {
            e.preventDefault();
            controller.deleteRow($(this).closest('tr'));
        });
    };

    ClinicOutcomeController.prototype.addRow = function () {
        let $options = $('#followup-outcome-options li.selected');
        let $period = $('#follow-up-period-options li.selected');
        let $role = $('#follow-up-role-options li.selected');
        let data = {};

        data.row_count = OpenEyes.Util.getNextDataKey(this.entry_table_selector + ' tbody.entries tr', 'key');
        data.condition_text = data.row_count ? "AND" : "";
        data.status_id = $options.data('id');
        data.status = $options.data('label');
        data.followup = $options.data('followup');
        data.patient_ticket = $options.data('patient-ticket');
        let template = data.patient_ticket ? this.patient_ticket_template_text : this.followup_template_text;
        if (data.followup){
            data.followup_quantity = $('#follow-up-quantity-options li.selected').data('quantity');
            data.followup_period_id = $period.data('period-id');
            data.followup_period = $period.data('label');
            let comments = $('#followup_comments').val();
            data.followup_comments = comments;
            data.followup_comments_display = comments !== '' ? ' ('+ comments + ')' : null;
            data.role_id = $role.data('role-id');
            data.role = ' with ' + $role.data('label');
        }

        //only one Virtual Review can be created
        if (inArray(data.status_id, $('#pt_status_list').data('statuses'))) {
            $('#followup-outcome-options li.selected').hide();
        }

        $(this.entry_table_selector + ' tbody.entries').append(Mustache.render(template, data));
        $('#followup-outcome-options li').each(function () {
            $(this).removeClass('selected');
        });
    };

    ClinicOutcomeController.prototype.deleteRow = function ($row) {
        if (inArray($row.data('status'), $('#pt_status_list').data('statuses'))) {
            $('#followup-outcome-options li').each(function () {
                $(this).show();
            });
        }

        $row.remove();
    };

    exports.ClinicOutcomeController = ClinicOutcomeController;

})(OpenEyes.OphCiExamination);

$(document).ready(function () {
    let controller = new OpenEyes.OphCiExamination.ClinicOutcomeController();
    $('.OEModule_OphCiExamination_models_Element_OphCiExamination_ClinicOutcome').data('controller', controller);
});