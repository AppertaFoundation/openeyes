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
    <div class="field-row flex-layout flex-left col-gap">
        <div class="cols-4">
            <table class="cols-full">
                <colgroup>
                    <col class="cols-4">
                </colgroup>
                <tbody>
                <tr>
                    <td>
                        <label for="<?= CHtml::modelName($element) . '_blood_pressure_systolic'; ?>">
                            <?= $element->getAttributeLabel('blood_pressure') ?>:
                        </label>
                    </td>
                    <td>
                        <?= CHtml::activeTextField($element, 'blood_pressure_systolic', array('autocomplete' => Yii::app()->params['html_autocomplete'], 'style' => 'width:50px; display:inline-block;', 'placeholder' => 'mmHg', 'tabindex' => '1')); ?>
                        /
                        <?= CHtml::activeTextField($element, 'blood_pressure_diastolic', array('autocomplete' => Yii::app()->params['html_autocomplete'], 'style' => 'width:50px; display:inline-block;', 'placeholder' => 'mmHg', 'tabindex' => '2')); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="<?= CHtml::modelName($element) . '_blood_glucose'; ?>">
                            <?= $element->getAttributeLabel('blood_glucose') ?>:
                        </label>
                    </td>
                    <td>
                        <?= CHtml::activeTextField($element, 'blood_glucose', array('autocomplete' => Yii::app()->params['html_autocomplete'], 'placeholder' => 'mmol/l', 'tabindex' => '5')); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="<?= CHtml::modelName($element) . '_weight'; ?>">
                            <?= $element->getAttributeLabel('weight') ?>:
                        </label>
                    </td>
                    <td>
                        <div class="bmi-keyup-event">
                            <?= CHtml::activeTextField($element, 'weight', array('autocomplete' => Yii::app()->params['html_autocomplete'], 'placeholder' => 'kg', 'tabindex' => '7')); ?>
                        </div>
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
                        <label for="<?= CHtml::modelName($element) . '_o2_sat'; ?>">
                            <?= $element->getAttributeLabel('o2_sat') ?>:
                        </label>
                    </td>
                    <td>
                        <?= CHtml::activeTextField($element, 'o2_sat', array('autocomplete' => Yii::app()->params['html_autocomplete'], 'placeholder' => '%', 'tabindex' => '3')); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="<?= CHtml::modelName($element) . '_hba1c'; ?>">
                            <?= $element->getAttributeLabel('hba1c') ?>:
                        </label>
                    </td>
                    <td>
                        <?= CHtml::activeTextField($element, 'hba1c', array('autocomplete' => Yii::app()->params['html_autocomplete'], 'placeholder' => 'mmol/mol', 'tabindex' => '6')); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="<?= CHtml::modelName($element) . '_height'; ?>">
                            <?= $element->getAttributeLabel('height') ?>:
                        </label>
                    </td>
                    <td>
                        <div class="bmi-keyup-event">
                            <?= CHtml::activeTextField($element, 'height', array('autocomplete' => Yii::app()->params['html_autocomplete'], 'placeholder' => 'cm', 'tabindex' => '8')); ?>
                        </div>
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
                        <label for="<?= CHtml::modelName($element) . '_pulse'; ?>">
                            <?= $element->getAttributeLabel('pulse') ?>:
                        </label>
                    </td>
                    <td>
                        <?= CHtml::activeTextField($element, 'pulse', array('autocomplete' => Yii::app()->params['html_autocomplete'], 'placeholder' => 'BPM', 'tabindex' => '4')); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>
                            BMI:
                        </label>
                    </td>
                    <td>
                        <div class="large-3 column" id="bmi-container">

                        </div>
                        <label class="large-3 column">&nbsp;</label>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function () {
        var heightElement = $("#OEModule_OphCiExamination_models_Element_OphCiExamination_Observations_height");
        var weightElement = $("#OEModule_OphCiExamination_models_Element_OphCiExamination_Observations_weight");
        height = heightElement.val();
        weight = weightElement.val();
        getBMI(height, weight);

        $('.bmi-keyup-event input[type="text"]').keyup(function () {
            height = heightElement.val();
            weight = weightElement.val();
            getBMI(height, weight);
        });

        function getBMI(height, weight) {
            bmi = 0;
            if ((height > 0) && (weight > 0)) {
                bmi = bmi_calculator(weight, height);
                result = bmi.toFixed(2) || 'N/A';
            } else {
                result = 'N/A';
            }
            $('#bmi-container').text(result);
        }

    });
</script>

