/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

OpenEyes.OphCiExamination.PreviousSurgeryController = (function() {

    function PreviousSurgeryController(options) {

        this.options = $.extend(true, {}, PreviousSurgeryController._defaultOptions, options);

        //TODO: these should be driven by  options
        this.$section = $('section.' + this.options.modelName);
        this.tableSelector = '#' + this.options.modelName + '_operation_table';
        this.$table = $('#' + this.options.modelName + '_operation_table');
        this.fuzyDateWrapperSelector = this.options.modelName + '_fuzzy_date';

        this.templateText = $("#OEModule_OphCiExamination_models_PastSurgery_operation_template").text();

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

        controller.$section.on('input', ('#'+controller.fuzyDateWrapperSelector), function(e) {
            var $fuzzy_fieldset = $(this).closest('fieldset');
            var date = controller.dateFromFuzzyFieldSet($fuzzy_fieldset);
            $fuzzy_fieldset.closest('td').find('input[type="hidden"]').val(date);
        });

        controller.$section.on('input', ('.'+controller.options.modelName + '_operations'), function(e) {
            var common_operation = $(this).find('option:selected').text();
            $(this).closest('td').find('.common-operation').val(common_operation);
            $(this).val(null);
        });

        controller.$section.on('change', ('.'+controller.options.modelName + '_sides input[type="radio"]'), function(e) {
            $(this).closest('td').find('input[type="hidden"]').val($(this).val());
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

        data['row_count'] = OpenEyes.Util.getNextDataKey( this.tableSelector + ' tbody tr', 'key');

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
     * @TODO: should be common function across history elements
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

    return PreviousSurgeryController;
})();

$(document).ready(function() {
    new OpenEyes.OphCiExamination.PreviousSurgeryController();
});
