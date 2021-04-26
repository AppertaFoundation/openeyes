<?php

use OEModule\OphCiExamination\models\PrismReflex_Entry;

/**
 * @var PrismReflex_Entry $entry
 * @var \OEModule\OphCiExamination\widgets\PrismReflex $this
 * @var string $field_prefix
 */
?>
<tr data-key="<?= $row_count ?>">
    <td>
        <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?= $entry->id ?>"/>
        <?= \CHtml::dropDownList("{$field_prefix}[prismdioptre_id]", $entry->prismdioptre_id,
                \CHtml::listData($entry->prismdioptre_options, 'id', 'name'), [
                    'empty' => '- Select -',
                    'nowrapper' => true,
                    'id' => "{$field_prefix}_prismdioptre_id_{$row_count}",
                    'data-adder-header' => $entry->getAttributeLabel('prismdioptre_id'),
                    'data-adder-id' => "{$field_prefix}_prismdioptre_id",
                ]); ?>
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
        <?= \CHtml::dropDownList("{$field_prefix}[prismbase_id]", $entry->prismbase_id,
                \CHtml::listData($entry->prismbase_options, 'id', 'name'), [
                    'empty' => '- Select -',
                    'nowrapper' => true,
                    'id' => "{$field_prefix}_prismbase_id_{$row_count}",
                    'data-adder-header' => $entry->getAttributeLabel('prismbase_id'),
                    'data-adder-id' => "{$field_prefix}_prismbase_id",
                ]);
        ?>
    </td>
    <td>
        <?= \CHtml::dropDownList("{$field_prefix}[finding_id]", $entry->finding_id,
            \CHtml::listData($entry->finding_options, 'id', 'name'), [
                'empty' => '- Select -',
                'nowrapper' => true,
                'id' => "{$field_prefix}_finding_id_{$row_count}",
                'data-adder-header' => $entry->getAttributeLabel('finding_id'),
                'data-adder-id' => "{$field_prefix}_finding_id",
            ]);
        ?>
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