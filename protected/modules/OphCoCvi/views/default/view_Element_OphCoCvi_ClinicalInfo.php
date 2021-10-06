<?php
/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Diagnosis_Not_Covered;
?>

<div class="element-data full-width">
        <div class="flex-layout flex-top col-gap">
            <div class="cols-full">
                <table class="label-value">
                    <tbody>

                    <?php $rows = [
                        $element->getAttributeLabel('consultant_id') => ($element->consultant->last_name ?? 'None'),
                        $element->getAttributeLabel('examination_date') => $element->NHSDate('examination_date'),
                        $element->getAttributeLabel('is_considered_blind') => is_null($element->is_considered_blind) ? 'Not recorded' : $element->displayconsideredblind,
                        $element->getAttributeLabel('information_booklet') => (is_null($element->information_booklet) ? 'Not recorded' : ($element->information_booklet == 1 ? 'Yes' : 'No')),
                        $element->getAttributeLabel('eclo') => is_null($element->eclo) ? 'Not recorded' : $element->displayeclo,
                    ];?>
                    <?php foreach ($rows as $label => $value) :?>
                        <tr>
                            <td><div class="data-label"><?= \CHtml::encode($label) ?></div></td>
                            <td><div class="data-value"><?= \CHtml::encode($value);?></div></td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex-layout flex-top col-gap">
            <div class="cols-half">
                <table class="label-value">
                    <tbody>
                    <?php $rows = [
                        $element->getAttributeLabel('best_corrected_right_va') =>
                            $element->displaybestcorrectedrightvaList . " " . !is_null($element->best_corrected_right_va) ? $element->displaybestcorrectedrightva : '',
                        $element->getAttributeLabel('best_recorded_right_va') => (is_null($element->best_recorded_right_va) ? 'Not recorded' : ($element->best_recorded_right_va == 1 ? 'Yes' : 'No')),


                    ];?>
                    <?php foreach ($rows as $label => $value) :?>
                        <tr>
                            <td><div class="data-label"><?= \CHtml::encode($label) ?></div></td>
                            <td><div class="data-value"><?= \CHtml::encode($value);?></div></td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </div>
            <div class="cols-half">
                <table class="label-value">
                    <tbody>

                    <?php $rows = [
                        $element->getAttributeLabel('best_corrected_left_va') =>
                            $element->displaybestcorrectedrightvaList . " " . !is_null($element->best_corrected_left_va) ? $element->displaybestcorrectedleftva : '',
                        $element->getAttributeLabel('best_recorded_left_va') => (is_null($element->best_recorded_left_va) ? 'Not recorded' : ($element->best_recorded_left_va == 1 ? 'Yes' : 'No')),
                    ];?>
                    <?php foreach ($rows as $label => $value) :?>
                        <tr>
                            <td><div class="data-label"><?= \CHtml::encode($label) ?></div></td>
                            <td><div class="data-value"><?= \CHtml::encode($value);?></div></td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex-layout flex-top col-gap">
            <div class="cols-full">
                <table class="label-value">
                    <tbody>

                    <?php $rows = [
                        $element->getAttributeLabel('best_corrected_binocular_va') =>
                            $element->displaybestcorrectedbinocularvaList . ' ' . !is_null($element->best_corrected_binocular_va) ? CHtml::encode($element->displaybestcorrectedbinocularva) : '',
                        $element->getAttributeLabel('best_recorded_binocular_va') => (is_null($element->best_recorded_binocular_va) ? 'Not recorded' : ($element->best_recorded_binocular_va == 1 ? 'Yes' : 'No')),
                        $element->getAttributeLabel('field_of_vision') => (is_null($element->field_of_vision) || $element->field_of_vision == 0 ? 'Not recorded' : ($element->field_of_vision == 1 ? 'Yes' : 'No')),
                        $element->getAttributeLabel('low_vision_service') => (is_null($element->low_vision_service) || $element->low_vision_service == 0 ? 'Not recorded' : CHtml::encode($element->displaylowvisionservice)),
                    ];?>
                    <?php foreach ($rows as $label => $value) :?>
                        <tr>
                            <td><div class="data-label"><?= \CHtml::encode($label) ?></div></td>
                            <td><div class="data-value"><?= \CHtml::encode($value);?></div></td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>

    <div class="row data-row">
        <div class="large-2 column">
            <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('disorders')) ?>:</div>
        </div>
    </div>
    <?php $this->renderPartial('view_Element_OphCoCvi_ClinicalInfo_Disorder_Assignment_Disorders', array(
        'element' => $element,
    ))?>
    <div class="row data-row">
        <div class="large-12 column">
            <div
                class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('diagnoses_not_covered')) ?>:</div>
        </div>
        <?php if (isset($element->diagnosis_not_covered)) : ?>
            <hr>
            <div class="column large-12">
                <table class="grid" id="diagnosis_not_covered_table">
                    <tbody>
                    <?php
                    foreach ($element->diagnosis_not_covered as $diagnosis) {
                        if (isset($diagnosis->disorder) || isset($diagnosis->clinicinfo_disorder)) {
                            switch ($diagnosis->eye_id) {
                                case 1:
                                    $eye = 'Left';
                                    break;
                                case 2:
                                    $eye = 'Right';
                                    break;
                                case 3:
                                    $eye = 'Bilateral';
                                    break;
                            }
                            if ($diagnosis->disorder_type == OphCoCvi_ClinicalInfo_Diagnosis_Not_Covered::TYPE_CLINICINFO_DISORDER) {
                                $disorder_name = $diagnosis->clinicinfo_disorder->term_to_display;
                                $disorder_code = $diagnosis->clinicinfo_disorder->code;
                            } else {
                                $disorder_name = $diagnosis->disorder->term;
                                $disorder_code = $diagnosis->code;
                            }
                            ?>
                            <tr id="diagnosis_not_covered_<?=CHtml::encode($diagnosis->id)?>">
                                <td>
                                    <?php echo CHtml::encode($eye) ?>
                                    <?php echo CHtml::encode($disorder_name) ?>
                                    <?php echo $diagnosis->main_cause == 1 ? '(main cause)' : '' ?> -
                                    <?php echo CHtml::encode($disorder_code) ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

</div>
