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
 * @var \OEModule\OphCiExamination\models\PrismFusionRange_Entry $entry
 * @var \OEModule\OphCiExamination\widgets\PrismFusionRange $this
 * @var string $field_prefix
 */
?>

<tr data-key="<?= $row_count ?>" class="<?=\CHtml::modelName($entry)?>">
    <td>
        <?= \CHtml::dropDownList(
            "{$field_prefix}[prism_over_eye_id]",
            $entry->prism_over_eye_id,
            $this->getPrismOverEyeOptions(),
            [
                'empty' => '- Select -',
                'nowrapper' => true,
                'id' => "{$field_prefix}_prism_over_eye_id",
                'data-adder-header' => $this->getReadingAttributeLabel('prism_over_eye_id'),
                'data-adder-id' => "{$field_prefix}_prism_over_eye_id"
            ]
        ); ?>
    </td>
    <td>
        <input type="text" class="fixed-width-small" placeholder="BO"
               name="<?= "{$field_prefix}[near_bo]" ?>"
               value="<?= CHtml::encode($entry->near_bo) ?>"
               data-ec-keep-field="true"
               data-adder-ignore="true"
               id = '<?="{$field_prefix}_near_bo"?>'
        >
    </td>
    <td>
        <input type="text" class="fixed-width-small" placeholder="BI"
               name="<?= "{$field_prefix}[near_bi]" ?>"
               value="<?= CHtml::encode($entry->near_bi) ?>"
               data-ec-keep-field="true"
               data-adder-ignore="true"
               id = '<?="{$field_prefix}_near_bi"?>'
        >
    </td>
    <td>
        <input type="text" class="fixed-width-small" placeholder="BU"
               name="<?= "{$field_prefix}[near_bu]" ?>"
               value="<?= CHtml::encode($entry->near_bu) ?>"
               data-ec-keep-field="true"
               data-adder-ignore="true"
               id = '<?="{$field_prefix}_near_bu"?>'
        >
    </td>
    <td>
        <input type="text" class="fixed-width-small" placeholder="BD"
               name="<?= "{$field_prefix}[near_bd]" ?>"
               value="<?= CHtml::encode($entry->near_bd) ?>"
               data-ec-keep-field="true"
               data-adder-ignore="true"
               id = '<?="{$field_prefix}_near_bd"?>'
        >
    </td>
    <td>
        <input type="text" class="fixed-width-small" placeholder="BO"
               name="<?= "{$field_prefix}[distance_bo]" ?>"
               value="<?= CHtml::encode($entry->distance_bo) ?>"
               data-ec-keep-field="true"
               data-adder-ignore="true"
               id = '<?="{$field_prefix}_distance_bo"?>'
        >
    </td>
    <td>
        <input type="text" class="fixed-width-small" placeholder="BI"
               name="<?= "{$field_prefix}[distance_bi]" ?>"
               value="<?= CHtml::encode($entry->distance_bi) ?>"
               data-ec-keep-field="true"
               data-adder-ignore="true"
               id = '<?="{$field_prefix}_distance_bi"?>'
        >
    </td>
    <td>
        <input type="text" class="fixed-width-small" placeholder="BU"
               name="<?= "{$field_prefix}[distance_bu]" ?>"
               value="<?= CHtml::encode($entry->distance_bu) ?>"
               data-ec-keep-field="true"
               data-adder-ignore="true"
               id = '<?="{$field_prefix}_distance_bu"?>'
        >
    </td>
    <td>
        <input type="text" class="fixed-width-small" placeholder="BD"
               name="<?= "{$field_prefix}[distance_bd]" ?>"
               value="<?= CHtml::encode($entry->distance_bd) ?>"
               data-ec-keep-field="true"
               data-adder-ignore="true"
               id = '<?="{$field_prefix}_distance_bd"?>'
        >
    </td>
    <td><?= \CHtml::dropDownList("{$field_prefix}[correctiontype_id]", $entry->correctiontype_id,
            \CHtml::listData($entry->correctiontype_options, 'id', 'name'), [
                'empty' => '- Select -',
                'nowrapper' => true,
                'data-adder-header' => $entry->getAttributeLabel('correctiontype_id'),
                'id' => "{$field_prefix}_correctiontype_id"
            ]); ?></td>
    <td><?= \CHtml::dropDownList("{$field_prefix}[with_head_posture]", $entry->with_head_posture,
            \CHtml::listData($entry->with_head_posture_options, 'id', 'name'), [
                'empty' => '- Select -',
                'nowrapper' => true,
                'data-adder-header' => $entry->getAttributeLabel('with_head_posture'),
                'id' => "{$field_prefix}_with_head_posture"
            ]); ?></td>
    <td><i class="oe-i trash"></i></td>
</tr>
