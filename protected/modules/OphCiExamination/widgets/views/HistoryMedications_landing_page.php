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

$current_eye_meds = array_filter($current, $eye_filter);
$stopped_eye_meds = array_filter($stopped, $eye_filter);

?>

<?php if (!$current_eye_meds && !$stopped_eye_meds) { ?>
    <div class="nil-recorded">Nil recorded.</div>
<?php } else { ?>
    <?php if ($current_eye_meds) { ?>
        <table id="<?= $model_name ?>_current_entry_table">
            <colgroup>
                <col class="cols-7">
            </colgroup>
            <thead style="display: none;">
                <tr>
                    <th>Drug</th>
                    <th></th>
                    <th>Eye &nbsp;&emsp;Start date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($current_eye_meds as $entry) { ?>
                <tr>
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
                            <i class="oe-i <?=$info_box->getIcon();?> small js-has-tooltip"
                               data-tooltip-content="<?= $tooltip_content ?>">
                        <?php } ?>
                    </td>
                    <td class="nowrap">
                        <?php $laterality = $entry->getLateralityDisplay();
                        $this->widget('EyeLateralityWidget', array('laterality' => $laterality));
                        ?>
                        <span class="oe-date"><?= $entry->getStartDateDisplay() ?></span>
                    </td>
                    <td>
                        <?php
                        $link = $entry->prescription_item_id && isset($entry->prescriptionItem->prescription->event) ? $this->getPrescriptionLink($entry) : $this->getExaminationLink();
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
                Stopped Medications
                <small>(<?= sizeof($stopped_eye_meds) ?>)</small>
            </div>
            <div class="collapse-data-content">
                <div class="restrict-data-shown">
                    <div class="restrict-data-content rows-10">
                        <table id="<?= $model_name ?>_stopped_entry_table">
                            <thead style="display: none;">
                                <tr>
                                    <th>Drug</th>
                                    <th></th>
                                    <th>Eye &nbsp;&emsp;Start date</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <colgroup>
                                <col class="cols-8">
                                <col>
                            </colgroup>
                            <tbody>
                            <?php foreach ($stopped_eye_meds as $entry) { ?>
                                <tr>
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
                                            <i class="oe-i <?=$info_box->getIcon();?> small js-has-tooltip"
                                               data-tooltip-content="<?= $tooltip_content ?>">
                                        <?php } ?>
                                    </td>
                                    <td class="nowrap">
                                        <?php $laterality = $entry->getLateralityDisplay();
                                        $this->widget('EyeLateralityWidget', array('laterality' => $laterality));
                                        ?>
                                        <span class="oe-date"><?= Helper::convertDate2HTML($entry->getEndDateDisplay()) ?></span>
                                    </td>
                                    <td><i class="oe-i"></i></td>
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
