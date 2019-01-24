<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @var \OEModule\OphCiExamination\models\HistoryMedicationsEntry[] $current
 * @var \OEModule\OphCiExamination\models\HistoryMedicationsEntry[] $stopped
 */

$model_name = CHtml::modelName($element);

$systemic_filter = function ($entry) {
    return $entry['route_id'] != 1;
};

$eye_filter = function ($entry) {
    return $entry['route_id'] == 1;
};

$current_systemic_meds = array_filter($current, $systemic_filter);
$stopped_systemic_meds = array_filter($stopped, $systemic_filter);
$current_eye_meds = array_filter($current, $eye_filter);
$stopped_eye_meds = array_filter($stopped, $eye_filter);

?>
<div class="group">
  <div class="label">Systemic Medications</div>
  <div class="data">
      <?php if (!$current_systemic_meds && !$stopped_systemic_meds): ?>
        <div class="nil-recorded">Nil recorded.</div>
      <?php else: ?>
          <?php if ($current_systemic_meds): ?>
          <table id="<?= $model_name ?>_entry_table">
            <tbody>
            <?php foreach ($current_systemic_meds as $entry): ?>
              <tr>
                <td>
                    <?php $this->widget('MedicationInfoBox', array('medication_id' => $entry->medication_id)); ?>
                    <?= $entry->getMedicationDisplay() ?></td>
                  <td>
                      <?php if($entry->getDoseAndFrequency()) {?>
                          <i class="oe-i info small pro-theme js-has-tooltip"
                             data-tooltip-content="<?= $entry->getDoseAndFrequency() ?>">
                          </i>
                      <?php } ?>
                  </td>
                <td><span class="oe-date"><?= $entry->getStartDateDisplay() ?></span></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
          <?php endif; ?>

          <?php if ($stopped_systemic_meds): ?>
          <table>
            <colgroup>
              <col class="cols-7">
            </colgroup>
            <thead>
            <tr>
              <th>Stopped</th>
              <th></th>
              <th>
                <i class="oe-i small pro-theme js-patient-expand-btn pad expand"></i>
              </th>
            </tr>
            </thead>
            <tbody style="display: none;">
            <?php foreach ($stopped_systemic_meds as $entry): ?>
              <tr>
                <td>
                    <?php $this->widget('MedicationInfoBox', array('medication_id' => $entry->medication_id)); ?>
                    <?= $entry->getMedicationDisplay() ?></td>
                <td><span class="oe-date"><?= $entry->getEndDateDisplay() ?></span></td>
                <td>
                    <?php if ($entry->usage_type == 'OphDrPrescription'): ?>
                      <a href="<?= $this->getPrescriptionLink($entry) ?>"><span
                            class="js-has-tooltip fa oe-i eye small pro-theme"
                            data-tooltip-content="View prescription"></span></a>
                    <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
          <?php endif; ?>
      <?php endif; ?>
  </div>
</div>
</div><!-- popup-overflow -->

<!-- oe-popup-overflow handles scrolling if data overflow height -->
<div class="oe-popup-overflow quicklook-data-groups">

  <div class="group">
    <div class="label">Eye Medications</div>
    <div class="data">
        <?php if (!$current_eye_meds && !$stopped_eye_meds): ?>
          <div class="nil-recorded">Nil recorded.</div>
        <?php else: ?>
            <?php if ($current_eye_meds): ?>
            <table id="<?= $model_name ?>_entry_table">
              <tbody>
              <?php foreach ($current_eye_meds as $entry): ?>
                <tr>
                  <td>
                      <?php $this->widget('MedicationInfoBox', array('medication_id' => $entry->medication_id)); ?>
                      <?= $entry->getMedicationDisplay() ?></td>
                  <td>
                      <?php $laterality = $entry->getLateralityDisplay();
                      $this->widget('EyeLateralityWidget', array('laterality' => $laterality));
                      ?>
                  </td>
                    <td>
                        <?php if($entry->getDoseAndFrequency()) {?>
                    <i class="oe-i info small pro-theme js-has-tooltip"
                       data-tooltip-content="<?= $entry->getDoseAndFrequency() ?>">
                    </i>
                        <?php } ?>
                    </td>
                  <td><span class="oe-date"><?= $entry->getStartDateDisplay() ?></span></td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
            <?php endif; ?>

            <?php if ($stopped_eye_meds): ?>
            <table>
              <thead>
              <tr>
                <th>Stopped</th>
                <th></th>
                <th>
                  <i class="oe-i small pad pro-theme js-patient-expand-btn expand"></i>
                </th>
              </tr>
              </thead>
              <tbody style="display: none;">
              <?php foreach ($stopped_eye_meds as $entry): ?>
                <tr>
                  <td>
                      <?php $this->widget('MedicationInfoBox', array('medication_id' => $entry->medication_id)); ?>
                      <?= $entry->getMedicationDisplay() ?></td>
                    <td>
                        <?php $laterality = $entry->getLateralityDisplay();
                        $this->widget('EyeLateralityWidget', array('laterality' => $laterality));
                        ?>
                    </td>
                  <td><span class="oe-date"><?= Helper::convertDate2HTML($entry->getEndDateDisplay()) ?></span></td>
                  <td>
                      <?php if ($entry->usage_type == 'OphDrPrescription'): ?>
                        <a href="<?= $this->getPrescriptionLink($entry) ?>">
                          <span class="js-has-tooltip fa oe-i eye small pro-theme"
                                data-tooltip-content="View prescription"></span>
                        </a>
                      <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
            <?php endif; ?>
        <?php endif; ?>
    </div>
  </div>