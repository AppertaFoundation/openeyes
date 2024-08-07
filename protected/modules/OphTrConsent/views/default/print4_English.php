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

use OEModule\OphTrConsent\models\Element_OphTrConsent_CapacityAssessment;

?>
<?php
$institution_id = Institution::model()->getCurrent()->id;
$site_id = Yii::app()->session['selected_site_id'];
$primary_identifier = PatientIdentifierHelper::getIdentifierForPatient(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $this->patient->id, $institution_id, $site_id);
$secondary_identifier = PatientIdentifierHelper::getIdentifierForPatient(SettingMetadata::model()->getSetting('display_secondary_number_usage_code'), $this->patient->id, $institution_id, $site_id);
$type_assessment = new OphTrConsent_Type_Assessment();
?>
<body class="open-eyes print<?= isset($elements['Element_OphTrConsent_Withdrawal']) ? ' void' : ''?>">
<?php $this->renderPartial('_consent_header') ?>
<div class="print-title text-c">
    <h1><b>Consent form 4</b></h1>
    <h1 class="highlighter">For adults without mental capacity to give valid consent</h1>
    <h2>Form for adults who are unable to consent to investigation or treatment</h2>
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
            <th>Special requirements <br> <small>(e.g other language/other communication method)</small></th>
            <td>
                <?php if (isset($elements['Element_OphTrConsent_Specialrequirements']) && $type_assessment->existsElementInConsentForm($elements['Element_OphTrConsent_Specialrequirements']->elementType->id, Element_OphTrConsent_Type::TYPE_UNABLE_TO_CONSENT_ID)) : ?>
                    <?= isset($elements['Element_OphTrConsent_Specialrequirements']) ?
                    nl2br(\CHtml::encode($elements['Element_OphTrConsent_Specialrequirements']->specialreq)) :
                    'None'?>
                <?php endif; ?>
            </td>
        </tr>
        <?php if (isset($elements['Element_OphTrConsent_Procedure']) && $type_assessment->existsElementInConsentForm($elements['Element_OphTrConsent_Procedure']->elementType->id, Element_OphTrConsent_Type::TYPE_UNABLE_TO_CONSENT_ID)) : ?>
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
    <h2>All sections to be completed by health professional proposing the procedure.</h2>
    <div class="group"><h4>Proposed procedure(s) or course of treatment:</h4>
        <div class="indent">
            <?php if (isset($elements['Element_OphTrConsent_Procedure']) && $type_assessment->existsElementInConsentForm($elements['Element_OphTrConsent_Procedure']->elementType->id, Element_OphTrConsent_Type::TYPE_UNABLE_TO_CONSENT_ID)) : ?>
                <?= $this->renderPartial('_proposed_procedures', array('css_class' => 'large', 'procedures' => $elements['Element_OphTrConsent_Procedure']->procedure_assignments)) ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($elements['Element_OphTrConsent_Specialrequirements']) && $type_assessment->existsElementInConsentForm($elements['Element_OphTrConsent_Specialrequirements']->elementType->id, Element_OphTrConsent_Type::TYPE_UNABLE_TO_CONSENT_ID)) : ?>
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
    <?php endif; ?>

    <?php if (isset($elements[Element_OphTrConsent_CapacityAssessment::class]) && $type_assessment->existsElementInConsentForm($elements[Element_OphTrConsent_CapacityAssessment::class]->elementType->id, Element_OphTrConsent_Type::TYPE_UNABLE_TO_CONSENT_ID)) : ?>
    <div class="group"><h4>Capacity Assessment</h4>
        <div class="indent"><h5>The patient lacks capacity to give or withhold consent to this procedure or course of
                treatment because of:</h5>
            <ul>
                <?php foreach ($elements[Element_OphTrConsent_CapacityAssessment::class]->lackOfCapacityReasons as $reason) : ?>
                    <li><?=CHtml::encode($reason->label)?></li>
                <?php endforeach; ?>
            </ul>
            <h5>How were above judgements reached:</h5>
            <ul>
                <li><?=$elements[Element_OphTrConsent_CapacityAssessment::class]->how_judgement_was_made?></li>
            </ul>
            <h5>What evidence has been relied upon:</h5>
            <ul>
                <li><?=$elements[Element_OphTrConsent_CapacityAssessment::class]->evidence?></li>
            </ul>
            <h5>What attempts were made to assist the patient to make their own decision and why not successful:</h5>
            <ul>
                <li><?=$elements[Element_OphTrConsent_CapacityAssessment::class]->attempts_to_assist?></li>
            </ul>
            <h5>Why patient lacks capacity and the basis for your decision:</h5>
            <ul>
                <li><?=$elements[Element_OphTrConsent_CapacityAssessment::class]->basis_of_decision?></li>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($elements['OEModule\OphTrConsent\models\Element_OphTrConsent_PatientAttorneyDeputy'])) : ?>
    <div class="group">
        <h4>Patient's attorney or deputy</h4>
        <div class="indent">
            <p>Where the patient has authorised an attorney to make decisions about the procedure in question under
                a Lasting Power of Attorney or a Court Appointed Deputy has been authorised to make decisions about the
                procedure in question, the attorney or deputy will have the final responsibility for determining whether
                a procedure is in the patient's best interests
            </p>

            <?php
            $criteria = new \CDbCriteria();
            $gp = $this->patient->gp;
            $criteria->addCondition('t.patient_id = ' . $this->patient->id);
            if (isset($gp)) {
                $criteria->addCondition('t.contact_id != ' . $gp->contact->id);
            }
            $criteria->addCondition('t.event_id = ' . $elements['OEModule\OphTrConsent\models\Element_OphTrConsent_PatientAttorneyDeputy']->event_id);
            $contacts = \PatientAttorneyDeputyContact::model()->findAll($criteria);
            foreach ($contacts as $key => $contact) {
                if ($key !== 0) { ?>
                    <div class="group">
                    <div class="indent">
                <?php } ?>
                <table class="row-lines">
                    <colgroup>
                        <col class="cols-3">
                        <col class="cols-5">
                        <col class="cols-5">
                    </colgroup>
                    <tbody>
                        <tr>
                            <th>Patient's attorney or deputy</th>
                            <td><?= $contact->contact->getFullName() ?></td>
                            <td><!-- empty --></td>
                        </tr>
                        <tr>
                            <th>Statement</th>
                            <td>I have been authorised to make decisions about the procedure in question:</td>
                            <td><span class="highlighter"><?= $contact->authorisedDecision->name ?></span></td>
                        </tr>
                        <tr>
                            <th>Statement</th>
                            <td>I have considered the relevant circumstances relating to the decision in question and believe
                                the procedure to be in the patients best interests:</td>
                            <td><span class="highlighter"><?= $contact->consideredDecision->name ?></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
                <?= $this->renderPartial(
                    '_print_signature',
                    array(
                    'vi' => ($css_class == 'impaired'),
                    'element' => $elements[Element_OphTrConsent_Esign::class],
                    'signature' => $elements[Element_OphTrConsent_Esign::class]
                    ->getSignatureByInitiatorAttributes(
                        (int)$elements['OEModule\OphTrConsent\models\Element_OphTrConsent_PatientAttorneyDeputy']->getElementType()->id,
                        (int)$contact->id
                    ),
                    'title_label' => 'Contact type',
                    'name_label' => 'Patient\'s attorney or deputy',
                    )
                ); ?>
            <?php } ?>
        <?php if (empty($contacts)) : ?>
        </div></div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (isset($elements['Element_OphTrConsent_OthersInvolvedDecisionMakingProcess'])) {
        $contact_element = $elements['Element_OphTrConsent_OthersInvolvedDecisionMakingProcess'];
        $contacts = $contact_element->consentContact;
        ?>
        <div class="group"><h4>Others consulted in the decision making process</h4>
            <div class="indent">
                <table class="row-lines">
                    <colgroup>
                        <col class="cols-2">
                        <col class="cols-3">
                        <col class="cols-2">
                    </colgroup>
                    <tbody>
                    <?php foreach ($contacts as $contact) { ?>
                        <tr>
                            <td><?= $contact->getRelationshipName() ?></td>
                            <td><?= $contact->getFullName() ?></td>
                            <td>Consulted: <?= $contact->getContactMethodName() ?></td>
                            <td><?= $contact->getSignatureRequiredString() ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
        foreach ($contacts as $contact) {
            if ((int)$contact->signature_required === 0) {
                continue;
            }
            ?>
            <?= $this->renderPartial(
                '_print_signature',
                array(
                'vi' => ($css_class == 'impaired'),
                'element' => $elements[Element_OphTrConsent_Esign::class],
                'signature' => $elements[Element_OphTrConsent_Esign::class]
                    ->getSignatureByInitiatorAttributes(
                        (int)$contact_element->getElementType()->id,
                        (int)$contact->id
                    ),
                'title_label' => 'Relationship',
                'name_label' => 'Name',
                )
            ); ?>
        <?php } ?>
    <?php } ?>

    <div class="group"><h4>Best Interests Decision</h4>
        <div class="indent"><h5>To the best of my knowledge, the patient has not refused this procedure in a valid
                advance directive.</h5><h5>Where possible and appropriate, I have encouraged the patient to participate
                in the decision and I have consulted with those close to the patient and with colleagues and those close
                to the patient.</h5><h5>In the case of a patient who does not have anyone close enough to help in the
                decision-making process and for whom serious medical treatment is proposed, I have consulted an
                Independent Medical Capacity Advocate and I believe the procedure to be in the patient’s best interests
                because:</h5>
            <ul>
                <?php
                if (isset($elements['OEModule\OphTrConsent\models\Element_OphTrConsent_BestInterestDecision']) && $elements['OEModule\OphTrConsent\models\Element_OphTrConsent_BestInterestDecision']->patient_has_not_refused == 1) { ?>
                <li>
                    <?= $elements['OEModule\OphTrConsent\models\Element_OphTrConsent_BestInterestDecision']->reason_for_procedure?>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>

    <?= $this->renderPartial(
        '_print_signature',
        array(
            'vi' => ($css_class === 'impaired'),
            'element' => $elements[Element_OphTrConsent_Esign::class],
            'signature' => $elements[Element_OphTrConsent_Esign::class]
                ->getSignatureByInitiatorAttributes($elements[Element_OphTrConsent_Esign::class]->getElementType()->id, 0),
            'title_label' => 'Job title',
            'name_label' => 'Print name',
        )
    ); ?>

    <div class="group"><span class="checkbox <?= isset($elements['Element_OphTrConsent_Withdrawal']) ? 'checked' : ''?>"></span><?= isset($elements['Element_OphTrConsent_Withdrawal']) ? '<b class="highlighter">' : ''?> Patient has withdrawn consent <?= isset($elements['Element_OphTrConsent_Withdrawal']) ? '</b>' : ''?></div>
    <?php if (isset($elements['Element_OphTrConsent_Withdrawal'])) {?>
        <p><b>Reason for withdrawal:</b> <?= isset($elements['Element_OphTrConsent_Withdrawal']->withdrawal_reason) ? $elements['Element_OphTrConsent_Withdrawal']->withdrawal_reason : '-'?></p>
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
    <?php if (\SettingMetadata::model()->getSetting('display_covid_19_consent')) : ?>
    <div class="highlighter"><h3>COVID-19</h3>
        <p>In the majority, COVID-19 causes a mild, self-limiting illness but symptoms may be highly variable amongst
            individuals. It is important that you understand the specific risk profile to yourself.</p>
        <p>Although we make every effort to minimise the risk of an infection, we cannot guarantee zero risk of COVID-19
            transmission.</p>
        <p>For more information: www.gov.uk/coronavirus</p>
    </div>
    <?php endif ?>
</main>
</body>
