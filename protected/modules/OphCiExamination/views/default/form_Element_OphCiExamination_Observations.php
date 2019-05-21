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
    <div class="data-group flex-layout flex-left col-gap">
        <div class="cols-4 data-group">
            <table class="cols-full">
                <colgroup>
                    <col class="cols-6">
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-2">
                </colgroup>
                <tbody>
                <tr>
                    <td>
                        <label for="<?= CHtml::modelName($element) . '_blood_pressure_systolic'; ?>">
                            <?= $element->getAttributeLabel('blood_pressure') ?>:
                        </label>
                    </td>
                    <td colspan="2">
                        <?= CHtml::activeTextField($element, 'blood_pressure_systolic',
                            ['class' => "cols-5", 'autocomplete' => Yii::app()->params['html_autocomplete'],
                            'style' => 'display:inline-block;', 'tabindex' => '1']); ?>
                        /
                        <?= CHtml::activeTextField($element, 'blood_pressure_diastolic',
                            ['class' => "cols-5", 'autocomplete' => Yii::app()->params['html_autocomplete'],
                            'style' => 'display:inline-block;', 'tabindex' => '2']); ?>
                    </td>
                    <td>mmHg/mmHg</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <label for="<?= CHtml::modelName($element) . '_blood_glucose'; ?>">
                            <?= $element->getAttributeLabel('blood_glucose') ?>:
                        </label>
                    </td>
                    <td>
                        <?= CHtml::activeTextField($element, 'blood_glucose',
                            ['class' => "cols-full", 'autocomplete' => Yii::app()->params['html_autocomplete'],
                            'tabindex' => '5']); ?>
                    </td>
                    <td>mmol/l</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <label for="<?= CHtml::modelName($element) . '_weight'; ?>">
                            <?= $element->getAttributeLabel('weight') ?>:
                        </label>
                    </td>
                    <td>
                        <div class="bmi-keyup-event">
                            <?= CHtml::activeTextField($element, 'weight',
                                ['class' => "cols-full", 'autocomplete' => Yii::app()->params['html_autocomplete'],
                                'tabindex' => '7']); ?>
                        </div>
                    </td>
                    <td>kg</td>

                </tr>
                </tbody>
            </table>
        </div>
        <div class="cols-4">
            <table class="cols-full">
                <colgroup>
                    <col class="cols-8">
                    <col class="cols-2">
                    <col class="cols-2">
                </colgroup>
                <tbody>
                <tr>
                    <td>
                        <label for="<?= CHtml::modelName($element) . '_o2_sat'; ?>">
                            O<sub>2</sub> Sat (air):
                        </label>
                    </td>
                    <td>
                        <?= CHtml::activeTextField($element, 'o2_sat', ['class' => "cols-full", 'autocomplete' => Yii::app()->params['html_autocomplete'], 'tabindex' => '3']); ?>
                    </td>
                    <td>%</td>
                </tr>
                <tr>
                    <td>
                        <label for="<?= CHtml::modelName($element) . '_hba1c'; ?>">
                            <?= $element->getAttributeLabel('hba1c') ?>:
                        </label>
                    </td>
                    <td>
                        <?= CHtml::activeTextField($element, 'hba1c', ['class' => "cols-full", 'autocomplete' => Yii::app()->params['html_autocomplete'], 'tabindex' => '6']); ?>
                    </td>
                    <td>mmol/mol</td>
                </tr>
                <tr>
                    <td>
                        <label for="<?= CHtml::modelName($element) . '_height'; ?>">
                            <?= $element->getAttributeLabel('height') ?>:
                        </label>
                    </td>
                    <td>
                        <div class="bmi-keyup-event">
                            <?= CHtml::activeTextField($element, 'height',
                                ['class' => "cols-full", 'autocomplete' => Yii::app()->params['html_autocomplete'],
                                'tabindex' => '8']); ?>
                        </div>
                    </td>
                    <td>cm</td>
                </tr>

                </tbody>
            </table>
        </div>
        <div class="cols-4">
            <table class="cols-full">
                <colgroup>
                    <col class="cols-8">
                    <col class="cols-2">
                    <col class="cols-2">                </colgroup>
                <tbody>
                <tr>
                    <td>
                        <label for="<?= CHtml::modelName($element) . '_pulse'; ?>">
                            <?= $element->getAttributeLabel('pulse') ?>:
                        </label>
                    </td>
                    <td>
                        <?= CHtml::activeTextField($element, 'pulse',
                        ['class' => "cols-full", 'autocomplete' => Yii::app()->params['html_autocomplete'],
                        'tabindex' => '4']); ?>
                    </td>
                    <td>BPM</td>
                </tr>
                <tr>
                    <td colspan="2">
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
        getAndSetBMI(height, weight);

        $('.bmi-keyup-event input[type="text"]').keyup(function () {
            height = heightElement.val();
            weight = weightElement.val();
            getAndSetBMI(height, weight);
        });

        function getAndSetBMI(height, weight) {
            let $bmiContainer = $('#bmi-container');
            bmi = 0;
            let red = "0%";
            let green = "80%";
            if ((height > 0) && (weight > 0)) {
                bmi = bmi_calculator(weight, height);
                result = bmi.toFixed(2) || 'N/A';
                let resultFloat = parseFloat(result);
                console.log(typeof parseFloat(result));
                if (resultFloat < 18.5 || resultFloat >= 30) {
                    red = "80%";
                    green = "0%";
                }
            } else {
                result = 'N/A';
            }
            $bmiContainer.text(result);
            // console.log($bmiContainer.closest("tr"));
            $bmiContainer.closest("tr").css("background-color", "rgb("+red+", "+green+", 0%)");
        }
    });
</script>

