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

    $url = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.assets.js'), true);
    Yii::app()->clientScript->registerScriptFile($url . '/OpenEyes.UI.AdderDialog.Contact.js', CClientScript::POS_END);
?>

<div class="add-data-actions flex-item-bottom">
    <button id="add_patient_contact_button" type="button" class="green hint js-add-select-btn"
            data-popup="add-patient-contact">Add withdrawal
    </button>
</div>

<script>
    $(document).ready(function () {
        function addContactSignatureRow(data) {
            let contact_type_id = data.contact_type_id;
            data.YII_CSRF_TOKEN = $(':input[name="YII_CSRF_TOKEN"]').val();
            data.event_id = OE_event_id;
            $.ajax({
                'type': 'POST',
                'url': baseUrl + '/OphTrConsent/default/saveWithdrawal',
                'data': data,
                'success': function(resp) {
                    if (resp.code === 0) {
                        new OpenEyes.UI.Dialog.Alert({
                            content: resp.message
                        }).open();
                    } else {
                        disableButtons();
                        location.reload();
                    }
                }
            });
        }

        new OpenEyes.UI.AdderDialog.Contact({
            id: 'patient_contact_adder',
            patientId: window.OE_patient_id || null,
            openButton: $('#add_patient_contact_button'),
            width: "600px",
            deselectOnReturn: true,
            deselectOnClose: true,
            newContactDialogURL: "<?= Yii::app()->createUrl('/OphTrConsent/default'); ?>/ContactPage",
            ulClass: "category-filter",
            listFilter: true,
            onReturnCallback: addContactSignatureRow,
            itemSets:
                $.map(<?= CJSON::encode($withdrawal_element->getContactItemSets()) ?>, function ($itemSet) {
                    return new OpenEyes.UI.AdderDialog.ItemSet($itemSet.items, {
                        'header': $itemSet.header,
                        'multiSelect': $itemSet.multiSelect,
                        'id': $itemSet.id
                    });
                }),
            notRequiredItemLabels: [ 'Contact method' ],
            searchOptions: {
                searchSource: "",
                code: window.OE_patient_id || null,
            },
        });

        $('body').on('click', '.remove-patient-contact', function () {
            $(this).closest('tr').remove();
        });
    });
</script>