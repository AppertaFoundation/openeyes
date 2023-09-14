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

(function (exports, Util) {
    'use strict';

    // Base Dialog.
    var Dialog = exports;

    function NewEventDialog(options) {
        options = $.extend(true, {}, NewEventDialog._defaultOptions, options);

        Dialog.call(this, options);
    }

    Util.inherits(Dialog, NewEventDialog);

    //TODO: ensure support for OprnCreateEpisode checking (i.e. only allow new 'episode' creation where appropriate)
    NewEventDialog._defaultOptions = {
        destroyOnClose: false,
        title: 'Add a new event',
        popupClass: 'oe-create-event-popup oe-popup',
        modal: true,
        minHeight: 400,
        maxHeight: 400,
        dialogClass: 'dialog oe-create-event-popup oe-popup',
        selector: '#add-new-event-template',
        subspecialtyTemplateSelector: '#subspecialty-template',
        newSubspecialtyTemplateSelector: '#new-subspecialty-template',
        currentSubspecialties: [],
        subspecialties: [],
        userSubspecialtyId: undefined,
        userContext: undefined,
        showSteps: false,
        mode: 'NewEvent',
        popupContentClass: 'oe-popup-content max',
    };

    // selectors for finding and hooking into various of the key elements.
    var selectors = {
        subspecialtyColumn: '.step-subspecialties',
        subspecialtyItem: '.oe-specialty-service',
        newSubspecialtyItem: '.new-added-subspecialty-service',
        contextItem: '.step-2',
        newSubspecialtyContainer: '.change-subspecialty',
        newSubspecialtyList: '.new-subspecialty',
        // container displaying instructions to user to select a subspecialty before choosing a service
        noSubspecialty: '.no-subspecialty',
        // container for displaying single service option for new subspecialty
        fixedService: '.fixed-service',
        // container for the service options list for new subspecialty
        serviceList: '.select-service',
        addNewSubspecialty: '#js-add-subspecialty-btn',
        removeNewSubspecialty: '.change-new-specialty',
        eventTypeItem: '.oe-event-type',
        workflowStepItem: '.oe-event-workflow-step',
        confirmChangeBtn: '.js-confirm-context-change'
    };

    /**
     * Manage all the provided option data into required internal data structures for initialisation.
     */
    NewEventDialog.prototype.create = function () {
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
                serviceName: self.options.currentSubspecialties[i].service,
                serviceId: self.options.currentSubspecialties[i].firm.id,
                multiple_services: self.options.currentSubspecialties[i].services_available.length > 0,
                services_available: self.options.currentSubspecialties[i].services_available
            });
            if (!inArray(self.options.currentSubspecialties[i].subspecialty.id, currentSubspecialtyIds)) {
                currentSubspecialtyIds.push(self.options.currentSubspecialties[i].subspecialty.id);
            }
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
        if (self.options.viewSubspecialtyId === undefined || String(self.options.viewSubspecialtyId) === String(self.options.userSubspecialtyId)) {
            self.defaultSubspecialtyId = self.options.userSubspecialtyId;
        }

        // parent initialisation
        NewEventDialog._super.prototype.create.call(self);

        self.setupEventHandlers();

        self.setDefaultSelections();
    };

    /**
     *
     * @param options
     * @returns {string}
     */
    NewEventDialog.prototype.getContent = function (options) {
        return this.compileTemplate({
            selector: options.selector,
            data: {
                currentSubspecialties: this.current,
                selectableSubspecialties: this.selectableSubspecialties
            },
            partials: {
                subspecialty: $(this.options.subspecialtyTemplateSelector).html()
            }
        });
    };

    NewEventDialog.prototype.updateTitle = function (subspecialty) {
        var title = this.options.title;
        if (subspecialty !== undefined && this.options.mode === 'NewEvent') {
            title = 'Add a new ' + subspecialty.name + ' event';
        }
        this.setTitle(title);
    };

    /**
     * Setup all the interaction event hooks for clicking and updating form elements in the dialog.
     */
    NewEventDialog.prototype.setupEventHandlers = function () {
        var self = this;

        self.content.on('click', selectors.subspecialtyItem, function (e) {
            let selected_subspecialty = self.content.find(selectors.subspecialtyItem + '.selected');
            let multiple_services = selected_subspecialty.find('.change-service');
            if (multiple_services.length !== 0) {
                multiple_services.hide();
                selected_subspecialty.find('.service').show();
            }
            selected_subspecialty.removeClass('selected');

            $(this).addClass('selected');
            let selected_multiple_services = $(this).find('.change-service');
            if (selected_multiple_services.length !== 0) {
                $(this).find('.service').hide();
                selected_multiple_services.show();
            }
            // check whether the new subspecialty should be removed because they've reverted to an existing subspecialty
            if (!$(this).hasClass('new')) {
                self.removeNewSubspecialty();
            }
            self.updateContextList();
        });

        self.content.on('click', 'select.change-service', function (e) {
            e.stopPropagation();
        });

        // change of the new subspecialty
        self.content.on('change', selectors.newSubspecialtyList, function (e) {
            self.newSubspecialty();
        });

        self.content.on('change', selectors.serviceList, function (e) {
            self.newSubspecialtyService();
        });

        // add new subspecialty
        self.content.on('click', selectors.addNewSubspecialty, function (e) {
            self.addNewSubspecialty();
        });

        // removal of new subspecialty selection
        self.content.on('click', selectors.removeNewSubspecialty, function (e) {
            self.removeNewSubspecialty();
        });

        // selection of context
        self.content.on('click', selectors.contextItem, function (e) {
            self.content.find(selectors.contextItem).removeClass('selected');
            $(this).addClass('selected');
            self.updateEventList();
        });

        // selection of event
        self.content.on('click', selectors.eventTypeItem, function (e) {
            if (!$(this).hasClass('add_event_disabled')) {
                // can proceed
                $(this).addClass("selected");
                self.createEvent($(this).data('eventtype-id'), $(this).data('eventsubtype') ? $(this).data('eventsubtype') : null);
            }
        });

        // selection of workflow
        self.content.on('click', selectors.workflowStepItem, function () {
            self.content.find(selectors.workflowStepItem).removeClass('selected');
            $(this).addClass('selected');
            self.changeEventContext();
        });

        self.content.on('click', selectors.confirmChangeBtn, function () {
            self.changeEventContext();
        });
    };

    NewEventDialog.prototype.setDefaultSelections = function () {
        var self = this;

        // ensure that the new subspecialty box is setup correctly on first view.
        self.content.find(selectors.newSubspecialtyList).trigger('change');

        // auto selection of subspecialty based on current view
        var selected = false;
        // Either subspecialty already active for the patient ...
        self.content.find(selectors.subspecialtyItem).each(function () {
            if (String($(this).data('subspecialty-id')) === String(self.defaultSubspecialtyId)) {
                $(this).trigger('click');
                selected = true;
                return false;
            }
        });

        if (!selected) {
            // ... or we short cut selection of default subspecialty for new container
            self.content.find(selectors.newSubspecialtyList + ' option').each(function () {
                if ($(this).val() === String(self.options.userSubspecialtyId)) {
                    $(this).prop('selected', true);
                    self.content.find(selectors.newSubspecialtyList).trigger('change');
                    // end iteration through options
                    return false;
                }
            });
        }
    };

    /**
     * Manages changes when a new subspecialty is selected for creating a new subspecialty for the event.
     */
    NewEventDialog.prototype.newSubspecialty = function () {
        var self = this;
        var id = self.content.find(selectors.newSubspecialtyList).val();
        if (id) {
            // deselect the current subspecialty card to provide clear visual clue to
            // user that they are now on a different path
            let selected_subspecialty = self.content.find(selectors.subspecialtyItem + '.selected');
            let multiple_services = selected_subspecialty.find('.change-service');
            if (multiple_services.length !== 0) {
                multiple_services.hide();
                selected_subspecialty.find('.service').show();
            }
            selected_subspecialty.removeClass('selected');

            self.updateContextList();
            var services = self.servicesBySubspecialtyId[id];
            if (services.length === 1) {
                self.setFixedService(services[0]);
            } else {
                self.setServiceOptions(services);
            }
        } else {
            self.content.find(selectors.addNewSubspecialty).addClass('disabled');
            self.content.find(selectors.noSubspecialty).show();
            self.content.find(selectors.fixedService).text("Select subspecialty");
            self.content.find(selectors.serviceList).hide();
        }
    };

    /**
     * Handle change of selection of the service for the new subspecialty
     */
    NewEventDialog.prototype.newSubspecialtyService = function () {
        var self = this;
        var id = self.content.find(selectors.serviceList).val();
        if (id) {
            self.content.find(selectors.addNewSubspecialty).removeClass('disabled');
        } else {
            self.content.find(selectors.addNewSubspecialty).addClass('disabled');
        }

    }

    /**
     * Set the service to a fixed value when creating a new subspecialty for the event.
     *
     * @param service
     */
    NewEventDialog.prototype.setFixedService = function (service) {
        var self = this;
        self.content.find(selectors.fixedService).html(service.name);
        self.content.find(selectors.fixedService).show();
        self.content.find(selectors.noSubspecialty).hide();
        self.content.find(selectors.serviceList).hide();
        self.content.find(selectors.addNewSubspecialty).removeClass('disabled');
    };

    /**
     * Sets the services that can be chosen when creating a new subspecialty for the event.
     *
     * @param services
     */
    NewEventDialog.prototype.setServiceOptions = function (services) {
        var self = this;
        var select = self.content.find(selectors.serviceList);
        select.html('');
        var options = '<option value="">Select ' + select.data('service-firm-label') + '</option>';
        for (var i in services) {
            options += '<option value="' + services[i].id + '"';
            // default to current runtime firm
            if (services[i].id === self.options.userContext.id) {
                options += ' selected';
            }
            options += '>' + services[i].name + '</option>';
        }
        select.html(options);
        select.show();
        self.content.find(selectors.fixedService).hide();
        self.content.find(selectors.noSubspecialty).hide();
        self.content.find(selectors.addNewSubspecialty).addClass('disabled');
    };

    /**
     * Add new subspecialty to list based on form, if it's complete.
     */
    NewEventDialog.prototype.addNewSubspecialty = function () {
        var self = this;
        var id = self.content.find(selectors.newSubspecialtyList).val();
        if (!id) {
            return;
        }
        var serviceId;
        if (self.servicesBySubspecialtyId[id].length === 1) {
            serviceId = self.servicesBySubspecialtyId[id][0].id;
        } else {
            serviceId = self.content.find(selectors.serviceList).val();
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
        var html = Mustache.render($(self.options.newSubspecialtyTemplateSelector).html(), {
            subspecialtyId: subspecialty.id,
            name: subspecialty.name,
            shortName: subspecialty.shortName,
            serviceId: service.id,
            serviceName: service.name,
            classes: 'new'
        });

        self.content.find(selectors.newSubspecialtyContainer).hide();
        self.content.find(selectors.subspecialtyColumn).append(html);
        self.content.find(selectors.newSubspecialtyItem).trigger('click');
    };

    /**
     * Simply removes any new subspecialty option if it
     */
    NewEventDialog.prototype.removeNewSubspecialty = function () {
        var self = this;
        self.content.find(selectors.newSubspecialtyItem).remove();
        self.content.find(selectors.newSubspecialtyContainer).show();
        self.resetNewSubspecialtyContainer();
    };

    /**
     * Ensures the new subspecialty component is reset to no selections
     */
    NewEventDialog.prototype.resetNewSubspecialtyContainer = function () {
        var self = this;
        self.content.find(selectors.newSubspecialtyList).val('').trigger('change');
    };

    /**
     * Update the context list to reflect the currently selected subspecialty
     */
    NewEventDialog.prototype.updateContextList = function () {
        var self = this;
        // get selected subspecialty
        var selected = self.content.find(selectors.subspecialtyItem + '.selected');

        var contextListIdx = undefined;
        if (selected.length) {
            self.updateTitle(self.subspecialtiesById[selected.data('subspecialtyId')]);
            var defaultContextId = self.options.userContext.id;
            if (selected.hasClass('new')) {
                // default to the same context as the service for the new subspecialty
                defaultContextId = String(selected.data('service-id'));
            }
            var subspecialtyId = selected.data('subspecialty-id');
            // get the context options for the subspecialty
            var list = '';
            for (var i in self.contextsBySubspecialtyId[subspecialtyId]) {
                var context = self.contextsBySubspecialtyId[subspecialtyId][i];
                list += '<li class="step-2" data-context-id="' + context.id + '" data-test="new-event-context">' + context.name + '</li>';

                if (self.options.mode === 'ChangeContext' && parseInt(context.id) === self.options.currentFirm) {
                    contextListIdx = i;
                } else if (contextListIdx === undefined && String(context.id) === defaultContextId) {
                    contextListIdx = i;
                }
            }
            self.content.find('.context-list').html(list);
            self.content.find('.step-context').css('visibility', 'visible');
            if (contextListIdx !== undefined) {
                self.content.find('.context-list li:eq(' + contextListIdx + ')').trigger('click');
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
    NewEventDialog.prototype.updateEventList = function () {
        var self = this;
        var selected = self.content.find('.step-2.selected');
        if (selected.length) {
            if (self.options.mode === 'NewEvent') {
                let selectedSubspecialty = self.subspecialtiesById[self.content.find(selectors.subspecialtyItem + '.selected').data('subspecialtyId')];
                if (selectedSubspecialty.supportServices) {
                    // Filter list based on whether Support Services is being chosen.
                    self.content.find(selectors.eventTypeItem).each(function () {
                        if (!$(this).data('support-services')) {
                            $(this).hide();
                        }
                    });
                } else {
                    self.content.find(selectors.eventTypeItem).show();
                }
            } else if (self.options.mode === 'ChangeContext' && self.options.showSteps === false) {
                $('.js-confirm-context-change').show();
            } else {
                $('.js-confirm-context-change').hide();
            }

            if (self.options.mode === 'ChangeContext' && this.options.showSteps) {
                self.content.find('#event-type-list').html('');
                $.getJSON(
                    '/ChangeEvent/findWorkflowSteps',
                    {firm_id: selected.data('context-id'), event_id: OE_event_id},
                    function (workflowSteps) {
                        let list = '';
                        if (workflowSteps.length > 0) {
                            const currentWorkflowStep = self.options.currentStep;
                            let applySelectedClass = false;
                            for (let i = 0; i < workflowSteps.length; i++) {
                                if ((workflowSteps.length < currentWorkflowStep.position) && i === (workflowSteps.length - 1)) {
                                    applySelectedClass = true;
                                } else {
                                    applySelectedClass = (workflowSteps[i].position === currentWorkflowStep.position);
                                }
                                list += '<li class="oe-event-workflow-step step-3 ' + (applySelectedClass ? 'selected' : '') + '" data-workflow-id="' + workflowSteps[i].id + '">' + workflowSteps[i].name + '</li>';
                            }
                        }
                        self.content.find('#event-type-list').html(list);
                    }
                );

            }
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
    NewEventDialog.prototype.createEvent = function (eventTypeId, eventSubTypePk = null) {
        // Stop ongoing background tasks such as lightning image loading. Otherwise user has to wait
        window.stop();
        var self = this;

        // disable click on other events
        self.content.find('#event-type-list').append('<i class="spinner" title="Loading..."></i>');
        self.content.find('#event-type-list').css('pointer-events', 'none');

        // build params for the new event request
        var requestParams = {
            patient_id: self.options.patientId,
            event_type_id: eventTypeId,
            context_id: self.content.find('.step-2.selected').data('context-id')
        };

        if (eventSubTypePk) {
            requestParams['event_subtype'] = eventSubTypePk;
        }

        var subspecialty = self.content.find('.oe-specialty-service.selected');
        if (subspecialty.hasClass('new-added-subspecialty-service')) {
            requestParams['service_id'] = subspecialty.data('service-id');
        } else {
            requestParams['episode_id'] = subspecialty.data('id');
        }

        if (typeof window.ticketMoveController !== "undefined" && document.querySelector('.PatientTicketing-moveTicket').querySelector('[name=to_queue_id]').value !== "") {
            window.ticketMoveController.navigateToEvent($('.PatientTicketing-moveTicket'), '/patientEvent/create?' + $.param(requestParams));
        } else {
            // set window location to the new event request URL
            window.location = '/patientEvent/create?' + $.param(requestParams);
        }
    };

    NewEventDialog.prototype.changeEventContext = function () {
        const self = this;
        const selectedContextItem = $(selectors.contextItem).filter('.selected');
        const selectedSubspecialtyItem = $(selectors.subspecialtyItem).filter('.selected');
        const newSubspecialty = $(selectors.newSubspecialtyItem);
        const selectedWorkflowStepItem = $(selectors.workflowStepItem).filter('.selected');
        const change_service = selectedSubspecialtyItem.find('.change-service');

        if (selectedContextItem.length !== 0) {
            //   if((parseInt(self.options.userContext.id) !== selectedContextItem.data('context-id')) || (parseInt(self.options.currentStep.id) !== selectedWorkflowStepItem.data('workflow-id'))) {
            let postData = {
                YII_CSRF_TOKEN: YII_CSRF_TOKEN,
                eventId: OE_event_id,
                patientId: self.options.patientId,
                selectedContextId: selectedContextItem.data('context-id'),
                selected_firm_id: selectedSubspecialtyItem.data('service-id'),
                selectedWorkflowStepId: selectedWorkflowStepItem.data('workflow-id')
            };

            if (newSubspecialty.length !== 0) {
                postData.selected_firm_id = newSubspecialty.data('service-id');
            }

            if (change_service.length > 0) {
                postData.change_service = change_service.children("option:selected").val();
            }

            $('nav.event-header').append($('<div>', {"class": 'spinner-loader'}).append($('<i>', {"class": "spinner"})));

            if (postData !== undefined) {
                $.post("/ChangeEvent/UpdateEpisode", postData, function (result) {
                    if (result === "true") {
                        $('.' + self.options.popupClass + ' .close-icon-btn').trigger('click');

                        $.post("/site/changesiteandfirm", {
                            YII_CSRF_TOKEN: YII_CSRF_TOKEN,
                            SiteAndFirmForm: {
                                'site_id': OE_site_id,
                                'firm_id': postData.selectedContextId
                            },

                        }, function () {

                            if (typeof moduleName && moduleName === 'OphCiExamination') {
                                window.location.href = `/OphCiExamination/default/step/${OE_event_id}?patient_id=${OE_patient_id}&step_id=${postData.selectedWorkflowStepId}`;
                            } else {
                                window.location.reload();
                            }
                        });
                    }
                });
            }
            // }
        }
    };

    exports.NewEvent = NewEventDialog;
}(OpenEyes.UI.Dialog, OpenEyes.Util));
