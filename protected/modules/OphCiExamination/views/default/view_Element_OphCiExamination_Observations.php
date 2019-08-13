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
<div class="element-fields full-width">
    <div class="flex-layout flex-left col-gap">
        <div class="cols-4 data-group">
            <table class="cols-full">
                <colgroup>
                    <col class="cols-4">
                </colgroup>
                <tbody>
                <tr>
                    <td>
                        <span class="data-label"><?= $element->getAttributeLabel('blood_pressure') ?>:</span>
                    </td>
                    <td>
                        <span class="data-value"><?php echo (!empty($element->blood_pressure_systolic)) ? $element->blood_pressure_systolic . '/' . $element->blood_pressure_diastolic . ' mmHg' : '' ?>
                            <span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="data-label"><?= $element->getAttributeLabel('blood_glucose') ?>:</span>
                    </td>
                    <td>
                        <span class="data-value"><?php echo (!empty($element->blood_glucose)) ? $element->blood_glucose . ' mmol/l' : '' ?></span>
                    </td>

                </tr>
                <tr>
                    <td>
                        <span class="data-label"><?= $element->getAttributeLabel('weight') ?>:</span>
                    </td>
                    <td>
                        <span class="data-value"><?php echo (!empty($element->weight)) ? $element->weight . ' kg' : '' ?></span>
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
                            <span class="data-label"><?= $element->getAttributeLabel('o2_sat') ?>:</span>
                        </td>
                        <td>
                            <span class="data-value"><?php echo (!empty($element->o2_sat)) ? $element->o2_sat . ' %' : '' ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="data-label"><?= $element->getAttributeLabel('hba1c') ?>:</span>
                        </td>
                        <td>
                            <span class="data-value"><?php echo (!empty($element->hba1c)) ? $element->hba1c . ' mmol/mol' : '' ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="data-label"><?= $element->getAttributeLabel('height') ?>:</span>
                        </td>
                        <td>
                            <span class="data-value"><?php echo (!empty($element->height)) ? $element->height . ' cm' : '' ?></span>
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
                            <span class="data-label"><?= $element->getAttributeLabel('pulse') ?>:</span>
                        </td>
                        <td>
                            <span class="data-value"><?php echo (!empty($element->pulse)) ? $element->pulse . ' BPM' : ''; ?></span>
                        </td>
                        <td>
                            <span class="data-label">BMI:</span>&emsp;&nbsp;&nbsp;
                        </td>
                        <td>
                            <?php
                                $bmi = 'N/A';
                                $bmi_container_color = "rgb(0%, 80%, 0%)";
                                if (ceil($element->weight) > 0 && ceil($element->height) > 0) {
                                    $bmi = $element->bmiCalculator($element->weight, $element->height);
                                    if($bmi < 18.5 || $bmi >= 30){
                                        $bmi_container_color = "rgb(80%, 0%, 0%)";
                                    }
                                } ?>
                            <div id="bmi-container" class="data-value" style="display:inline-block; text-align: center; background-color: <?= $bmi_container_color ?>;">
                                <?= $bmi ?>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
    </div>
</div>
