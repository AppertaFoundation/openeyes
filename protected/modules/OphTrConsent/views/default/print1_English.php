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
$type_assessment = new OphTrConsent_Type_Assessment();
?>
<body class="open-eyes print">
<?php $this->renderPartial('_consent_header') ?>
<div class="print-title text-c">
    <h1><b>Consent form 1</b></h1>
    <h1 class="highlighter">For adults with mental capacity to give valid consent</h1>
    <h2>Patient agreement to investigation or treatment</h2>
</div>
<hr class="divider"/>
<main class="print-main">
    <h3>Patient details (or pre-printed label)</h3>
    <table class="normal-text row-lines">
        <colgroup>
            <col class="cols-5">
            <col class="cols-7">
        </colgroup>
        <tbody>
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
                <th>Witness required</th>
                <td>Yes</td>
            </tr>
            <tr>
                <th>Witness name</th>
                <td>{{witness_name}}</td>
            </tr>
            <tr>
                <th>Interpreter required</th>
                <td>No</td>
            </tr>
            <?php if (isset($elements['Element_OphTrConsent_Procedure']) && $type_assessment->existsElementInConsentForm($elements['Element_OphTrConsent_Procedure']->elementType->id, Element_OphTrConsent_Type::TYPE_PATIENT_AGREEMENT_ID)) : ?>
            <tr>
                <th>Procedure(s)</th>
                <td>
                    <?php foreach ($elements['Element_OphTrConsent_Procedure']->procedure_assignments as $i => $procedure) {
                        if ($i > 0) {
                            echo ', ';
                        }
                        echo \CHtml::encode($procedure->eye) . ' - ' . \CHtml::encode($procedure->proc->term);
                    } ?>
                </td>
            </tr>
            <?php endif; ?>
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
        </tbody>
    </table>
    <hr class="divider">
    <?php if (isset($elements['Element_OphTrConsent_Procedure']) && $type_assessment->existsElementInConsentForm($elements['Element_OphTrConsent_Procedure']->elementType->id, Element_OphTrConsent_Type::TYPE_PATIENT_AGREEMENT_ID)) : ?>
    <div class="group"><h4>Proposed procedure(s) or course of treatment:</h4>
        <div class="indent">
            <?= $this->renderPartial('_proposed_procedures', array('css_class' => 'large', 'procedures' => $elements['Element_OphTrConsent_Procedure']->procedure_assignments)) ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($elements['Element_OphTrConsent_BenefitsAndRisks']) && $type_assessment->existsElementInConsentForm($elements['Element_OphTrConsent_BenefitsAndRisks']->elementType->id, Element_OphTrConsent_Type::TYPE_PATIENT_AGREEMENT_ID)) : ?>
    <div class="group">
        <h4>Statement of health professional (to be filled in by a health professional with appropriate
            knowledge of the proposed procedure(s), as specified in the consent policy)</h4>
        <div class="indent"><p>I have explained the procedure to the patient. In particular, I have explained:</p>
            <p></p><h5>The intended benefits:</h5>
            <?= nl2br($elements['Element_OphTrConsent_BenefitsAndRisks']->benefits) ?>
            <p></p>
            <p></p><h5>Serious, frequently occurring or unavoidable risks:</h5>
            <?= nl2br($elements['Element_OphTrConsent_BenefitsAndRisks']->risks) ?>
            <p></p>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($elements['Element_OphTrConsent_BenefitsAndRisks']) && $type_assessment->existsElementInConsentForm($elements['Element_OphTrConsent_BenefitsAndRisks']->elementType->id, Element_OphTrConsent_Type::TYPE_PATIENT_AGREEMENT_ID)) : ?>
    <div class="group">
        <h4>Any extra procedures which may become necessary during the procedure(s):</h4>
        <div class="indent">
            <?php if (!empty($elements['Element_OphTrConsent_Procedure']->additional_procedures)) { ?>
                <?= $this->renderPartial('_proposed_procedures', array('css_class' => 'large', 'procedures' => $elements['Element_OphTrConsent_Procedure']->additionalprocedure_assignments)) ?>
            <?php } else {?>
                <p>No extra procedures</p>
            <?php } ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($elements['Element_OphTrConsent_PatientQuestions']) && $type_assessment->existsElementInConsentForm($elements['Element_OphTrConsent_PatientQuestions']->elementType->id, Element_OphTrConsent_Type::TYPE_PATIENT_AGREEMENT_ID)) : ?>
        <?php
        $purifier = new CHtmlPurifier();
        $purifier->setOptions(array('HTML.Trusted' => true));
        ?>
    <div class="group">
        <h4>Any questions asked by the patient:</h4>
        <div class="indent">
            <?= $purifier->purify((trim($elements['Element_OphTrConsent_PatientQuestions']->questions) === "" ? "None" : $elements['Element_OphTrConsent_PatientQuestions']->questions)) ?>
        </div>
    </div>

    <div class="group">
        <h4>Patient refuses the following procedures:</h4>
        <div class="indent">
            <?= $purifier->purify((trim($elements['Element_OphTrConsent_PatientQuestions']->refused_procedures) === "" ? "None" : $elements['Element_OphTrConsent_PatientQuestions']->refused_procedures)) ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($elements['Element_OphTrConsent_Specialrequirements']) && $type_assessment->existsElementInConsentForm($elements['Element_OphTrConsent_Specialrequirements']->elementType->id, Element_OphTrConsent_Type::TYPE_PATIENT_AGREEMENT_ID)) : ?>
    <div class="group">
        <h4>Any special requirements:</h4>
        <?php if (!empty($elements['Element_OphTrConsent_Specialrequirements']->specialreq)) { ?>
            <div class="indent">
                <?=nl2br(\CHtml::encode($elements['Element_OphTrConsent_Specialrequirements']->specialreq)) ?>
            </div>
        <?php } else {?>
            <div class="indent"><p>No special requirements</p></div>
        <?php } ?>
    </div>
    <p>I have also discussed what the procedure is likely to involve, the benefits and risks of any available
        alternative treatments (including no treatment) and any particular concerns of this patient. I assess that this
        patient has the capacity to give valid consent.</p>
    <?php endif; ?>

    <?php if (isset($elements['Element_OphTrConsent_Leaflets']) && $type_assessment->existsElementInConsentForm($elements['Element_OphTrConsent_Leaflets']->elementType->id, Element_OphTrConsent_Type::TYPE_PATIENT_AGREEMENT_ID)) : ?>
        <?php if ($elements['Element_OphTrConsent_Leaflets']->leaflets) { ?>
        <div class="group">
            <h4>The following informational leaflets have been provided:</h4>
            <div class="indent">
                <?php if (empty($elements['Element_OphTrConsent_Leaflets']->leaflets)) { ?>
                    None
                <?php } else { ?>
                    <?php foreach ($elements['Element_OphTrConsent_Leaflets']->leaflets as $leaflet) { ?>
                        <span class="checkbox <?= $elements['Element_OphTrConsent_Leaflets']->leaflets ? 'checked' : '' ?>"> </span>
                        <?php echo $leaflet->leaflet->name ?>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
    <?php endif; ?>

    <?php if (isset($elements['Element_OphTrConsent_Procedure']) && $type_assessment->existsElementInConsentForm($elements['Element_OphTrConsent_Procedure']->elementType->id, Element_OphTrConsent_Type::TYPE_PATIENT_AGREEMENT_ID)) : ?>
    <div class="group">
        <h4>This procedure will involve:</h4>
        <div class="indent">
            <span class="checkbox <?= $elements['Element_OphTrConsent_Procedure']->hasAnaestheticTypeByCode('GA') ? 'checked' : '' ?>"></span> General and/or regional anaesthesia
            <span class="checkbox <?= $elements['Element_OphTrConsent_Procedure']->hasAnaestheticTypeByCode(array('Topical', 'LAC', 'LA', 'LAS')) ? 'checked' : '' ?>"></span> Local anaesthesia
            <span class="checkbox <?= $elements['Element_OphTrConsent_Procedure']->hasAnaestheticTypeByCode('Sedation') ? 'checked' : '' ?>"></span> Sedation
        </div>
    </div>
    <?php endif; ?>

    <!-- consultant signature here -->
    <!-- doctor (second opinion) signature here -->

    <h5 class="text-r">Contact details: 01234 456789</h5>
    <div class="break"><!-- **** page break ***** --></div>
    <hr class="divider">
    <h2>Statement of patient</h2><h5>Please read this form carefully. If your treatment has been planned in advance, you
        should already have your own copy of the page which describes the benefits and risks of the proposed treatment.
        If not, you will be offered a copy now. If you have any questions, do ask - we are here to help you. You have
        the right to change your mind at any time, including after you have signed this form.</h5>
    <div class="spacer"><!-- spacer --></div>
    <p><b>I agree</b> to the procedure or course of treatment described on this form.</p>
    <p><b>I understand</b> that you cannot give me a guarantee that a particular person will perform the procedure. The
        person will, however, have appropriate experience.</p>

    <?php if (isset($elements['Element_OphTrConsent_Procedure']) && $type_assessment->existsElementInConsentForm($elements['Element_OphTrConsent_Procedure']->elementType->id, Element_OphTrConsent_Type::TYPE_PATIENT_AGREEMENT_ID)) : ?>
        <?php if ($elements['Element_OphTrConsent_Procedure']->hasAnaestheticTypeByCode('GA')) { ?>
        <strong>I understand</strong> that I will have the opportunity to discuss the details of anaesthesia before the procedure, unless the urgency of my situation prevents this.<br/>
        <?php } ?>
    <p><b>I understand</b> that any procedure in addition to those described on this form will only be carried out if it
        is necessary to save my life or to prevent serious harm to my health.</p>
    <p>I have been told <b>about additional procedures which may become necessary during my treatment. I have listed
            below any procedures</b> which I do not wish to be carried out <b>without further discussion.</b></p>
    <div class="dotted-write"><!-- Provide a dotted line area to write in --></div>
    <?php endif; ?>

    <!-- patient signature here -->

    <p>A <b>witness</b> should sign below <b>if the patient is unable to sign but has indicated their consent.</b></p>

    <!-- witness signature here -->

    <div class="break"><!-- **** page break ***** --></div>
    <hr class="divider">
    <h2>Confirmation of consent</h2><h6>To be completed by a health professional when the patient is admitted, if the
        patient has signed the form in advance.</h6>
    <p>On behalf of the team treating the patient, I have confirmed with the patient that has no further questions and
        wishes the procedure to go ahead.</p>

    <?php
    if (isset($elements['Element_OphTrConsent_Esign'])) {
        if ($type_assessment->existsElementInConsentForm($elements['Element_OphTrConsent_Esign']->elementType->id, Element_OphTrConsent_Type::TYPE_PATIENT_AGREEMENT_ID)) {
            echo $this->renderPartial('_print_signature',
                array(
                    'vi' => ($css_class == 'impaired'),
                    'element' => $elements['Element_OphTrConsent_Esign'],
                    'custom_key' => 'healthprof_signature_id',
                    'title_label' => 'Job title',
                    'name_label' => 'Print name'
                )
            );
        }
    }
    ?>

    <div class="spacer"><!-- **** empty vertical spacer ***** --></div>
    <p><b>Important notes:</b> (tick if applicable)</p>
    <p></p>
    <?php if (isset($elements['Element_OphTrConsent_AdvancedDecision']) && $type_assessment->existsElementInConsentForm($elements['Element_OphTrConsent_AdvancedDecision']->elementType->id, Element_OphTrConsent_Type::TYPE_PATIENT_AGREEMENT_ID)) : ?>
    <div class="group"><span class="checkbox <?= strlen($elements['Element_OphTrConsent_AdvancedDecision']->description) > 0 ? 'checked' : ''?>"></span> See also advance decision refusing treatment (including a Jehovah’s Witness form)
    </div>
    <?php endif; ?>
    <div class="group"><span class="checkbox"></span> Patient has withdrawn consent (ask patient to sign /date here)
        <div class="dotted-write"><!-- Provide a dotted line area to write in --></div>
    </div>
    <hr class="divider">
    <?php if (isset($elements['Element_OphTrConsent_Permissions']) && $type_assessment->existsElementInConsentForm($elements['Element_OphTrConsent_Permissions']->elementType->id, Element_OphTrConsent_Type::TYPE_PATIENT_AGREEMENT_ID)) : ?>
        <?php if (isset($elements['Element_OphTrConsent_Permissions'])) : ?>
    <h2>Form 1: Supplementary consent</h2>
    <h3>Images</h3>
    <p>Photographs, x-rays or other images may be taken as part of your treatment and will form part of your medical
        record. It is very unlikely that you would be recognised from these images. If however you could be recognised
        we would seek your specific consent before any particular publication.</p>
    <div class="group"><h4>I agree to use in audit, education and publication:</h4>
        <div class="indent">
            <span class="checkbox <?=$elements['Element_OphTrConsent_Permissions']->images->name == 'Yes' ? 'checked' : ''?>"></span> Yes&nbsp;&nbsp;&nbsp;
            <span class="checkbox <?=$elements['Element_OphTrConsent_Permissions']->images->name == 'No' ? 'checked' : ''?>"></span> No
            <span class="checkbox <?=$elements['Element_OphTrConsent_Permissions']->images->name == 'Not applicable' ? 'checked' : ''?>"></span> Not applicable
        </div>
    </div>
    <p>If you do not wish to take part in the above, your care will not be compromised in any way.</p>
    <div class="spacer"><!-- **** empty vertical spacer ***** --></div>
        <?php endif; ?>
    <?php endif; ?>
    <div class="highlighter"><h3>COVID-19</h3>
        <p>In the majority, COVID-19 causes a mild, self-limiting illness but symptoms may be highly variable amongst
            individuals. It is important that you understand the specific risk profile to yourself.</p>
        <p>Although we make every effort to minimise the risk of an infection, we cannot guarantee zero risk of COVID-19
            transmission.</p>
        <p>For more information: www.gov.uk/coronavirus</p></div>


</main>
</body>