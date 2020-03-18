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

(function (exports, Util) {
    'use strict';

    // Base Dialog.
    var Dialog = exports;

    function SaveSearchDialog(options) {
        options = $.extend(true, {}, SaveSearchDialog._defaultOptions, options);

        Dialog.call(this, options);
    }

    Util.inherits(Dialog, SaveSearchDialog);

    SaveSearchDialog._defaultOptions = {
        destroyOnClose: false,
        title: '',
        popupContentClass: 'oe-popup-content popup-search-query',
        modal: true,
        width: null,
        minHeight: 400,
        maxHeight: 400,
        dialogClass: 'dialog oe-save-search-popup',
        selector: '#save-search-template',
    };

    /**
     * Manage all the provided option data into required internal data structures for initialisation.
     */
    SaveSearchDialog.prototype.create = function () {
        var self = this;

        // parent initialisation
        SaveSearchDialog._super.prototype.create.call(self);
    };
    /**
     *
     * @param options
     * @returns {string}
     */
    SaveSearchDialog.prototype.getContent = function (options) {
        let $paramTable = $('#param-list').clone();
        let $variableTable = $('#js-variable-table').clone();
        let $variableList = $('#js-variable-list');
        $paramTable.find('td:has(i)').remove();
        $variableTable.find('td:has(i)').remove();
        return this.compileTemplate({
            selector: options.selector,
            data: {
                queryTable: $paramTable.html(),
                variableTable:$variableTable.html(),
                variableList: $variableList.val()
            }
        });
    };

    exports.SaveSearch = SaveSearchDialog;
}(OpenEyes.UI.Dialog, OpenEyes.Util));
