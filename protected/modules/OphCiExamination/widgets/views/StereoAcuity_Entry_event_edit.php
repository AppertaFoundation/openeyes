<?php

use OEModule\OphCiExamination\models\StereoAcuity_Entry;

/**
 * @var StereoAcuity_Entry $entry
 * @var \OEModule\OphCiExamination\widgets\StereoAcuity $this
 * @var string $field_prefix
 */
?>
<tr data-key="<?= $row_count ?>">
    <td>
        <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?= $entry->id ?>"/>
        <?= \CHtml::dropDownList("{$field_prefix}[method_id]", $entry->method_id,
                \CHtml::listData($entry->method_options, 'id', 'name'), [
                    'empty' => '- Select -',
                    'nowrapper' => true,
                    'id' => "{$field_prefix}_method_id_{$row_count}",
                    'data-adder-header' => $entry->getAttributeLabel('method_id'),
                    'data-adder-id' => "{$field_prefix}_method_id"
                ]); ?>
    </td>
    <td>
        <?= \CHtml::dropDownList("{$field_prefix}[inconclusive]", $entry->inconclusive,
                \CHtml::listData($entry->inconclusive_options, 'id', 'name'), [
                    'empty' => '- Select -',
                    'nowrapper' => true,
                    'id' => "{$field_prefix}_inconclusive_{$row_count}",
                    'data-adder-header' => $entry->getAttributeLabel('inconclusive'),
                    'data-adder-id' => "{$field_prefix}_inconclusive",
                    'data-adder-requires-item-set' => "{$field_prefix}_method_id",
                ]);
?>
        <?= \CHtml::textField("{$field_prefix}[result]", $entry->result, [
            'placeholder' => 'Result',
            'id' => "{$field_prefix}_result_{$row_count}",
            'data-adder-id' => "{$field_prefix}_result",
            'data-adder-header' => 'Value',
            'data-adder-show-info' => 'Enter on form',
            'data-adder-requires-item-set' => "{$field_prefix}_inconclusive",
            'data-adder-requires-item-set-values' => json_encode([StereoAcuity_Entry::NOT_INCONCLUSIVE]),
        ]) ?>
    </td>
    <td>
        <?= \CHtml::dropDownList("{$field_prefix}[correctiontype_id]", $entry->correctiontype_id,
                \CHtml::listData($entry->correctiontype_options, 'id', 'name'), [
                    'empty' => '- Select -',
                    'nowrapper' => true,
                    'data-adder-header' => $entry->getAttributeLabel('correctiontype_id'),
                    'id' => "{$field_prefix}_correctiontype_id_{$row_count}"
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