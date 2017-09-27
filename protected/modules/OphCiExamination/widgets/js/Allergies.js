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

(function(exports) {
    function AllergiesController(options) {
        this.options = $.extend(true, {}, AllergiesController._defaultOptions, options);

        this.$noAllergiesWrapper = $('#' + this.options.modelName + '_no_allergies_wrapper');
        this.$noAllergiesFld = $('#' + this.options.modelName + '_no_allergies');

        this.$allergySelect = $('.' + this.options.modelName + '_allergy_id');
        this.$other = $('#' + this.options.modelName + '_other');
        this.otherWrapperSelector = '.' + this.options.modelName + '_other_wrapper';
        this.$commentFld = $('#' + this.options.modelName + '_comments');

        this.tableSelector = '#' + this.options.modelName + '_entry_table';
        this.$table = $(this.tableSelector);
        this.templateText = $('#' + this.options.modelName + '_entry_template').text();

        this.initialiseTriggers();

    }

    AllergiesController._defaultOptions = {
        modelName: 'OEModule_OphCiExamination_models_Allergies'
    };

    AllergiesController.prototype.initialiseTriggers = function()
    {
        var controller = this;
        controller.$table.on('change', '.other', function(e) {
            var $selected = $(this).find('option:selected');

            if ($selected.data('other')) {
                $(this).closest('td').find(controller.otherWrapperSelector).show();
            }
            else {
                $(this).closest('td').find(controller.otherWrapperSelector).hide();
                controller.$other.val('');
            }
        });


        $('#' + controller.options.modelName + '_add_entry').on('click', function(e) {
            e.preventDefault();
            controller.addEntry();
        });

        this.$table.on('click', 'button.remove', function(e) {
            e.preventDefault();
            $(this).closest('tr').remove();
            controller.updateNoAllergiesState();
        });

        this.$noAllergiesFld.on('click', function() {
            if (controller.$noAllergiesFld.prop('checked')) {
                controller.$table.hide();
            }
            else {
                controller.$table.show();
            }
        });
    };

    /**
     *
     * @param data
     * @returns {*}
     */
    AllergiesController.prototype.createRow = function(data)
    {
        if (data === undefined)
            data = {};

        data['row_count'] = OpenEyes.Util.getNextDataKey( this.tableSelector + ' tbody tr', 'key');

        return Mustache.render(
            this.templateText,
            data
        );
    };

    AllergiesController.prototype.updateNoAllergiesState = function()
    {
        if (this.$table.find('tbody tr').length === 0) {
            this.$noAllergiesWrapper.show();
        } else {
            this.$noAllergiesWrapper.hide();
            this.$noAllergiesFld.prop('checked', false);
        }
    }

    /**
     * Add a family history section if its valid.
     */
    AllergiesController.prototype.addEntry = function()
    {
        this.$table.find('tbody').append(this.createRow());
        this.updateNoAllergiesState();
    };

    exports.AllergiesController = AllergiesController;

})(OpenEyes.OphCiExamination);

