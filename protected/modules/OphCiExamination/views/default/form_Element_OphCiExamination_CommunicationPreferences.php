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
            <?php echo $form->checkBox(
                $element,
                'correspondence_in_large_letters',
                array('nowrapper' => true)
            ) ?>
        </td>
    </tr>
    <tr>
        <td>
            <div class="flex-layout">
                <div class="cols-4 column">
                    <?php echo $form->checkBox(
                        $element,
                        'agrees_to_insecure_email_correspondence',
                        array('nowrapper' => true, 'data-test' => 'agrees_to_insecure_email_correspondence')
                    ) ?>
                </div>
                <div class="cols-8 column">
                    <?php
                    $patient = $this->patient;
                    $address = $patient->contact->address;
                    $contact = $patient->contact;
                    $emailAddress = Yii::app()->request->getPost('Contact');
                    if (isset($emailAddress)) {
                        $contact->email = $emailAddress['email'];
                    }
                    ?>
                    <span id="patient_email_address_container" class="flex-layout flex-left">
                        <label for="patient_email_address">Email:</label>
                        <input type="text" name="Contact[email]" id="patient_email_address" placeholder="Enter Email Address" size="40" value="<?= $contact->email; ?>" />
                        <input type="hidden" name="Contact[id]" value="<?= $contact->id; ?>"/>
                    </span>
                </div>
            </div>
        </td>
    </tr>
    <?php
    $exam_api = Yii::app()->moduleAPI->get('OphCiExamination');
    $examination_communication_preferences = $exam_api->getLatestElement('OEModule\OphCiExamination\models\Element_OphCiExamination_CommunicationPreferences', $patient);
    if ($examination_communication_preferences && !is_null($examination_communication_preferences->language_id)) {
        $element->language_id = $examination_communication_preferences->language_id;
    }
    if ($examination_communication_preferences && !is_null($examination_communication_preferences->interpreter_required_id)) {
        $element->interpreter_required_id = $examination_communication_preferences->interpreter_required_id;
    }
    ?>
    <tr>
        <td>
            <?php echo $form->dropDownList($element, 'language_id',
                CHtml::listData(Language::model()->findAll(array('order' => 'name asc')), 'id', 'name') + array('0'=>'Other'), array('empty' => '- Please select -'), false, array('label' => 6, 'field' => 6)) ?>
        </td>
    </tr>
    <tr>
        <td>
            <?php echo $form->dropDownList($element, 'interpreter_required_id',
                CHtml::listData(Language::model()->findAll(array('order' => 'name asc')), 'id', 'name') + array('0'=>'Other'), array('empty' => '- Please select -'), false, array('label' => 6, 'field' => 6)) ?>
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