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

$systemic_filter = function ($med) {
    return $med->laterality === null;
};

$eye_filter = function($e) {
    /** @var EventMedicationUse $e */
    return !is_null($e->route_id) && $e->route->has_laterality;
};

$current_systemic_meds = array_filter($current, $systemic_filter);
$stopped_systemic_meds = array_filter($stopped, $systemic_filter);
$current_eye_meds = array_filter($current, $eye_filter);
$stopped_eye_meds = array_filter($stopped, $eye_filter);

?>
<div class="group" name="group-systemic-medications">
    <div class="label">Systemic Medications</div>
    <div class="data">
        <?php if (!$current_systemic_meds && !$stopped_systemic_meds && !$element->no_systemic_medications_date) { ?>
            <div class="nil-recorded">Nil recorded.</div>
        <?php } elseif ($element->no_systemic_medications_date) { ?>
            <div class="nil-recorded">Patient has had no previous systemic treatment</div>
        <?php } else { ?>
            <?php if ($current_systemic_meds) { ?>
                <table id="<?= $model_name ?>_systemic_current_entry_table">
                    <colgroup>
                        <col class="cols-8">
                        <col>
                    </colgroup>
                    <thead style="display:none;">
                        <th>Drug</th>
                        <th>Date</th>
                        <th>Link</th>
                    </thead>
                    <tbody>
                    <?php $index = 0; ?>
                    <?php foreach ($current_systemic_meds as $entry) : ?>
                        <tr data-key="<?= $index ?>">
                            <td>
                                <?= $entry->getMedicationDisplay() ?>
                            </td>
                            <td>
                                <?php
                                $info_box = new MedicationInfoBox();
                                $info_box->medication_id = $entry->medication->id;
                                $info_box->init();

                                $tooltip_content = $entry->getTooltipContent() . "<br />" . $info_box->getAppendLabel();
                                if (!empty($tooltip_content)) { ?>
                                    <i class="oe-i <?=$info_box->getIcon();?> small pro-theme js-has-tooltip pad-right"
                                       data-tooltip-content="<?= $tooltip_content ?>">
                                    </i>
                                <?php } ?>
                                <span class="oe-date"><?= $entry->getStartDateDisplay() ?></span>
                            </td>
                            <td>
                                <?php if ($entry->prescription_item_id) { ?>
                                    <a href="<?= $this->getPrescriptionLink($entry) ?>"><span
                                            class="js-has-tooltip fa oe-i direction-right-circle small pad pro-theme"
                                            data-tooltip-content="View prescription"></span></a>
                                    <?php } ?>
                            </td>
                        </tr>
                        <?php $index++; ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <div class="data-value not-recorded">
                    No current Systemic Medications
                </div>
            <?php } ?>

            <?php if ($stopped_systemic_meds) { ?>
                <div class="collapse-data">
                    <div class="collapse-data-header-icon expand">
                        Stopped
                        <small>(<?= sizeof($stopped_systemic_meds) ?>)</small>
                    </div>
                    <div class="collapse-data-content">
                        <table id="<?= $model_name ?>_systemic_stopped_entry_table">
                            <colgroup>
                                <col class="cols-7">
                                <col>
                            </colgroup>
                        <thead style="display:none;">
                            <th>Drug</th>
                            <th>Date</th>
                            <th>Link</th>
                        </thead>
                            <tbody>
                            <?php $index = 0; ?>
                            <?php foreach ($stopped_systemic_meds as $entry) : ?>
                                <tr data-key="<?= $index ?>">
                                    <td>
                                        <?= $entry->getMedicationDisplay() ?>
                                    </td>
                                    <td>
                                        <?php
                                        $info_box = new MedicationInfoBox();
                                        $info_box->medication_id = $entry->medication->id;
                                        $info_box->init();

                                        $tooltip_content = $entry->getTooltipContent() . "<br />" . $info_box->getAppendLabel();
                                        if (!empty($tooltip_content)) { ?>
                                            <i class="oe-i <?=$info_box->getIcon();?> small pro-theme js-has-tooltip pad-right"
                                               data-tooltip-content="<?= $tooltip_content ?>">
                                            </i>
                                        <?php } ?>
                                        <span class="oe-date"><?= $entry->getEndDateDisplay() ?></span>
                                    </td>
                                    <td>
                                    <?php if ($entry->prescription_item_id) { ?>
                                        <a href="<?= $this->getPrescriptionLink($entry) ?>"><span
                                                class="js-has-tooltip fa oe-i direction-right-circle small pad pro-theme"
                                                data-tooltip-content="View prescription"></span></a>
                                        <?php } ?>
                                    </td>

                                </tr>
                                <?php $index++; ?>
                            <?php endforeach; ?>
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
    <div class="group" name="group-eye-medications">
        <div class="label">Eye Medications</div>
        <div class="data">
            <?php if (!$current_eye_meds && !$stopped_eye_meds && !$element->no_ophthalmic_medications_date) { ?>
                <div class="nil-recorded">Nil recorded.</div>
            <?php } elseif ($element->no_ophthalmic_medications_date) { ?>
                <div class="nil-recorded">Patient has had no previous eye surgery or laser treatment</div>
            <?php } else { ?>
                <?php if ($current_eye_meds) { ?>
                    <table id="<?= $model_name ?>_eye_current_entry_table">
                        <colgroup>
                            <col class="cols-8">
                            <col>
                        </colgroup>
                        <thead style="display:none;">
                            <th>Drug</th>
                            <th>Tooltip</th>
                            <th>Date</th>
                            <th>Link</th>
                        </thead>
                        <tbody>
                        <?php $index = 0; ?>
                        <?php foreach ($current_eye_meds as $entry) : ?>
                            <tr data-key="<?= $index ?>">
                                <td>
                                    <?= $entry->getMedicationDisplay() ?>
                                </td>
                                <td>

                                   <?php
                                        $info_box = new MedicationInfoBox();
                                        $info_box->medication_id = $entry->medication->id;
                                        $info_box->init();

                                    $tooltip_content = $entry->getTooltipContent() . "<br />" . $info_box->getAppendLabel();
                                    if (!empty($tooltip_content)) { ?>
                                        <i class="oe-i <?=$info_box->getIcon();?> small pro-theme js-has-tooltip"
                                           data-tooltip-content="<?= $tooltip_content ?>">
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
                                    $link = $entry->prescription_item_id && isset($entry->prescriptionItem->prescription->event) ? $this->getPrescriptionLink($entry) : $this->getExaminationLink();
                                    $tooltip_content = 'View' . (strpos(strtolower($link), 'prescription') ? ' prescription' : ' examination'); ?>
                                    <a href="<?= $link ?>">
                                        <i class="js-has-tooltip fa pro-theme oe-i direction-right-circle small pad"
                                           data-tooltip-content="<?= $tooltip_content ?>"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php $index++; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php } ?>
                <?php if ($stopped_eye_meds) { ?>
                    <div class="collapse-data">
                        <div class="collapse-data-header-icon expand">
                            Stopped Medications
                            <small>(<?= sizeof($stopped_eye_meds) ?>)</small>
                        </div>
                        <div class="collapse-data-content">
                            <table id="<?= $model_name ?>_eye_stopped_entry_table">
                                <colgroup>
                                    <col class="cols-8">
                                    <col>
                                </colgroup>
                                <thead style="display:none;">
                                    <th>Drug</th>
                                    <th></th>
                                    <th>Tooltip</th>
                                    <th>Date</th>
                                    <th>Link</th>
                                </thead>
                                <tbody>
                                <?php $index = 0; ?>
                                <?php foreach ($stopped_eye_meds as $entry) { ?>
                                    <tr data-key="<?= $index ?>">
                                        <td>
                                            <?= $entry->getMedicationDisplay() ?>
                                        </td>
                                        <td></td>
                                        <td>
                                            <?php
                                            $info_box = new MedicationInfoBox();
                                            $info_box->medication_id = $entry->medication->id;
                                            $info_box->init();

                                            $tooltip_content = $entry->getTooltipContent() . "<br />" . $info_box->getAppendLabel();
                                            if (!empty($tooltip_content)) { ?>
                                                <i class="oe-i <?=$info_box->getIcon()?> small pro-theme js-has-tooltip"
                                                   data-tooltip-content="<?= $tooltip_content ?>">
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
                                    <?php $index++; ?>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
