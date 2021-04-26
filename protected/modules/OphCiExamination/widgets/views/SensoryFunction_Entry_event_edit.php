<?php

use OEModule\OphCiExamination\models\SensoryFunction_Entry;

/**
 * @var SensoryFunction_Entry $entry
 * @var \OEModule\OphCiExamination\widgets\SensoryFunction $this
 * @var string $field_prefix
 */
?>
<tr data-key="<?= $row_count ?>">
    <td>
        <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?= $entry->id ?>"/>
        <?= \CHtml::dropDownList("{$field_prefix}[entry_type_id]", $entry->entry_type_id,
            \CHtml::listData($entry->entry_type_options, 'id', 'name'), [
                'empty' => '- Select -',
                'nowrapper' => true,
                'id' => "{$field_prefix}_entry_type_id_{$row_count}",
                'required' => 'required',
                'data-adder-header' => $entry->getAttributeLabel('entry_type_id'),
                'data-adder-id' => "{$field_prefix}_entry_type_id"
            ]); ?>
    </td>
    <td>
        <?= \CHtml::dropDownList("{$field_prefix}[distance_id]", $entry->distance_id,
            \CHtml::listData($entry->distance_options, 'id', 'name'), [
                'empty' => '- Select -',
                'nowrapper' => true,
                'id' => "{$field_prefix}_distance_id_{$row_count}",
                'required' => 'required',
                'data-adder-header' => $entry->getAttributeLabel('distance_id'),
                'data-adder-id' => "{$field_prefix}_distance_id"
            ]); ?>
    </td>
    <td>
        <?= \CHtml::dropDownList("{$field_prefix}[correctiontypes]", $entry->correctiontypes,
            \CHtml::listData($entry->correctiontypes_options, 'id', 'name'), [
                'empty' => '- Select -',
                'nowrapper' => true,
                'id' => "{$field_prefix}_correctiontypes_{$row_count}",
                'data-adder-header' => $entry->getAttributeLabel('correctiontypes'),
                'data-adder-id' => "{$field_prefix}_correctiontypes",
                'multiple' => true
            ]); ?>
    </td>
    <td>
        <?= \CHtml::dropDownList("{$field_prefix}[result_id]", $entry->result_id,
            \CHtml::listData($entry->result_options, 'id', 'name'), [
                'empty' => '- Select -',
                'nowrapper' => true,
                'id' => "{$field_prefix}_result_id_{$row_count}",
                'required' => 'required',
                'data-adder-header' => $entry->getAttributeLabel('result_id'),
                'data-adder-id' => "{$field_prefix}_result_id"
            ]); ?>
    </td>
    <td>
        <?= \CHtml::dropDownList("{$field_prefix}[with_head_posture]", $entry->with_head_posture,
            \CHtml::listData($entry->with_head_posture_options, 'id', 'name'), [
                'empty' => '- Select -',
                'nowrapper' => true,
                'data-adder-header' => $entry->getAttributeLabel('with_head_posture'),
                'id' => "{$field_prefix}_with_head_posture_{$row_count}"
            ]); ?>
    </td>
    <td>
        <i class="oe-i trash"></i>
    </td>
</tr>