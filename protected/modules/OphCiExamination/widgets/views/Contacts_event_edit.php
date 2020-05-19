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
<script type="text/javascript" src="<?= $this->getJsPublishedPath('Contacts.js') ?>"></script>
<div class="element-fields full-width" id="<?= $model_name ?>_element">
    <div class="data-group cols-full">
        <h1>PAS Contacts</h1>
        <input type="hidden" name="<?= $model_name ?>[present]" value="1"/>
        <div class="cols-full">
            <table id="<?= $model_name ?>_pas_table"
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
                <?php if (isset($this->patient->gp)) {
                    echo $this->render(
                        'ContactsEntry_event_edit',
                        array(
                            'contact' => $this->patient->gp->contact,
                            'show_comments' => false,
                            'row_count' => 0,
                            'model_name' => $model_name,
                            'field_prefix' => $model_name,
                            'removable' => false,
                        'is_template' => true,
                        )
                    );
                } ?>
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
                <tbody>
                <?php
                $row_count = 0;
                foreach ($this->contact_assignments as $contact_assignment) { ?>
                    <?= $this->render(
                        'ContactsEntry_event_edit',
                        array(
                            'element' => $element,
                            'form' => $form,
                            'entry' => $contact_assignment,
                            'show_comments' => true,
                            'row_count' => $row_count,
                            'field_prefix' => $model_name . '[entries][' . ($row_count) . ']',
                            'model_name' => $model_name,
                            'removable' => true,
                            'is_template' => true
                        )
                    );
                    $row_count++; ?>
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

<script type="text/template" class="entry-template hidden" id="<?= CHtml::modelName($element) . '_entry_template' ?>">
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
            'field_prefix' => $model_name . '[entries][{{row_count}}]',
            'row_count' => '{{row_count}}',
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
        let contactController;
        contactController = new OpenEyes.OphCiExamination.ContactsController({
            modelName: '<?=$model_name?>',
            contactFilterId: 'contact-type-filter'
        });


        <?php $contact_labels = ContactLabel::model()->findAll(
            [
            'select' => 't.name,t.id, t.max_number_per_patient',
            'group' => 't.name',
            'distinct' => true,
            ]
        );?>

        new OpenEyes.UI.AdderDialog({
            itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                array_map(function ($contact_label) {
                    return [
                        'label' => $contact_label->name,
                        'id' => $contact_label->id,
                        'patient_limit' => $contact_label->max_number_per_patient
                    ];
                },
                $contact_labels)
            ) ?>, {'header': 'Contact Type', 'id': 'contact-type-filter'})],
            openButton: $('#add-contacts-btn'),
            onReturn: function (adderDialog, selectedItems) {
                if (!contactController.isContactInTable(selectedItems)) {
                    contactController.addEntry(selectedItems);
                }
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