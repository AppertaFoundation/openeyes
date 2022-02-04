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
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

(function (exports) {
    function InvestigationsController(options) {
        this.options = $.extend(true, {}, InvestigationsController._defaultOptions, options);
        this.$element = this.options.element;
        this.tableSelector = '#' + this.options.modelName + '_entry_table';
        this.$table = $(this.tableSelector);
        this.templateText = $('#' + this.options.modelName + '_entry_template').text();

        this.registerController();
        this.initialiseTriggers();
        this.initialiseDatepicker();
    }
    InvestigationsController._defaultOptions = {
        modelName: 'OEModule_OphCiExamination_models_Element_OphCiExamination_Investigation',
        element: undefined
    };

    InvestigationsController.prototype.initialiseTriggers = function () {
        this.$table.on('click', 'i.trash', function (e) {
            e.preventDefault();
            $(this).closest('tr').remove();
        });
    };

    InvestigationsController.prototype.createRows = function (investigations = {}) {
        var newRows = [];
        var template = this.templateText;
        var tableSelector = this.tableSelector;
        $(investigations).each(function () {
            var data = {};
            data.row_count = OpenEyes.Util.getNextDataKey( tableSelector + ' tbody tr', 'key')+ newRows.length;
            data.investigation_code = this.id;
            data.investigation_code_name = this.label;
            newRows.push(Mustache.render(
                template,
                data ));
        });
        return newRows;
    };
    InvestigationsController.prototype.addEntry = function (investigations) {
        this.$table.find('tbody').append(this.createRows(investigations));
        $('.flex-item-bottom').find('.selected').removeClass('selected');
        autosize($('.autosize'));
    };

    InvestigationsController.prototype.registerController = function () {
        this.$element.data("controller", this);
    };

    /**
     * Show the table. (useful for when adding a row to an empty and thus hidden table)
     */
    InvestigationsController.prototype.showTable = function () {
        this.$table.show();
    };

    InvestigationsController.prototype.initialiseDatepicker = function () {
        var tableSelector = this.tableSelector;
        let row_count = OpenEyes.Util.getNextDataKey(tableSelector + ' tbody tr', 'key');
        for (let i = 0; i < row_count; i++) {
            let datepicker_name = '#investigation-entry-datepicker-' + i;
            let datepicker = $(this.$table).find(datepicker_name);
            if (datepicker.length != 0) {
                pickmeup(datepicker_name, {
                    format: 'Y-m-d',
                    hide_on_select: true,
                    default_date: false
                });
            }
        }
    };

    exports.InvestigationsController = InvestigationsController;
})(OpenEyes.OphCiExamination);