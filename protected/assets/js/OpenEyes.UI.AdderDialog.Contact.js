var OpenEyes = OpenEyes || {};

(function (exports, Util) {
    var AdderDialog = exports;
    var controller = null;
    var otherRelationshipInput = null;
    var otherContactMethodInput = null;

    function Contact(options) {
        var selectedData = [];
        controller = this;
        options = $.extend(true, {}, Contact._defaultOptions, options);
        options.onSelect = function () {
            controller.onSelect(this);
        };
        options.onClose = function () {
            controller.onClose();
        };
        options.onOpen = function () {
            controller.onOpen(this);
        };
        options.onReturn = function (dialog, itemset) {
            return controller.onReturn(itemset);
        };
        AdderDialog.call(this, options);
    }

    Util.inherits(AdderDialog, Contact);

    Contact.prototype.setOtherContactMethod = function (_li) {
        let active = !$(_li).hasClass('selected');

        if (active) {
            otherContactMethodInput.show();
        } else {
            otherContactMethodInput.hide();
        }
        otherContactMethodInput.focus();
    };

    Contact.prototype.setOtherRelationship = function (_li) {
        let active = !$(_li).hasClass('selected');
        if (active) {
            otherRelationshipInput.show();
        } else {
            otherRelationshipInput.hide();
        }
        otherRelationshipInput.focus();
    };

    Contact.prototype.showHideRelationship = function (_li) {
        let relationshipTd = $("ul[data-id='contact_adder_relationship']");
        let relationshipItems = relationshipTd.find('li');
        let otherItems = relationshipTd.find('li').not("li[data-js_special_attr='HP']");
        let HPelement = $("ul[data-id='contact_adder_relationship'] li[data-js_special_attr='HP']"); // 'health professional'

        relationshipItems.removeClass('selected');

        if ($(_li).data('id') === 'adder_dialog_openeyes_users_contact_button') {
            otherItems.hide();
            HPelement.addClass('selected');
        } else {
            otherItems.show();
        }
    };

    Contact.prototype.openeyesUserSelected = function (_li) {
        let active = $(_li).hasClass('selected');
        this.options.searchOptions.searchSource = $(_li).data('search_url');
        this.revalidateSearchForm(!active);
    };

    Contact.prototype.patientContactSelected = function (_li) {
        let active = $(_li).hasClass('selected');
        this.options.searchOptions.searchSource = $(_li).data('search_url');
        this.revalidateSearchForm(!active); // autoSearch off
    };

    Contact.prototype.showHideRelationshipOtherInput = function (_li) {
        let other = $(_li).data('label').toLower === 'other';
        if (!other) {
            otherRelationshipInput.hide();
        }
    };

    Contact.prototype.showHideContactMethodOtherInput = function (_li) {
        let other = $(_li).data('label').toLower === 'other';
        if (!other) {
            otherContactMethodInput.hide();
        }
    };

    Contact.prototype.onSelect = function (_li) {
        if ($(_li).closest('ul').data('id') === 'contact_adder_type') {
            this.showHideRelationship(_li);
        }

        if ($(_li).closest('ul').data('id') === 'contact_adder_relationship') {
            this.showHideRelationshipOtherInput(_li);
        }

        if ($(_li).closest('ul').data('id') === 'contact_adder_method') {
            this.showHideContactMethodOtherInput(_li);
        }

        let selectedItem = $(_li);
        let action = selectedItem.data('js_action');
        if (typeof action !== 'undefined') {
            this[action](_li);
        }
    };

    Contact.prototype.getDataFromAddNewContactDialog = function (contactDialog) {
        var errors = [];
        let data = {};
        let inputs = contactDialog.content.find('.js-contact-field');
        let errorDiv = contactDialog.content.find('.js-contact-error-box');
        let adder_dialog = this;

        inputs.each(function () {
            let validationResult = adder_dialog.checkRequiredValue($(this));
            if (validationResult !== true) {
                errors.push("Missing value: " + validationResult);
            }
            data[$(this).data('name')] = $(this).val();
        });

        if (errors.length > 0) {
            let errorList = "<li>" + (errors.join("</li><li>")) + "</li>";
            errorDiv.find('.js-contact-errors').html(errorList);
            errorDiv.show();
            return false;
        }

        this.selectedData = $.extend(data, this.selectedData);
        this.selectedData.name = data.first_name + ' ' + data.last_name;

        return true;
    };

    Contact.prototype.initialiseDialogTriggers = function (contactDialog) {
        contactDialog.content.on('click', '.js-add-new-contact', (event) => {
            event.preventDefault();
            if(this.getDataFromAddNewContactDialog(contactDialog)){
                this.AddContact();
                contactDialog.close();
            } else {
                let top = $('.oe-popup-content').position().top;
                $('.oe-popup-content').scrollTop(top);
            }
        });
    };

    Contact.prototype.checkRequiredValue = function (inp) {
        let isRequired = inp.attr('required') === 'required';
        let empty = inp.val() === '';
        if (empty && isRequired) {
            return inp.data('label');
        }
        return true;
    };

    Contact.prototype.openAddNewContactDialog = function () {
        let contactDialog = new OpenEyes.UI.Dialog($.extend({}, this.addContactDialogOptions, {
            url: this.options.newContactDialogURL,
            title: 'Add a new "' + this.selectedData.contact_type_name + '" contact',
            width: 500,
            data: {
                returnUrl: "",
                selected_contact_method_type_id: this.selectedData.consent_patient_contact_method_id,
                selected_contact_method: this.selectedData.consent_patient_contact_method,

                selected_relationship_type_id: this.selectedData.consent_patient_relationship_id,
                selected_relationship: this.selectedData.relationship,

                patient_id: this.options.patientId,
            }
        }));
        contactDialog.open();
        this.initialiseDialogTriggers(contactDialog);
    };

    Contact.prototype.revalidateSearchForm = function (search) {
        let search_input = $('#patient_contact_adder').find('.js-search-autocomplete');
        search_input.val(' ');
        search_input.show();

        if (search !== false) {
            search_input.keyup();
        }

        $('#patient_contact_adder').find('.js-search-results').html('');
    };

    Contact.prototype.createNewPatientContact = function () {
        this.revalidateSearchForm();
    };

    Contact.prototype.alert = function (msg) {
        new OpenEyes.UI.Dialog.Alert({
            content: msg
        }).open();

    };

    Contact.prototype.AddContact = function () {
        let signature_require = this.selectedData.signature_require;
        let $table = $("#js-patient_contacts tbody");
        let templateObj = $('#js-mustache_template tbody').clone();
        let jsonData = JSON.stringify(this.selectedData);
        let jsonInput = templateObj.find('.contact_json_data');
        let signatureRequireContainer = templateObj.find('.js-signature-needed');
        let signatureRequireRadioContainer = templateObj.find('.js-signature-needed-radio');

        if (signature_require === 2) {
            signatureRequireContainer.remove();
            signatureRequireRadioContainer.show();
        } else {
            signatureRequireRadioContainer.remove();
            signatureRequireContainer.show();
        }

        jsonInput.val(jsonData);

        let templateText = templateObj.html();
        let $row = Mustache.render(templateText, this.selectedData);
        $table.append($row);
    };

    Contact.prototype.initMenuButton = function () {
        let adder_dialog = this;
        let other_patient_relationship = $("#patient_contact_adder").find("[data-js_action='setOtherRelationship']");
        let other_patient_contact_method = $("#patient_contact_adder").find("[data-js_action='setOtherContactMethod']");

        let _inp1 = '<input type="text" style="display: none;" placeholder="Relationship description" name="other_patient_relationship" id="other_patient_relationship_input">';
        other_patient_relationship.closest('ul').append($(_inp1));

        let _inp2 = '<input type="text" style="display: none;" placeholder="Contact Method description" name="other_patient_contact_method" id="other_patient_contact_method_input">';
        other_patient_contact_method.closest('ul').append($(_inp2));

        // Global variable for new dom elements
        otherRelationshipInput = $('#other_patient_relationship_input');
        otherContactMethodInput = $('#other_patient_contact_method_input');

        $('[data-id="adder_dialog_add_new_contact_button"]').on('click', function () {
            let active = $(this).hasClass('selected');
            adder_dialog.options.searchOptions.searchSource = $(this).data('search_url');
            adder_dialog.createNewPatientContact(!active);
        });
    };

    Contact.prototype.onClose = function () {
        otherRelationshipInput.remove();
        otherContactMethodInput.remove();
        // Reset to default
        this.showHideRelationship();
    };

    Contact.prototype.onOpen = function () {
        this.revalidateSearchForm(false);
        this.initMenuButton();
        // remove all selections
        this.popup.find('li').each(function () {
            $(this).removeClass('selected');
        });
    };

    Contact.prototype.validateReturnData = function (selectedItems) {
        let errors = [];
        let hasContactType = false;
        let hasRelationship = false;
        let hasContact = false;
        let hasContactMethod = false;
        let hasOtherRelationshipDescription = true;

        $.each(selectedItems, function (k, item) {
            if (item.itemSet) {
                if (item.itemSet.options.header === "Contact type") {
                    hasContactType = true;
                }
                if (item.itemSet.options.header === "Relationship") {
                    hasRelationship = true;
                    if (item.label.toLowerCase() === "other") {
                        if (otherRelationshipInput.val().length === 0) {
                            hasOtherRelationshipDescription = false;
                        }
                    }
                }
                if (item.itemSet.options.header === "Contact method") {
                    let other_patient_contact_method = $('#other_patient_contact_method_input').val();
                    if (item.label.toLowerCase() === "other") {
                        if (other_patient_contact_method === '') {
                            hasContactMethod = false;
                        } else {
                            hasContactMethod = true;
                        }
                    } else {
                        hasContactMethod = true;
                    }
                }
            } else if (item.type === "Contact" || item.type === 'custom') { // Search result is different one
                hasContact = true;
            }
        });

        if (!hasContactType) {
            errors.push("Please select a contact type.");
        }
        if (!hasRelationship) {
            errors.push("Please select a relationship.");
        }
        if (!hasContact) {
            errors.push("Please select a contact.");
        }
        if (!hasOtherRelationshipDescription) {
            errors.push("Please add a description for the relationship.");
        }
        if (!hasContactMethod) {
            errors.push("Please select a contact method.");
        }

        if (errors.length > 0) {
            this.alert(errors.join("<br> "));
            return false;
        }
        return true;
    };

    Contact.prototype.onReturn = function (selectedItems) {
        let data = {};

        if (selectedItems.length === 0) {
            this.alert("Please select options from the pop-up window.");
            return false;
        }
        if (!this.validateReturnData(selectedItems)) {
            return false;
        }
        let selectedContactTypeData = selectedItems[0];
        let selectedContactData = selectedItems[2];
        let selectedRelationshipData = selectedItems[1];
        let selectedContactMethodData = selectedItems[3];
        let addNewContact = selectedContactData.type === "custom";

        data = selectedContactData;
        data.relationship = selectedRelationshipData.label;
        data.consent_patient_relationship_id = selectedRelationshipData.item_id;
        data.consent_patient_contact_method_id = selectedContactMethodData.item_id;
        data.consent_patient_contact_method = selectedContactMethodData.label;
        data.uniqueId = Math.random().toString(16).slice(2);
        data.contact_type_id = selectedContactTypeData.contact_type_id;
        data.signature_require = selectedContactMethodData.signature_require;
        data.signature_require_string = selectedContactMethodData.signature_require_string;
        data.contact_type_name = selectedContactTypeData.label;

        data.existing_id = '';

        if (data.relationship.toLowerCase() === 'other') {
            data.other_relationship = $('#other_patient_relationship_input').val();
            data.relationship = data.other_relationship;
        }

        if (data.consent_patient_contact_method.toLowerCase() === 'other') {
            data.consent_patient_contact_method = $('#other_patient_contact_method_input').val();
            data.other_contact_method = data.consent_patient_contact_method;

        }

        this.selectedData = data;

        if (addNewContact) {
            this.openAddNewContactDialog();
        } else {
            this.AddContact();
        }

        return true;
    };

    exports.Contact = Contact;

}(OpenEyes.UI.AdderDialog, OpenEyes.Util));
