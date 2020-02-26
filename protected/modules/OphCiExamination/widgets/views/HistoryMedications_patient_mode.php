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

$current_systemic_meds = array_filter($current, $systemic_filter);
$stopped_systemic_meds = array_filter($stopped, $systemic_filter);
$current_eye_meds = array_filter($current, $eye_filter);
$stopped_eye_meds = array_filter($stopped, $eye_filter);

?>
<div class="group">
    <div class="label">Systemic Medications</div>
    <div class="data">
        <?php if (!$current_systemic_meds && !$stopped_systemic_meds) { ?>
            <div class="nil-recorded">Nil recorded.</div>
        <?php } else { ?>
            <?php if ($current_systemic_meds) { ?>
                <table id="<?= $model_name ?>_entry_table">
                    <tbody>
                    <?php foreach ($current_systemic_meds as $entry) : ?>
                        <tr>
                            <td>
                                <i class="oe-i start small pad-right pro-theme"></i>
                                <?= $entry->getMedicationDisplay() ?>
                            </td>
                            <td>
                                <?php if ($entry->getDoseAndFrequency()) { ?>
                                    <i class="oe-i info small pro-theme js-has-tooltip"
                                       data-tooltip-content="<?= $entry->getDoseAndFrequency() ?>">
                                    </i>
                                <?php } ?>
                                <span class="oe-date"><?= $entry->getStartDateDisplay() ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <div class="data-value not-recorded">
                    No current Systemic MedicationsT
                </div>
            <?php } ?>

            <?php if ($stopped_systemic_meds) { ?>
                <div class="collapse-data">
                    <div class="collapse-data-header-icon expand">
                        Stopped
                        <small>(<?= sizeof($stopped_systemic_meds) ?>)</small>
                    </div>
                    <div class="collapse-data-content">
                        <table>
                            <tbody>
                            <?php foreach ($stopped_systemic_meds as $entry) { ?>
                                <tr>
                                    <td>
                                        <i class="oe-i stop small pad-right pro-theme"></i>
                                        <?= $entry->getMedicationDisplay() ?>
                                    </td>
                                    <td>
                                        <?php if ($entry->getDoseAndFrequency()) { ?>
                                            <i class="oe-i info small pro-theme js-has-tooltip"
                                               data-tooltip-content="<?= $entry->getDoseAndFrequency() ?>">
                                            </i>
                                        <?php } ?>
                                        <span class="oe-date"><?= $entry->getEndDateDisplay() ?></span>
                                    </td>
                                    <?php if ($entry->prescription_item) { ?>
                                        <td>
                                        <a href="<?= $entry->getPrescriptionLink() ?>"><span
                                                    class="js-has-tooltip fa oe-i eye small pro-theme"
                                                    data-tooltip-content="View prescription"></span></a>
                                         </td>
                                    <?php } ?>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
</div>
</div><!-- popup-overflow -->

<!-- oe-popup-overflow handles scrolling if data overflow height -->
<div class="oe-popup-overflow quicklook-data-groups">
    <div class="group">
        <div class="label">Eye Medications</div>
        <div class="data">
            <?php if (!$current_eye_meds && !$stopped_eye_meds) { ?>
                <div class="nil-recorded">Nil recorded.</div>
            <?php } else { ?>
                <?php if ($current_eye_meds) { ?>
                    <table id="<?= $model_name ?>_entry_table">
                        <colgroup>
                            <col class="cols-8">
                            <col>
                        </colgroup>
                        <tbody>
                        <?php foreach ($current_eye_meds as $entry) { ?>
                            <tr>
                                <td>
                                    <i class="oe-i start small pad-right pro-theme"></i>
                                    <?= $entry->getMedicationDisplay() ?>
                                </td>
                                <td>
                                    <?php if ($entry->getDoseAndFrequency()) { ?>
                                        <i class="oe-i info small pro-theme js-has-tooltip"
                                           data-tooltip-content="<?= $entry->getDoseAndFrequency() ?>">
                                        </i>
                                    <?php } ?>
                                </td>
                                <td class="nowrap">
                                    <?php $laterality = $entry->getLateralityDisplay();
                                    $this->widget('EyeLateralityWidget', array('laterality' => $laterality, 'pad' => ''));
                                    ?>
                                    <span class="oe-date"><?= $entry->getStartDateDisplay() ?></span>
                                </td>
                                <td>
                                    <?php
                                    $link = $entry->prescription_item ? $entry->getPrescriptionLink() : $entry->getExaminationLink();
                                    $tooltip_content = 'View' . (strpos(strtolower($link), 'prescription') ? ' prescription' : ' examination'); ?>
                                    <a href="<?= $link ?>">
                                        <i class="js-has-tooltip fa oe-i direction-right-circle small pad"
                                           data-tooltip-content="<?= $tooltip_content ?>"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <div class="data-value none">
                        No current Eye Medications
                    </div>
                <?php } ?>

                <?php if ($stopped_eye_meds) { ?>
                    <div class="collapse-data">
                        <div class="collapse-data-header-icon expand">
                            Stopped
                            <small>(<?= sizeof($stopped_eye_meds) ?>)</small>
                        </div>
                        <div class="collapse-data-content">
                            <table>
                                <colgroup>
                                    <col class="cols-8">
                                    <col>
                                </colgroup>
                                <tbody>
                                <?php foreach ($stopped_eye_meds as $entry) { ?>
                                    <tr>
                                        <td>
                                            <i class="oe-i stop small pad-right pro-theme"></i>
                                            <?= $entry->getMedicationDisplay() ?>
                                        </td>
                                        <td></td>
                                        <td>
                                            <?php if ($entry->getDoseAndFrequency()) { ?>
                                                <i class="oe-i info small pro-theme js-has-tooltip"
                                                   data-tooltip-content="<?= $entry->getDoseAndFrequency() ?>">
                                                </i>
                                            <?php } ?>
                                        </td>
                                        <td class="nowrap">
                                            <?php $laterality = $entry->getLateralityDisplay();
                                            $this->widget('EyeLateralityWidget', array('laterality' => $laterality, 'pad' => ''));
                                            ?>
                                            <span class="oe-date"><?= $entry->getEndDateDisplay() ?></span>
                                        </td>
                                        <td><i class="oe-i"></i></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
