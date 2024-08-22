/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

(function (exports) {
    function ContactsController(options) {
        this.options = $.extend(true, {}, ContactsController._defaultOptions, options);
        this.addContactDialogOptions = {
            id: 'contact-dialog',
            title: 'Add a new contact'
        };
        this.pasContactLabels = ['General Practitioner'];
        this.tableSelector = '#' + this.options.modelName + '_entry_table';
        this.pasTableSelector = '#' + this.options.modelName + '_pas_table';
        this.$table = $(this.tableSelector);
        this.$pasTable = $(this.pasTableSelector);
        this.templateText = $('#' + this.options.modelName + '_entry_template').text();
        this.contactFilterSelector = 'ul[data-id="' + this.options.contactFilterId + '"]';
        this.initialiseTriggers();

    }

    ContactsController._defaultOptions = {
        modelName: 'OEModule_OphCiExamination_models_Allergies',
        contactFilterId: 'contact-type-filter'
    };

    ContactsController.prototype.initialiseTriggers = function () {
        let controller = this;

        controller.$table.on('click', 'i.trash', function (e) {
            e.preventDefault();
            $(this).closest('tr').remove();
        });
    };

    /**
     *
     * @param contact
     * @returns {*}
     */
    ContactsController.prototype.createRows = function (contact = {}) {
        let controller = this;
        let newRows = [];
        let data = {};
        let row;
        data.id = contact.id;
        data.label = contact.contact_label;
        data.full_name = contact.name;
        data.email = contact.email;
        data.phone = contact.phone;
        data.address = contact.address;
        data.row_count = OpenEyes.Util.getNextDataKey(controller.tableSelector + ' tbody tr', 'key') + newRows.length;
        row = Mustache.render(controller.templateText, data);
        newRows.push(row);

        return newRows;
    };

    ContactsController.prototype.isContactInTable = function (selected_contacts) {
        let contact_already_exists = false;
        let controller = this;
        let current_contact_ids = [];
        controller.$table.find('tbody tr').each(function () {
            current_contact_ids.push(parseInt($(this).find("input[name*='[contact_id]']").val()));
        });

        let existing_contact = selected_contacts.find(function (contact) {
            return current_contact_ids.includes(contact.id);
        });

        if (existing_contact !== undefined) {
            controller.showSameContactDialog(existing_contact);
            contact_already_exists = true;
        }

        return contact_already_exists ;
    };

    /**
     * Add a family history section if its valid.
     */
    ContactsController.prototype.addEntry = function (selectedItems) {
        let controller = this;
        let selectedFilter;
        let selectedFilterName;
        let patientContactLimit;
        let createContactPageDialog = false;
        let newRows = [];
        let contactTypeLimitReached = false;
        let selectedItemsValid = false;

        for (let index = 0; index < selectedItems.length; ++index) {
            if (selectedItems[index].type === "custom") {
                createContactPageDialog = true;
                selectedItemsValid = true;
            } else if (selectedItems[index].itemSet) {
                selectedFilter = selectedItems[index].id;
                selectedFilterName = selectedItems[index].label;
                patientContactLimit = controller.getContactLabelLimit(selectedFilterName);
            } else {
                selectedFilterName = selectedItems[index].contact_label;
                patientContactLimit = controller.getContactLabelLimit(selectedFilterName);
                newRows = controller.createRows(selectedItems[index]);
                selectedItemsValid = true;
            }
        }

        if (typeof patientContactLimit !== undefined) {
            contactTypeLimitReached = controller.isContactTypeAboveLimit(selectedFilterName, patientContactLimit);
        }

        if (contactTypeLimitReached && selectedItemsValid) {
            controller.showPatientContactLimitDialog(selectedFilterName, patientContactLimit, createContactPageDialog, newRows);
        } else {
            if (createContactPageDialog) {
                controller.openAddNewContactDialog(selectedFilter);
            }
            if (newRows.length > 0) {
                controller.$table.find('tbody').append(newRows);
                autosize($('.autosize'));
            }
        }
    };

    ContactsController.prototype.getContactLabelLimit = function (filterName) {
        let controller = this;
        let contactLabel = $(controller.contactFilterSelector).find('li[data-label="' + filterName + '"]');
        return contactLabel.data('patient_limit');
    };

    ContactsController.prototype.isContactTypeAboveLimit = function (contactLabel, contactLimit) {
        let controller = this;
        let contactLabelCount = 0;
        controller.$table.find('.js-contact-label').each(function () {
            if ($(this).text() === contactLabel) {
                contactLabelCount++;
            }
        });

        if (controller.pasContactLabels.includes(contactLabel)) {
            controller.$pasTable.find('.js-contact-label').each(function () {
                if ($(this).text() === contactLabel) {
                    contactLabelCount++;
                }
            });
        }

        return contactLabelCount >= contactLimit;
    };

    ContactsController.prototype.showSameContactDialog = function (contact) {
        let dialog = new OpenEyes.UI.Dialog.Alert({
            content: contact.label + " is already in the list of contacts."
        });
        dialog.open();
    };

    ContactsController.prototype.showPatientContactLimitDialog = function (
        selectedFilterName,
        patientContactLimit,
        createContactPageDialog,
        newRows
    ) {
        let controller = this;
        if (controller.pasContactLabels.includes(selectedFilterName)) {
            new OpenEyes.UI.Dialog.Alert({
                content: "You have reached the limit for " + selectedFilterName +
                    " (only " +
                    patientContactLimit +
                    " allowed per patient). To make a change for this contact type you must do so in PAS"
            }).open();
        } else if (patientContactLimit !== 1 || createContactPageDialog) {
            new OpenEyes.UI.Dialog.Alert({
                content: "You have reached the limit for " +
                    selectedFilterName +
                    " if you would like to insert a new one you have to delete one first"
            }).open();
        } else {
            let dialog = new OpenEyes.UI.Dialog.Confirm({
                content: "You have reached the limit for " +
                    selectedFilterName +
                    ". Would you like to replace your current " + selectedFilterName
            });
            dialog.on('ok', function () {
                controller.deleteByContactLabel(selectedFilterName);
                controller.$table.find('tbody').append(newRows);
                autosize($('.autosize'));
            }.bind(this));
            dialog.open();
        }
    };

    ContactsController.prototype.openAddNewContactDialog = function (filter) {
        let controller = this;
        let contactDialog = new OpenEyes.UI.Dialog($.extend({}, this.addContactDialogOptions, {
            url: baseUrl + '/OphCiExamination/contact/ContactPage',
            width: 500,
            data: {
                returnUrl: "",
                selected_contact_type: filter,
                patient_id: window.OE_patient_id || null
            }
        }));
        contactDialog.open();
        controller.initialiseDialogTriggers(contactDialog);
    };

    ContactsController.prototype.initialiseDialogTriggers = function (contactDialog) {
        let controller = this;
        let contactLabelError = "";

        contactDialog.content.on('change', '#contact_label_id', function () {
            let contactTypeLimitReached = false;
            let selectedLabel = $(this).find(":selected").text();
            let contactLabelLimit = controller.getContactLabelLimit(selectedLabel);
            if (typeof patientContactLimit !== undefined) {
                contactTypeLimitReached = controller.isContactTypeAboveLimit(selectedLabel, contactLabelLimit);
            }

            contactLabelError = controller.handleContactTypeLabelError(contactTypeLimitReached, selectedLabel);
        });

        contactDialog.content.on('click', '.js-add-new-contact', function (event) {
            event.preventDefault();
            let data = controller.getDataFromAddNewContactDialog();
            data['contact_label_error'] = contactLabelError;
            controller.saveNewContact(data);
        });
    };

    ContactsController.prototype.handleContactTypeLabelError = function (contactTypeLimitReached, selectedLabel) {
        let contactLabelError;
        if (contactTypeLimitReached) {
            $('.js-contact-error-box').show();
            let $errorsList = $('.js-contact-errors');
            $errorsList.html("");
            contactLabelError = "You have reached the limit for " + selectedLabel +
                " if you would like to insert a new one you have to delete one first";
            $errorsList.append("<li><a>" + contactLabelError + "</a> </li>");
        } else {
            $('.js-contact-error-box').hide();
            contactLabelError = "";
        }

        return contactLabelError;
    };

    ContactsController.prototype.getDataFromAddNewContactDialog = function () {
        let data = {};

        $('.js-contact-field').each(function () {
            if ($(this).data('label') === 'active') {
                data[$(this).data('label')] = $(this).is(":checked");
            } else {
                data[$(this).data('label')] = $(this).val();
            }
        });

        return data;
    };

    ContactsController.prototype.saveNewContact = function (data) {
        let controller = this;
        $.ajax({
            'type': 'POST',
            'data': "data=" + JSON.stringify(data) + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
            'url': baseUrl + '/OphCiExamination/contact/saveNewContact',
            'success': function (response) {
                response = JSON.parse(response);

                if (response.errors) {
                    $('.js-contact-error-box').show();
                    let $errorsList = $('.js-contact-errors');
                    $errorsList.html("");

                    Object.keys(response.errors).forEach(function (key) {
                        $errorsList.append("<li><a>" + response.errors[key] + "</a> </li>");
                    });
                } else {
                    let row;
                    $('.js-contact-error-box').hide();
                    row = controller.createRows(response);
                    controller.$table.find('tbody').append(row);
                    autosize($('.autosize'));
                    $('.oe-popup-wrap').not('#js-overlay').remove();
                }
            }
        });
    };

    ContactsController.prototype.deleteByContactLabel = function (contactLabelName) {
        let controller = this;
        controller.$table.find('.js-contact-label').each(function () {
            if ($(this).text() === contactLabelName) {
                $(this).closest('tr').remove();
            }
        });
    };

    exports.ContactsController = ContactsController;
})(OpenEyes.OphCiExamination);
