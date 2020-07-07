<?php
/**
 * (C) Copyright Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php
$observations = null;
foreach ($results as $result) {
    if (isset($result->observations)) {
        $observations = $result->observations;
    }
}
$element = $observations ?? new $model;
?>
<tr class="no-line">
    <td>
        <label for="<?= CHtml::modelName($element) . '_blood_pressure_systolic'; ?>">
            <?= $element->getAttributeLabel('blood_pressure') ?>
        </label>
    </td>
    <td>
        <?= CHtml::textField(
            $name_stub  . '[blood_pressure_systolic]',
            isset($element) ? $element->blood_pressure_systolic : '',
            ['class' => "cols-4", 'autocomplete' => Yii::app()->params['html_autocomplete'],
                'style' => 'display:inline-block;',
                'tabindex' => '1']
        ); ?>
        /
        <?= CHtml::textField(
            $name_stub  . '[blood_pressure_diastolic]',
            isset($element) ? $element->blood_pressure_diastolic : '',
            ['class' => "cols-4", 'autocomplete' => Yii::app()->params['html_autocomplete'],
                'style' => 'display:inline-block;',
                'tabindex' => '2']
        ); ?>
    </td>
    <td>
        <label for="<?= CHtml::modelName($element) . '_pulse'; ?>">
            <?= $element->getAttributeLabel('pulse') ?>
        </label>
    </td>
    <td>
        <?= CHtml::textField(
            $name_stub . '[pulse]',
            isset($element) ? $element->pulse : '',
            ['class' => "cols-4", 'autocomplete' => Yii::app()->params['html_autocomplete'],
                'tabindex' => '3']
        ); ?>
    </td>
    <td>
        <label for="<?= CHtml::modelName($element) . '_temperature'; ?>">
            <?= $element->getAttributeLabel('temperature') ?> &#8451;
        </label>
    </td>
    <td>
        <?= CHtml::textField(
            $name_stub . '[temperature]',
            isset($element) ? $element->temperature : '',
            ['class' => "cols-4", 'autocomplete' => Yii::app()->params['html_autocomplete'],
                'tabindex' => '4']
        ); ?>
    </td>
</tr>
<tr class="no-line">
    <td>
        <label for="<?= CHtml::modelName($element) . '_respiration'; ?>">
            <?= $element->getAttributeLabel('respiration') ?>
        </label>
    </td>
    <td>
        <?= CHtml::textField(
            $name_stub . '[respiration]',
            isset($element) ? $element->respiration : '',
            ['class' => "cols-4", 'autocomplete' => Yii::app()->params['html_autocomplete'],
                'tabindex' => '5']
        ); ?>
    </td>
    <td>
        <label for="<?= CHtml::modelName($element) . '_o2_sat'; ?>">
            <?= $element->getAttributeLabel('o2_sat') ?>
        </label>
    </td>
    <td>
        <?= CHtml::textField(
            $name_stub . '[o2_sat]',
            isset($element) ? $element->o2_sat : '',
            ['class' => "cols-4", 'autocomplete' => Yii::app()->params['html_autocomplete'],
                'tabindex' => '6']
        ); ?>
    </td>
    <td>
        <label for="<?= CHtml::modelName($element) . '_ews'; ?>">
            <?= $element->getAttributeLabel('ews') ?>
        </label>
    </td>
    <td>
        <?= CHtml::textField(
            $name_stub . '[ews]',
            isset($element) ? $element->ews : '',
            ['class' => "cols-4", 'autocomplete' => Yii::app()->params['html_autocomplete'],
                'tabindex' => '7']
        ); ?>
    </td>
</tr>
<tr class="no-line">
    <td>
        <label for="<?= CHtml::modelName($element) . '_blood_glucose'; ?>">
            <?= $element->getAttributeLabel('blood_glucose') ?>
        </label>
    </td>
    <td>
        <?= CHtml::textField(
            $name_stub . '[blood_glucose]',
            isset($element) ? $element->blood_glucose : '',
            ['class' => "cols-4", 'autocomplete' => Yii::app()->params['html_autocomplete'],
                'tabindex' => '8']
        ); ?>
    </td>
    <td>
        <label for="<?= CHtml::modelName($element) . '_hba1c'; ?>">
            <?= $element->getAttributeLabel('hba1c') ?>
        </label>
    </td>
    <td>
        <?= CHtml::textField(
            $name_stub . '[hba1c]',
            isset($element) ? $element->hba1c : '',
            ['class' => "cols-4", 'autocomplete' => Yii::app()->params['html_autocomplete'],
                'tabindex' => '9']
        ); ?>
    </td>
    <td>
        <label for="<?= CHtml::modelName($element) . '_inr'; ?>">
            <?= $element->getAttributeLabel('inr') ?>
        </label>
    </td>
    <td>
        <?= CHtml::textField(
            $name_stub . '[inr]',
            isset($element) ? $element->inr : '',
            ['class' => "cols-4", 'autocomplete' => Yii::app()->params['html_autocomplete'],
                'tabindex' => '10']
        ); ?>
    </td>
</tr>

