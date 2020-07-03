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

<table class="standard">
    <colgroup>
        <col class="cols-3">
    </colgroup>
    <tbody>
    <tr>
        <td>
            <?php echo $form->checkBox($element, 'correspondence_in_large_letters') ?>
        </td>
    </tr>
    <tr>
        <td>
            <div class="flex-layout">
                <div class="cols-4 column">
                    <!-- Not using the $form->checkBox because it changes the label to be 2 cols and field to be 10 cols, which causes the checkbox to be overlapped with the label -->
                    <label for="<?=\CHtml::modelName($element).'_agrees_to_insecure_email_correspondence';?>">
                        <?=\CHtml::encode($element->getAttributeLabel('agrees_to_insecure_email_correspondence'))?>:
                    </label>
                    <?=\CHtml::hiddenField(CHtml::modelName($element)."[agrees_to_insecure_email_correspondence]", '0', array('id' => CHtml::modelName($element).'_agrees_to_insecure_email_correspondence_hidden'))?>
                    <?=\CHtml::checkBox(CHtml::modelName($element)."[agrees_to_insecure_email_correspondence]", $element->agrees_to_insecure_email_correspondence)?>
                </div>
                <div class="cols-8 column">
                    <?php
                    // If it is the new record, get the patient_id from the url
                    $patientId = Yii::app()->request->getQuery('patient_id', null);
                    if (!isset($patientId)) {
                        // patientId will be null on updating the event
                        $eventId = $element->event_id;
                        $event = Event::model()->findByPk($eventId);
                        $patientId = $event->episode->patient_id;
                    }
                    $patient = Patient::model()->findByPk($patientId);
                    $address = $patient->contact->address;
                    $postAddress = Yii::app()->request->getPost('Address');
                    if (isset($postAddress)) {
                        $address->email = $postAddress['email'];
                    }
                    ?>
                    <span id="patient_email_address_container" class="flex-layout flex-left">
                        <label for="patient_email_address">Email:</label>
                        <input type="text" name="Address[email]" id="patient_email_address" placeholder="Enter Email Address" size="40" value="<?= $address->email; ?>" />
                        <input type="hidden" name="Address[id]" value="<?= $address->id; ?>"/>
                    </span>
                </div>
            </div>
        </td>
    </tr>
    </tbody>
</table>

<script>
    $(document).ready(function () {
        const agreeToInsecureEmailElementId = "OEModule_OphCiExamination_models_Element_OphCiExamination_CommunicationPreferences_agrees_to_insecure_email_correspondence";
        togglePatientEmailContainer(agreeToInsecureEmailElementId); // this is launched on load
        document.getElementById(agreeToInsecureEmailElementId).onclick = function(e){
            togglePatientEmailContainer(agreeToInsecureEmailElementId); // this is launched on checkbox click
        };
    });

    function togglePatientEmailContainer(agreeToInsecureEmailElementId) {
        const patientEmailAddressContainerElementId = "patient_email_address_container";
        if (document.getElementById(agreeToInsecureEmailElementId).checked) {
            document.getElementById(patientEmailAddressContainerElementId).style.display = 'block';
        } else {
            document.getElementById(patientEmailAddressContainerElementId).style.display = 'none';
        }
    }
</script>