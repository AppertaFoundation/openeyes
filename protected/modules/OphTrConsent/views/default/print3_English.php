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
$primary_identifier = PatientIdentifierHelper::getIdentifierForPatient(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $this->patient->id, $institution_id, $site_id);
$secondary_identifier = PatientIdentifierHelper::getIdentifierForPatient(SettingMetadata::model()->getSetting('display_secondary_number_usage_code'), $this->patient->id, $institution_id, $site_id);
$type_assessment = new OphTrConsent_Type_Assessment();
$additional_signatures = false;
if (isset($elements['OEModule\OphTrConsent\models\Element_OphTrConsent_AdditionalSignatures'])) {
    $additional_signatures = $elements['OEModule\OphTrConsent\models\Element_OphTrConsent_AdditionalSignatures'];
}
?>
<body class="open-eyes print<?= isset($elements['Element_OphTrConsent_Withdrawal']) ? ' void' : '' ?>">
<?php $this->renderPartial('_consent_header') ?>
<div class="print-title text-c">
    <h1><b>Consent form 3</b></h1>
    <h1 class="highlighter">For adults with mental capacity to give valid consent</h1>
    <h2>Parental agreement to investigation or treatment</h2>
</div>
<hr class="divider"/>
<?php if (isset($elements['Element_OphTrConsent_Withdrawal'])) { ?>
    <h1 class="highlighter">Patient has withdrawn consent</h1>
    <hr class="divider"/>
<?php } ?>
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
            <th>Hospital #</th>
            <td><?= PatientIdentifierHelper::getIdentifierValue($primary_identifier) ?></td>
        </tr>
        <tr>
            <th>NHS #</th>
            <td><?= PatientIdentifierHelper::getIdentifierValue($secondary_identifier) ?></td>
        </tr>
        <tr>
            <th>Sex</th>
            <td><?= $this->patient->genderString ?></td>
        </tr>
        <tr>
            <th>Witness required</th>
            <td>
                <?php if ($additional_signatures !== false && $additional_signatures->witness_required === "1") {
                    echo 'Yes';
                } else {
                    echo 'No';
                } ?>
            </td>
        </tr>
        <?php if ($additional_signatures !== false && $additional_signatures->witness_required === "1") { ?>
            <tr>
                <th>Witness name</th>
                <td><?= \CHtml::encode($additional_signatures->witness_name) ?></td>
            </tr>
        <?php } ?>
        <tr>
            <th>Interpreter required</th>
            <td>
                <?php if ($additional_signatures !== false && $additional_signatures->interpreter_required === "1") {
                    echo 'Yes';
                } else {
                    echo 'No';
                } ?>
            </td>
        </tr>

        <?php if ($additional_signatures !== false && $additional_signatures->interpreter_required === "1") { ?>
            <tr>
                <th>Interpreter name</th>
                <td><?= \CHtml::encode($additional_signatures->interpreter_name) ?></td>
            </tr>
        <?php } ?>
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
        </tbody>
    </table>
    <div class="break"><!-- **** page break ***** --></div>
    <hr class="divider">
    <div class="group"><h4>Proposed procedure(s) or course of treatment:</h4>
        <div class="indent">
            <?php if (isset($elements['Element_OphTrConsent_Procedure']) && $type_assessment->existsElementInConsentForm($elements['Element_OphTrConsent_Procedure']->elementType->id, Element_OphTrConsent_Type::TYPE_PATIENT_AGREEMENT_ID)) : ?>
                <?= $this->renderPartial('_proposed_procedures', array('css_class' => 'large', 'procedures' => $elements['Element_OphTrConsent_Procedure']->procedure_assignments)) ?>
            <?php endif; ?>
        </div>
    </div>

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

    <?php
    if (isset($elements['Element_OphTrConsent_ExtraProcedures']) && $type_assessment->existsElementInConsentForm($elements['Element_OphTrConsent_ExtraProcedures']->elementType->id, Element_OphTrConsent_Type::TYPE_PATIENT_PARENTAL_AGREEMENT_ID)) : ?>
        <div class="group">
            <h4>Any extra procedures which may become necessary during the procedure(s):</h4>
            <?php
            if (!empty($elements['Element_OphTrConsent_ExtraProcedures']->extra_procedure_assignments)) { ?>
                <div class="indent">
                    <?= $this->renderPartial('_extra_procedures', array('procedures' => $elements['Element_OphTrConsent_ExtraProcedures']->extra_procedure_assignments)) ?>
                </div>
            <?php } else { ?>
                <div class="indent"><p>No extra procedures</p></div>
            <?php } ?>
        </div>
    <?php endif; ?>

    <hr class="divider">
    <h2>Form 1: Supplementary consent</h2>
    <?php
    if (isset($elements["Element_OphTrConsent_SupplementaryConsent"]) && count($elements["Element_OphTrConsent_SupplementaryConsent"]->element_question) > 0) {
        echo $this->renderPartial(
            '_print_supplementary_consent',
            array(
                'element' => $elements['Element_OphTrConsent_SupplementaryConsent'],
            )
        );
    } else { ?>
        <div class="alert-box info">There are no active supplementary consent questions.</div>
    <?php } ?>

    <?php if (isset($elements['Element_OphTrConsent_Specialrequirements']) && $type_assessment->existsElementInConsentForm($elements['Element_OphTrConsent_Specialrequirements']->elementType->id, Element_OphTrConsent_Type::TYPE_PATIENT_AGREEMENT_ID)) : ?>
        <div class="group">
            <h4>Any special requirements:</h4>
            <?php if (!empty($elements['Element_OphTrConsent_Specialrequirements']->specialreq)) { ?>
                <div class="indent">
                    <?= nl2br(\CHtml::encode($elements['Element_OphTrConsent_Specialrequirements']->specialreq)) ?>
                </div>
            <?php } else { ?>
                <div class="indent"><p>No special requirements</p></div>
            <?php } ?>
        </div>
    <?php endif; ?>

    <p>I have also discussed what the procedure is likely to involve, the benefits and risks of any available
        alternative treatments (including no treatment) and any particular concerns of this patient. I assess that this
        patient has the capacity to give valid consent.</p>

    <p>I have informed the patient of the material risk involved in the proposed treatment, and have considered and
        discussed those risks which the patient would or would be likely to consider significant.</p>

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
                <span class="checkbox <?= $elements['Element_OphTrConsent_Procedure']->hasAnaestheticTypeByCode('GA') ? 'checked' : '' ?>"></span>
                General and/or regional anaesthesia
                <span class="checkbox <?= $elements['Element_OphTrConsent_Procedure']->hasAnaestheticTypeByCode(array('Topical', 'LAC', 'LA', 'LAS')) ? 'checked' : '' ?>"></span>
                Local anaesthesia
                <span class="checkbox <?= $elements['Element_OphTrConsent_Procedure']->hasAnaestheticTypeByCode('Sedation') ? 'checked' : '' ?>"></span>
                Sedation
            </div>
        </div>
    <?php endif; ?>
    <?php
    if (isset($elements['Element_OphTrConsent_Esign'])) {
        if ($type_assessment->existsElementInConsentForm($elements['Element_OphTrConsent_Esign']->elementType->id, Element_OphTrConsent_Type::TYPE_PATIENT_AGREEMENT_ID)) {
            echo $this->renderPartial(
                '_print_signature',
                array(
                    'vi' => ($css_class == 'impaired'),
                    'element' => $elements['Element_OphTrConsent_Esign'],
                    'signature' => $elements['Element_OphTrConsent_Esign']
                        ->getSignatureByInitiatorAttributes(
                            $elements['Element_OphTrConsent_Esign']->getElementType()->id,
                            0
                        ),
                    'custom_key' => 'healthprof_signature_id',
                    'title_label' => 'Job title',
                    'name_label' => 'Print name'
                )
            );
        }
    }
    ?>

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
            <strong>I
                understand</strong> that I will have the opportunity to discuss the details of anaesthesia before the procedure, unless the urgency of my situation prevents this.
            <br/>
        <?php } ?>
    <?php endif; ?>
    <p><b>I understand</b> that any procedure in addition to those described on this form will only be carried out if it
        is necessary to save my life or to prevent serious harm to my health.</p>
    <p>I have been told <b>about additional procedures which may become necessary during my treatment. I have listed
            below any procedures</b> which I do not wish to be carried out <b>without further discussion.</b></p>

    <!-- patient signature -->

    <?= $this->renderPartial(
        '_print_signature',
        array(
            'vi' => ($css_class == 'impaired'),
            'element' => $elements[Element_OphTrConsent_Esign::class],
            'signature' => $elements[Element_OphTrConsent_Esign::class]
                ->getSignatureByInitiatorAttributes($additional_signatures->getElementType()->id, 5),
            'title_label' => 'Role',
            'name_label' => 'Patient name',
        )
    ); ?>

    <?php if ($additional_signatures && $additional_signatures->witness_required) { ?>
        <p>A <b>witness</b> should sign below <b>if the patient is unable to sign but has indicated their consent.</b>
        </p>
        <?php
        if ($type_assessment->existsElementInConsentForm($additional_signatures->elementType->id, Element_OphTrConsent_Type::TYPE_PATIENT_AGREEMENT_ID)) {
            echo $this->renderPartial(
                '_print_signature',
                array(
                    'vi' => ($css_class == 'impaired'),
                    'element' => $elements[Element_OphTrConsent_Esign::class],
                    'signature' => $elements[Element_OphTrConsent_Esign::class]
                        ->getSignatureByInitiatorAttributes($additional_signatures->getElementType()->id, 1),
                    'title_label' => 'Role',
                    'name_label' => 'Witness name',
                )
            );
        }
    }
    ?>

    <!-- Interpreter's signature -->

    <?php if ($additional_signatures && $additional_signatures->interpreter_required) : ?>
        <h2>Statement of interpreter</h2>
        <p>I have interpreted the information above to the patient to the best of my ability and in a way in which I
            believe s/he can understand.</p>
        <?= $this->renderPartial(
            '_print_signature',
            array(
                'vi' => ($css_class == 'impaired'),
                'element' => $elements[Element_OphTrConsent_Esign::class],
                'signature' => $elements[Element_OphTrConsent_Esign::class]
                    ->getSignatureByInitiatorAttributes($additional_signatures->getElementType()->id, 2),
                'title_label' => 'Job title',
                'name_label' => 'Interpreter name',
            )
        ); ?>
    <?php endif; ?>

    <div class="break"><!-- **** page break ***** --></div>
    <hr class="divider">
    <?php if (isset($elements['Element_OphTrConsent_Confirm'])) { ?>
        <h2>Confirmation of consent</h2><h6>To be completed by a health professional when the patient is admitted, if
            the
            patient has signed the form in advance.</h6>
        <p>On behalf of the team treating the patient, I have confirmed with the patient that has no further questions
            and
            wishes the procedure to go ahead.</p>
        <?= $this->renderPartial(
            '_print_signature',
            array(
                'vi' => ($css_class === 'impaired'),
                'element' => $elements['Element_OphTrConsent_Esign'],
                'signature' => $elements['Element_OphTrConsent_Esign']
                    ->getSignatureByInitiatorAttributes(
                        $elements['Element_OphTrConsent_Confirm']->getElementType()->id,
                        6
                    ),
                'title_label' => 'Job title',
                'name_label' => 'Print name',
                'job_title' => $elements['Element_OphTrConsent_Confirm']->user->role
            )
        );
    } ?>

    <div class="group"><span
                class="checkbox <?= isset($elements['Element_OphTrConsent_Withdrawal']) ? 'checked' : '' ?>"></span><?= isset($elements['Element_OphTrConsent_Withdrawal']) ? '<b class="highlighter">' : '' ?>
        Patient has withdrawn consent <?= isset($elements['Element_OphTrConsent_Withdrawal']) ? '</b>' : '' ?></div>
    <?php if (isset($elements['Element_OphTrConsent_Withdrawal'])) { ?>
        <p><b>Reason for
                withdrawal:</b> <?= isset($elements['Element_OphTrConsent_Withdrawal']->withdrawal_reason) ? $elements['Element_OphTrConsent_Withdrawal']->withdrawal_reason : '-' ?>
        </p>
        <?= $this->renderPartial(
            '_print_signature',
            array(
                'vi' => ($css_class === 'impaired'),
                'element' => $elements['Element_OphTrConsent_Esign'],
                'signature' => $elements['Element_OphTrConsent_Esign']
                    ->getSignatureByInitiatorAttributes(
                        $elements['Element_OphTrConsent_Withdrawal']->getElementType()->id,
                        $elements['Element_OphTrConsent_Withdrawal']->id,
                    ),
                'title_label' => 'Job title',
                'name_label' => 'Print name'
            )
        );
    } ?>

    <div class="highlighter"><h3>COVID-19</h3>
        <p>In the majority, COVID-19 causes a mild, self-limiting illness but symptoms may be highly variable amongst
            individuals. It is important that you understand the specific risk profile to yourself.</p>
        <p>Although we make every effort to minimise the risk of an infection, we cannot guarantee zero risk of COVID-19
            transmission.</p>
        <p>For more information: www.gov.uk/coronavirus</p></div>


</main>
</body>
