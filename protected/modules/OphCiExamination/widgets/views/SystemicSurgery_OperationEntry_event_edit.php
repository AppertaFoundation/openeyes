<?php
/**
 * OpenEyes.
 *
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

use OEModule\OphCiExamination\models\SystemicSurgery_Operation;

?>

<?php
if (!isset($values)) {
    $values = [
        'id' => $operation->id,
        'operation' => $operation->operation,
        'side_id' => $operation->side_id,
        'side_display' => $operation->side ? $operation->side->adjective : 'None',
        'date' => $operation->date,
        'date_display' => $operation->getDisplayDate(),
        'had_operation' => $operation->had_operation
    ];
}
$required = isset($required) ? $required : false;
?>

<tr class="row-<?= $row_count; ?><?php if ($removable) {
    echo " read-only";
               } ?>"
    <?= $removable ? "data-key='{$row_count}'" : ''; ?>
    id="<?= $model_name ?>_operations_<?= $row_count ?>">
    <td>
        <?php if (!$removable || $required) { ?>
            <?= $values['operation'] ?>
            <?= \CHtml::hiddenField($field_prefix . "[id]", $values['operation']); ?>
            <?= \CHtml::hiddenField($field_prefix . '[operation]', $values['operation']); ?>
        <?php } else { ?>
            <?= $values['operation'] ?>
            <?php $type = $values['operation'] !== '' ? 'hidden' : 'text' ?>
            <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?= $values['id'] ?>"/>
            <input class="common-operation" type="<?= $type ?>" name="<?= $field_prefix ?>[operation]"
                   value="<?= $values['operation'] ?>"
                   placeholder="Enter procedure name" autocomplete="off"/>
        <?php } ?>
    </td>
    <td class="systemic-past-surgery-entry has-operation">
        <?php if (!$required) {
            if ($values['had_operation'] === (string)SystemicSurgery_Operation::$NOT_PRESENT) { ?>
                <label class="inline highlight">
                    <?= \CHtml::radioButton(
                        $field_prefix . '[had_operation]',
                        $values['had_operation'] === (string)SystemicSurgery_Operation::$PRESENT,
                        ['value' => SystemicSurgery_Operation::$PRESENT]
                    ); ?>
                    yes
                </label>
                <label class="inline highlight">
                    <?= \CHtml::radioButton(
                        $field_prefix . '[had_operation]',
                        $values['had_operation'] === (string)SystemicSurgery_Operation::$NOT_PRESENT,
                        ['value' => SystemicSurgery_Operation::$NOT_PRESENT]
                    ); ?>
                    no
                </label>
            <?php } else {
                echo CHtml::hiddenField($field_prefix . '[had_operation]', (string)SystemicSurgery_Operation::$PRESENT);
            }
        } else { ?>
            <label class="inline highlight">
                <?= \CHtml::radioButton(
                    $field_prefix . '[had_operation]',
                    $posted_not_checked,
                    ['value' => SystemicSurgery_Operation::$NOT_CHECKED]
                ); ?>
                Not checked
            </label>
            <label class="inline highlight">
                <?= \CHtml::radioButton(
                    $field_prefix . '[had_operation]',
                    $values['had_operation'] === (string)SystemicSurgery_Operation::$PRESENT,
                    ['value' => SystemicSurgery_Operation::$PRESENT]
                ); ?>
                yes
            </label>
            <label class="inline highlight">
                <?= \CHtml::radioButton(
                    $field_prefix . '[had_operation]',
                    $values['had_operation'] === (string)SystemicSurgery_Operation::$NOT_PRESENT,
                    ['value' => SystemicSurgery_Operation::$NOT_PRESENT]
                ); ?>
                no
            </label>
        <?php } ?>
    </td>
    <?php if (!$removable) { ?>
        <td class="<?= $model_name ?>_sides" style="white-space:nowrap">
            <?php if ($values['side'] == 'Right' || $values['side'] == 'Both') { ?>
                <i class="oe-i laterality R small pad"></i>
            <?php } ?>
        </td>
        <td class="<?= $model_name ?>_sides" style="white-space:nowrap">
            <?php if ($values['side'] == 'Left' || $values['side'] == 'Both') { ?>
                <i class="oe-i laterality L small pad"></i>
            <?php } ?>
        </td>
        <td></td>
        <td></td>
    <?php } else { ?>
        <?php $this->widget('application.widgets.EyeSelector', [
            'inputNamePrefix' => $field_prefix,
            'selectedEyeId' => $values['side_id'] ? $values['side_id'] : EyeSelector::$NOT_CHECKED
        ]); ?>
    <?php } ?>


    <?php
    if (!$removable) { ?>
        <td>
            <?= Helper::formatFuzzyDate($values['date']) ?>
        </td>
    <?php } else { ?>
        <?php /* I have seen a css class instead of this (???) style="width:90px" */ ?>
        <td>
            <input id="systemic-past-surgery-datepicker-<?= $row_count ?>" style="width:90px"
                   class="date"
                   placeholder="yyyy-mm-dd"
                   name="<?= $field_prefix ?>[date]" value="<?= $values['date'] ?>" autocomplete="off">
        </td>
        <td>
            <i class="js-has-tooltip oe-i info small pad right"
               data-tooltip-content="You can enter date format as yyyy-mm-dd, or yyyy-mm or yyyy."></i>
        </td>
    <?php } ?>

    <?php if ($removable && !$required) { ?>
        <td>
            <i class="oe-i trash remove_item"></i>
        </td>
    <?php } elseif (!$required) { ?>
        <td>read only
            <i class="js-has-tooltip oe-i info small pad right"
               data-tooltip-content="This operation is recorded as an Operation Note event in OpenEyes and cannot be edited here"></i>
        </td>
    <?php } elseif ($required) { ?>
        <td>mandatory
            <i class="js-has-tooltip oe-i info small pad right"
               data-tooltip-content="<?= $values['operation']; ?> is mandatory to collect."></i>
        </td>
    <?php } ?>
</tr>