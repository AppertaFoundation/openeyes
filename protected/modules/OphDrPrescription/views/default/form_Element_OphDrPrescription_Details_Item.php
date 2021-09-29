<?php
/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/** @var Patient $patient */
/** @var OphDrPrescription_Item $item */

$fpten_setting = SettingMetadata::model()->getSetting('prescription_form_format');
$overprint_setting = SettingMetadata::model()->getSetting('enable_prescription_overprint');
$fpten_dispense_condition = OphDrPrescription_DispenseCondition::model()->findByAttributes(array('name' => 'Print to {form_type}'));

$dispense_conditions = OphDrPrescription_DispenseCondition::model()->withSettings($overprint_setting, $fpten_dispense_condition->id)->findAllAtLevel(ReferenceData::LEVEL_INSTITUTION);

$dispense_condition_options = array(
    $fpten_dispense_condition->id => array('label' => "Print to $fpten_setting")
);
?>
<tr data-key="<?= $key ?>" class="prescription-item prescriptionItem <?= $item->getErrors() ? 'errors' : '' ?> ">
    <td>
        <button class="js-add-taper">
            <i class="oe-i child-arrow small"></i>
        </button>
    </td>
    <td class="drug-details">
        <input type="hidden" name="Element_OphDrPrescription_Details[items][<?= $key ?>][usage_type]"
               value="<?= OphDrPrescription_Item::getUsageType() ?>"/>
        <input type="hidden" name="Element_OphDrPrescription_Details[items][<?= $key ?>][usage_subtype]"
               value="<?= OphDrPrescription_Item::getUsageSubType() ?>"/>
        <?php if (isset($patient) && $patient->hasDrugAllergy($item->medication_id)) : ?>
            <i class="oe-i warning small pad js-has-tooltip"
               data-tooltip-content="Allergic to <?= implode(',', $patient->getPatientDrugAllergy($item->medication_id)) ?>"></i>
        <?php endif; ?>
        <span class='js-medication-display'><?= $item->getMedicationDisplay(true) ?></span>
        <?php $this->widget('MedicationInfoBox', array('medication_id' => $item->medication_id)); ?>
        <?=$item->renderPGDInfo($key);?>
        <?php if ($item->id) { ?>
            <input type="hidden" name="Element_OphDrPrescription_Details[items][<?= $key ?>][id]"
                   value="<?= $item->id ?>" /><?php
        } ?>
        <input type="hidden" name="Element_OphDrPrescription_Details[items][<?= $key ?>][medication_id]"
               value="<?= $item->medication_id ?>"/>
        <?php if ($item->comments) { ?>
            <i class="oe-i comments-added active medium-icon pad js-add-comments js-has-tooltip" style=""
               data-tooltip-content="<?= \CHtml::encode($item->comments) ?>"></i>
        <?php } else { ?>
            <i class="oe-i comments medium-icon pad js-add-comments" style=""></i>
        <?php } ?>
        <div id="comments-<?= $key ?>" class="cols-full prescription-comments" style="display:none"
             data-key="<?= $key ?>">
            <!-- comment-group, textarea + icon -->
            <div class="comment-group flex-layout flex-left" style="padding-top:5px">
                <?php
                $htmlOptions = [
                    'placeholder' => 'Comments', 'autocomplete' => 'off',
                    'rows' => '1', 'class' => 'js-input-comments cols-full ',
                    'style' => 'overflow-x: hidden;word-wrap: break-word;'
                ];
                echo CHtml::textArea(
                    'Element_OphDrPrescription_Details[items][' . $key . '][comments]',
                    CHtml::encode($item->comments),
                    $htmlOptions
                ) ?>
                <i class="oe-i remove-circle small-icon pad-left  js-remove-add-comments"></i>
            </div>
        </div>
    </td>
    <td class="prescriptionItemDose">
        <?php
        $css_class = 'cols-4 inline';
        if ($item->dose === null || is_numeric($item->dose) || $item->dose === '') {
            $css_class .= " input-validate numbers-only";
            if ($item->dose_unit_term === 'mg') {
                $css_class .= ' decimal';
            }
        }
        ?>

        <?= \CHtml::textField(
            'Element_OphDrPrescription_Details[items][' . $key . '][dose]',
            $item->dose,
            array('autocomplete' => Yii::app()->params['html_autocomplete'], 'class' => $css_class)
        )
?>
        <?php if ($item->dose_unit_term) { ?>
            <span><?= $item->dose_unit_term ?></span>
            <input type="hidden" name="Element_OphDrPrescription_Details[items][<?= $key ?>][dose_unit_term]"
                   value="<?= $item->dose_unit_term ?>"/>
        <?php } else {
            echo CHtml::dropDownList(
                'Element_OphDrPrescription_Details[items][' . $key . '][dose_unit_term]',
                null,
                \CHtml::listData($unit_options, 'description', 'description'),
                [
                    'empty' => '-Unit-',
                    'class' => 'cols-7',
                ]
            );
        } ?>
    </td>
    <td>
        <?= \CHtml::dropDownList(
            'Element_OphDrPrescription_Details[items][' . $key . '][route_id]',
            $item->route_id,
            CHtml::listData(
                MedicationRoute::model()->activeOrPk([$item->route_id])->findAll([
                    'condition' => 'source_type =:source_type',
                    'params' => [':source_type' => 'DM+D'],
                    'order' => "term ASC"]),
                'id',
                'term'
            ),
            array('empty' => '-- Select --', 'class' => 'drugRoute cols-11')
        ); ?>
    </td>

    <?php if (!strpos(Yii::app()->controller->action->id, 'Admin')) { ?>
        <td class='route_option_cell'>

            <?php if ($item->route && $item->route->has_laterality && $options = $item->route->options /* TODO this is not going to work when we remove route.options */) {
                echo CHtml::dropDownList(
                    'Element_OphDrPrescription_Details[items][' . $key . '][laterality]',
                    $item->laterality,
                    CHtml::listData($options, 'id', 'name'),
                    array('empty' => '-- Select --')
                );
            } else {
                echo '-';
            } ?>
        </td>
    <?php } ?>

    <td class="prescriptionItemFrequencyId">
        <?= \CHtml::dropDownList(
            'Element_OphDrPrescription_Details[items][' . $key . '][frequency_id]',
            $item->frequency_id,
            CHtml::listData(MedicationFrequency::model()->activeOrPk([$item->frequency_id])->findAll(array('order' => 'display_order')), 'id', 'term'),
            array('empty' => '-- Select --', 'class' => 'cols-11')
        ); ?>
    </td>
    <td class="prescriptionItemDurationId">
        <?= \CHtml::dropDownList(
            'Element_OphDrPrescription_Details[items][' . $key . '][duration_id]',
            $item->duration_id,
            CHtml::listData(MedicationDuration::model()->activeOrPk([$item->duration_id])->findAll(array('order' => 'display_order')), 'id', 'name'),
            array('empty' => '-- Select --', 'class' => 'cols-11')
        ) ?>
    </td>
    <td>
        <?= \CHtml::dropDownList(
            'Element_OphDrPrescription_Details[items][' . $key . '][dispense_condition_id]',
            $item->dispense_condition_id,
            CHtml::listData(
                $dispense_conditions,
                'id',
                'name'
            ),
            array('class' => 'dispenseCondition cols-11', 'empty' => 'Select', 'options' => $dispense_condition_options)
        ) ?>

    </td>
    <td>
        <?php
        $locations = $item->dispense_condition ? $item->dispense_condition->getLocationsForCurrentInstitution() : array('');
        $style = $item->dispense_condition ? '' : 'display: none;';
        echo CHtml::dropDownList(
            'Element_OphDrPrescription_Details[items][' . $key . '][dispense_location_id]',
            $item->dispense_location_id,
            CHtml::listData($locations, 'id', 'name'),
            array('class' => 'dispenseLocation cols-11', 'style' => $style)
        );
        ?>
    </td>
    <td>
        <i class="oe-i trash removeItem"></i>
    </td>
</tr>

<?php foreach ($item->tapers as $count => $taper) : ?>
    <tr data-key="<?= $key ?>" data-taper="<?= $count ?>"
        class="prescription-taper <?= ($key % 2) ? 'odd' : 'even'; ?>">
        <td></td>
        <td>
            <i class="oe-i child-arrow small no-click pad"></i>
            <em class="fade">then</em>
            <?php if ($taper->id) { ?>
                <input type="hidden"
                       name="Element_OphDrPrescription_Details[items][<?= $key ?>][taper][<?= $count ?>][id]"
                       value="<?= $taper->id ?>"/>
            <?php } ?>
        </td>
        <td>
            <?php

            $css_class = 'cols-11';
            if ($taper->dose === null || is_numeric($taper->dose) || $item->dose === '') {
                $css_class .= " input-validate numbers-only";
                if ($item->dose_unit_term === 'mg') {
                    $css_class .= ' decimal';
                }
            }

            echo \CHtml::textField(
                'Element_OphDrPrescription_Details[items][' . $key . '][taper][' . $count . '][dose]',
                $taper->dose,
                array('autocomplete' => Yii::app()->params['html_autocomplete'], 'class' => $css_class)
            ) ?>
        </td>
        <td></td>
        <?php if (!strpos(Yii::app()->controller->action->id, 'Admin')) { ?>
            <td></td>
        <?php } ?>
        <td>
            <?= \CHtml::dropDownList(
                'Element_OphDrPrescription_Details[items][' . $key . '][taper][' . $count . '][frequency_id]',
                $taper->frequency_id,
                CHtml::listData(
                    MedicationFrequency::model()->activeOrPk([$taper->frequency_id])->findAll(array()),
                    'id',
                    'term'
                ),
                array('empty' => '-- Select --', 'class' => 'cols-11')
            ); ?>
        </td>
        <td>
            <?= \CHtml::dropDownList(
                'Element_OphDrPrescription_Details[items][' . $key . '][taper][' . $count . '][duration_id]',
                $taper->duration_id,
                CHtml::listData(
                    MedicationDuration::model()->activeOrPk([$taper->duration_id])->findAll(array('order' => 'display_order asc')),
                    'id',
                    'name'
                ),
                array('empty' => '-- Select --', 'class' => 'cols-11')
            ); ?>
        </td>
        <td></td>
        <td></td>
        <td class="prescription-actions">
            <i class="oe-i trash removeTaper"></i>
        </td>
    </tr>
<?php endforeach; ?>
