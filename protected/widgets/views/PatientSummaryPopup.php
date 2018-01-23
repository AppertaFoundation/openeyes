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
<!-- Patient Quickloog popup. Show Risks, Medical Data, Management Summary and Problem and Plans -->
<div class="patient-popup-quicklook" id="patient-summary-quicklook" style="display: none;">
    <div class="flex-layout flex-top">
      <!-- oe-popup-overflow handles scrolling if data overflow height -->
      <div class="cols-4 oe-popup-overflow pad">
        <!-- Warnings -->
        <div class="alert-box patient">
          <strong>Patient has allergies</strong>  - Cephalosporins, Opiates, Brimonidine<br>
          <strong>Patient has risks</strong> - Cannot Lie Flat<br>
        </div>
        <!-- Data -->
        <div class="summary-data">
          <div class="row">
            <div class="label">Ophthalmic Diagnoses</div>
            <div class="data">
                <?php $this->widget('OEModule\OphCiExamination\widgets\PastSurgery', array(
                    'patient' => $this->patient,
                    'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE
                )); ?>
            </div><!-- data -->
          </div><!-- row -->
        </div><!-- summary-data -->
      </div><!-- popup-overflow -->

      <!-- oe-popup-overflow handles scrolling if data overflow height -->
      <div class="cols-4 oe-popup-overflow pad">

        <!-- Data -->
        <div class="summary-data">

          <div class="row">
            <div class="label">Systemic Diagnoses</div>
            <div class="data">

              <table>
                <tbody>
                <?php foreach ($this->patient->systemicDiagnoses as $diagnosis) {
                    Yii::log("Operation details ".var_export($diagnosis->eye->adjective,true)); ?>
                  <tr>
                    <td><?php echo $diagnosis->eye ? $diagnosis->eye->adjective : ''?> <?php echo $diagnosis->disorder->term?></td>
                    <td>
                        <?php if ($diagnosis->eye && $diagnosis->eye->adjective=='Right') { ?>
                          <i class="oe-i laterality R small pad"></i>
                          <i class="oe-i laterality NA small pad"></i>
                        <?php } elseif ($diagnosis->eye && $diagnosis->eye->adjective=='Both') { ?>
                          <i class="oe-i laterality R small pad"></i>
                          <i class="oe-i laterality L small pad"></i>
                        <?php } elseif ($diagnosis->eye && $diagnosis->eye->adjective=='Left') { ?>
                          <i class="oe-i laterality NA small pad"></i>
                          <i class="oe-i laterality L small pad"></i>
                        <?php } ?>
                    </td>
                    <td><?php echo $diagnosis->dateText?></td>
                  </tr>
                <?php }?>
                </tbody>
              </table>

            </div><!-- data -->
          </div><!-- row -->
        </div><!-- summary-data -->
      </div><!-- popup-overflow -->


      <!-- oe-popup-overflow handles scrolling if data overflow height -->
      <div class="cols-4 oe-popup-overflow pad">

        <!-- Data -->
        <div class="summary-data">

          <div class="row">
            <div class="label">Medications</div>
            <div class="data">
                <?php $this->widget('OEModule\OphCiExamination\widgets\HistoryMedications', array(
                    'patient' => $this->patient,
                    'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE
                )); ?>
            </div><!-- .data -->
          </div><!-- .row -->

        </div><!-- .summary-data -->
      </div><!-- 	.oe-popup-overflow -->

    </div><!-- .flex-layout -->
  </div>
<!-- .row -->
<!-- .patient-popup-quicklook -->

<!-- Show full patient Demographies -->
<div class="patient-popup-demographics" id="patient-popup-demographics" style="display: none;">
  <div class="row">
    <div class="col-left">
      <!-- oe-popup-overflow handles scrolling if data overflow height -->
      <div class="oe-popup-overflow">
        <!-- demographics (NHS CUI) -->
        <div class="demographics">
          <div class="subtitle">Demographics</div>
          <table class="patient-summary">
            <tbody>
            <tr>
              <td>Born</td>
              <td><b><?php echo ($this->patient->dob) ? $this->patient->NHSDate('dob') : 'Unknown' ?></b> (52y)</td>
            </tr><tr>
              <td>Address</td>
              <td><?php echo $this->patient->getSummaryAddress()?></td>
            </tr><tr>
              <td>Ethnic Group</td>
              <td><?php echo $this->patient->getEthnicGroupString() ?></td>
            </tr><tr>
              <td>Telephone</td>
              <td><?php echo !empty($this->patient->primary_phone) ? $this->patient->primary_phone : 'Unknown'?></td>
            </tr><tr>
              <td>General Practitioner</td>
              <td><?php echo ($this->patient->gp) ? $this->patient->gp->contact->fullName : 'Unknown'; ?></td>
            </tr><tr>
              <td>GP Address</td>
              <td><?php echo ($this->patient->gp && $this->patient->gp->contact->address) ? $this->patient->gp->contact->address->letterLine : 'Unknown'; ?></td>
            </tr><tr>
              <td>GP Telephone</td>
              <td><?php echo ($this->patient->gp && $this->patient->gp->contact->primary_phone) ? $this->patient->gp->contact->primary_phone : 'Unknown'; ?></td>
            </tr>
            </tbody>
          </table>
        </div><!-- .demographics -->
      </div><!-- .oe-popup-overflow -->

    </div><!-- .col-left -->

    <div class="col-right">

      <!-- oe-popup-overflow handles scrolling if data overflow height -->
      <div class="oe-popup-overflow">

        <div class="popup-management-summaries collapse-group" data-collapse="expanded">
          <div class="collapse-group-icon"><i class="oe-i minus pro-theme"></i></div>
          <div class="collapse-group-header subtitle">Management Summaries</div>
          <div class="collapse-group-content">

            <ul class="management-summaries">
            </ul>

          </div><!-- .collapse-group-content -->
        </div> <!-- .popup-management-summaries -->

        <div class="problems-plans">

          <div class="subtitle">Problems &amp; Plans</div>

          <ul class="problems-plans-sortable" id="problems-plans-sortable">
          </ul>
          <div class="create-new-problem-plan">
            <input id="create-problem-plan" type="text">
            <div class="add-problem-plan-btn tiny" id="js-add-pp-btn"><i class="oe-i plus pro-theme"></i></div>
          </div>

        </div>
      </div>
      <!-- .oe-popup-overflow -->
    </div>
    <!-- .col-right -->
  </div>
  <!-- .row -->
<!--  Old Code -->
    <!-- Patient icon -->
    <button
        class="hide toggle-patient-summary-popup icon-patient-patient-id_small<?= count($this->warnings) ? '-warning' : ''; ?>">
        Toggle patient summary
    </button>

    <!-- Quicklook icon -->
    <button
        class="toggle-patient-summary-popup icon-alert-quicklook"
        data-hide-icon="icon-alert-cross"
        data-show-icon="icon-alert-quicklook">
        Toggle patient summary
    </button>

    <div class="panel patient-popup" id="patient-summary-popup">
        <!-- Help hint -->
        <span
            class="help-hint"
            data-text='{
                "close": {
                    "full": "Click to close",
                    "short": "Close"
                },
                "lock": {
                    "full": "Click to lock",
                    "short": "Lock"
                }
            }'>
            Click to lock
        </span>

        <div class="zone2">
            <div class="row">
                <div class="large-2 column label">Born</div>
                <div class="large-10 column">
                    <b><?= ($this->patient->dob) ? $this->patient->NHSDate('dob') : 'Unknown' ?></b>
                    <?= $this->patient->dob ? '(' . $this->patient->getAge() . 'y' .
                            ($this->patient->isDeceased() ? ' - Deceased' : '') . ')'
                        : '' ?>
                </div>
            </div>
            <div class="row">
                <div class="large-2 column label">Address</div>
                <div class="large-10 column data"><?= $this->patient->getSummaryAddress(', ') ?></div>
            </div>
        </div>
        
        <!-- Warnings -->
        <?php if ($this->warnings) { ?>
            <div class="alert-box patient with-icon">
                <span>
                    <?php foreach ($this->warnings as $warn) { ?>
                        <strong><?php echo $warn['long_msg']; ?></strong>
                        - <?php echo $warn['details']; ?><br />
                    <?php } ?>
                </span>
            </div>
        <?php } ?>
        <div class="oe-popup-overflow">
            <div class="summary-data">
              <?php if ($this->ophthalmicDiagnoses) { ?>
                <div class="row">
                  <div class="large-2 column label">
                    Ophthalmic Diagnoses
                  </div>
                  <div class="large-10 column data">
                      <?php echo $this->ophthalmicDiagnoses; ?>
                  </div>
                </div>
                <?php } ?>
                <?php if ($this->systemicDiagnoses) { ?>
                  <div class="row">
                    <div class="large-2 column label">
                      Systemic Diagnoses
                    </div>
                    <div class="large-10 column data">
                        <?php echo $this->systemicDiagnoses; ?>
                    </div>
                  </div>
                <?php } ?>
              <div class="row">
                <div class="large-2 column label">
                  CVI Status
                </div>
                <div class="large-10 column data">
                    <?php echo $this->cviStatus; ?>
                </div>
              </div>
                <?php if ($this->operations) { ?>
                  <div class="row surgical-history">
                    <div class="large-2 column label">
                      Surgical History
                    </div>
                    <div class="large-10 column data">
                        <?php echo $this->operations; ?>
                    </div>
                  </div>
                <?php } ?>

    </div><!-- .col-right -->
  </div><!-- .row -->
</div>