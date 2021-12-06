<?php

use OEModule\OphCiExamination\models\ContrastSensitivity_Result;

/**
 * @var ContrastSensitivity_Result $result
 * @var \OEModule\OphCiExamination\widgets\ContrastSensitivity $this
 * @var string $field_prefix
 */
?>
<tr data-key="<?= $row_count ?>">
    <td>
        <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?= $entry->id ?>"/>
        <?= \CHtml::dropDownList(
            "{$field_prefix}[contrastsensitivity_type_id]",
            $entry->contrastsensitivity_type_id,
            \CHtml::listData(
                $entry->contrastsensitivity_type_options,
                'id',
                'name'
            ),
                                   [
                                   'empty' => '- Select -',
                                   'nowrapper' => true,
                                   'id' => "{$field_prefix}_contrastsensitivity_type_id_{$row_count}",
                                   'data-adder-header' => $entry->getAttributeLabel('contrastsensitivity_type_id'),
                                   'data-adder-id' => "{$field_prefix}_contrastsensitivity_type_id",
            ]
        ); ?>
    </td>
    <td class="no-wrap">
        <input name="<?= $field_prefix ?>[value]"
               id="<?= $field_prefix ?>_value"
               type="text" placeholder="0" class="cols-8"
               value="<?= CHtml::encode($entry->value) ?>"
               data-adder-id="<?= $field_prefix ?>_value"
               data-adder-header="<?= $entry->getAttributeLabel('value') ?>"
               data-adder-item-set-type="float"
               data-adder-item-set-max="9"
               required="required"
        />
    </td>
    <td>
        <span class="oe-eye-lat-icons">
            <i class="oe-i laterality R small pad" <?= ($entry->eye_id !== "0") ? 'style="display:none;"' : ""; ?>></i>
            <i class="oe-i NA small pad" <?= ($entry->eye_id !== "0" && $entry->eye_id !== "1") ? 'style="display:none;"' : ""; ?>></i>
            <i class="oe-i laterality L small pad" <?= ($entry->eye_id !== "1") ? 'style="display:none;"' : ""; ?>></i>
            <i class="oe-i NO small pad" <?= ($entry->eye_id !== "2") ? 'style="display:none;"' : ""; ?>></i>
            <i class="oe-i beo small pad" <?= ($entry->eye_id !== "2") ? 'style="display:none;"' : ""; ?>></i>
        </span>
        <?= \CHtml::dropDownList(
            "{$field_prefix}[eye_id]",
            $entry->eye_id,
            \CHtml::listData(
                $entry->eye_options,
                'id',
                'name'
            ),
                                          [
                                          'empty' => '- Select -',
                                          'nowrapper' => true,
                                          'id' => "{$field_prefix}_eye_id_{$row_count}",
                                          'data-adder-header' => $entry->getAttributeLabel('eye_id'),
                                          'data-adder-id' => "{$field_prefix}_eye_id",
                                          'data-ec-keep-field' => true,
                                          'style' => "visibility:hidden;"
            ]
        ); ?>
    </td>
    <td>
        <?= \CHtml::dropDownList(
            "{$field_prefix}[correctiontype_id]",
            $entry->correctiontype_id,
            \CHtml::listData(
                $entry->correctiontype_options,
                'id',
                'name'
            ),
            [
                'empty' => '- Select -',
                'nowrapper' => true,
                'data-adder-header' => $entry->getAttributeLabel('correctiontype_id'),
                'id' => "{$field_prefix}_correctiontype_id"
            ]
        ); ?>
    </td>
    <td>
        <i class="oe-i trash"></i>
    </td>
</tr>