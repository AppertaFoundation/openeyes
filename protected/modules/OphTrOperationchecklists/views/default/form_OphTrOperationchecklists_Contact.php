<?php
/**
 * (C) Copyright Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php
$patientId = $this->patient->id;
?>
<td>
    <div class="add-data-actions flex-item-bottom">
        <button class="button hint green js-add-select-search" id="add-contacts-btn" type="button">
            <i class="oe-i plus pro-theme"></i>
        </button>
    </div>
</td>

<script type="text/javascript">
    $(document).ready(function () {
        <?php
        $criteria = new CDbCriteria();
        $criteria->select = array('name', 'id', 'max_number_per_patient');
        $criteria->group = 'name';
        $criteria->distinct = true;
        $criteria->addInCondition("LOWER(name)", ["carer", "parent", "relative", "spouse", "paramedic"]);
        $contact_labels = ContactLabel::model()->findAll($criteria);?>

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
                if (selectedItems.length === 2){
                    $('[name="<?= $name_stub . '[' . $question_id . ']'?>[answer]"]').val(selectedItems[1].name);
                    $('[name="<?= $name_stub . '[' . ($question_id + 1) . ']'?>[answer]"]').val(selectedItems[1].phone);
                }
            },
            searchOptions: {
                searchSource: "/OphCiExamination/contact/patientcontacts",
                code: "<?= $patientId ?>",
            },
            enableCustomSearchEntries: false,
            filter: true,
            filterDataId: "contact-type-filter",
        });
    });
</script>