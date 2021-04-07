<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2018
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
$institution_id = Institution::model()->getCurrent()->id;
$site_id = Yii::app()->session['selected_site_id'];
$primary_identifier = PatientIdentifierHelper::getIdentifierForPatient(Yii::app()->params['display_primary_number_usage_code'], $this->patient->id, $institution_id, $site_id);
?>
<main class="print-main">
    <?php $this->renderPartial('_consent_header') ?>
    <h1 class="print-title">
        Consent form 3<br/>
        <?= $this->patient->fullName.', '. PatientIdentifierHelper::getIdentifierPrompt($primary_identifier) .': '. PatientIdentifierHelper::getIdentifierValue($primary_identifier) ?>
    </h1>
    <h3>Patient/parental agreement to investigation or treatment (procedures where consciousness not impaired)</h3>
    <p>
        <strong>Procedure(s):</strong>
        <?= $elements['Element_OphTrConsent_Procedure']->eye ?>
        <?php foreach ($elements['Element_OphTrConsent_Procedure']->procedures as $i => $procedure) {
            if ($i > 0) {
                echo ', ';
            }
            echo \CHtml::encode($procedure->term);
        } ?>
    </p>
    <p>
        <strong>Statement of health professional</strong> (to be filled in by health professional with appropriate
        knowledge of proposed procedure, as specified in consent policy)
    </p>
    <p>
        <strong>I have explained the procedure to the patient/parent. In particular, I have explained:</strong>
        <br/>
    </p>
    <p>
        <strong>The intended benefits:</strong>
        <br><?= nl2br($elements['Element_OphTrConsent_BenefitsAndRisks']->benefits) ?>
        <br>
    </p>
    <p>
        <strong>Serious, frequently occurring or unavoidable risks:</strong>
        <br><?= nl2br($elements['Element_OphTrConsent_BenefitsAndRisks']->risks) ?>
    </p>
    <?php if (!empty($elements['Element_OphTrConsent_Procedure']->additional_procedures)) { ?>
        <p>Any extra procedures which may become necessary during the procedure(s):</p>
        <?= $this->renderPartial('_proposed_procedures', array('css_class' => 'large', 'procedures' => $elements['Element_OphTrConsent_Procedure']->additional_procedures, 'eye' => $elements['Element_OphTrConsent_Procedure']->eye->adjective)) ?>
    <?php } ?>
    <p>
        I have also discussed what the procedure is likely to involve, the benefits and risks of any available
        alternative treatments (including no treatment) and any particular concerns of those involved.
    </p>
    <?php if ($elements['Element_OphTrConsent_Leaflets']->leaflets) { ?>
        <div class="group flex-layout">
                <span class="nowrap">
                    <span class="checkbox <?= $elements['Element_OphTrConsent_Leaflets']->leaflets ? 'checked' : '' ?>"> </span>The following informational leaflets have been provided:
                    <?= $this->renderPartial('view_Element_OphTrConsent_Leaflets', ['element' => $elements['Element_OphTrConsent_Leaflets']]) ?>
                </span>
        </div>
    <?php } ?>
    <?= $this->renderPartial('signature_table1', array('vi' => ($css_class == 'impaired'), 'consultant' => $elements['Element_OphTrConsent_Other']->consultant)) ?>
    <div class="break"></div>
    <?php if ($elements['Element_OphTrConsent_Other']->interpreter_required) { ?>
        <h3>Statement of interpreter</h3>
        <p>
            I have interpreted the information above to the patient/parent to the best of my ability and in a way in
            which I believe s/he/they can understand.
        </p>
        <?= $this->renderPartial('signature_table3', array('vi' => ($css_class == 'impaired'), 'name' => $elements['Element_OphTrConsent_Other']->interpreter_name)) ?>
    <?php } ?>
    <h3>Statement of patient/person with parental responsibility for patient I agree to the procedure described
        above.</h3>
    <p>
        I understand that you cannot give me a guarantee that a particular person will perform the procedure. The person
        will, however, have appropriate experience. I understand that the procedure will/will not involve local
        anaesthesia.
    </p>
    <?= $this->renderPartial('signature_table2', array('vi' => ($css_class == 'impaired'))) ?>
    <p>
        Confirmation of consent (to be completed by a health professional when the patient is admitted for the
        procedure, if the patient/parent has signed the form in advance) I have confirmed that the patient/parent has no
        further questions and wishes the procedure to go ahead.
    </p>
    <?= $this->renderPartial('signature_table1', array('vi' => ($css_class == 'impaired'), 'consultant' => $elements['Element_OphTrConsent_Other']->consultant, 'mask_consultant' => true)) ?>
    <h3>Top copy accepted by patient: yes/no <span class="noth3">(please ring)</span></h3>
</main>
