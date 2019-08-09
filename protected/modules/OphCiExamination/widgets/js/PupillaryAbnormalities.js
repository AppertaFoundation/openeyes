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

(function (exports) {
    function PupillaryAbnormalitiesController(options) {
        this.options = $.extend(true, {}, PupillaryAbnormalitiesController._defaultOptions, options);
        this.$element = this.options.element;
        this.templateText = $('#' + this.options.modelName + '_entry_template').text();
        this.abnormalitySelector = ' [name$="[abnormality_id]"]';
        this.noAbnormalitiesSelector = '[name$="no_pupillaryabnormalities]"]';
        this.noAbnormalities = $(this.noAbnormalitiesSelector);
        this.entryTableSelector = '.pa-entry-table';
        this.entryTable = $(this.entryTableSelector);
        this.adderBtn = '#add-abnormality-btn-';
        this.noAbnormalitiesWrapper = '#' + this.options.modelName + '_no_abnormalities_wrapper_';

        this.initialiseTriggers();
        this.dedupeAbnormalitiesSelector('.left-eye');
        this.dedupeAbnormalitiesSelector('.right-eye');
    }

    PupillaryAbnormalitiesController._defaultOptions = {
        modelName: 'OEModule_OphCiExamination_models_PupillaryAbnormalities',
        element: undefined,
        abnormalityNotCheckedValue: "-9",
        abnormalityNoValue: "0",
        abnormalityYesValue: "1",
    };

    PupillaryAbnormalitiesController.prototype.initialiseTriggers = function () {
        var controller = this;

        $(document).ready(function () {

            $('.side').each(function () {
                var side = $(this).attr('data-side');
                var table = $('.' + side + '-eye ' + controller.entryTableSelector);

                if ($('#' + controller.options.modelName + '_' + side + '_no_pupillaryabnormalities').prop('checked')) {
                    table.find('tr:not(:first-child)').hide();
                    controller.setRadioButtonsToNo(table);
                    $(this.adderBtn + side).hide();
                }
            });
        });

        this.noAbnormalities.on('click', function (e) {
            var side = $(this).closest('.side').attr('data-side');
            var table = $('.' + side + '-eye ' + controller.entryTableSelector);

            if ($(this).prop('checked')) {
                table.find('tr:not(:first-child)').hide();
                $(controller.adderBtn + side).hide();
                controller.setRadioButtonsToNo(table);
            } else {
                $(controller.adderBtn + side).show();
                controller.dedupeAbnormalitiesSelector('.' + side + '-eye');
                table.find('tr:not(:first-child)').show();
                $(this).removeAttr('checked');
                table.find('input[type=radio]').removeAttr('checked');
            }
        });

        this.entryTable.on('click', 'i.trash', function (e) {
            var side = $(this).closest('.side').attr('data-side');

            $(this).closest('tr').remove();
            controller.dedupeAbnormalitiesSelector('.' + side + '-eye');
            controller.updateNoAbnormality(side);
            e.preventDefault();
        });

        this.entryTable.on('change', 'input[type=radio]', function () {
            var side = $(this).closest('.side').attr('data-side');
            controller.updateNoAbnormality(side);
        });
    };

    PupillaryAbnormalitiesController.prototype.setRadioButtonsToNo = function (table) {
        console.log(table.find('input[type=radio]'));
        table.find('input[type=radio]').each(function () {
            if ($(this).val() === "0") {
                $(this).prop('checked', true);
            }
        });
    };

    PupillaryAbnormalitiesController.prototype.dedupeAbnormalitiesSelector = function (side) {
        var abnormalitySelector = this.abnormalitySelector;
        var selectedAbnormalities = [];

        $(side + abnormalitySelector).each(function () {
            var value = this.getAttribute('value');
            selectedAbnormalities.push(value);
        });

        $(side + ' li').each(function () {
            if (inArray(this.getAttribute('data-id'), selectedAbnormalities)) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
    };

    PupillaryAbnormalitiesController.prototype.createRows = function (selectedItems, tableSelector) {
        var newRows = [];
        var side = $(tableSelector.split(' ')[0]).attr('data-side');
        var eye_id = (side === "left") ? 1 : 2;
        var template = this.templateText;

        $(selectedItems).each(function () {
            var data = {};
            data.side = side;
            data.row_count = OpenEyes.Util.getNextDataKey(tableSelector + ' tbody tr', 'key') + newRows.length;
            data.abnormality_id = this.id;
            data.abnormality_display = this.label;
            data.eye_id = eye_id;
            newRows.push(Mustache.render(template, data));
        });
        return newRows;
    };

    PupillaryAbnormalitiesController.prototype.addEntry = function (tableSelector, selectedItems) {
        var side = $(tableSelector.split(' ')[0]).attr('data-side');

        $(tableSelector + ' tbody').append(this.createRows(selectedItems, tableSelector));
        $('.flex-item-bottom').find('.selected').removeClass('selected');

        this.updateNoAbnormality(side);
        this.dedupeAbnormalitiesSelector('.' + side + '-eye');
    };

    PupillaryAbnormalitiesController.prototype.isAbnormalitiesChecked = function (value, side){
        var valueChecked = false;
        var table = $('.' + side + '-eye ' + this.entryTableSelector);

        table.find('input[type=radio]:checked , input[type=hidden][id$="has_abnormality"]').each(function () {
            if ($(this).val() === value) {
                valueChecked = true;
                return false;
            }
        });
        return valueChecked;
    };

    PupillaryAbnormalitiesController.prototype.updateNoAbnormality = function (side) {
        var wrapper = $(this.noAbnormalitiesWrapper + side);
        var field = $('#' + this.options.modelName + '_' + side + '_no_pupillaryabnormalities');

        if (wrapper.prop('checked')) {
            field.prop('checked', false);
            $(this.adderBtn + side).show();
        }
        if(this.isAbnormalitiesChecked(this.options.abnormalityYesValue, side)){
            wrapper.hide();
            $(this.adderBtn + side).show();
            field.prop('checked', false);
        } else {
            wrapper.show();
        }
    };

    exports.PupillaryAbnormalitiesController = PupillaryAbnormalitiesController;

})(OpenEyes.OphCiExamination);