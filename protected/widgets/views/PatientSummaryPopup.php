<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php
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
<div class="oe-patient-popup" id="patient-popup-demographics" style="display:none;">
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
          <tr>
            <td>Mobile</td>
            <td>Unknown</td>
          </tr>
          <tr>
            <td>Email</td>
            <td><?= !empty($this->patient->contact->address->email) ? $this->patient->contact->address->email : 'Unknown' ?></td>
          </tr>
          <tr>
            <td>Next of kin</td>
            <td>Unknown</td>
          </tr>
          </tbody>
        </table>
      </div><!-- .popup-overflow -->
    </div><!-- .cols-left -->

    <div class="cols-right">

      <div class="popup-overflow">

        <div class="subtitle">&nbsp;</div>

        <table class="patient-demographics" style="position: relative; right: 0;">
          <tbody>
          <tr>
            <td>General Practitioner</td>
            <td><?= $this->patient->gp ? $this->patient->gp->contact->fullName : 'Unknown'; ?></td>
          </tr>
          <tr>
            <td>GP Address</td>
            <td><?= ($this->patient->gp && $this->patient->gp->contact->address) ? $this->patient->gp->contact->address->letterLine : 'Unknown'; ?></td>
          </tr>
          <tr>
            <td>GP Telephone</td>
            <td><?= ($this->patient->gp && $this->patient->gp->contact->primary_phone) ? $this->patient->gp->contact->primary_phone : 'Unknown'; ?></td>
          </tr>
          <tr>
            <td>Optician</td>
            <td>Mr Pink</td>
          </tr>
          </tbody>
        </table>
      </div><!-- .popup-overflow -->
    </div><!-- .cols-right -->
  </div><!-- flex -->
</div>

<!-- Patient Quicklook popup. Show Risks, Medical Data, Management Summary and Problem and Plans -->
<div class="oe-patient-popup" id="patient-summary-quicklook" style="display:none;">
  <div class="situational-awareness flex-layout flex-left flex-top">
      <?php
      $visualAcuityRight = $exam_api->getLetterVisualAcuityRight($patient);
      $visualAcuityLeft = $exam_api->getLetterVisualAcuityLeft($patient);

      if ($visualAcuityLeft || $visualAcuityRight) {
          $lDate = $exam_api->getLetterVisualAcuityDate($patient, 'left');
          $rDate = $exam_api->getLetterVisualAcuityDate($patient, 'right');
          ?>
        <div class="group">
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
        </div>
      <?php } else { ?>
        <div class="group">
          <span class="data-value not-available">VA: NA</span>
        </div>
      <?php } ?>

    <div class="group">
        <?php
        $leftRefraction = $correspondence_api->getLastRefraction($patient, 'left');
        $rightRefraction = $correspondence_api->getLastRefraction($patient, 'right');
        if ($leftRefraction !== null || $rightRefraction !== null) {
            ?>
          <span class="data">R <?= $rightRefraction ?: 'NA' ?></span>
          <span class="data">L <?= $leftRefraction ?: 'NA' ?></span>
          <span class="oe-date"
                style="text-align: left"><?= Helper::convertDate2NHS($correspondence_api->getLastRefractionDate($patient)) ?></span>
        <?php } else { ?>
          <span class="data-value not-available">Refraction: NA</span>
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
  </div>
  <div class="flex-layout flex-top">
    <!-- oe-popup-overflow handles scrolling if data overflow height -->
    <div class="oe-popup-overflow quicklook-data-groups">
      <div class="group">
        <div class="label">Eye diagnoses</div>
        <div class="data">
          <table>
            <tbody>
            <?php
            $ophthalmic_diagnoses = $this->patient->getOphthalmicDiagnosesSummary();
            if (count($ophthalmic_diagnoses) === 0) { ?>
              <tr>
                <td>
                  <div style="font-style: italic; color: rgba(255,255,255,0.5);">Nil recorded</div>
                </td>
              </tr>
            <?php } ?>

            <?php foreach ($ophthalmic_diagnoses as $ophthalmic_diagnosis) {
                list($side, $name, $date) = explode('~', $ophthalmic_diagnosis, 3); ?>
              <tr>
                <td><?= $name ?></td>
                <td>
                    <?php $this->widget('EyeLateralityWidget', array('laterality' => $side)) ?>
                </td>
                <td>
                  <span class="oe-date"><?= Helper::convertDate2HTML($date) ?></span>
                </td>
              </tr>
            <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
      <!-- group-->
      <div class="group">
        <div class="label">Systemic Diagnoses</div>
        <div class="data">
          <table>
            <tbody>
            <?php if (count($this->patient->systemicDiagnoses) === 0) { ?>
              <tr>
                <td>
                  <div style="font-style: italic; color: rgba(255,255,255,0.5);">Nil recorded</div>
                </td>
              </tr>
            <?php } ?>
            <?php foreach ($this->patient->systemicDiagnoses as $diagnosis) { ?>
              <tr>
                <td> <?= $diagnosis->disorder->term ?></td>
                <td>
                    <?php $this->widget('EyeLateralityWidget', array('eye' => $diagnosis->eye)) ?>
                </td>
                <td><span class="oe-date"><?= Helper::convertDate2HTML($diagnosis->dateText) ?></span></td>
              </tr>
            <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- oe-popup-overflow handles scrolling if data overflow height -->
    <div class="oe-popup-overflow quicklook-data-groups">
      <!-- Data -->
      <div class="group">
        <div class="label">Surgical History</div>
        <div class="data">
            <?php $this->widget(\OEModule\OphCiExamination\widgets\PastSurgery::class , array(
                'patient' => $this->patient,
                'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE,
            )); ?>
        </div>
      </div>
        <?php $this->widget(\OEModule\OphCiExamination\widgets\HistoryMedications::class, array(
            'patient' => $this->patient,
            'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE,
        )); ?>


      <div class="group">
        <div class="label">Family</div>
        <div class="data">
            <?php $this->widget(\OEModule\OphCiExamination\widgets\FamilyHistory::class, array(
                'patient' => $this->patient,
                'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE,
            )); ?>
        </div>
      </div>
      <!-- group-->

      <div class="group">
        <div class="label">Social</div>
        <div class="data">
            <?php $this->widget(\OEModule\OphCiExamination\widgets\SocialHistory::class, array(
                'patient' => $this->patient,
                'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE,
            )); ?>
        </div>
      </div>
    </div><!-- 	.oe-popup-overflow -->

  </div><!-- .flex-layout -->
</div>

<div class="oe-patient-popup" id="patient-popup-management" style="display: none;">
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
                          <td class="fade">
                                <span class="oe-date">
                                    <span class="day"><?= $summary->date[0] ?></span>
                                    <span class="month"><?= $summary->date[1] ?></span>
                                    <span class="year"><?= $summary->date[2] ?></span>
                                </span>
                          </td>
                          <td><?= $summary->comments ?></td>
                          <td><i class="oe-i info small pro-theme js-has-tooltip"
                                 data-tooltip-content="<?= $summary->user ?>"></i></td>
                      </tr>
                  <?php }
              } ?>
              </tbody>
          </table>
      </div><!-- .popup-overflow -->
    </div><!-- left -->

  </div><!-- flex -->
</div>

<div class="oe-patient-popup" id="patient-popup-allergies-risks" style="display: none;">
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
      </div><!-- .popup-overflow -->
    </div><!-- .col-right -->
  </div><!-- .flex -->
</div>