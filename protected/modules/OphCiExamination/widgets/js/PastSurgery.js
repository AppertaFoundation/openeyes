/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

OpenEyes.OphCiExamination.PreviousSurgeryController = (function() {

    function PreviousSurgeryController(options) {

        this.options = $.extend(true, {}, PreviousSurgeryController._defaultOptions, options);

        //TODO: these should be driven by  options
        this.$section = $('section.' + this.options.modelName);
        this.$commonOpFld = $('#' + this.options.modelName + '_common_previous_operation');
        this.$opFld = $('#' + this.options.modelName + '_previous_operation');
        this.$sideFld = $('.' + this.options.modelName + '_previous_operation_side');
        this.$dateFieldSet = $('.' + this.options.modelName + '_previousOperation');
        this.$table = $('#' + this.options.modelName + '_operation_table');
        this.$fuzy_date = $('#' + this.options.modelName + '_fuzzy_date');

        this.templateText = $("#OEModule_OphCiExamination_models_PastSurgery_operation_template").text();

        var controller = this;

        // $('#' + this.options.modelName + '_add_previous_operation').on('click', function(e) {
        //     e.preventDefault();
        //     if (controller.validateForm()) {
        //         controller.addOperation();
        //     }
        // });

        // this.$table.on('click', '.button', function(e) {
        //     e.preventDefault();
        //     $(e.target).parents('tr').remove();
        // });

        this.initialiseTriggers();
    }

    PreviousSurgeryController._defaultOptions = {
        modelName: 'OEModule_OphCiExamination_models_PastSurgery',
        monthNames: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
    };

    PreviousSurgeryController.prototype.initialiseTriggers = function(){

        var controller = this;
        $('#' + controller.options.modelName + '_add_entry').on('click', function(e) {
            e.preventDefault();
            controller.addEntry();
        });

        controller.$table.on('click', 'button.remove', function(e) {
            e.preventDefault();
            $(e.target).parents('tr').remove();
        });

        controller.$section.on('input', ('#' + this.options.modelName + '_fuzzy_date'), function(e) {

            var $fuzzy_fieldset = $(this).closest('fieldset');
            var date = controller.dateFromFuzzyFieldSet($fuzzy_fieldset);
            $fuzzy_fieldset.closest('td').find('input[type="hidden"]').val(date);
        });

    };

    /**
     *
     * @param data
     * @returns {*}
     */
    PreviousSurgeryController.prototype.createRow = function(data)
    {
        if (data === undefined)
            data = {};
        data['row_count'] = this.$table.find('tbody tr').length;

        return Mustache.render(
            template = this.templateText,
            data
        );
    };

    /**
     * Add a family history section if its valid.
     */
    PreviousSurgeryController.prototype.addEntry = function()
    {
        var row = this.createRow();
        this.$table.find('tbody').append(row);
    };

    /**
     * @returns {boolean}
     */
    /*PreviousSurgeryController.prototype.validateForm = function()
    {
        if (!this.$commonOpFld.val() && !this.$opFld.val()) {
            new OpenEyes.UI.Dialog.Alert({
                content: "Please select a common operation or type a custom entry."
            }).open();
            return false;
        }
        if (!this.validateFuzzyDateFieldSet(this.$dateFieldSet)) {
            new OpenEyes.UI.Dialog.Alert({
                content: "Please select a valid date for the operation."
            }).open();
            return false;
        }
        return true;
    };*/

    /**
     * @TODO: should set the year back to current year
     */
    /*PreviousSurgeryController.prototype.resetForm = function()
    {
        this.$commonOpFld.find('option:selected').prop('selected', false);
        this.$opFld.val('');
        this.$sideFld.prop('checked', false);
        this.$dateFieldSet.find('select[name="fuzzy_day"] option:selected').prop('selected', false);
        this.$dateFieldSet.find('select[name="fuzzy_month"] option:selected').prop('selected', false);
    };*/

    /**
     * Simple wrapper to generate table row content from the template.
     *
     * @param data
     * @returns {*}
     */
   /* PreviousSurgeryController.prototype.createRow = function(data)
    {
        indices = this.$table.find('tr').map(function () { return $(this).data('index'); });

        data.index = indices.length ? Math.max.apply(null, indices) + 1 : 0;

        return Mustache.render(
            template = this.templateText,
            data
        );
    };*/

    /**
     * Simple validation of selected values for a fuzzy date fieldset.
     *
     * @param fieldset
     * @returns {boolean}
     */
    PreviousSurgeryController.prototype.validateFuzzyDateFieldSet = function(fieldset)
    {
        if (parseInt(fieldset.find('select[name="fuzzy_day"] option:selected').val()) > 0) {
            if (!parseInt(fieldset.find('select[name="fuzzy_month"] option:selected').val()) > 0) {
                return false;
            }
        }
        return true;
    };

    /**
     * @TODO: determine if we need to be smarter about date parsing to submit in the form.
     * @param fieldset
     * @returns {*}
     */
    PreviousSurgeryController.prototype.dateFromFuzzyFieldSet = function(fieldset)
    {
        res = fieldset.find('select.fuzzy_year').val();
        var month = parseInt(fieldset.find('select.fuzzy_month option:selected').val());
        res += '-' + ((month < 10) ? '0' + month.toString() : month.toString());
        var day = parseInt(fieldset.find('select.fuzzy_day option:selected').val());
        res += '-' + ((day < 10) ? '0' + day.toString() : day.toString());

        return res;
    };

    /**
     *
     * @param dt yyyy-mm-dd
     * @returns {string}
     */
    PreviousSurgeryController.prototype.getFuzzyDateDisplay = function(dt)
    {
        var res = [],
            bits = dt.split('-');

        if(bits[2] != '00') {
            res.push(parseInt(bits[2]).toString());
        }

        if(bits[1] != '00') {
            res.push(this.options.monthNames[parseInt(bits[1])-1]);
        }
        res.push(bits[0]);

        return res.join(' ');
    };

    /**
     * Builds the data structure for the table row.
     *
     * @returns {{}}
     */
    PreviousSurgeryController.prototype.generateDataFromForm = function()
    {
        var data = {};
        if (this.$commonOpFld.find('option:selected').val()) {
            data.operation = this.$commonOpFld.find('option:selected').text();
        }
        else {
            data.operation = this.$opFld.val();
        }
        data.side_id = this.$sideFld.filter(':checked').val();
        data.side_display = this.$sideFld.filter(':checked').closest('label').text();
        data.date = this.dateFromFuzzyFieldSet(this.$dateFieldSet);
        data.date_display = this.getFuzzyDateDisplay(data.date);
        return data;
    };

    /**
     * Action method to parse from and create entry.
     */
    PreviousSurgeryController.prototype.addOperation = function()
    {
        // create table row
        var tableRow = this.createRow(this.generateDataFromForm());
        this.$table.append(tableRow);
        // then reset
        this.resetForm();
    };

    return PreviousSurgeryController;
})();

$(document).ready(function() {
    new OpenEyes.OphCiExamination.PreviousSurgeryController();
});
