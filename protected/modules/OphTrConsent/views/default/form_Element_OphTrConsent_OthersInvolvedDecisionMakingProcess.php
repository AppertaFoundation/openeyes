<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
$model_name = CHtml::modelName($element);
?>

<div class="element-actions"><!-- note: order is important because of Flex, trash must be last element -->
    <!-- No icons --></div><!-- *** Element DATA in EDIT mode *** -->
<div class="element-fields full-width">
    <div class="flex">
        <div class="cols-11">
            <table class="cols-full last-left" id="js-patient_contacts">
                <colgroup>
                    <col class="cols-2">
                    <col class="cols-3">
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-1">
                </colgroup>
                <thead>
                    <th>Relationship</th>
                    <th>Contact Person</th>
                    <th>Contact Method</th>
                    <th>Signature required</th>
                    <th>Comment</th>
                </thead>
                <tbody>
                <?php foreach ($element->consentContact as $row_id => $contact) { ?>
                    <tr>
                        <th><?= $contact->getRelationshipName() ?></th>
                        <td><?= $contact->getFullName() ?></td>
                        <td><?= $contact->getContactMethodName() ?></td>
                        <td>
                            <?php if ($contact->getSignatureRequiredFromContactMethodType()===2) {
                                echo \CHtml::radioButtonList(
                                    $model_name . "[signature_required][]_" . $row_id,
                                    $contact->getSignatureRequired(),
                                    [
                                        1 => $contact->consentPatientContactMethod::getTypeLabel('SIGNATURE_REQUIRED'),
                                        0 => $contact->consentPatientContactMethod::getTypeLabel('SIGNATURE_NOT_REQUIRED'),
                                    ],
                                    array_merge(
                                        [],
                                        $contact->consentPatientContactMethod->need_signature === '2' ? [] : ['disabled' => 'disabled']
                                    )
                                );
                            } else {
                                echo $contact->consentPatientContactMethod->getDefaultSignatureRequiredString();
                                echo \CHtml::hiddenField(
                                    $model_name . "[signature_required][]_" . $row_id,
                                    $contact->getSignatureRequired(),
                                    ['class' => 'signature_required', 'id' => $model_name . "_signature_required_" . $row_id]
                                );
                            }
                            ?>
                        </td>
                        <td>
                            <?= \CHtml::hiddenField(
                                $model_name . "[jsonData][]",
                                $contact->getJsonData(),
                                ['class' => 'contact_json_data', 'id'=> $model_name.'_'.$row_id.'_jsonData']
                            ) ?>
                            <div class="cols-full">
                                <div class="js-comment-container flex-layout flex-left"
                                    id="<?= $model_name ?>_<?= $row_id ?>_comment_container"
                                    style="<?= (strlen($contact->comment) === 0) ? 'display: none;' : '' ?>"
                                    data-comment-button="#<?= $model_name ?>_<?= $row_id ?>_comments_button"

                                >
                                        <textarea
                                                class="js-comment-field autosize cols-full"
                                                rows="1"
                                                placeholder="Comment"
                                                autocomplete="off"
                                                name="<?= $model_name ?>[comment][]"
                                                id="<?= $model_name ?>_<?= $row_id ?>_comment"
                                        ><?= \CHtml::encode($contact->comment) ?></textarea>
                                    <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
                                </div>
                                <button id="<?= $model_name ?>_<?= $row_id ?>_comments_button"
                                        class="button js-add-comments"
                                        data-comment-container="#<?= $model_name ?>_<?= $row_id ?>_comment_container"
                                        type="button"
                                        data-hide-method="display"
                                        style="<?= (strlen($contact->comment) > 0) ? 'display: none;' : '' ?>"
                                ><i class="oe-i comments small-icon"></i>
                                </button>
                            </div>
                        </td>
                        <td>
                            <i class="oe-i trash remove-patient-contact"></i>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="add-data-actions flex-item-bottom">
            <button id="add_patient_contact_button" type="button" class="green hint js-add-select-btn"
                    data-popup="add-patient-contact">Add contact
            </button>
        </div>
    </div>
</div>

<!-- MOUSTACHE TEMPLATE !-->
<table class="hidden" id="js-mustache_template">
    <tr>
        <th>{{relationship}}</th>
        <td>{{name}}</td>
        <td>{{consent_patient_contact_method}}</td>
        <td>
            <div class='hidden js-signature-needed'>
                {{signature_require_string}}
                <?= \CHtml::hiddenField(
                    $model_name . "[signature_required][]_{{uniqueId}}",
                        '{{signature_require}}',
                        ['class' => 'signature_required', 'id' => $model_name . "_signature_required_{{uniqueId}}"]
                    );
?>
            </div>
            <div class='hidden js-signature-needed-radio'>
                <?php
                    echo \CHtml::radioButtonList(
                        $model_name . "[signature_required][]_{{uniqueId}}",
                        '',
                        [1 => 'Signature required', 0 => 'No signature'],
                        ['class'=>'js-signature-require'],
                    );
                    ?>
            </div>
        </td>
        <td>
            <?= \CHtml::hiddenField(
                $model_name . "[jsonData][]",
                '',
                ['class' => 'contact_json_data', 'id'=> $model_name.'_{{uniqueId}}_jsonData']
            ) ?>
            <div class="cols-full">
                <div class="js-comment-container flex-layout flex-left"
                     id="<?= $model_name ?>_{{uniqueId}}_comment_container"
                     style="display: none;"
                     data-comment-button="#<?= $model_name ?>_{{uniqueId}}_comments_button">
                        <textarea
                                class="js-comment-field autosize cols-full"
                                rows="1"
                                placeholder="Comment"
                                autocomplete="off"
                                name="<?= $model_name ?>[comment][]"
                                id="<?= $model_name ?>_{{uniqueId}}_comment"
                        ></textarea>
                    <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
                </div>
                <button id="<?= $model_name ?>_{{uniqueId}}_comments_button"
                        class="button js-add-comments"
                        data-comment-container="#<?= $model_name ?>_{{uniqueId}}_comment_container"
                        type="button"
                        data-hide-method="display"
                        style=""
                ><i class="oe-i comments small-icon"></i>
                </button>
            </div>
        </td>
        <td>
            <i class="oe-i trash remove-patient-contact"></i>
        </td>
    </tr>
</table>

<script>
    $(document).ready(function () {
        let search_input;
        let create_new_patient_contact_btn;
        let ContactAD = OpenEyes.UI.AdderDialog.prototype;

        $('#js-patient_contacts').on("click", ".remove-patient-contact", function () {
            $(this).closest('tr').remove();
        })

        ContactAD.selectedData = {};

        ContactAD.addContactDialogOptions = {
            id: 'contact-dialog',
            title: 'Add a new contact'
        }

        ContactAD.getDataFromAddNewContactDialog = function (contactDialog) {
            errors = [];
            let data = {};
            let inputs = contactDialog.content.find('.js-contact-field');
            let errorDiv = contactDialog.content.find('.js-contact-error-box');

            inputs.each(function () {
                validationResult = ContactAD.checkRequiredValue($(this));
                if (validationResult !== true) {
                    errors.push("Missing value: " + validationResult);
                }
                data[$(this).data('name')] = $(this).val();
            });

            ContactAD.selectedData = $.extend(data,ContactAD.selectedData);
            ContactAD.selectedData.name = data.first_name+' '+data.last_name;
            if (errors.length > 0) {
                let errorList = "<li>" + (errors.join("</li><li>")) + "</li>";
                errorDiv.find('.js-contact-errors').html(errorList);
                errorDiv.show();
                return false;
            }

            return ContactAD.selectedData;
        };

        ContactAD.checkRequiredValue = function (inp) {
            let isRequired = inp.attr('required') === 'required';
            let empty = inp.val() === '';
            if (empty && isRequired) {
                return inp.data('label');
            }
            return true;
        }

        ContactAD.initialiseDialogTriggers = function (contactDialog) {
            contactDialog.content.on('click', '.js-add-new-contact', function (event) {
                event.preventDefault();
                let data = ContactAD.getDataFromAddNewContactDialog(contactDialog);
                if (data) {
                    ContactAD.AddContact();
                    contactDialog.close();
                }
            });
        };

        ContactAD.openAddNewContactDialog = function (adderDialog) {
            let filter = ContactAD.selectedData.consent_patient_relationship_id;
            let contactDialog = new OpenEyes.UI.Dialog($.extend({}, this.addContactDialogOptions, {
                url: "<?= Yii::app()->createUrl('/OphTrConsent/default'); ?>/ContactPage",
                width: 500,
                data: {
                    returnUrl: "",
                    selected_contact_type_id: filter,
                    patient_id: window.OE_patient_id || null
                }
            }));
            contactDialog.open();
            adderDialog.initialiseDialogTriggers(contactDialog);
        };

        ContactAD.revalidateSearchForm = function (search) {
            search_input.val(' ');
            search_input.show();

            if (search !== false) {
                search_input.keyup();
            }

            $('#patient_contact_adder').find('.js-search-results').html('');
        }

        ContactAD.createNewPatientContact = function () {
            this.revalidateSearchForm();
        }

        ContactAD.alert = function (msg) {
            new OpenEyes.UI.Dialog.Alert({
                content: msg
            }).open();

        }

        ContactAD.AddContact = function () {
            let signature_require = ContactAD.selectedData.signature_require;
            let $table = $("#js-patient_contacts tbody");
            let templateObj = $('#js-mustache_template tbody').clone();
            let jsonData = JSON.stringify(ContactAD.selectedData);
            let jsonInput = templateObj.find('.contact_json_data');
            let signatureRequireContainer = templateObj.find('.js-signature-needed');
            let signatureRequireRadioContainer = templateObj.find('.js-signature-needed-radio');

            if(signature_require === 2){
                signatureRequireContainer.remove();
                signatureRequireRadioContainer.show();
            } else {
                signatureRequireRadioContainer.remove();
                signatureRequireContainer.show();
            }

            jsonInput.val(jsonData);

            let templateText = templateObj.html();
            $row = Mustache.render(templateText, ContactAD.selectedData);
            $table.append($row);
        }

        ContactAD.initMenuButton = function () {
            let adder_dialog = this;
            let other_patient_relationship = $("#patient_contact_adder").find("[data-action='setOtherRelationsip']");
            let other_patient_contact_method = $("#patient_contact_adder").find("[data-action='setOtherContactMethod']");

            let _inp1 = '<input type="text" style="display: none;" placeholder="Relationship description" name="other_patient_relationship" id="other_patient_relationship_input">';
            other_patient_relationship.closest('ul').append($(_inp1));

            let _inp2 = '<input type="text" style="display: none;" placeholder="Contact Method description" name="other_patient_contact_method" id="other_patient_contact_method_input">';
            other_patient_contact_method.closest('ul').append($(_inp2));

            // Global variable
            otherRelationsipInput = $('#other_patient_relationship_input');
            otherContactMethodInput = $('#other_patient_contact_method_input');

            other_patient_relationship.on('click', function () {
                let active = !$(this).hasClass('selected');
                if (active) {
                    otherRelationsipInput.show();
                } else {
                    otherRelationsipInput.hide();
                }
                otherRelationsipInput.focus();
            });

            other_patient_contact_method.on('click', function () {
                let active = !$(this).hasClass('selected');
                if (active) {
                    otherContactMethodInput.show();
                } else {
                    otherContactMethodInput.hide();
                }
                otherContactMethodInput.focus();
            });

            otherRelationsipInput.on('blur', function () {
                let other_patient_relationship = $("#patient_contact_adder").find("[data-action='setOtherRelationsip']");
                let active = other_patient_relationship.hasClass('selected');
                if (active) {
                    return true;
                }
                if ($(this).val() === "") {
                    $(this).hide();
                }
            });

            otherContactMethodInput.on('blur', function () {
                let other_patient_contact_method = $("#patient_contact_adder").find("[data-action='setOtherContactMethod']");
                let active = other_patient_contact_method.hasClass('selected');
                if (active) {
                    return true;
                }
                if ($(this).val() === "") {
                    $(this).hide();
                }
            });

            $('[data-id="adder_dialog_patient_contact_button"]').on('click', function () {
                let active = $(this).hasClass('selected');
                adder_dialog.options.searchOptions.searchSource = $(this).data('search_url');
                adder_dialog.revalidateSearchForm(!active); // autoSearch off
            });

            $('[data-id="adder_dialog_openeyes_users_contact_button"]').on('click', function () {
                let active = $(this).hasClass('selected');
                adder_dialog.options.searchOptions.searchSource = $(this).data('search_url');
                adder_dialog.revalidateSearchForm(!active);
            });

            $('[data-id="adder_dialog_add_new_contact_button"]').on('click', function () {
                let active = $(this).hasClass('selected');
                adder_dialog.options.searchOptions.searchSource = $(this).data('search_url');
                adder_dialog.createNewPatientContact(!active);
            });
        }

        ContactAD.validateReturnData = function (selectedItems) {
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
                            if (otherRelationsipInput.val().length === 0) {
                                hasOtherRelationshipDescription = false;
                            }
                        }

                    }
                    if (item.itemSet.options.header === "Contact method") {
                        hasContactMethod = true;
                    }
                } else {
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
            if(!hasContactMethod){
                errors.push("Please select a contact method.");
            }

            if (errors.length > 0) {
                ContactAD.alert(errors.join("<br> "));
                return false;
            }
            return true;
        }

        new OpenEyes.UI.AdderDialog({
            id: 'patient_contact_adder',
            openButton: $('#add_patient_contact_button'),
            ulClass: "category-filter",
            listFilter: true,
            itemSets:
                $.map(<?= CJSON::encode($element->getContactTypeItemSet()) ?>, function ($itemSet) {
                    return new OpenEyes.UI.AdderDialog.ItemSet($itemSet.items, {
                        'header': $itemSet.header,
                        'multiSelect': $itemSet.multiSelect
                    });
                }),
            onReturn: function (adderDialog, selectedItems) {
                let data = {};
                if (selectedItems.length === 0) {
                    adderDialog.alert("Please select a contact.");
                    return false;
                }
                if (!adderDialog.validateReturnData(selectedItems)) {
                    return false;
                }

                let jsonData = '';
                let selectedContactTypeData = selectedItems[0];
                let selectedContactData = selectedItems[2];
                let selectedRelationshipData = selectedItems[1];
                let selectedContactMethodData = selectedItems[3];
                let addNewContact = selectedContactData.type === "custom";

                data = selectedContactData;
                data.relationship = selectedRelationshipData.label;
                data.consent_patient_relationship_id = selectedRelationshipData.item_id;
                data.consent_patient_contact_method_id = selectedContactMethodData.item_id;
                data.consent_patient_contact_method = selectedContactMethodData.label
                data.uniqueId = Math.random().toString(16).slice(2);
                data.contact_type_id = selectedContactTypeData.contact_type_id;
                data.signature_require = selectedContactMethodData.signature_require;
                data.signature_require_string = selectedContactMethodData.signature_require_string;

                data.existing_id = '';

                if (data.relationship.toLowerCase() === 'other') {
                    data.other_relationship = $('#other_patient_relationship_input').val();
                    data.relationship = data.other_relationship;
                }

                if (data.consent_patient_contact_method.toLowerCase() === 'other') {
                    data.other_contact_method = $('#other_patient_contact_method_input').val();
                }

                ContactAD.selectedData = data;

                if (addNewContact) {
                    adderDialog.openAddNewContactDialog(adderDialog);
                } else {
                    adderDialog.AddContact();
                }

                return true;
            },
            onClose: function () {
                otherRelationsipInput.hide();
            },
            onOpen: function (adder_dialog) {
                search_input = $('#patient_contact_adder').find('.js-search-autocomplete');
                create_new_patient_contact_btn = $('#create_new_patient_contact_btn');
                create_new_patient_contact_btn.hide();

                adder_dialog.popup.find('li').each(function () {
                    $(this).removeClass('selected');
                });
                adder_dialog.revalidateSearchForm(false);
                adder_dialog.initMenuButton();
            },
            searchOptions: {
                searchSource: "",
                code: window.OE_patient_id || null,
            },
            enableCustomSearchEntries: true,
            searchAsTypedPrefix: 'Add a new contact:',
        });

        // Change columns 2-3
        jQuery.each($("#patient_contact_adder tr"), function () {
            $(this).children(":eq(3)").after($(this).children(":eq(2)"));
        });

        jQuery.each($("#patient_contact_adder thead"), function () {
            $(this).children(":eq(3)").after($(this).children(":eq(2)"));
        });
    });
</script>