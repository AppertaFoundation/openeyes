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
    <div class="data-group flex-layout cols-10">
        <input type="hidden" name="<?= $model_name ?>[present]" value="1"/>
        <table id="<?= $model_name ?>_entry_table"
               class=" cols-full <?php echo $element_errors ? 'highlighted-error error' : '' ?>">
            <colgroup>
                <col class="cols-1">
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
            <?php
            foreach ($this->contacts as $contact) { ?>
                <tr>
                    <td><?= $contact->label ? $contact->label->name : ""; ?></td>
                    <td><?= $contact->getFullName(); ?></td>
                    <td><?= $contact->address ? $contact->address->email : ""; ?></td>
                    <td><?= $contact->primary_phone; ?></td>
                    <td><?= $contact->address ? $contact->address->getLetterLine() : ""; ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="flex-layout flex-right">
        <div class="add-data-actions flex-item-bottom" id="contacts-popup">
            <button class="button hint green js-add-select-search" id="add-contacts-btn" type="button">
                <i class="oe-i plus pro-theme"></i>
            </button>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function () {

            <?php $contacts = \Contact::model()->getActiveContacts($this->patient->id);
            ?>
            new OpenEyes.UI.AdderDialog({
                openButton: $('#add-contacts-btn'),
                itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                    array_map(function ($key, $contact) {
                        return ['label' => $contact['first_name'] . " (" . $contact->label->name . ")",
                            'id' => $contact['id'],
                            'title' => $contact->title,
                            'last_name' => $contact->last_name,
                            'first_name' => $contact->first_name,
                            'contact_label' => $contact->label ? $contact->label->name : "",
                        ];
                    }, array_keys($contacts), $contacts)
                ) ?>, {'multiSelect': true})],
                onReturn: function (adderDialog, selectedItems) {
                    for (let i = 0; i < selectedItems.length; ++i) {

                    }
                },
                searchOptions: {
                    searchSource: ""
                },
                enableCustomSearchEntries: true,
            });
        });
    </script>