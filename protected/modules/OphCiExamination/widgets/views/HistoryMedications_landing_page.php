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
 * @var \OEModule\OphCiExamination\models\HistoryMedications $element
 * @var \EventMedicationUse[] $current
 * @var \EventMedicationUse[] $stopped
 */

$model_name = CHtml::modelName($element);

$eye_filter = function($e) {
	/** @var EventMedicationUse $e */
	return !is_null($e->route_id) && $e->route->has_laterality;
};

$systemic_filter = function ($entry) use ($eye_filter){
    return !$eye_filter($entry);
};

$current = $element->current_entries;
$stopped = $element->closed_entries;

$current_eye_meds = array_filter($current, $eye_filter);
$stopped_eye_meds = array_filter($stopped, $eye_filter);

?>

<?php if (!$current_eye_meds && !$stopped_eye_meds) { ?>
    <div class="nil-recorded">Nil recorded.</div>
<?php } else { ?>
    <?php if ($current_eye_meds) { ?>
        <table id="<?= $model_name ?>_entry_table">
            <colgroup>
                <col class="cols-7">
            </colgroup>
            <tbody>
            <?php foreach ($current_eye_meds as $entry) { ?>
                <tr>
                    <td>
                        <i class="oe-i start small pad-right"></i>
                        <?= $entry->getMedicationDisplay() ?>
                    </td>
                    <td>
                        <?php $tooltip_content = $entry->getTooltipContent();
                        if (!empty($tooltip_content)) { ?>
                            <i class="oe-i info small-icon js-has-tooltip"
                               data-tooltip-content="<?= $tooltip_content ?>">
                            </i>
                        <?php } ?>
                    </td>
                    <td class="nowrap">
                        <?php $laterality = $entry->getLateralityDisplay();
                        $this->widget('EyeLateralityWidget', array('laterality' => $laterality));
                        ?>
                        <span class="oe-date"><?= $entry->getStartDateDisplay() ?></span>
                    </td>
                    <td>
                        <?php if ($entry->usage_type === "OphDrPrescription") { ?>
                            <a href="<?= $this->getPrescriptionLink($entry); ?>">
                                        <span class="js-has-tooltip fa oe-i eye small"
                                              data-tooltip-content="View prescription"></span>
                            </a>
                        <?php } ?>
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
                <div class="restrict-data-shown">
                    <div class="restrict-data-content rows-10">
                        <table>
                            <colgroup>
                                <col class="cols-7">
                            </colgroup>
                            <tbody>
                            <?php foreach ($stopped_eye_meds as $entry) { ?>
                                <tr>
                                    <td>
                                        <i class="oe-i stop small pad-right"></i>
                                        <?= $entry->getMedicationDisplay() ?>
                                    </td>
                                    <td>
                                        <?php $tooltip_content = $entry->getTooltipContent();
                                        if (!empty($tooltip_content)) { ?>
                                            <i class="oe-i info small-icon js-has-tooltip"
                                               data-tooltip-content="<?= $tooltip_content ?>">
                                            </i>
                                        <?php } ?>
                                    </td>
                                    <td class="nowrap">
                                        <?php $laterality = $entry->getLateralityDisplay();
                                        $this->widget('EyeLateralityWidget', array('laterality' => $laterality)); ?>
                                        <span class="oe-date"><?= $entry->getEndDateDisplay() ?></span>
                                    </td>
                                    <td>
                                        <?php if ($entry->usage_type === "OphDrPrescription") { ?>
                                            <a href="<?= $this->getPrescriptionLink($entry); ?>">
                                                <span class="js-has-tooltip fa oe-i eye small"
                                                      data-tooltip-content="View prescription">
                                                </span>
                                            </a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } ?>
