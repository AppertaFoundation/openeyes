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

$observations = null;
foreach ($results as $result) {
    if (isset($result->observations)) {
        $observations = $result->observations;
    }
}
$element = $observations ?? new $model();
// INR results
$api = Yii::app()->moduleAPI->get('OphInLabResults');
$patientId = $_GET['patient_id'] ?? $this->patient->id;
$eventId = $this->event->id ?? null;
$inrResult = $api->getLabResultTypeResult($patientId, $eventId, "INR");
?>
<table class="cols-full">
    <tbody>
        <?php
        echo \CHtml::hiddenField('Element_OphTrOperationchecklists_Admission[checklistResults][' . $question->id . '][mandatory]', $question->mandatory);
        if (isset($results)) {
            echo \CHtml::hiddenField('Element_OphTrOperationchecklists_Admission[checklistResults][' . $question->id . '][id]', @$results[$question->id]->id);
        }
        echo \CHtml::hiddenField('Element_OphTrOperationchecklists_Admission[checklistResults][' . $question->id . '][question_id]', $question->id);
        echo \CHtml::hiddenField('Element_OphTrOperationchecklists_Admission[checklistResults][' . $question->id . '][answer_id]', @$results[$question->id]->answer_id, array('id' => 'result_answer_id' . 'Element_OphTrOperationchecklists_Admission[checklistResults]' . $question->id));
        echo \CHtml::hiddenField('Element_OphTrOperationchecklists_Admission[checklistResults][' . $question->id . '][answer]', @$results[$question->id]->answer, array('id' => 'result_answer' . 'Element_OphTrOperationchecklists_Admission[checklistResults]' . $question->id));
        ?>
        <tr>
            <td colspan="3">
                <?= $question->question; ?>
            </td>
            <td colspan="3">
                <?php if ($question->is_comment_field_required) : ?>
                    <?php $comment_button_id = $model_relation . $question->id . '_comment'; ?>
                    <div class="cols-full ">
                        <button id="<?= $comment_button_id . '_button' ?>"
                                type="button"
                                class="button js-add-comments"
                                style="<?php if (isset($results[$question->id]->comment) && @$results[$question->id]->comment != '') :
                                    ?>display: none;<?php
                                       endif; ?>"
                                data-comment-container="#<?= $comment_button_id . '_container'; ?>">
                            <i class="oe-i comments small-icon"></i>
                        </button>
                        <div class="flex-layout flex-left comment-group js-comment-container"
                             id="<?= $comment_button_id . '_container'; ?>"
                             style="<?php if (! (isset($results[$question->id]->comment) && @$results[$question->id]->comment != '')) :
                                    ?>display: none;<?php
                                    endif; ?>"
                             data-comment-button="#<?= $comment_button_id . '_button' ?>">
                            <?=\CHtml::textArea($name_stub . '[' . $question->id . '][comment]', @$results[$question->id]->comment, array(
                                'class' => 'autosize cols-full js-comment-field',
                                'rows' => 1,
                                'placeholder' => 'Comments',
                            )); ?>
                            <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
                        </div>
                    </div>

                <?php endif; ?>
            </td>
        </tr>
        <tr class="no-line">
            <td>
                <label for="<?= CHtml::modelName($element) . '_blood_pressure_systolic'; ?>">
                    <?= $element->getAttributeLabel('blood_pressure') ?>
                </label>
            </td>
            <td>
                <?= CHtml::textField(
                    $name_stub . '[' . $question->id . ']' . '[' . $relation . ']'  . '[blood_pressure_systolic]',
                    isset($element) ? $element->blood_pressure_systolic : '',
                    ['class' => "cols-4", 'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                        'style' => 'display:inline-block;',
                        'tabindex' => '1']
                ); ?>
                /
                <?= CHtml::textField(
                    $name_stub . '[' . $question->id . ']' . '[' . $relation . ']'  . '[blood_pressure_diastolic]',
                    isset($element) ? $element->blood_pressure_diastolic : '',
                    ['class' => "cols-4", 'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                        'style' => 'display:inline - block;',
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
                    $name_stub . '[' . $question->id . ']' . '[' . $relation . ']' . '[pulse]',
                    isset($element) ? $element->pulse : '',
                    ['class' => "cols-4", 'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
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
                    $name_stub . '[' . $question->id . ']' . '[' . $relation . ']' . '[temperature]',
                    isset($element) ? $element->temperature : '',
                    ['class' => "cols-4", 'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
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
                    $name_stub . '[' . $question->id . ']' . '[' . $relation . ']' . '[respiration]',
                    isset($element) ? $element->respiration : '',
                    ['class' => "cols-4", 'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
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
                    $name_stub . '[' . $question->id . ']' . '[' . $relation . ']' . '[o2_sat]',
                    isset($element) ? $element->o2_sat : '',
                    ['class' => "cols-4", 'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
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
                    $name_stub . '[' . $question->id . ']' . '[' . $relation . ']' . '[ews]',
                    isset($element) ? $element->ews : '',
                    ['class' => "cols-4", 'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
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
                    $name_stub . '[' . $question->id . ']' . '[' . $relation . ']' . '[blood_glucose]',
                    isset($element) ? $element->blood_glucose : '',
                    ['class' => "cols-4", 'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
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
                    $name_stub . '[' . $question->id . ']' . '[' . $relation . ']' . '[hba1c]',
                    isset($element) ? $element->hba1c : '',
                    ['class' => "cols-4", 'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                        'tabindex' => '9']
                ); ?>
            </td>
            <td>
                <label>
                    INR
                </label>
            </td>
            <td>
                <?php if (isset($inrResult)) {
                    echo $inrResult['result'];
                } else { ?>
                    Not recorded
                    <i class="js-has-tooltip oe-i info small pad right" data-tooltip-content="INR result is not recorded for this patient. This can be recorded in the Lab Results event."></i>
                <?php } ?>
            </td>
        </tr>
    </tbody>
</table>
