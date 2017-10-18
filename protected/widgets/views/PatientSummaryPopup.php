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
<div id="patient-popup-container" class="patient-popup-container">

    <!-- Patient warnings -->
    <?php
    if ($this->warnings) { ?>
        <div class="patient-warnings toggle-patient-summary-popup">
            <?php echo implode(', ', array_unique(array_map(function ($warning) {
                return $warning['short_msg'];
            }, $this->warnings))); ?>
        </div>
    <?php } ?>

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
        <!-- Overflow alert -->
        <div class="oe-popup-overflow-alert">+ &nbsp; click to view all data</div>
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
                  <div class="row">
                    <div class="large-2 column label">
                      POH
                    </div>
                    <div class="large-10 column data">
                        <?php echo $this->operations; ?>
                    </div>
                  </div>
                <?php } ?>
                <?php $this->widget('OEModule\OphCiExamination\widgets\HistoryMedications', array(
                    'patient' => $this->patient,
                    'mode' => BaseEventElementWidget::$PATIENT_POPUP_MODE
                )); ?>

            </div>
        </div>
    </div>
</div>