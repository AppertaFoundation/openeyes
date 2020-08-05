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
?>
<!-- Show full patient Demographics -->
<div class="patient-popup-demographics oe-patient-quick-overview" style="display:none;"
     data-patient-id="<?= $this->patient->id ?>">
    <div class="close-icon-btn">
        <i class="oe-i remove-circle medium"></i>
    </div>
    <!-- To reuse the code of PatientMeta -->
    <?php
        $this->render('application.widgets.views.PatientMeta');
    ?>
    <div class="quick-overview-content">
        <!-- insert the data -->
        <?php if ($this->patient->nhsNumberStatus) : ?>
            <div class="alert-box <?= $this->patient->nhsNumberStatus->icon->banner_class_name ?: 'issue' ?>">
                <i class="oe-i exclamation pad-right no-click medium-icon"></i>
                <b> NHS Number: <?= $this->patient->nhsNumberStatus->description; ?></b>
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
                    <?= Yii::app()->params['nhs_num_label'] ?>
                    <?= $this->patient->nhsnum ?>
                </div>
            </div>
        <?php } ?>
        <div class="data-group">
            <table class="patient-demographics" style="position: relative; right: 0;">
                <tbody>
                <tr>
                    <td>Born</td>
                    <td>
                        <?= $this->patient->dob ? $this->patient->NHSDate('dob') : 'Unknown' ?>
                        (<?= $this->patient->getAge() ?>y)
                    </td>
                </tr>
                <tr>
                    <td>Address</td>
                    <td><?= $this->patient->getSummaryAddress(" ") ?></td>
                </tr>
                <tr>
                    <td>Ethnic Group</td>
                    <td><?= $this->patient->getEthnicGroupString() ?></td>
                </tr>
                <tr>
                    <td>Telephone</td>
                    <td><?= !empty($this->patient->primary_phone) ? $this->patient->primary_phone : 'Unknown' ?></td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td><?= !empty($this->patient->contact->email) ? $this->patient->contact->email : 'Unknown' ?></td>
                </tr>
                <tr>
                    <td><h3>PAS Contacts</h3></td>
                    <td></td>
                </tr>
                <tr>
                    <td><?php echo Yii::app()->params['general_practitioner_label'] ?></td>
                    <td><?= $this->patient->gp ? $this->patient->gp->contact->fullName : 'Unknown'; ?></td>
                </tr>
                <tr>
                    <td><?php echo Yii::app()->params['gp_label'] ?> Address</td>
                    <td><?= ($this->patient->gp && $this->patient->gp->contact->address) ? $this->patient->gp->contact->address->letterLine : 'Unknown'; ?></td>
                </tr>
                <tr>
                    <td><?php echo Yii::app()->params['gp_label'] ?> Telephone</td>
                    <td><?= ($this->patient->gp && $this->patient->gp->contact->primary_phone) ? $this->patient->gp->contact->primary_phone : 'Unknown'; ?></td>
                </tr>
                <?php
                    $gp_contact_id = $this->patient->gp ? $this->patient->gp->contact->id : null;
                foreach ($this->patient->contactAssignments as $contactAssignment) {
                    $contact = $contactAssignment->contact;

                    if (isset($contact) && $contact->id != $gp_contact_id) { ?>
                            <tr>
                                <td>
                                    Patient Contacts
                                </td>
                            </tr>
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
                </tbody>
            </table>
        </div><!-- data-group -->
    </div>
</div>

<!-- Patient Quicklook popup. Show Risks, Medical Data, Management Summary and Problem and Plans -->
<div class="oe-patient-quick-overview patient-summary-quicklook" style="display:none;"
     data-patient-id="<?= $this->patient->id ?>">
    <div class="close-icon-btn">
        <i class="oe-i remove-circle medium"></i>
    </div>
    <?php
        $this->render('application.widgets.views.PatientMeta');
    ?>
    <div class="quick-overview-content">
        <div class="data-group">
            <div class="quicklook-data-groups">
                <div class="group">
                    <?php
                        $visualAcuityRight = $exam_api->getLetterVisualAcuityRight($patient);
                        $visualAcuityLeft = $exam_api->getLetterVisualAcuityLeft($patient);
                    if ($visualAcuityLeft || $visualAcuityRight) {
                        $lDate = $exam_api->getLetterVisualAcuityDate($patient, 'left');
                        $rDate = $exam_api->getLetterVisualAcuityDate($patient, 'right');
                        ?>
                        <?php if ($lDate == $rDate) { ?>
                                <span class="data">R <?= $visualAcuityRight ?: 'NA'; ?>
                                </span>
                                <span class="data"
                                      style="display : <?= $visualAcuityRight ? '' : 'none' ?>">
                                  <?= $exam_api->getLetterVAMethodName($patient, 'right') ?>
                                </span>
                                <span class="data">
                                    L <?= $visualAcuityLeft ?: 'NA' ?>
                                </span>
                                <span class="data"
                                      style="display : <?= $visualAcuityLeft ? '' : 'none' ?>">
                                  <?= $exam_api->getLetterVAMethodName($patient, 'left') ?>
                                </span>
                                <span class="oe-date"
                                      style="text-align: left;">
                                  <?= Helper::convertDate2NHS($rDate); ?>
                                </span>
                        <?php } else { ?>
                                <span class="data">
                                    R <?= $visualAcuityRight ?: 'NA'; ?>
                                </span>
                                <span class="data"
                                      style="display : <?= $visualAcuityRight ? '' : 'none' ?>">
                                  <?= $exam_api->getLetterVAMethodName($patient, 'right') ?>
                                </span>
                                <span class="oe-date"
                                      style="display : <?= $visualAcuityRight ? '' : 'none' ?>">
                                  <?= Helper::convertDate2NHS($rDate); ?>
                                </span>
                                <span class="data">
                                    L <?= $visualAcuityLeft ?: 'NA' ?>
                                </span>
                                <span class="data"
                                      style="display : <?= $visualAcuityLeft ? '' : 'none' ?>">
                                  <?= $exam_api->getLetterVAMethodName($patient, 'left') ?>
                                </span>
                                <span class="oe-date"
                                      style="text-align: left; display : <?= $visualAcuityLeft ? '' : 'none' ?>">
                                  <?= Helper::convertDate2NHS($lDate); ?>
                                </span>
                        <?php } ?>
                    <?php } else { ?>
                            <span class="data-value not-available">VA: NA</span>
                    <?php } ?>
                </div>

                <div class="group">
                    <?php
                        $leftRefraction = $correspondence_api->getLastRefraction($patient, 'left');
                        $rightRefraction = $correspondence_api->getLastRefraction($patient, 'right');
                    if ($leftRefraction !== null || $rightRefraction !== null) { ?>
                            <span class="data">R <?= $rightRefraction ?: 'NA' ?></span>
                            <span class="data">L <?= $leftRefraction ?: 'NA' ?></span>
                            <span class="oe-date"
                                  style="text-align: left"><?= Helper::convertDate2NHS($correspondence_api->getLastRefractionDate($patient)) ?></span>
                    <?php } else { ?>
                            <span class="data">Refraction: NA</span>
                    <?php } ?>
                </div>

                <div class="group">
                    <?php if ($this->cviStatus[0] !== 'Unknown') { ?>
                        <span class="data">CVI Status: <?= $this->cviStatus[0]; ?></span>
                        <span class="oe-date"> <?= $this->cviStatus[1] && $this->cviStatus[1] !== '0000-00-00' ? \Helper::convertDate2HTML($this->cviStatus[1]) : 'N/A' ?></span>
                    <?php } else { ?>
                        <span class="data">CVI Status: NA</span>
                    <?php } ?>
                </div>

                <div class="group">
                    <div class="label">Eye Diagnoses</div>
                    <div class="data">
                        <table>
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
                                list($side, $name, $date) = explode('~', $ophthalmic_diagnosis); ?>
                                <tr>
                                    <td><?= $name ?></td>
                                    <td>
                                        <?php $this->widget('EyeLateralityWidget', array('laterality' => $side)) ?>
                                    </td>
                                    <td>
                                        <span class="oe-date"><?= $date ?></span>
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
                            <tbody>
                            <?php if (count($this->patient->systemicDiagnoses) === 0) { ?>
                                <tr>
                                    <td>
                                        <div class="nil-recorded">Nil recorded</div>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php foreach ($this->patient->systemicDiagnoses as $diagnosis) { ?>
                                <tr>
                                    <td> <?= $diagnosis->disorder->term ?></td>
                                    <td>
                                        <?php $this->widget('EyeLateralityWidget', array('eye' => $diagnosis->eye)) ?>
                                    </td>
                                    <td><span class="oe-date"><?= $diagnosis->getHTMLformatedDate() ?></span></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="group">
                    <div class="label">Surgical History</div>
                    <div class="data">
                        <?php $this->widget(
                            \OEModule\OphCiExamination\widgets\PastSurgery::class,
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
            </div>
        </div>
    </div>
</div>

<div class="oe-patient-quick-overview patient-popup-management" style="display: none;"
     data-patient-id="<?= $this->patient->id ?>">
    <div class="close-icon-btn">
        <i class="oe-i remove-circle medium"></i>
    </div>
    <?php
        $this->render('application.widgets.views.PatientMeta');
    ?>
    <div class="data-group">
        <h3>Management Summaries</h3>
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
    </div>
    <div class="data-group">
        <h3>Appointments</h3>
        <?php $this->widget('Appointment', ['patient' => $this->patient, 'pro_theme' => 'pro-theme', 'is_popup' => true]) ?>
        <?php $this->widget('application.widgets.PlansProblemsWidget', ['patient_id' => $this->patient->id, 'pro_theme' => 'pro-theme', 'is_popup' => true]); ?>
    </div><!-- flex -->
</div>
<!--patient popup management-->

<div class="oe-patient-quick-overview patient-popup-allergies-risks" style="display: none;"
     data-patient-id="<?= $this->patient->id ?>">
    <div class="close-icon-btn">
        <i class="oe-i remove-circle medium"></i>
    </div>
    <?php
        $this->render('application.widgets.views.PatientMeta');
    ?>
    <div class="quick-overview-content">
        <!-- Warnings: Allergies -->
        <div class="data-group">
            <?php $this->widget(\OEModule\OphCiExamination\widgets\Allergies::class, array(
              'patient' => $this->patient,
              'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE,
            )); ?>
        </div><!-- .data-group -->
        <!-- Warnings: Risks -->
        <div class="data-group">
            <?php $this->widget(\OEModule\OphCiExamination\widgets\HistoryRisks::class, array(
              'patient' => $this->patient,
              'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE,
            )); ?>
            <table class="risks alert-box patient">
                <tbody>
                <?php $diabetes_disorders = $this->patient->getDisordersOfType(Disorder::$SNOMED_DIABETES_SET);
                foreach ($diabetes_disorders as $disorder) { ?>
                        <tr>
                            <td><?= $disorder->term ?></td>
                            <td></td>
                        </tr>
                <?php } ?>
                </tbody>
            </table>
        </div><!-- .data-group -->
    </div><!-- .quick-overview-content -->
</div>

<?php if (Yii::app()->getModule('OETrial')) { ?>
    <div class="oe-patient-quick-overview patient-popup-trials" style="display: none;"
         data-patient-id="<?= $this->patient->id ?>">
        <div class="close-icon-btn">
            <i class="oe-i remove-circle medium"></i>
        </div>
        <?php
            $this->render('application.widgets.views.PatientMeta');
        ?>
        <?php
            $this->widget('application.modules.OETrial.widgets.PatientTrialSummary', array(
              'patient' => $this->patient,
            ));
        ?>
    </div>
<?php } ?>
