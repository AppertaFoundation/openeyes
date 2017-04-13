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

    //TODO: ensure support for OprnCreateEpisode checking (i.e. only allow new 'episode' creation where appropriate)
    NewEventDialog._defaultOptions = {
        destroyOnClose: false,
        title: 'Add a new event',
        modal: true,
        width: 1000,
        minHeight: 'auto',
        dialogClass: 'dialog oe-create-event-popup',
        selector: '#add-new-event-template',
        currentSubspecialties: [],
        subspecialties: [],
        userSubspecialtyId: undefined,
        userContext: undefined
    };

    /**
     * Manage all the provided option data into required internal data structures for initialisation.
     */
    NewEventDialog.prototype.create = function ()
    {
        var self = this;

        // current subspecialties for patient initialisation
        self.current = [];
        var currentSubspecialtyIds = [];
        for (var i in self.options.currentSubspecialties) {
            self.current.push({
                id: self.options.currentSubspecialties[i].id,
                subspecialtyId: self.options.currentSubspecialties[i].subspecialty.id,
                name: self.options.currentSubspecialties[i].subspecialty.name,
                shortName: self.options.currentSubspecialties[i].subspecialty.shortName,
                serviceName: self.options.currentSubspecialties[i].service
            });
            currentSubspecialtyIds.push(self.options.currentSubspecialties[i].subspecialty.id);
        }

        // subspecialties/services/contexts initialisation
        self.selectableSubspecialties = [];
        self.contextsBySubspecialtyId = {};
        self.servicesBySubspecialtyId = {};
        self.subspecialtiesById = {};
        for (var i in self.options.subspecialties) {
            var subspecialty = self.options.subspecialties[i];
            if (!inArray(subspecialty.id, currentSubspecialtyIds)) {
                self.selectableSubspecialties.push(subspecialty);
            }
            self.subspecialtiesById[subspecialty.id] = subspecialty;
            self.contextsBySubspecialtyId[subspecialty.id] = subspecialty.contexts;
            self.servicesBySubspecialtyId[subspecialty.id] = subspecialty.services;
        }
        self.defaultSubspecialtyId = undefined;
        if (self.options.viewSubspecialtyId === undefined || self.options.viewSubspecialtyId === self.options.userSubspecialtyId) {
            self.defaultSubspecialtyId = self.options.userSubspecialtyId;
        }

        // parent initialisation
        NewEventDialog._super.prototype.create.call(self);

        // event handling setup
        self.content.on('click', '.oe-specialty-service', function(e) {
            self.content.find('.oe-specialty-service').removeClass('selected');
            $(this).addClass('selected');
            if (!$(this).hasClass('new')) {
                self.removeNewSubspecialty();
            }
            self.updateContextList();
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
        });

        // selection of context
        self.content.on('click', '.step-2', function(e) {
            self.content.find('.step-2').removeClass('selected');
            $(this).addClass('selected');
            self.updateEventList();
        });

        self.content.on('click', '.step-3', function(e) {
            if (!$(this).hasClass('add_event_disabled')) {
                // can proceed
                self.createEvent($(this).data('eventtype-id'));
            }
        });

        // auto selection of subspecialty based on current view
        // Either subspecialty already active for the patient ...
        self.content.find('.step-1').each(function() {
            if ($(this).data('subspecialty-id') === self.defaultSubspecialtyId) {
                $(this).trigger('click');
                return false;
            }
        });

        // ... or we short cut selection of default subspecialty for new container
        self.content.find('.new-subspecialty option').each(function() {
            if (parseInt($(this).val()) === self.options.userSubspecialtyId) {
                $(this).prop('selected', true);
                self.content.find('.new-subspecialty').trigger('change');
                return false;
            }
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
            },
            partials: {
                subspecialty: $('#add-new-event-subspecialty-step').html()
            }
        });
    };

    NewEventDialog.prototype.updateTitle = function(subspecialty)
    {
        var title = this.options.title;
        if (subspecialty !== undefined) {
            title = 'Add a new ' + subspecialty.name + ' event';
        }
        this.setTitle(title);
    }


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
            options += '<option value="'+services[i].id+'"';
            // default to current runtime firm
            if (services[i].id === self.options.userContext.id) {
                options += ' selected';
            }
            options += '>' + services[i].name + '</option>';
        }
        select.html(options);
        select.show();
        self.content.find('.fixed-service').hide();
        self.content.find('.no-subspecialty').hide();
    };

    /**
     * Add new subspecialty to list based on form, if it's complete.
     */
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
        var subspecialty = self.subspecialtiesById[id];
        var html = Mustache.render($('#add-new-event-subspecialty-step').html(), {
            subspecialtyId: subspecialty.id,
            name: subspecialty.name,
            shortName: subspecialty.shortName,
            serviceId: service.id,
            serviceName: service.name,
            classes: 'new'
        });

        self.content.find('.change-subspecialty').hide();
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
    };

    /**
     * Update the context list to reflect the currently selected subspecialty
     */
    NewEventDialog.prototype.updateContextList = function()
    {
        var self = this;
        // get selected subspecialty
        var selected = self.content.find('.oe-specialty-service.selected');

        var contextListIdx = undefined;
        if (selected.length) {
            self.updateTitle(self.subspecialtiesById[selected.data('subspecialtyId')]);
            var defaultContextId = parseInt(self.options.userContext.id);
            if (selected.hasClass('new')) {
                defaultContextId = parseInt(selected.data('service-id'));
            }
            var subspecialtyId = selected.data('subspecialty-id');
            // get the context options for the subspecialty
            var list = '';
            for (var i in self.contextsBySubspecialtyId[subspecialtyId]) {
                var context = self.contextsBySubspecialtyId[subspecialtyId][i];
                list += '<li class="step-2" data-context-id="'+context.id+'">' + context.name + '</li>';
                if (parseInt(context.id) === defaultContextId) {
                    contextListIdx = i;
                }
            }
            self.content.find('.context-list').html(list);
            self.content.find('.step-context').css('visibility', 'visible');
            if (contextListIdx !== undefined) {
                self.content.find('.context-list li:eq('+contextListIdx+')').trigger('click');
            }
        } else {
            self.updateTitle();
            self.content.find('.step-2').removeClass('selected');
            self.content.find('.step-context').css('visibility', 'hidden');
        }

        self.updateEventList();
    };

    /**
     * show or hide the event list
     */
    NewEventDialog.prototype.updateEventList = function() {
        var self = this;
        var selected = self.content.find('.step-2.selected');
        // TODO: filter list based on whether Support Services is being chosen.
        if (selected.length) {
            self.content.find('.step-event-types').css('visibility', 'visible');
        } else {
            self.content.find('.step-event-types').css('visibility', 'hidden');
        }
    };

    /**
     * Trigger request for creating the new event
     *
     * @param eventTypeId
     */
    NewEventDialog.prototype.createEvent = function(eventTypeId) {
        var self = this;
        // build params for the new event request
        var requestParams = {
            patient_id: self.options.patientId,
            event_type_id: eventTypeId,
            context_id: self.content.find('.step-2.selected').data('context-id')
        };

        var subspecialty = self.content.find('.oe-specialty-service.selected');
        if (subspecialty.hasClass('new')) {
            requestParams['service_id'] = subspecialty.data('service-id');
        } else {
            requestParams['episode_id'] = subspecialty.data('id');
        }

        // set window location to the new event request URL
        window.location = '/patientevent/create?'+$.param(requestParams);
    };

    exports.NewEvent = NewEventDialog;
}(OpenEyes.UI.Dialog, OpenEyes.Util));