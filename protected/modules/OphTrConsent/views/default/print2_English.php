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
$secondary_identifier = PatientIdentifierHelper::getIdentifierForPatient(Yii::app()->params['display_secondary_number_usage_code'], $this->patient->id, $institution_id, $site_id);
?>
<main class="print-main">
    <?php $this->renderPartial('_consent_header') ?>
    <h1 class="print-title">
        Consent form 2<br />
        Parental agreement to investigation or treatment for a child or young person
    </h1>
    <h3>Patient details (or pre-printed label)</h3>
    <table class="large">
        <colgroup>
            <col class="cols-5">
            <col class="cols-7">
        </colgroup>
        <tr>
            <th>Patient's surname/family name</th>
            <td><?= $this->patient->last_name ?></td>
        </tr>
        <tr>
            <th>Patient's first names</th>
            <td><?= $this->patient->first_name ?></td>
        </tr>
        <tr>
            <th>Date of birth</th>
            <td><?= $this->patient->NHSDate('dob') ?></td>
        </tr>
        <tr>
            <th><?= PatientIdentifierHelper::getIdentifierPrompt($primary_identifier) ?></th>
            <td><?= PatientIdentifierHelper::getIdentifierValue($primary_identifier) ?></td>
        </tr>
        <tr>
            <th><?= PatientIdentifierHelper::getIdentifierPrompt($secondary_identifier) ?></th>
            <td><?= PatientIdentifierHelper::getIdentifierValue($secondary_identifier) ?></td>
        </tr>
        <tr>
            <th>Gender</th>
            <td><?= $this->patient->genderString ?></td>
        </tr>
        <tr>
            <th>&nbsp;<br />Special requirements</th>
            <td>&nbsp;<div class="dotted-write"></div>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>(e.g. other language/other communication method)</td>
        </tr>
        <tr>
            <th>Witness required</th>
            <td><?= $elements['Element_OphTrConsent_Other']->witness_required ? 'Yes' : 'No' ?></td>
        </tr>
        <?php if ($elements['Element_OphTrConsent_Other']->witness_required) { ?>
            <tr>
                <th>Witness name</th>
                <td><?= $elements['Element_OphTrConsent_Other']->witness_name ?></td>
            </tr>
        <?php } ?>
        <tr>
            <th>Interpreter required</th>
            <td><?= $elements['Element_OphTrConsent_Other']->interpreter_required ? 'Yes' : 'No' ?></td>
        </tr>
        <?php if ($elements['Element_OphTrConsent_Other']->interpreter_required) { ?>
            <tr>
                <th>Interpreter name</th>
                <td><?= $elements['Element_OphTrConsent_Other']->interpreter_name ?></td>
            </tr>
        <?php } ?>
        <tr>
            <th>Procedure(s)</th>
            <td>
                <?php foreach ($elements['Element_OphTrConsent_Procedure']->procedure_assignments as $i => $procedure) {
                    if ($i > 0) {
                        echo ', ';
                    }
                    echo \CHtml::encode($procedure->eye->adjective) . ' - ' . \CHtml::encode($procedure->proc->term);
                } ?>
            </td>
        </tr>
        <?php if ($elements['Element_OphTrConsent_AdvancedDecision']->description) { ?>
            <tr>
                <th>Advanced Decision</th>
                <td><?= $elements['Element_OphTrConsent_AdvancedDecision']->description ?></td>
            </tr>
        <?php } ?>
        <tr>
            <th>&nbsp;<br />Consent date</th>
            <td>&nbsp;<div class="dotted-write"></div>
            </td>
        </tr>
    </table>
    <h4>To be retained in patient's notes</h4>
    <div class="break"></div>
    <h3>Statement of parent</h3>
    <p>
        Please read this form carefully. If the procedure has been planned in advance, you should already have your own
        copy of page 3 which describes the benefits and risks of the proposed treatment. If not, you will be offered a
        copy now. If you have any further questions, do ask - we are here to help you and your child. You have the right
        to change your mind at any time, including after you have signed this form.
    </p>
    <p>
        <strong>I agree</strong> to the procedure or course of treatment described on this form and I confirm that I
        have 'parental responsibility' for this child.<br />
        <strong>I understand</strong> that you cannot give me a guarantee that a particular person will perform the
        procedure. The person will, however, have appropriate experience.<br />
        <strong>I understand</strong> that my child and I will have the opportunity to discuss the details of
        anaesthesia with an anaesthetist before the procedure, unless the urgency of the situation prevents this. (This
        only applies to children having general or regional anaesthesia.)<br />
        <strong>I understand that any</strong> procedure in addition to those described on this form will only be
        carried out if it is necessary to save the life of my child or to prevent serious harm to his or her
        health.<br />
        <strong>I have been told about</strong> additional procedures which may become necessary during my child's
        treatment. I have listed below any procedures which I do not wish to be carried out without further discussion:
    <div class="dotted-write"></div>
    </p>
    <?= $this->renderPartial('signature_table5', array('vi' => ($css_class == 'impaired'))) ?>
    <div class="break"></div>
    <h3>Child's agreement to treatment (if child wishes to sign)</h3>
    <p>
        I agree to have the treatment I have been told about.
    </p>
    <?= $this->renderPartial('signature_table3', array('vi' => ($css_class == 'impaired'), 'name' => $this->patient->first_name . ' ' . $this->patient->last_name)) ?>
    <h3>Confirmation of consent <span class="noth3">(to be completed by a health professional when the child is admitted for the procedure, if the parent/child have signed the form in advance)</span>
    </h3>
    <p>
        On behalf of the team treating the patient, I have confirmed with the child and his or her parent(s) that they
        have no further questions and wish the procedure to go ahead.
    </p>
    <?= $this->renderPartial('signature_table1', array('vi' => ($css_class == 'impaired'), 'consultant' => $elements['Element_OphTrConsent_Other']->consultant, 'mask_consultant' => true)) ?>
    <h3>Important notes: (tick if applicable)</h3>
    <p>
        See also advance directive/living will (eg Jehovah's Witness form)
    </p>
    <p>
        Parent has withdrawn consent (ask parent to sign /date here)
    <div class="dotted-write" style="display: block"></div>
    </p>
    <div class="break"></div>
    <h3>Name of proposed procedure or course of treatment</h3>
    <?= $this->renderPartial('_proposed_procedures', array('css_class' => 'large', 'procedures' => $elements['Element_OphTrConsent_Procedure']->procedure_assignments)) ?>
    <h3>Statement of health professional <span class="noth3">(to be filled in by a health professional with appropriate knowledge of the proposed procedure(s), as specified in the consent policy)</span>
    </h3>
    <p>
        <strong>I have explained the procedure to the patient. In particular, I have explained:</strong>
    </p>
    <p>
        <strong>The intended benefits:</strong>
        <br><?= nl2br($elements['Element_OphTrConsent_BenefitsAndRisks']->benefits) ?><br />
        <strong>Serious, frequently occurring or unavoidable risks:</strong>
        <br><?= nl2br($elements['Element_OphTrConsent_BenefitsAndRisks']->risks) ?>
    </p>
    <?php if (!empty($elements['Element_OphTrConsent_Procedure']->additionalprocedure_assignments)) { ?>
            <p>Any extra procedures which may become necessary during the procedure(s)</p>
            <?= $this->renderPartial('_proposed_procedures', array('css_class' => 'large', 'procedures' => $elements['Element_OphTrConsent_Procedure']->additionalprocedure_assignments)) ?>
        <?php } ?>
    <p>
        I have also discussed what the procedure is likely to involve, the benefits and risks of any available
        alternative treatments (including no treatment) and any particular concerns of this patient
        and <?= $this->patient->pos ?> parents.
    </p>
    <?php if ($elements['Element_OphTrConsent_Leaflets']->leaflets) { ?>
        <div class="group flex-layout">
            <span class="nowrap">
                <span class="checkbox <?= $elements['Element_OphTrConsent_Leaflets']->leaflets ? 'checked' : '' ?>"> </span>The following informational leaflets have been provided:
                <?= $this->renderPartial('view_Element_OphTrConsent_Leaflets', ['element' => $elements['Element_OphTrConsent_Leaflets']]) ?>
            </span>
        </div>
    <?php } ?>
    <?php if ($elements['Element_OphTrConsent_Other']->anaesthetic_leaflet) { ?>
        <div class="group">
            <span class="checkbox <?= $elements['Element_OphTrConsent_Other']->anaesthetic_leaflet ? 'checked' : '' ?>"></span> Anaesthesia leaflet has been provided
        </div>
    <?php } ?>
    <div class="group">
        This procedure will involve: <span class="checkbox <?= $elements['Element_OphTrConsent_Procedure']->hasAnaestheticTypeByCode('GA') ? 'checked' : '' ?>"></span>
        general and/or regional anaesthesia&nbsp;&nbsp;<span class="checkbox <?= $elements['Element_OphTrConsent_Procedure']->hasAnaestheticTypeByCode(array('Topical', 'LAC', 'LA', 'LAS')) ? 'checked' : '' ?>"></span>
        local anaesthesia&nbsp;&nbsp;<span class="checkbox <?= $elements['Element_OphTrConsent_Procedure']->hasAnaestheticTypeByCode('Sedation') ? 'checked' : '' ?>"></span> sedation
    </div>
    <?= $this->renderPartial('signature_table1', array('vi' => ($css_class == 'impaired'), 'consultant' => $elements['Element_OphTrConsent_Other']->consultant)) ?>
    <br />
    <?php if ($elements['Element_OphTrConsent_Other']->interpreter_required) { ?>
        <h3>Statement of interpreter</h3>
        <span>I have interpreted the information above to the child and <?= $this->patient->pos ?> parents to the best of my ability and in a way in which I believe they can understand.</span>
        <br /><br />
        <?= $this->renderPartial('signature_table3', array('vi' => ($css_class == 'impaired'), 'name' => $elements['Element_OphTrConsent_Other']->interpreter_name)) ?>
    <?php } ?>

    <div class="break"></div>
    <p>Top copy accepted by patient: yes/no (please ring)</p>
    <?php if ($elements['Element_OphTrConsent_Other']->include_supplementary_consent) { ?>
        <div class="break"></div>
        <h2>Form 2: Supplementary consent</h2>
        <h3>Images</h3>
        <p>
            Photographs, x-rays or other images may be taken as part of your child's treatment and will form part of the
            medical record. It is very unlikely that your child would be recognised from these images. If however your
            child could be recognised we would seek your specific consent before any particular publication.
        </p>
        <p>
            <strong>I agree to use in audit, education and publication:</strong>
        </p>
        <div class="group">
            <span class="checkbox <?= $elements['Element_OphTrConsent_Permissions']->images->name == 'Yes' ? 'checked' : '' ?>"></span>
            Yes
            <span class="checkbox <?= $elements['Element_OphTrConsent_Permissions']->images->name == 'No' ? 'checked' : '' ?>"></span>
            No
            <span class="checkbox <?= $elements['Element_OphTrConsent_Permissions']->images->name == 'Not applicable' ? 'checked' : '' ?>"></span>
            Not applicable
        </div>
        <p>
            If you do not wish to take part in the above, your care will not be compromised in any way.
        </p>
        <p>
            Signature of Parent/Guardian
        <div class="dotted-write"></div>
        </p>
        <p>
            Date
        <div class="dotted-write"></div>
        </p>
    <?php } ?>
</main>