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
    function PupillaryAbnormalitiesController(options) {
        this.options = $.extend(true, {}, PupillaryAbnormalitiesController._defaultOptions, options);
        this.$element = this.options.element;
        this.template_text = $('#' + this.options.model_name + '_entry_template').text();
        this.abnormality_selector = '[name$="[abnormality_id]"]';
        this.no_abnormalities_selector = '[name$="no_pupillaryabnormalities]"]';
        this.no_abnormalities = $(this.no_abnormalities_selector);
        this.entry_table_selector = '.pa-entry-table';
        this.entry_table = $(this.entry_table_selector);
        this.adder_btn = '#add-abnormality-btn-';
        this.no_abnormalities_wrapper = '#' + this.options.model_name + '_no_abnormalities_wrapper_';

        this.initialiseTriggers();
    }

    PupillaryAbnormalitiesController._defaultOptions = {
        model_name: 'OEModule_OphCiExamination_models_PupillaryAbnormalities',
        element: undefined,
        abnormality_not_checked_value: "-9",
        abnormality_no_value: "0",
        abnormality_yes_value: "1",
    };

    PupillaryAbnormalitiesController.prototype.initialiseTriggers = function () {
        let controller = this;

        $('.side').each(function () {
            let side = $(this).attr('data-side');
            let table = $('.' + side + '-eye ' + controller.entry_table_selector);

            if ($('#' + controller.options.model_name + '_' + side + '_no_pupillaryabnormalities').prop('checked')) {
                table.find('tr:not(:first-child)').hide();
                controller.setRadioButtonsToNo(table);
                $(this.adder_btn + side).hide();
            }
        });

        this.no_abnormalities.on('click', function () {
            let side = $(this).closest('.side').attr('data-side');
            let table = $('.' + side + '-eye ' + controller.entry_table_selector);

            if ($(this).prop('checked')) {
                table.find('tr').hide();
                $(controller.adder_btn + side).hide();
                controller.setRadioButtonsToNo(table);
            } else {
                $(controller.adder_btn + side).show();
                controller.dedupeAbnormalitiesSelector(side);
                table.find('tr').show();
                $(this).removeAttr('checked');
                table.find('input[type=radio]').removeAttr('checked');
            }
        });

        this.entry_table.on('click', 'i.trash', function (e) {
            let side = $(this).closest('.side').attr('data-side');

            $(this).closest('tr').remove();
            controller.dedupeAbnormalitiesSelector(side);
            controller.updateNoAbnormality(side);
            e.preventDefault();
        });

        this.entry_table.on('change', 'input[type=radio]', function () {
            let side = $(this).closest('.side').attr('data-side');
            controller.updateNoAbnormality(side);
        });
    };

    PupillaryAbnormalitiesController.prototype.setRadioButtonsToNo = function (table) {
        table.find('input[type=radio]').each(function () {
            if ($(this).val() === "0") {
                $(this).prop('checked', true);
            }
        });
    };

    PupillaryAbnormalitiesController.prototype.dedupeAbnormalitiesSelector = function (side) {
        let abnormality_selector = this.abnormality_selector;
        let selected_abnormalities = [];

        $('.' + side + '-eye ' + abnormality_selector).each(function () {
            let value = this.getAttribute('value');
            selected_abnormalities.push(value);
        });

        $('ul[data-id="pupillary_abnormalities_list_' + side + '"] li').each(function () {
            if (inArray(this.getAttribute('data-id'), selected_abnormalities)) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
    };

    PupillaryAbnormalitiesController.prototype.createRows = function (selected_items, table_selector) {
        let newRows = [];
        let side = $(table_selector.split(' ')[0]).attr('data-side');
        let eye_id = (side === "left") ? 1 : 2;
        let template = this.template_text;

        $(selected_items).each(function () {
            let data = {};
            data.side = side;
            data.row_count = OpenEyes.Util.getNextDataKey(table_selector + ' tbody tr', 'key') + newRows.length;
            data.abnormality_id = this.id;
            data.abnormality_display = this.label;
            data.eye_id = eye_id;
            newRows.push(Mustache.render(template, data));
        });
        return newRows;
    };

    PupillaryAbnormalitiesController.prototype.addEntry = function (table_selector, selected_items) {
        let side = $(table_selector.split(' ')[0]).attr('data-side');

        $(table_selector + ' tbody').append(this.createRows(selected_items, table_selector));
        $('.flex-item-bottom').find('.selected').removeClass('selected');

        this.updateNoAbnormality(side);
        this.dedupeAbnormalitiesSelector(side);
    };

    PupillaryAbnormalitiesController.prototype.isAbnormalitiesChecked = function (value, side){
        let value_checked = false;
        let table = $('.' + side + '-eye ' + this.entry_table_selector);

        table.find('input[type=radio]:checked , input[type=hidden][id$="has_abnormality"]').each(function () {
            if ($(this).val() === value) {
                value_checked = true;
                return false;
            }
        });
        return value_checked;
    };

    PupillaryAbnormalitiesController.prototype.updateNoAbnormality = function (side) {
        let wrapper = $(this.no_abnormalities_wrapper + side);
        let field = $('#' + this.options.model_name + '_' + side + '_no_pupillaryabnormalities');

        if (wrapper.prop('checked')) {
            field.prop('checked', false);
            $(this.adder_btn + side).show();
        }
        if(this.isAbnormalitiesChecked(this.options.abnormality_yes_value, side)){
            wrapper.hide();
            $(this.adder_btn + side).show();
            field.prop('checked', false);
        } else {
            wrapper.show();
        }
    };

    exports.PupillaryAbnormalitiesController = PupillaryAbnormalitiesController;

})(OpenEyes.OphCiExamination);

$(document).ready(function () {
    let controller = new OpenEyes.OphCiExamination.PupillaryAbnormalitiesController();
    $('.OEModule_OphCiExamination_models_PupillaryAbnormalities').data('controller', controller);
});