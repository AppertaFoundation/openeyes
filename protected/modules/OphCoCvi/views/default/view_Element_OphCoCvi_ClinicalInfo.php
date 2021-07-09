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
            <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('consultant_id')) ?>:</div>
        </div>
        <div class="large-10 column end">
            <div class="data-value"><?php echo $element->consultant ? CHtml::encode($element->consultant->last_name) : 'None' ?></div>
        </div>
    </div>
    <div class="row data-row">
        <div class="large-2 column">
            <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('examination_date')) ?>:</div>
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
        <div class="large-4 column">
            <div class="data-value"><?php echo CHtml::encode($element->displayconsideredblind); ?></div>
        </div>
        <div class="large-2 column">
            <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('sight_varies_by_light_levels')) ?>:</div>
        </div>
        <div class="large-4 column end">
            <div class="data-value"><?php echo CHtml::encode($element->displaylightlevels); ?></div>
        </div>
    </div>

    <div class="indent-correct element-data element-eyes row">
        <div class="element-eye right-eye column">
            <table>
                <tbody>
                <tr>
                    <td>
                        <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('unaided_right_va'))?>:</div>
                    </td>
                    <td>
                        <div class="data-value"><?php echo CHtml::encode($element->unaided_right_va)?></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('best_corrected_right_va'))?>:</div>
                    </td>
                    <td>
                        <div class="data-value"><?php echo CHtml::encode($element->best_corrected_right_va)?></div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="element-eye left-eye column">
            <table>
                <tbody>
                <tr>
                    <td>
                    <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('unaided_left_va'))?>:</div>
                    </td>
                    <td>
                    <div class="data-value"><?php echo CHtml::encode($element->unaided_left_va)?></div>
                    </td>
                </tr>
                <tr>
                    <td>
                    <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('best_corrected_left_va'))?>:</div>
                    </td>
                    <td>
                    <div class="data-value"><?php echo CHtml::encode($element->best_corrected_left_va)?></div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row data-row">
        <div class="large-3 large-push-2 column">
            <div
                class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('best_corrected_binocular_va')) ?>:</div>
        </div>
        <div class="large-3 large-push-2 column end">
            <div class="data-value"><?php echo CHtml::encode($element->best_corrected_binocular_va) ?></div>
        </div>
    </div>
    <div class="row data-row">
        <div class="large-2 column">
            <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('low_vision_status_id')) ?>:</div>
        </div>
        <div class="large-4 column">
            <div class="data-value"><?php echo $element->low_vision_status ? CHtml::encode($element->low_vision_status->name) : 'None' ?></div>
        </div>
        <div class="large-2 column">
            <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('field_of_vision_id')) ?>:</div>
        </div>
        <div class="large-4 column end">
            <div
                class="data-value"><?php echo $element->field_of_vision ? CHtml::encode($element->field_of_vision->name) : 'None' ?></div>
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
        <div class="large-2 column">
            <div
                class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('diagnoses_not_covered')) ?>:</div>
        </div>
        <div class="large-10 column end">
            <div class="data-value"><?php echo CHtml::encode($element->diagnoses_not_covered) ?></div>
        </div>
    </div>

</div>
