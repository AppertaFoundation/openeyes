<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 *
 * @var $entry ObservationEntry
 */
?>
<?php use OEModule\OphCiExamination\models\AllergyEntry; ?>

<?php
if (!isset($values)) {
    $values = array(
        'id' => $entry->id,
        'element_id' => $entry->element_id,
        'blood_pressure_systolic' => $entry->blood_pressure_systolic,
        'blood_pressure_diastolic' => $entry->blood_pressure_diastolic,
        'blood_glucose' => $entry->blood_glucose,
        'weight' => $entry->weight,
        'o2_sat' => $entry->o2_sat,
        'rr' => $entry->rr,
        'hba1c' => $entry->hba1c,
        'height' => $entry->height,
        'pulse' => $entry->pulse,
        'temperature' => $entry->temperature,
        'other' => $entry->other,
        'taken_at' => $entry->taken_at,
    );
}
?>

<div class="data-group flex-layout flex-left col-gap" data-key="<?= $entry_index ?>">
    <div class="cols-4 data-group">
        <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?= $values['id'] ?>"/>
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
                    <label for="<?= CHtml::modelName($entry) . '_taken_at'; ?>">
                        <?= $entry->getAttributeLabel('taken_at') ?>
                    </label>
                </td>
                <td>
                     <?= CHtml::textField(
                         $field_prefix . '[taken_at]',
                         strtotime($values['taken_at']) ? date('H:m', strtotime($values['taken_at'])) : $values['taken_at'],
                         ['class' => "cols-5", 'autocomplete' => Yii::app()->params['html_autocomplete'],
                         'style' => 'display:inline-block; width:200%']
                     ); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="<?= CHtml::modelName($entry) . '_blood_pressure_systolic'; ?>">
                        <?= $entry->getAttributeLabel('blood_pressure') ?> (mmHg/mmHg)
                    </label>
                </td>
                <td colspan="2">
                    <?= CHtml::textField(
                        $field_prefix . '[blood_pressure_systolic]',
                        $values['blood_pressure_systolic'],
                        ['class' => "cols-5", 'autocomplete' => Yii::app()->params['html_autocomplete'],
                        'style' => 'display:inline-block;',
                        'tabindex' => '1']
                    ); ?>
                    /
                    <?= CHtml::textField(
                        $field_prefix . '[blood_pressure_diastolic]',
                        $values['blood_pressure_diastolic'],
                        ['class' => "cols-5", 'autocomplete' => Yii::app()->params['html_autocomplete'],
                        'style' => 'display:inline-block;',
                        'tabindex' => '2']
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="<?= CHtml::modelName($entry) . '_blood_glucose'; ?>">
                        <?= $entry->getAttributeLabel('blood_glucose') ?> (mmol/l)
                    </label>
                </td>
                <td colspan="2">
                    <?= CHtml::textField(
                        $field_prefix . '[blood_glucose]',
                        $values['blood_glucose'],
                        ['class' => "cols-5", 'autocomplete' => Yii::app()->params['html_autocomplete'],
                        'tabindex' => '5']
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="<?= CHtml::modelName($entry) . '_weight'; ?>">
                        <?= $entry->getAttributeLabel('weight') ?> (kg)
                    </label>
                </td>
                <td colspan="2">
                    <div class="bmi-keyup-event">
                        <?= CHtml::textField(
                            $field_prefix . '[weight]',
                            $values['weight'],
                            ['class' => "cols-5 bmi-weight-field", 'autocomplete' => Yii::app()->params['html_autocomplete'],
                            'tabindex' => '8', 'data-bmi-index' => $entry_index]
                        ); ?>
                    </div>
                </td>

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
                    <label for="<?= CHtml::modelName($entry) . '_o2_sat'; ?>">
                        O<sub>2</sub> Sat (air)
                    </label>
                </td>
                <td>
                    <?= CHtml::textField($field_prefix . '[o2_sat]', $values['o2_sat'], ['class' => "cols-full", 'autocomplete' => Yii::app()->params['html_autocomplete'], 'tabindex' => '3']); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="<?= CHtml::modelName($entry) . '_rr' ?>">
                        Respiratory rate (breaths/min)
                    </label>
                </td>
                <td>
                    <?= CHtml::textField($field_prefix . '[rr]', $values['rr'], ['class' => 'cols-full', 'autocomplete' => Yii::app()->params['html_autocomplete'], 'tabindex' => '3']) ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="<?= CHtml::modelName($entry) . '_hba1c'; ?>">
                        <?= $entry->getAttributeLabel('hba1c') ?> (mmol/mol)
                    </label>
                </td>
                <td>
                    <?= CHtml::textField($field_prefix . '[hba1c]', $values['hba1c'], ['class' => "cols-full", 'autocomplete' => Yii::app()->params['html_autocomplete'], 'tabindex' => '6']); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="<?= CHtml::modelName($entry) . '_height'; ?>">
                        <?= $entry->getAttributeLabel('height') ?> (cm)
                    </label>
                </td>
                <td>
                    <div class="bmi-keyup-event">
                        <?= CHtml::textField(
                            $field_prefix . '[height]',
                            $values['height'],
                            ['class' => "cols-full bmi-height-field", 'autocomplete' => Yii::app()->params['html_autocomplete'],
                            'tabindex' => '9', 'data-bmi-index' => $entry_index]
                        ); ?>
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
                <col class="cols-6">
                <col class="cols-2">
            </colgroup>
            <tbody>
            <tr>
                <td colspan="2">
                    <label for="<?= CHtml::modelName($entry) . '_pulse'; ?>">
                        <?= $entry->getAttributeLabel('pulse') ?> (bpm)
                    </label>
                </td>
                <td>
                    <?= CHtml::textField(
                        $field_prefix . '[pulse]',
                        $values['pulse'],
                        ['class' => "cols-full", 'autocomplete' => Yii::app()->params['html_autocomplete'],
                        'tabindex' => '4']
                    ); ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <label for="<?= CHtml::modelName($entry) . '_temperature'; ?>">
                        <?= $entry->getAttributeLabel('temperature') ?> (&deg;C)
                    <label>
                </td>
                <td>
                    <?= CHtml::textField(
                        $field_prefix . '[temperature]',
                        $values['temperature'],
                        ['class' => "cols-full", 'autocomplete' => Yii::app()->params['html_autocomplete'],
                        'tabindex' => '7']
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="<?= CHtml::modelName($entry) . '_other'?>">
                        <?= $entry->getAttributeLabel('other') ?>
                    </label>
                </td>
                <td colspan="2">
                    <?= CHtml::textField(
                        $field_prefix . '[other]',
                        $values['other'],
                        ['class' => "cols-full", 'autocomplete' => Yii::app()->params['html_autocomplete'],
                        'tabindex' => '7']
                    ); ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <label>
                        BMI
                    </label>
                </td>
                <td>
                    <div class="bmi-container" style="text-align: center" data-bmi-index="<?= $entry_index ?>">
                        <label>&nbsp;</label>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div><i class="oe-i trash"></i></div>
</div>
