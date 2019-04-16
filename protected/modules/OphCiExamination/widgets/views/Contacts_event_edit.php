<?php
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
?>

<?php

$model_name = CHtml::modelName($element);
$element_errors = $element->getErrors();
?>
<div class="element-fields full-width" id="<?= $model_name ?>_element">
    <div class="data-group cols-full">
        <h1>PAS Contacts</h1>
        <input type="hidden" name="<?= $model_name ?>[present]" value="1"/>
        <div class="cols-full">
            <table id="<?= $model_name ?>_entry_table"
                   class=" cols-full <?php echo $element_errors ? 'highlighted-error error' : '' ?>">
                <colgroup>
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-2">
                </colgroup>
                <thead>
                <tr>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Address</th>
                </tr>
                </thead>
                <tbody>
                <?= $this->render(
                    'ContactsEntry_event_edit',
                    array(
                        'contact' => $this->patient->gp->contact,
                        'show_comments' => false,
                        'model_name' => $model_name,
                        'removable' => false,
                        'is_template' => true,)); ?>
                </tbody>
            </table>
        </div>
        <hr class="divider">
        <h1>Patient Contacts</h1>
        <div class="cols-full">
            <table id="<?= $model_name ?>_entry_table"
                   class=" cols-full <?php echo $element_errors ? 'highlighted-error error' : '' ?>">
                <colgroup>
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-1">
                </colgroup>
                <thead>
                <tr>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Address</th>
                    <th>Comments</th>
                    <th></th>
                </tr>
                </thead>
                <tbody id="contact-assignment-table">
                <?php
                foreach ($this->contact_assignments as $contact_assignment) { ?>
                    <?= $this->render(
                        'ContactsEntry_event_edit',
                        array(
                            'element' => $element,
                            'form' => $form,
                            'entry' => $contact_assignment,
                            'show_comments' => true,
                            'model_name' => $model_name,
                            'removable' => true,
                            'is_template' => true
                        )
                    ); ?>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex-layout flex-right">
        <div class="add-data-actions flex-item-bottom" id="contacts-popup">
            <button class="button hint green js-add-select-search" id="add-contacts-btn" type="button">
                <i class="oe-i plus pro-theme"></i>
            </button>
        </div>
    </div>
</div>

<script type="text/template" class="entry-template hidden" id="contact-entry-template">
    <?php

    $empty_entry = new PatientContactAssignment();
    echo $this->render(
        'ContactsEntry_event_edit',
        array(
            'entry' => $empty_entry,
            'model_name' => $model_name,
            'form' => $form,
            'show_comments' => true,
            'removable' => true,
            'is_template' => true,
            'values' => array(
                'id' => '{{id}}',
                'label' => '{{label}}',
                'full_name' => '{{full_name}}',
                'email' => '{{email}}',
                'phone' => '{{phone}}',
                'address' => '{{address}}'
            ),
        )
    );
    ?>
</script>

<script type="text/javascript">
    $(document).ready(function () {

        <?php $contact_labels = ContactLabel::model()->findAll(
        [
            'condition' => 'is_private = 0',
            'select' => 't.name,t.id',
            'group' => 't.name',
            'distinct' => true,
        ]
    );
        ?>

        // removal button for table entries
        $('#contact-assignment-table').on('click', 'i.trash', function (e) {
            e.preventDefault();
            $(e.target).parents('tr').remove();
        });
        // Default dialog options.
        var options = {
            id: 'site-and-firm-dialog',
            title: 'Add a new contact'
        };

        new OpenEyes.UI.AdderDialog({
            itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                array_map(function ($contact_label) {
                    return ['label' => $contact_label->name, 'id' => $contact_label->id];
                }, $contact_labels)) ?>, {'header': 'Contact Type', 'id': 'contact-type-filter'})],
            openButton: $('#add-contacts-btn'),
            onReturn: function (adderDialog, selectedItems) {
                let templateText = $('#contact-entry-template').text();
                let newRows = [];
                for (let index = 0; index < selectedItems.length; ++index) {

                    if (selectedItems[index].type === "custom") {
                        new OpenEyes.UI.Dialog($.extend({}, options, {
                            url: baseUrl + '/OphCiExamination/contact/ContactPage',
                            width: 500,
                            data: {
                                returnUrl: "",
                                patient_id: window.OE_patient_id || null
                            }
                        })).open();
                    } else {
                        data = {};
                        data.id = selectedItems[index].id;
                        data.label = selectedItems[index].contact_label;
                        data.full_name = selectedItems[index].name;
                        data.email = selectedItems[index].email;
                        data.phone = selectedItems[index].phone;
                        data.address = selectedItems[index].address;
                        row = Mustache.render(templateText, data);
                        newRows.push(row);
                    }
                }
                $('#contact-assignment-table').append(newRows);
                $('.autosize').autosize();
            },
            searchOptions: {
                searchSource: "/OphCiExamination/contact/autocomplete"
            },
            enableCustomSearchEntries: true,
            searchAsTypedPrefix: 'Add a new contact:',
            filter: true,
            filterDataId: "contact-type-filter",
        });
    });
</script>