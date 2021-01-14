<?php
/**
 * (C) Apperta Foundation, 2020
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

/**
 * @var \OEModule\OphCiExamination\models\OphCiExamination_Refraction_Reading $reading
 * @var \OEModule\OphCiExamination\widgets\Refraction $this
 * @var bool $force_type // to ensure that we set type id to __other__ and display other type, even if other type has no value (validation error)
 */
?>

<tr data-key="<?= $row_count ?>">
    <td>
        <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?= $reading->id ?>"/>
        <?= \CHtml::textField("{$field_prefix}[sphere]", $reading->sphere, [
            'id' => "{$field_prefix}_sphere_{$row_count}",
            'data-adder-id' => "{$field_prefix}_sphere",
            'data-adder-header' => $reading->getAttributeLabel('sphere'),
            'data-adder-item-set-type' => "float",
            'data-adder-item-set-max' => "30",
            'data-adder-item-set-support-sign' => "true",
            'data-adder-item-set-support-decimal-values' => "true",
            'data-ec-keep-field' => true,
            'data-ec-format-fixed' => "2",
            'data-ec-format-force-sign' => "true",
            'class' => 'fixed-width-small js-sphere'
        ]) ?>
    </td>
    <td>
        <?= \CHtml::textField("{$field_prefix}[cylinder]", $reading->cylinder, [
            'id' => "{$field_prefix}_cylinder_{$row_count}",
            'data-adder-id' => "{$field_prefix}_cylinder",
            'data-adder-header' => $reading->getAttributeLabel('cylinder'),
            'data-adder-item-set-type' => "float",
            'data-adder-item-set-max' => "25",
            'data-adder-item-set-support-sign' => "true",
            'data-adder-item-set-support-decimal-values' => "true",
            'data-ec-keep-field' => true,
            'data-ec-format-fixed' => "2",
            'data-ec-format-force-sign' => "true",
            'class' => 'fixed-width-small js-cylinder'
        ]) ?>
    </td>
    <td>
        <?= \CHtml::textField("{$field_prefix}[axis]", $reading->axis, [
            'id' => "{$field_prefix}_axis_{$row_count}",
            'data-adder-id' => "{$field_prefix}_axis",
            'data-adder-header' => $reading->getAttributeLabel('axis'),
            'data-adder-item-set-type' => "float",
            'data-adder-item-set-max' => "180",
            'data-ec-keep-field' => true,
            'class' => 'fixed-width-small js-axis'
        ]) ?>
    </td>
    <td>
        <?= \CHtml::dropDownList(
            "{$field_prefix}[type_id]",
            $reading->type_id ?? ($force_type ? "__other__" : null),
            $this->getReadingRefractionTypeOptions($reading),
            [
                'nowrapper' => true,
                'data-adder-header' => $reading->getAttributeLabel('type_id'),
                'id' => "{$field_prefix}_type_id_{$row_count}",
                'style' => $reading->type_other ? "display: none;" : "",
                'data-adder-id' => "{$field_prefix}_type_id",
            ]
        ) ?>
        <?= \CHtml::textField(
            "{$field_prefix}[type_other]",
            $reading->type_other,
            [
                'id' => "{$field_prefix}_type_other_{$row_count}",
                'data-ec-keep-field' => true,
                'style' => $reading->type_other ? "" : "display: none;"
            ]
        ) ?>
    </td>
    <td>
        <i class="oe-i trash"></i>
    </td>
</tr>

