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
        var subspecialtiesById = {};
        for (var i in self.options.subspecialties) {
            var subspecialty = self.options.subspecialties[i];
            if (!inArray(subspecialty.id, currentIds)) {
                selectableSubspecialties.push(subspecialty);
            }
            subspecialtiesById[subspecialty.id] = subspecialty;
            contextsBySubspecialtyId[subspecialty.id] = subspecialty.contexts;
            servicesBySubspecialtyId[subspecialty.id] = subspecialty.services;
        }
        self.selectableSubspecialties = selectableSubspecialties;
        self.subspecialtiesById = subspecialtiesById;
        self.contextsBySubspecialtyId = contextsBySubspecialtyId;
        self.servicesBySubspecialtyId = servicesBySubspecialtyId;

        NewEventDialog._super.prototype.create.call(self);

        self.content.on('click', '.oe-specialty-service', function(e) {
            self.content.find('.oe-specialty-service').removeClass('selected');
            $(this).addClass('selected');
            if (!$(this).hasClass('new')) {
                self.removeNewSubspecialty();
            }
        });

        // change of the new subspecialty
        self.content.on('change', '.new-subspecialty', function(e) {
            self.newSubspecialty();
        });

        // add new subspecialty
        self.content.on('click', '#js-add-subspecialty-btn', function(e) {
            self.addNewSubspecialty();
        });

        // removal of new subspecialty selection
        self.content.on('click', '.change-new-specialty', function(e) {
            self.removeNewSubspecialty();
        })
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
        if (id) {
            var services = self.servicesBySubspecialtyId[id];
            if (services.length === 1) {
                self.setService(services[0]);
            } else {
                self.setServiceOptions(services);
            }
        } else {
            self.content.find('.no-subspecialty').show();
            self.content.find('.fixed-service').hide();
            self.content.find('.select-service').hide();
        }
    };

    /**
     * Set the service to a fixed value when creating a new subspecialty for the event.
     * @param service
     */
    NewEventDialog.prototype.setService = function(service)
    {
        var self = this;
        self.content.find('.fixed-service').html(service.name);
        self.content.find('.fixed-service').show();
        self.content.find('.no-subspecialty').hide();
        self.content.find('.select-service').hide();
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
        var options = '<option>- Please Select -</option>';
        for (var i in services) {
            options += '<option value="'+services[i].id+'">' + services[i].name + '</option>';
        }
        select.html(options);
        select.show();
        self.content.find('.fixed-service').hide();
        self.content.find('.no-subspecialty').hide();
    };

    NewEventDialog.prototype.addNewSubspecialty = function()
    {
        var self = this;
        var id = self.content.find('.new-subspecialty').val();
        if (!id) {
            return;
        }
        var serviceId;
        if (self.servicesBySubspecialtyId[id].length === 1) {
            serviceId = self.servicesBySubspecialtyId[id][0].id;
        } else {
            serviceId = self.content.find('.select-service').val();
        }
        if (!serviceId) {
            return;
        }
        var service;
        for (var i in self.servicesBySubspecialtyId[id]) {
            if (self.servicesBySubspecialtyId[id][i].id == serviceId) {
                service = self.servicesBySubspecialtyId[id][i];
                break;
            }
        }
        var html = '<li class="step-1 oe-specialty-service new">'+self.subspecialtiesById[id].name;
        html += '<span class="tag">'+self.subspecialtiesById[id].shortName+'</span>';
        html += '<span class="service">'+service.name+'</span>';
        html += '<div class="change-new-specialty"></div>';
        html += '</li>';
        $('.change-subspecialty').hide();
        self.content.find('.subspecialties-list').append(html);
        self.content.find('.subspecialties-list li:last').trigger('click');
    };

    /**
     * Simply removes any new subspecialty option if it
     */
    NewEventDialog.prototype.removeNewSubspecialty = function()
    {
        var self = this;
        self.content.find('.oe-specialty-service.new').remove();
        self.content.find('.change-subspecialty').show();
    }

    exports.NewEvent = NewEventDialog;
}(OpenEyes.UI.Dialog, OpenEyes.Util));