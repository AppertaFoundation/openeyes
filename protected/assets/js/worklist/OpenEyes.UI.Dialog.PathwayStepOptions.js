/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

(function (exports, Util) {
    'use strict';

    // Base Dialog.
    const Dialog = exports;

    function PathwayStepOptions(options)
    {
        options = $.extend(true, {}, PathwayStepOptions._defaultOptions, options);

        Dialog.call(this, options);
    }

    Util.inherits(Dialog, PathwayStepOptions);

    PathwayStepOptions._defaultOptions = {
        destroyOnClose: false,
        onReturn: null,
        itemSets: [],
        title: '',
        popupContentClass: 'oe-popup-content popup-path-step-options',
        modal: true,
        width: null,
        minHeight: 400,
        maxHeight: 400,
        dialogClass: 'dialog oe-path-step-options-popup',
        selector: '#path-step-options-template',
    };

    /**
     * Manage all the provided option data into required internal data structures for initialisation.
     */
    PathwayStepOptions.prototype.create = function () {
        const self = this;

        // parent initialisation
        PathwayStepOptions._super.prototype.create.call(self);

        $(document).off('click', '.js-add-pathway').on('click', '.js-add-pathway', this.returnValues.bind(this));
        $(document).off('click', '.js-cancel-popup-steps').on('click', '.js-cancel-popup-steps', this.cancelAdd.bind(this));
        $(document).off('change', '.js-step-options input[type="radio"]')
            .on('change', '.js-step-options input[type="radio"]', this.valueSelected.bind(this));
    };

    /**
     *
     * @param options
     * @returns {string}
     */
    PathwayStepOptions.prototype.getContent = function (options) {
        // Display the screen using the specified template.
        return this.compileTemplate({
            selector: options.selector,
            data: {
                itemSets: options.itemSets
            }
        });
    };

    PathwayStepOptions.prototype.valueSelected = function (e) {
        let itemset_id = $(e.target).closest('.js-itemset').data('itemset-id');
        let selected_value = $(e.target).val();
        const itemset = this.options.itemSets.find(element => element.id === itemset_id);
        if (itemset.onSelectValue) {
            itemset.onSelectValue(this, itemset, selected_value, $(e.target).next('div.li').text());
        }
    };

    PathwayStepOptions.prototype.returnValues = function () {
        if (this.options.onReturn) {
            let selected_values = [];
            $('.js-step-options input[type="radio"]:checked').each(function () {
                selected_values.push({
                    field: $(this).attr('name'),
                    value: $(this).val()
                });
            });
            this.options.onReturn(this, selected_values);
        }
    };

    PathwayStepOptions.prototype.cancelAdd = function () {
        this.close();
    };

    exports.PathwayStepOptions = PathwayStepOptions;
}(OpenEyes.UI.Dialog, OpenEyes.Util));
