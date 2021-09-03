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

    /**
     * @var PatientSummaryPopup $this
     * @var \OEModule\OphCiExamination\components\OphCiExamination_API $exam_api
     * @var OphCoCorrespondence_API $correspondence_api
     * @var \OEModule\OphCoCvi\components\OphCoCvi_API $co_cvi_api
     */

    $exam_api = Yii::app()->moduleAPI->get('OphCiExamination');
    $correspondence_api = Yii::app()->moduleAPI->get('OphCoCorrespondence');
    $co_cvi_api = Yii::app()->moduleAPI->get('OphCoCvi');
    $patient_overview_popup_mode = isset(Yii::app()->controller->jsVars['popupMode']) ? Yii::app()->controller->jsVars['popupMode'] : SettingMetadata::model()->getSetting('patient_overview_popup_mode');

    use OEModule\OphCiExamination\models\SystemicDiagnoses_Diagnosis; ?>
<!-- Show full patient Demographics -->
<div class="oe-patient-popup patient-popup-demographics" style="display:none;">
    <?php if ($this->patient->nhsNumberStatus) : ?>
        <div class="alert-box <?= $this->patient->nhsNumberStatus->icon->banner_class_name ?: 'issue' ?>">
            <i class="oe-i exclamation pad-right no-click medium-icon"></i>
            <b>
                <?php echo \SettingMetadata::model()->getSetting('nhs_num_label') .
                  ((Yii::app()->params['institution_code'] === 'CERA') ? '' : ' Number') ?>:
                <?= $this->patient->nhsNumberStatus->description; ?>
            </b>
        </div>
    <?php endif; ?>
    <?php if (count($this->patient->identifiers) > 0) { ?>
        <div class="patient-numbers flex-layout">
            <div class="local-numbers">
                <?php foreach ($this->patient->identifiers as $identifier) { ?>
                    <?php if ($identifier->hasValue() || $identifier->displayIfEmpty()) { ?>
                        <div class="num">
                            <?= $identifier->getLabel() ?>
                            <label class="inline highlight">
                                <?= $identifier->value ?>
                            </label>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
            <div class="nhs-number">
                <?= \SettingMetadata::model()->getSetting('nhs_num_label') ?>
                <?= $this->patient->nhsnum ?>
            </div>
        </div>
    <?php } ?>
    <div class="flex-layout flex-top">
        <div class="cols-left">
            <div class="popup-overflow">
                <div class="subtitle">Demographics</div>
                <table class="patient-demographics" style="position: relative; right: 0;">
                    <tbody>
                    <tr>
                        <td>Born</td>
                        <td>
                            <b><?= $this->patient->dob ? $this->patient->NHSDate('dob') : 'Unknown' ?></b>
                            (<?= $this->patient->getAge() ?>y)
                        </td>
                    </tr>
                    <tr>
                        <td>Address</td>
                        <td><?= $this->patient->getSummaryAddress() ?></td>
                    </tr>
                    <tr>
                        <td>Ethnic Group</td>
                        <td><?= $this->patient->getEthnicGroupString() ?></td>
                    </tr>
                    <tr>
                        <td>Telephone</td>
                        <td><?= !empty($this->patient->primary_phone) ? $this->patient->primary_phone : 'Unknown' ?></td>
                    </tr>
                    <?php if (Yii::app()->params['institution_code'] !== 'CERA') : ?>
                        <tr>
                            <td>Mobile</td>
                            <td>Unknown</td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td>Email</td>
                        <td><?= $this->patient->contact->email ?? 'Unknown' ?></td>
                    </tr>
                    <?php if (Yii::app()->params['institution_code'] !== 'CERA') : ?>
                        <tr>
                            <td>Next of kin</td>
                            <td>Unknown</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div><!-- .popup-overflow -->
        </div><!-- .cols-left -->

        <div class="cols-right">
            <div class="popup-overflow">
                <div class="subtitle">&nbsp;</div>
                <?php if (Yii::app()->params['institution_code'] === 'CERA') { ?>
                    <table class="patient-demographics" style="position: relative; right: 0; cursor: default;">
                        <tbody>
                        <tr>
                            <td><?php echo \SettingMetadata::model()->getSetting('general_practitioner_label') ?></td>
                            <td><?= $this->patient->gp ? $this->patient->gp->contact->fullName : 'Unknown'; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo \SettingMetadata::model()->getSetting('general_practitioner_label') . ' Role' ?></td>
                            <td><?= ($this->patient->gp && $this->patient->gp->contact->label) ? $this->patient->gp->contact->label->name : 'Unknown'; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo \SettingMetadata::model()->getSetting('gp_label') ?> Telephone</td>
                            <td><?= ($this->patient->gp && $this->patient->gp->contact->primary_phone) ? $this->patient->gp->contact->primary_phone : 'Unknown'; ?></td>
                        </tr>
                        <?php if (($this->patient->gp_id)) {
                            $gp = Gp::model()->findByPk(array('id' => $this->patient->gp_id));
                            $practice = Practice::model()->findByPk(array('id' => $this->patient->practice_id));
                        } ?>
                        <tr>
                            <td>Referring Practice Address</td>
                            <td> <?= isset($practice) ? $practice->getAddresslines() : 'Unknown' ?></td>
                        </tr>
                        <tr>
                            <td>Referring Practice Telephone</td>
                            <td><?= isset($practice->phone) ? $practice->phone : 'Unknown'; ?></td>
                        </tr>
                        <?php if (isset($this->referredTo)) { ?>
                            <tr>
                                <td><?php echo 'Referred to ' ?></td>
                                <td><?php echo $this->referredTo->getFullNameAndTitle(); ?></td>
                            </tr>
                        <?php } ?>
                        <?php
                        if (isset($this->patient->patientContactAssociates)) {
                            $index = 1;
                            foreach ($this->patient->patientContactAssociates as $pca) {
                                if ($index > 3) {
                                    break;
                                }
                                //                  Removed the check for other practitioner not being the same as a referring practitioner and a check for whether
                                //                  a  a ref prac id is set as this was causing no contacts to be displayed - CERA-504
                                if (isset($pca->gp)) {
                                    $gp = $pca->gp; ?>
                                        <tr>
                                            <td>
                                                Other Practitioner <br> Contact <?= $index; ?>
                                            </td>
                                            <td>
                                                <div>
                                                    <?= $gp->contact->fullName . (isset($gp->contact->label) ? ' - ' . $gp->contact->label->name : ''); ?>
                                                </div>
                                                <?php
                                                if (isset($pca->practice)) {
                                                    $practice = $pca->practice;
                                                    if (isset($practice)) {
                                                        $address = $practice->contact->address; ?>
                                                            <div>
                                                                <?= isset($address) ? $address->letterLine : 'Unknown address for this contact.'; ?>
                                                            </div>
                                                            <?php
                                                    }
                                                } ?>
                                            </td>
                                        </tr>
                                        <?php
                                        $index += 1;
                                }
                            }
                        }
                        ?>
                        <tr>
                            <td>
                                Created Date:
                            </td>
                            <td>
                                <!--                  Added a timestamp for create date and modified date -- CERA-490 -->
                                <label for="patient_create_date"><?= date("d-M-Y h:i a", strtotime($this->patient->created_date)) ?></label>
                            </td>
                        </tr>
                        <tr>
                        </tr>
                        <tr>
                            <td><?php echo \SettingMetadata::model()->getSetting('general_practitioner_label') ?></td>
                            <td><?= $this->patient->gp ? $this->patient->gp->contact->fullName : 'Unknown'; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo \SettingMetadata::model()->getSetting('gp_label') ?> Address</td>
                            <td>
                                <?php
                                    // Show GP Practice address if available, otherwise fallback to GP address
                                if ($this->patient->practice && $this->patient->practice->contact->address) {
                                    echo $this->patient->practice->contact->address->letterLine;
                                } elseif ($this->patient->gp && $this->patient->gp->contact->address) {
                                    echo $this->patient->gp->contact->address->letterLine;
                                } else {
                                    echo 'Unknown';
                                } ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo \SettingMetadata::model()->getSetting('gp_label') ?> Telephone</td>
                            <td>
                                <?php
                                    // Show Practice phone number first, if not, fall back to GP phone number
                                if ($this->patient->practice && $this->patient->practice->contact->primary_phone) {
                                    echo $this->patient->practice->contact->primary_phone;
                                } elseif ($this->patient->gp && $this->patient->gp->contact->primary_phone) {
                                    echo $this->patient->gp->contact->primary_phone;
                                } else {
                                    echo 'Unknown';
                                } ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Last Modified Date:
                            </td>
                            <td>
                                <label for="patient_create_date"><?= date("d-M-Y h:i a", strtotime($this->patient->last_modified_date)) ?></label>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                <?php } ?>
                <?php if (Yii::app()->params['demographics_content']['pas'] === true) { ?>
                    <table class="patient-demographics" style="position: relative; right: 0;">
                        <tbody>
                        <tr>
                            <td>
                                <h2>PAS Contacts</h2>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo \SettingMetadata::model()->getSetting('general_practitioner_label') ?></td>
                            <td><?= $this->patient->gp ? $this->patient->gp->contact->fullName : 'Unknown'; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo \SettingMetadata::model()->getSetting('gp_label') ?> Address</td>
                            <td>
                                <?php
                                    // Show GP Practice address if available, otherwise fallback to GP address
                                if ($this->patient->practice && $this->patient->practice->contact->address) {
                                    echo $this->patient->practice->contact->address->letterLine;
                                } elseif ($this->patient->gp && $this->patient->gp->contact->address) {
                                    echo $this->patient->gp->contact->address->letterLine;
                                } else {
                                    echo 'Unknown';
                                } ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo \SettingMetadata::model()->getSetting('gp_label') ?> Telephone</td>
                            <td>
                                <?php
                                    // Show Practice phone number first, if not, fall back to GP phone number
                                if ($this->patient->practice && $this->patient->practice->contact->primary_phone) {
                                    echo $this->patient->practice->contact->primary_phone;
                                } elseif ($this->patient->gp && $this->patient->gp->contact->primary_phone) {
                                    echo $this->patient->gp->contact->primary_phone;
                                } else {
                                    echo 'Unknown';
                                } ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo \SettingMetadata::model()->getSetting('gp_label') ?> Email</td>
                            <td>
                                <?php
                                    // Show Email address
                                if ($this->patient->gp && $this->patient->gp->contact) {
                                    echo $this->patient->gp->contact->email;
                                } else {
                                    echo 'Unknown';
                                } ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h2>Patient Contacts</h2>
                            </td>
                        </tr>
                        <?php
                            $gp_contact_id = $this->patient->gp ? $this->patient->gp->contact->id : null;
                        foreach ($this->patient->contactAssignments as $contactAssignment) {
                            $contact = $contactAssignment->contact;
                            if (isset($contact) && $contact->id != $gp_contact_id) { ?>
                                    <tr>
                                        <td><?= $contact->label ? $contact->label->name : "" ?></td>
                                        <td><?= $contact->fullName ?></td>
                                    </tr>
                                    <tr>
                                        <td>Address</td>
                                        <td><?= $contact->address ? $contact->address->letterLine : "" ?></td>
                                    </tr>
                                    <tr>
                                        <td>Telephone</td>
                                        <td><?= $contact->primary_phone ?></td>
                                    </tr>
                            <?php }
                        } ?>

                        <?php $examination_communication_preferences = $exam_api->getLatestElement('OEModule\OphCiExamination\models\Element_OphCiExamination_CommunicationPreferences', $patient); ?>
                        <tr>
                            <td>
                                <h2>Preferences</h2>
                            </td>
                        </tr>
                        <tr>
                            <td>Large print</td>
                            <td>
                                <span class="large-text"><?= ($examination_communication_preferences && $examination_communication_preferences->correspondence_in_large_letters) ? 'Yes' : 'No' ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td>Email correspondence</td>
                            <td>
                                <span class="large-text"><?= ($examination_communication_preferences && $examination_communication_preferences->agrees_to_insecure_email_correspondence) ? 'Yes' : 'No' ?></span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                <?php } ?>
            </div><!-- .popup-overflow -->
        </div><!-- .cols-right -->
    </div><!-- flex -->
</div>

<!-- Patient Quicklook popup. Show Risks, Medical Data, Management Summary and Problem and Plans -->
<div class="oe-patient-popup patient-summary-quicklook" style="display:none;">
    <div class="situational-awareness flex-layout flex-left flex-top">
        <?php
        $vaData = $exam_api->getMostRecentVADataStandardised($patient);

        if ($vaData) { ?>
            <div class="group">
                <span class="label">VA:</span>
                <?php if ($vaData['has_beo']) { ?>
                    <span class="data">BEO <?= $vaData['beo_result'] ?></span>
                    <span class="data"><?= $vaData['beo_method_abbr'] ?></span>
                <?php } ?>
                <span class="data">R <?= $vaData['has_right'] ? $vaData['right_result'] : 'NA'; ?></span>
                <?php if ($vaData['has_right']) { ?>
                    <span class="data"><?= $vaData['right_method_abbr'] ?></span>
                <?php } ?>
                <span class="data"> L <?= $vaData['has_left'] ? $vaData['left_result'] : 'NA' ?></span>
                <?php if ($vaData['has_left']) { ?>
                    <span class="data"><?= $vaData['left_method_abbr'] ?></span>
                <?php } ?>
                <span class="oe-date" style="text-align: left;">
                    <?= Helper::convertDate2NHS($vaData['event_date']); ?>
                </span>
            </div>
        <?php } ?>

        <div class="group">
            <span class="label">Ref:</span>
            <?php
                $refractionData = $exam_api->getLatestRefractionReadingFromAnyElementType($patient);

            if ($refractionData) {
                ?>
                    <span class="data">R <?= $refractionData['right'] ?: 'NA' ?></span>
                    <span class="data">L <?= $refractionData['left'] ?: 'NA' ?></span>
                    <span class="oe-date"
                          style="text-align: left"><?= Helper::convertDate2NHS($refractionData['event_date']) ?></span>
            <?php } else { ?>
                    <span class="data">NA</span>
            <?php } ?>
        </div>

        <div class="group">
            <span class="label">CCT:</span>
            <?php
                $leftCCT = $exam_api->getCCTLeft($patient);
                $rightCCT = $exam_api->getCCTRight($patient);
            if ($leftCCT !== null || $rightCCT !== null) {
                ?>
                    <span class="data">R <?= $rightCCT ?: 'NA' ?> </span>
                    <span class="data">L <?= $leftCCT ?: 'NA' ?> </span>
                    <span class="oe-date"
                          style="text-align: left"><?= Helper::convertDate2NHS($exam_api->getCCTDate($patient)); ?></span>
            <?php } else { ?>
                    <span class="data">NA</span>
            <?php } ?>
        </div>

        <div class="group">
            <span class="label">CVI Status:</span>
            <?php if ($this->cviStatus[0] !== 'Unknown') { ?>
                <span class="data"><?= $this->cviStatus[0]; ?></span>
                <span class="oe-date"> <?= $this->cviStatus[1] && $this->cviStatus[1] !== '0000-00-00' ? \Helper::convertDate2HTML($this->cviStatus[1]) : 'N/A' ?></span>
            <?php } else { ?>
                <span class="data">NA</span>
            <?php } ?>
        </div>
    </div>
    <div class="flex-layout flex-top">
        <!-- oe-popup-overflow handles scrolling if data overflow height -->
        <div class="oe-popup-overflow quicklook-data-groups">
            <div class="group">
                <div class="label">Eye Diagnoses</div>
                <div class="data">
                    <table>
                        <colgroup>
                            <col class="cols-8">
                            <col>
                        </colgroup>
                        <tbody>
                        <?php
                            $ophthalmic_diagnoses = $this->patient->getOphthalmicDiagnosesSummary();
                        if (count($ophthalmic_diagnoses) === 0) { ?>
                                <tr>
                                    <td>
                                        <div class="nil-recorded">Nil recorded</div>
                                    </td>
                                </tr>
                        <?php } ?>

                        <?php foreach ($ophthalmic_diagnoses as $ophthalmic_diagnosis) {
                            list($side, $name, $date, $event_id) = explode('~', $ophthalmic_diagnosis); ?>
                            <tr>
                                <td><?= $name ?></td>
                                <td><i class="oe-i"></i></td>
                                <td class="nowrap">
                                    <?php $this->widget('EyeLateralityWidget', array('laterality' => $side, 'pad' => '')) ?>
                                    <span class="oe-date"><?= $date ?></span>
                                </td>
                                <td>
                                    <?php if (isset($event_id) && $event_id) { ?>
                                        <a href="/OphCiExamination/default/view/<?= $event_id ?>"><i
                                                    class="oe-i pro-theme direction-right-circle small pad"></i></a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="group">
                <div class="label">Systemic Diagnoses</div>
                <div class="data">
                    <table>
                        <colgroup>
                            <col class="cols-8">
                            <col>
                        </colgroup>
                        <tbody>
                        <?php if (count($this->patient->systemicDiagnoses) === 0 && !$this->patient->get_no_systemic_diagnoses_date()) { ?>
                            <tr>
                                <td>
                                    <div class="nil-recorded">Nil recorded</div>
                                </td>
                            </tr>
                        <?php } elseif ($this->patient->get_no_systemic_diagnoses_date()) { ?>
                            <tr>
                                <td>
                                    <div class="nil-recorded">Patient has no known Systemic Diagnoses</div>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php foreach ($this->patient->systemicDiagnoses as $systemic_diagnosis) { ?>
                            <tr>
                                <td> <?= $systemic_diagnosis->disorder->term ?></td>
                                <td><i class="oe-i"></i></td>
                                <td class="nowrap">
                                    <?php $this->widget('EyeLateralityWidget', array('eye' => $systemic_diagnosis->eye, 'pad' => '')) ?>
                                    <div class="oe-date"><?= $systemic_diagnosis->getHTMLformatedDate() ?></div>
                                </td>
                                <td>
                                    <?php $diagnosis = SystemicDiagnoses_Diagnosis::model()->find('secondary_diagnosis_id=?', array($systemic_diagnosis->id));
                                    if ($diagnosis) { ?>
                                            <?php $event_id = $diagnosis->element->event_id ?>
                                            <a href="/OphCiExamination/default/view/<?= $event_id ?>"><i
                                                        class="oe-i direction-right-circle pro-theme small pad"></i></a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- oe-popup-overflow handles scrolling if data overflow height -->
        <div class="oe-popup-overflow quicklook-data-groups">
            <div class="group">
                <div class="label">Eye Procedures</div>
                <div class="data">
                    <?php $this->widget(
                        \OEModule\OphCiExamination\widgets\PastSurgery::class,
                        [
                        'patient' => $this->patient,
                        'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE,
                        'pro_theme' => 'pro-theme',
                        ]
                    ); ?>
                </div>
            </div>

            <div class="group">
                <div class="label">Systemic Procedures</div>
                <div class="data">
                    <?php $this->widget(
                        \OEModule\OphCiExamination\widgets\SystemicSurgery::class,
                        [
                        'patient' => $this->patient,
                        'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE,
                        ]
                    ); ?>
                </div>
            </div>

            <?php $this->widget(
                \OEModule\OphCiExamination\widgets\HistoryMedications::class,
                [
                'patient' => $this->patient,
                'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE,
                ]
            ); ?>

            <div class="group">
                <div class="label">Family History</div>
                <div class="data">
                    <?php $this->widget(
                        \OEModule\OphCiExamination\widgets\FamilyHistory::class,
                        [
                        'patient' => $this->patient,
                        'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE,
                        ]
                    ); ?>
                </div>
            </div>

            <div class="group">
                <div class="label">Social History</div>
                <div class="data">
                    <?php $this->widget(
                        \OEModule\OphCiExamination\widgets\SocialHistory::class,
                        [
                        'patient' => $this->patient,
                        'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE,
                        ]
                    ); ?>
                </div>
            </div>
        </div><!--    .oe-popup-overflow -->
    </div><!-- .flex-layout -->
</div>

<div class="oe-patient-popup patient-popup-management" style="display: none;">
    <div class="flex-layout flex-top">
        <div class="cols-left">
            <div class="popup-overflow">
                <div class="subtitle">Management Summaries</div>
                <table class="management-summaries">
                    <tbody>
                    <?php $summaries = $exam_api->getManagementSummaries($patient);
                    if (sizeof($summaries) != 0) {
                        foreach ($summaries as $summary) { ?>
                                <tr>
                                    <td><?= $summary->service ?></td>
                                    <td><?= $summary->comments ?></td>
                                    <td class="fade">
                                    <span class="oe-date">
                                        <span class="day"><?= $summary->date[0] ?></span>
                                        <span class="month"><?= $summary->date[1] ?></span>
                                        <span class="year"><?= $summary->date[2] ?></span>
                                    </span>
                                    </td>
                                    <td><i class="oe-i info small pro-theme js-has-tooltip"
                                           data-tooltip-content="<?= $summary->user ?>"></i></td>
                                </tr>
                        <?php }
                    } ?>
                    </tbody>
                </table>
            </div><!-- .popup-overflow -->
            <div class="popup-overflow">
                <div class="subtitle">Appointments</div>
                <?php $this->widget('Appointment', ['patient' => $this->patient, 'pro_theme' => 'pro-theme', 'is_popup' => true]) ?>
            </div><!-- .popup-overflow -->
        </div><!-- left -->
        <div class="cols-right">
            <div class="popup-overflow">
                <?php $this->widget('application.widgets.PlansProblemsWidget', ['patient_id' => $this->patient->id, 'pro_theme' => 'pro-theme', 'is_popup' => true]); ?>
            </div><!-- .popup-overflow -->
        </div>
    </div><!-- flex -->
</div>

<div class="oe-patient-popup patient-popup-allergies-risks" style="display: none;">
    <div class="flex-layout flex-top">
        <div class="cols-left">
            <!-- Warnings: Allergies -->
            <div class="popup-overflow">
                <?php $this->widget(\OEModule\OphCiExamination\widgets\Allergies::class, array(
                  'patient' => $this->patient,
                  'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE,
                )); ?>
            </div><!-- .popup-overflow -->
        </div><!-- cols-left -->

        <div class="cols-right">
            <!-- Warnings: Risks -->
            <div class="popup-overflow">
                <?php $this->widget(\OEModule\OphCiExamination\widgets\HistoryRisks::class, array(
                  'patient' => $this->patient,
                  'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE,
                )); ?>
            </div><!-- .popup-overflow -->
        </div><!-- .col-right -->
    </div><!-- .flex -->
</div>

<?php if (Yii::app()->getModule('OETrial')) { ?>
    <div class="oe-patient-popup patient-popup-trials" style="display: none;">
        <div class="flex-layout flex-top">
            <?php
                $this->widget('application.modules.OETrial.widgets.PatientTrialSummary', array(
                  'patient' => $this->patient,
                ));
            ?>
        </div>
    </div>
<?php } ?>
