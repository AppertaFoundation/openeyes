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

    /**
     * Manage all the provided option data into required internal data structures for initialisation.
     */
    NewEventDialog.prototype.create = function ()
    {
        var self = this;

        var current = [];
        var currentIds = [];
        for (var i in self.options.currentSubspecialties) {
            current.push({
                id: self.options.currentSubspecialties[i].id,
                name: self.options.currentSubspecialties[i].subspecialty.name,
                shortName: self.options.currentSubspecialties[i].subspecialty.shortName,
                serviceName: self.options.currentSubspecialties[i].service
            });
            currentIds.push(self.options.currentSubspecialties[i].id);
        }
        self.current = current;
        var selectableSubspecialties = [];
        var contextsBySubspecialtyId = {};
        var servicesBySubspecialtyId = {};
        for (var i in self.options.subspecialties) {
            var subspecialty = self.options.subspecialties[i];
            if (!inArray(subspecialty.id, currentIds)) {
                selectableSubspecialties.push(subspecialty);
            }
            contextsBySubspecialtyId[subspecialty.id] = subspecialty.contexts;
            servicesBySubspecialtyId[subspecialty.id] = subspecialty.services;
        }
        self.selectableSubspecialties = selectableSubspecialties;
        self.contextsBySubspecialtyId = contextsBySubspecialtyId;
        self.servicesBySubspecialtyId = servicesBySubspecialtyId;

        NewEventDialog._super.prototype.create.call(self);

        self.content.on('change', '.new-subspecialty', function(e) {
            self.newSubspecialty();
        });
    };

    /**
     *
     * @param options
     * @returns {string}
     */
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

    /**
     * Manages changes when a new subspecialty is selected for creating a new subspecialty for the event.
     */
    NewEventDialog.prototype.newSubspecialty = function ()
    {
        var self = this;
        var id = self.content.find('.new-subspecialty').val();
        var contexts = self.contextsBySubspecialtyId[id];
        if (contexts.length === 1) {
            self.setService(contexts[0]);
        } else {
            self.setServiceOptions(contexts);
        }
    };

    /**
     * Set the service to a fixed value when creating a new subspecialty for the event.
     * @param service
     */
    NewEventDialog.prototype.setService = function(service)
    {
        // TODO: complete me
    };

    /**
     * Sets the services that can be chosen when creating a new subspecialty for the event.
     *
     * @param services
     */
    NewEventDialog.prototype.setServiceOptions = function(services)
    {
        var self = this;
        var select = self.content.find('.select-service');
        select.html('');
        var options = '';
        for (var i in services) {
            options += '<option value="'+services[i].id+'">' + services[i].name + '</option>';
        }
        select.html(options);
    };

    exports.NewEvent = NewEventDialog;
}(OpenEyes.UI.Dialog, OpenEyes.Util));