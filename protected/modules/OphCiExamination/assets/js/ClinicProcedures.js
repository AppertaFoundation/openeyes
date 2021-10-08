/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

OpenEyes.OphCiExamination.ClinicProceduresController = (function (){
    let row_count = 0;

    function ClinicProceduresController(options) {
        this.options = $.extend(true, {}, ClinicProceduresController._defaultOptions, options);
        this.tableSelector = '#' + this.options.modelName + '_table';
        this.$table = $('#' + this.options.modelName + '_table');
        this.$popupSelector = $('#add-clinic-procedures');
        this.templateText = $('#' + this.options.modelName + '_template').text();
        this.$pastProcedures = $('#past_clinic_procedures');
        this.initialiseTriggers();
        this.initialiseDatepicker();
    }

    ClinicProceduresController._defaultOptions = {
        modelName: 'OEModule_OphCiExamination_models_Element_OphCiExamination_ClinicProcedures',
    };

    ClinicProceduresController.prototype.initialiseDatepicker = function () {
        let row = row_count + 1;
        for (let i = 0; i < row; i++) {
            this.constructDatepicker(i);
        }
    };

    ClinicProceduresController.prototype.setDatepicker = function () {
        this.constructDatepicker(row_count);
    };

    ClinicProceduresController.prototype.constructDatepicker = function (line_no) {
        let datepicker_name = '#clinic-procedures-datepicker-' + line_no;
        let datepicker = $(this.tableSelector).find(datepicker_name);
        if (datepicker.length !== 0) {
            pickmeup(datepicker_name, {
                format: 'Y-m-d',
                hide_on_select: true,
                default_date: new Date(),
            });
        }
    };

    ClinicProceduresController.prototype.setCurrentTime = function () {
        let date = new Date();
        let hours = date.getHours();
        let minutes = ('0' + date.getMinutes()).slice(-2); // This handles instances where the minute value is <10 as a leading 0 is required.
        return hours + ':' + minutes;
    };

    ClinicProceduresController.prototype.initialiseTriggers = function () {
        let controller = this;
        row_count = 0;

        controller.$table.on('click', '.remove_item', function (e) {
            e.preventDefault();
            $(e.target).parents('tr').remove();
        });

        controller.$table.on('click', '.js-add-comments', function (e) {
            e.preventDefault();
            $(this).hide();
            $(this).next('.js-input-comments').show();
        });

        controller.$table.on('click', '.js-remove-add-comments', function (e) {
            e.preventDefault();
            $(e.target).prev('textarea').val(null);
            $(e.target).parents('.js-input-comments').hide();
            $(e.target).parents('.js-input-comments').prev().show();
        });

        controller.$pastProcedures.on('click', '.collapse-data-header-icon', function (e) {
            if ($(this).hasClass('expand')) {
                $(this).removeClass('expand');
                $(this).addClass('collapse');
                controller.$pastProcedures.find('.collapse-data-content').show();
            } else {
                $(this).removeClass('collapse');
                $(this).addClass('expand');
                controller.$pastProcedures.find('.collapse-data-content').hide();
            }
        });
    };

    ClinicProceduresController.prototype.createRow = function (selectedItems) {
        let newRows = [];
        let template = this.templateText;
        let row = row_count;
        let time = this.setCurrentTime();
        $(selectedItems).each(function () {
            let data = {};
            data['row_count'] = row;
            row += 1;
            data['procedure_id'] = this['id'];
            data['procedure'] = this['label'];
            data['outcome_time'] = time;
            newRows.push(Mustache.render(template, data));
        });
        return newRows;
    };

    ClinicProceduresController.prototype.addEntry = function (selectedItems) {
        let rows = this.createRow(selectedItems);
        for (let i in rows) {
            this.$table.find('tbody').append(rows[i]);

            let $operation = this.$table.find('tbody tr:last');
            if (!$operation.val()) {
                $operation.prop('type', 'text');
            }

            this.setDatepicker();
            row_count += 1;
        }
        this.$popupSelector.find('.selected').removeClass('selected');
    };

    return ClinicProceduresController;
})();
