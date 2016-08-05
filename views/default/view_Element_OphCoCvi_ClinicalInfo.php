<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

?>

<div class="element-data">
    <div class="row data-row">
        <div class="large-2 column">
            <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('examination_date')) ?></div>
        </div>
        <div class="large-10 column end">
            <div class="data-value"><?php echo CHtml::encode($element->NHSDate('examination_date')) ?></div>
        </div>
    </div>
    <div class="row data-row">
        <div class="large-2 column">
            <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('is_considered_blind')) ?>:
            </div>
        </div>
        <div class="large-10 column end">
            <div class="data-value"><?php echo $element->is_considered_blind ? 'Yes' : 'No' ?></div>
        </div>
    </div>
    <div class="row data-row">
        <div class="large-2 column">
            <div
                class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('sight_varies_by_light_levels')) ?>
                :
            </div>
        </div>
        <div class="large-10 column end">
            <div class="data-value"><?php echo $element->sight_varies_by_light_levels ? 'Yes' : 'No' ?></div>
        </div>
    </div>
    <div class="row data-row">
        <div class="large-2 column">
            <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('unaided_right_va')) ?></div>
        </div>
        <div class="large-10 column end">
            <div class="data-value"><?php echo CHtml::encode($element->unaided_right_va) ?></div>
        </div>
    </div>
    <div class="row data-row">
        <div class="large-2 column">
            <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('unaided_left_va')) ?></div>
        </div>
        <div class="large-10 column end">
            <div class="data-value"><?php echo CHtml::encode($element->unaided_left_va) ?></div>
        </div>
    </div>
    <div class="row data-row">
        <div class="large-2 column">
            <div
                class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('best_corrected_right_va')) ?></div>
        </div>
        <div class="large-10 column end">
            <div class="data-value"><?php echo CHtml::encode($element->best_corrected_right_va) ?></div>
        </div>
    </div>
    <div class="row data-row">
        <div class="large-2 column">
            <div
                class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('best_corrected_left_va')) ?></div>
        </div>
        <div class="large-10 column end">
            <div class="data-value"><?php echo CHtml::encode($element->best_corrected_left_va) ?></div>
        </div>
    </div>
    <div class="row data-row">
        <div class="large-2 column">
            <div
                class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('best_corrected_binocular_va')) ?></div>
        </div>
        <div class="large-10 column end">
            <div class="data-value"><?php echo CHtml::encode($element->best_corrected_binocular_va) ?></div>
        </div>
    </div>
    <div class="row data-row">
        <div class="large-2 column">
            <div
                class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('low_vision_status_id')) ?></div>
        </div>
        <div class="large-10 column end">
            <div
                class="data-value"><?php echo $element->low_vision_status ? $element->low_vision_status->name : 'None' ?></div>
        </div>
    </div>
    <div class="row data-row">
        <div class="large-2 column">
            <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('field_of_vision_id')) ?></div>
        </div>
        <div class="large-10 column end">
            <div
                class="data-value"><?php echo $element->field_of_vision ? $element->field_of_vision->name : 'None' ?></div>
        </div>
    </div>
    <div class="row data-row">
        <div class="large-2 column">
            <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('disorders')) ?>:</div>
        </div>
        <div class="large-10 column end">
            <div class="data-value"><?php if (!$element->disorders) { ?>
                    None
                <?php } else { ?>
                    <?php foreach ($element->disorders as $item) {
                        echo $item->ophcocvi_clinicinfo_disorder->name ?><br/>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="row data-row">
        <div class="large-2 column">
            <div
                class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('diagnoses_not_covered')) ?></div>
        </div>
        <div class="large-10 column end">
            <div class="data-value"><?php echo CHtml::encode($element->diagnoses_not_covered) ?></div>
        </div>
    </div>
    <div class="row data-row">
        <div class="large-2 column">
            <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('consultant_id')) ?></div>
        </div>
        <div class="large-10 column end">
            <div class="data-value"><?php echo $element->consultant ? $element->consultant->last_name : 'None' ?></div>
        </div>
    </div>
</div>
