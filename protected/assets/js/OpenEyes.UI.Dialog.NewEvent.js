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

(function(exports, Util) {
    'use strict';

    // Base Dialog.
    var Dialog = exports;

    function NewEventDialog(options)
    {
        options = $.extend(true, {}, NewEventDialog._defaultOptions, options);

        Dialog.call(this, options);
    }

    Util.inherits(Dialog, NewEventDialog);

    NewEventDialog._defaultOptions = {
        destroyOnClose: false,
        modal: true,
        width: 1000,
        minHeight: 'auto',
        title: 'Create new event',
        dialogClass: 'dialog oe-create-event-popup',
        selector: '#add-new-event-template',
        currentSubspecialties: [],
        subspecialties: []
    };

    NewEventDialog.prototype.create = function ()
    {
        var current = [];
        var currentIds = [];
        for (var i in this.options.currentSubspecialties) {
            current.push({
                id: this.options.currentSubspecialties[i].id,
                name: this.options.currentSubspecialties[i].subspecialty.name,
                shortName: this.options.currentSubspecialties[i].subspecialty.shortName,
                serviceName: this.options.currentSubspecialties[i].service
            });
            currentIds.push(this.options.currentSubspecialties[i].id);
        }
        this.current = current;
        var selectableSubspecialties = [];
        var contextsBySubspecialtyId = {};
        for (var i in this.options.subspecialties) {
            var subspecialty = this.options.subspecialties[i];
            if (!inArray(subspecialty.id, currentIds)) {
                selectableSubspecialties.push(subspecialty);
            }
            contextsBySubspecialtyId[subspecialty.id] = subspecialty.contexts;
        }
        this.selectableSubspecialties = selectableSubspecialties;
        this.contextsBySubspecialtyId = contextsBySubspecialtyId;

        NewEventDialog._super.prototype.create.call(this);
    };

    NewEventDialog.prototype.getContent = function (options)
    {
        return this.compileTemplate({
            selector: options.selector,
            data: {
                currentSubspecialties: this.current,
                selectableSubspecialties: this.selectableSubspecialties

            }
        });
    };

    exports.NewEvent = NewEventDialog;

}(OpenEyes.UI.Dialog, OpenEyes.Util));