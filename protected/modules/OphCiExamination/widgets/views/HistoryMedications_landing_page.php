<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
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

$current_eye_meds = array_filter($current, $eye_filter);
$stopped_eye_meds = array_filter($stopped, $eye_filter);

?>

<?php if (!$current_eye_meds && !$stopped_eye_meds) { ?>
    <div class="nil-recorded">Nil recorded.</div>
<?php } else { ?>
    <?php if ($current_eye_meds): ?>
        <table id="<?= $model_name ?>_entry_table">
            <colgroup>
                <col class="cols-7">
            </colgroup>
            <tbody>
            <?php foreach ($current_eye_meds as $entry): ?>
                <tr>
                    <td><strong><?= $entry->getMedicationDisplay() ?></strong></td>
                    <td>
                        <?php $laterality = $entry->getLateralityDisplay();
                        $this->widget('EyeLateralityWidget', array('laterality' => $laterality));
                        ?>
                    </td>
                    <td>
                        <?php if ($entry->getDoseAndFrequency()) { ?>
                            <i class="oe-i info small pro-theme js-has-tooltip"
                               data-tooltip-content="<?= $entry->getDoseAndFrequency() ?>"
                            </i>
                        <?php } ?>
                    </td>
                    <td class="date"><span class="oe-date"><?= $entry->getStartDateDisplay() ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if ($stopped_eye_meds): ?>
        <table>
            <colgroup>
                <col class="cols-7">
            </colgroup>
            <thead>
            <tr>
                <th>Stopped</th>
                <th></th>
                <th colspan="2">
                    <i class="oe-i small pad js-patient-expand-btn expand"></i>
                </th>
            </tr>
            </thead>
            <tbody style="display: none;">
            <?php foreach ($stopped_eye_meds as $entry): ?>
                <tr>
                    <td><strong><?= $entry->getMedicationDisplay() ?></strong></td>
                    <td>
                        <?php $laterality = $entry->getLateralityDisplay();
                        $this->widget('EyeLateralityWidget', array('laterality' => $laterality));
                        ?>
                    </td>
                    <td class="date"><span class="oe-date"><?= Helper::convertDate2HTML($entry->getEndDateDisplay()) ?></span></td>
                    <td>
                        <?php if ($entry->prescription_item): ?>
                            <a href="<?= $this->getPrescriptionLink($entry) ?>">
                  <span class="js-has-tooltip fa oe-i eye small"
                        data-tooltip-content="View prescription"></span>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
<?php } ?>