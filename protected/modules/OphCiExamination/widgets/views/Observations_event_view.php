<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php use OEModule\OphCiExamination\models\ObservationEntry; ?>
<div class="element-fields full-width">
<?php if (!count($element->entries)) : ?>
    <div class="data-value not-recorded left" style="text-align: left;">
        No entries recorded
    </div>
<?php else : ?>
    <?php foreach ($element->entries as $entry) : ?>
    <div class="flex-layout flex-left col-gap">
        <div class="cols-4 data-group">
            <table class="cols-full">
                <colgroup>
                    <col class="cols-4">
                </colgroup>
                <tbody>
                <tr>
                    <td>
                        <span class="data-label"><?= $entry->getAttributeLabel('taken_at') ?>:</span>
                    </td>
                    <td>
                        <span class="data-value"><?php echo ($entry->taken_at !== null) ? date('H:m', strtotime($entry->taken_at)) : '' ?><span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="data-label"><?= $entry->getAttributeLabel('blood_pressure') ?>:</span>
                    </td>
                    <td>
                        <span class="data-value"><?php echo ($entry->blood_pressure_systolic !== null) ? $entry->blood_pressure_systolic . '/' . $entry->blood_pressure_diastolic . ' mmHg' : '' ?>
                            <span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="data-label"><?= $entry->getAttributeLabel('blood_glucose') ?>:</span>
                    </td>
                    <td>
                        <span class="data-value"><?php echo ($entry->blood_glucose !== null) ? $entry->blood_glucose . ' mmol/l' : '' ?></span>
                    </td>

                </tr>
                <tr>
                    <td>
                        <span class="data-label"><?= $entry->getAttributeLabel('weight') ?>:</span>
                    </td>
                    <td>
                        <span class="data-value"><?php echo ($entry->weight !== null) ? $entry->weight . ' kg' : '' ?></span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
            <div class="cols-4">
                <table class="cols-full">
                    <colgroup>
                        <col class="cols-4">
                    </colgroup>
                    <tbody>
                    <tr>
                        <td>
                            <span class="data-label"><?= $entry->getAttributeLabel('o2_sat') ?>:</span>
                        </td>
                        <td>
                            <span class="data-value"><?php echo ($entry->o2_sat !== null) ? $entry->o2_sat . ' %' : '' ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="data-label"><?= $entry->getAttributeLabel('hba1c') ?>:</span>
                        </td>
                        <td>
                            <span class="data-value"><?php echo ($entry->hba1c !== null) ? $entry->hba1c . ' mmol/mol' : '' ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="data-label"><?= $entry->getAttributeLabel('height') ?>:</span>
                        </td>
                        <td>
                            <span class="data-value"><?php echo ($entry->height !== null) ? $entry->height . ' cm' : '' ?></span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="cols-4">
                <table class="cols-full">
                    <colgroup>
                        <col class="cols-4">
                    </colgroup>
                    <tbody>
                    <tr>
                        <td>
                            <span class="data-label"><?= $entry->getAttributeLabel('pulse') ?>:</span>
                        </td>
                        <td>
                            <span class="data-value"><?php echo ($entry->pulse !== null) ? $entry->pulse . ' BPM' : ''; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="data-label"><?= $entry->getAttributeLabel('temperature') ?>:</span>
                        </td>
                        <td>
                            <span class="data-value"><?php echo ($entry->temperature !== null) ? $entry->temperature . ' &deg;C' : ''; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="data-label">BMI:</span>&emsp;&nbsp;&nbsp;
                        </td>
                        <td>
                            <?php
                                $bmi = 'N/A';
                                $bmi_container_color = "";
                            if (ceil($entry->weight) > 0 && ceil($entry->height) > 0) {
                                $bmi = $entry->bmiCalculator($entry->weight, $entry->height);
                                if ($bmi < 18.5 || $bmi >= 30) {
                                    $bmi_container_color = 'highlighter warning';
                                } else {
                                    $bmi_container_color = 'highlighter good';
                                }
                            }
                            ?>
                            <div id="bmi-container" class="data-value <?= $bmi_container_color ?>" style="display:inline-block; text-align: center;">
                                <?= $bmi ?>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>
