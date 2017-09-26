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

(function(exports) {

    function SystemicDiagnosesController(options) {
        this.options = $.extend(true, {}, SystemicDiagnosesController._defaultOptions, options);

        this.$element = this.options.element;
        this.$table = this.$element.find('table');
        this.templateText = this.$element.find('.entry-template').text();

        this.initialiseTriggers();
    }

    SystemicDiagnosesController._defaultOptions = {
        modelName: 'OEModule_OphCiExamination_models_SystemicDiagnoses',
        element: undefined,
        addButtonSelector: '.add-entry',
        searchSource: '/medication/finddrug'
    }

    SystemicDiagnosesController.prototype.initialiseTriggers = function()
    {
        var controller = this;

        // removal button for table entries
        controller.$table.on('click', '.button.remove', function(e) {
            e.preventDefault();
            $(e.target).parents('tr').remove();
        });

        // setup current table row behaviours
        controller.$table.find('tbody tr').each(function() {
            controller.initialiseRow($(this));
        });

        // adding entries
        controller.$element.on('click', controller.options.addButtonSelector, function(e) {
            e.preventDefault();
            controller.addEntry();
        });
    }

    SystemicDiagnosesController.prototype.initialiseRow = function($row)
    {
        var controller = this;
        var DiagnosesSearchController = null;
        var $radioButtons = $row.find('.sides-radio-group');

        $row.on('change', '.fuzzy-date select', function(e) {
            var $fuzzyFieldset = $(this).closest('fieldset');
            var date = controller.dateFromFuzzyFieldSet($fuzzyFieldset);
            $fuzzyFieldset.find('input[type="hidden"]').val(date);
        });

        DiagnosesSearchController = new OpenEyes.UI.DiagnosesSearchController({
            'inputField': $row.find('.diagnoses-search-autocomplete'),
            'fieldPrefix': $row.closest('section').data('element-type-class')
        });
        $row.find('.diagnoses-search-autocomplete').data('DiagnosesSearchController', DiagnosesSearchController );

        // radio buttons
        $radioButtons.on('change', 'input', function(){
            $(this).closest('tr').find('.diagnosis-side-value').val( $(this).val() );
        });
    }

    SystemicDiagnosesController.prototype.dateFromFuzzyFieldSet = function(fieldset)
    {
        var res = fieldset.find('select.fuzzy_year').val();
        var month = parseInt(fieldset.find('select.fuzzy_month option:selected').val());
        res += '-' + ((month < 10) ? '0' + month.toString() : month.toString());
        var day = parseInt(fieldset.find('select.fuzzy_day option:selected').val());
        res += '-' + ((day < 10) ? '0' + day.toString() : day.toString());

        return res;
    };

    SystemicDiagnosesController.prototype.createRow = function(data)
    {
        if (data === undefined)
            data = {};

        data['row_count'] = OpenEyes.Util.getNextDataKey( this.$element.find('table tbody tr'), 'key');
        return Mustache.render(
            this.templateText,
            data
        );
    };

    SystemicDiagnosesController.prototype.addEntry = function()
    {
        var row = this.createRow();
        this.$table.find('tbody').append(row);
        this.initialiseRow(this.$table.find('tbody tr:last'));
    };

    exports.SystemicDiagnosesController = SystemicDiagnosesController;
})(OpenEyes.OphCiExamination);
