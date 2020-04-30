/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */


var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

OpenEyes.OphCiExamination.SystemicPreviousSurgeryController = (function () {

    function SystemicPreviousSurgeryController(options) {

        this.options = $.extend(true, {}, SystemicPreviousSurgeryController._defaultOptions, options);

        this.$section = $('section.' + this.options.modelName);
        this.tableSelector = '#' + this.options.modelName + '_operation_table';
        this.$table = $('#' + this.options.modelName + '_operation_table');
        this.$popupSelector = $('#add-to-systemic-past-surgery');
        this.templateText = $("#OEModule_OphCiExamination_models_SystemicSurgery_operation_template").text();
        this.initialiseTriggers();
        this.initialiseDatepicker();
    }

    SystemicPreviousSurgeryController._defaultOptions = {
        modelName: 'OEModule_OphCiExamination_models_SystemicSurgery',
    };
    /**
     * Setup Datepicker
     */
    SystemicPreviousSurgeryController.prototype.initialiseDatepicker = function () {
        let row_count = OpenEyes.Util.getNextDataKey(this.tableSelector + ' tbody tr', 'key');
        for (let i = 0; i < row_count; i++) {
            this.constructDatepicker(i);
        }
    };
    SystemicPreviousSurgeryController.prototype.setDatepicker = function () {
        let row_count = OpenEyes.Util.getNextDataKey(this.tableSelector + ' tbody tr', 'key') - 1;
        this.constructDatepicker(row_count);
    };

    SystemicPreviousSurgeryController.prototype.constructDatepicker = function (line_no) {
        let datepicker_name = '#systemic-past-surgery-datepicker-' + line_no;
        let datepicker = $(this.tableSelector).find(datepicker_name);
        if (datepicker.length != 0) {
            pickmeup(datepicker_name, {
                format: 'Y-m-d',
                hide_on_select: true,
                default_date: false
            });
        }
    };

    SystemicPreviousSurgeryController.prototype.initialiseTriggers = function () {

        let controller = this;
        controller.$popupSelector.on('click', '.add-icon-btn', function (e) {
            e.preventDefault();
            controller.addEntry();
        });

        controller.$table.on('click', '.remove_item', function (e) {
            e.preventDefault();
            $(e.target).parents('tr').remove();
        });

        controller.$section.on('input', ('.' + controller.options.modelName + '_operations'), function () {
            let common_operation = $(this).find('option:selected').text();
            $(this).closest('td').find('.common-operation').val(common_operation);
            $(this).val(null);
        });
        controller.$table.on('click', ('.' + controller.options.modelName + '_previous_operation_side'), function (e) {
            $(e.target).parent().siblings('tr input[type="hidden"]').val($(e.target).val());
        });

        let eye_selector = new OpenEyes.UI.EyeSelector({
            element: controller.$section
        });

        controller.$table.data('eyeSelector', eye_selector);
    };

    /**
     *
     * @param data
     * @returns {*}
     */
    SystemicPreviousSurgeryController.prototype.createRow = function (selectedItems) {
        let newRows = [];
        let template = this.templateText;
        let tableSelector = this.tableSelector;
        $(selectedItems).each(function () {
            let data = {};
            data['row_count'] = OpenEyes.Util.getNextDataKey(tableSelector + ' tbody tr', 'key') + newRows.length;
            data['id'] = this['id'];
            if (this['label'] === 'Other') {
                data['operation'] = '';
            } else {
                data['operation'] = this['label'];
            }
            newRows.push(Mustache.render(
                template,
                data));
        });
        return newRows;

    };

    /**
     * Add a family history section if its valid.
     */
    SystemicPreviousSurgeryController.prototype.addEntry = function (selectedItems) {
        let rows = this.createRow(selectedItems);
        for (let i in rows) {
            this.$table.find('tbody').append(rows[i]);

            let $operation = this.$table.find('tbody tr:last').find('.common-operation');
            if (!$operation.val()) {
                $operation.prop('type', 'text');
            }

            this.setDatepicker();
        }
        this.$popupSelector.find('.selected').removeClass('selected');
    };

    return SystemicPreviousSurgeryController;
})();