<?php

use OEModule\OphCiExamination\models\CoverAndPrismCover_Entry;

/**
 * @var CoverAndPrismCover_Entry $entry
 * @var \OEModule\OphCiExamination\widgets\CoverAndPrismCover $this
 * @var string $field_prefix
 */
?>
<tr data-key="<?= $row_count ?>">
    <td>
        <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?= $entry->id ?>"/>
        <?= \CHtml::dropDownList("{$field_prefix}[distance_id]", $entry->distance_id,
            \CHtml::listData($entry->distance_options, 'id', 'name'), [
                'empty' => '- Select -',
                'nowrapper' => true,
                'id' => "{$field_prefix}_distance_id",
                'data-adder-header' => $entry->getAttributeLabel('distance_id'),
                'data-adder-id' => "{$field_prefix}_distance_id",
                'required' => 'required'
            ]); ?>
    </td>
    <td>
        <?= \CHtml::dropDownList("{$field_prefix}[correctiontype_id]", $entry->correctiontype_id,
            \CHtml::listData($entry->correctiontype_options, 'id', 'name'), [
                'empty' => '- Select -',
                'nowrapper' => true,
                'data-adder-header' => $entry->getAttributeLabel('correctiontype_id'),
                'id' => "{$field_prefix}_correctiontype_id",
                'required' => 'required'
            ]); ?>
    </td>
    <td id="capc-comment-wrapper-<?= $row_count ?>" class="flex-layout">
        <div class="cols-full js-comment-container"
             data-comment-button="#capc-comment-wrapper-<?= $row_count ?> .js-add-comments"
             style="display: <?= $entry->comments ? : "none"; ?>;">
            <!-- comment-group, textarea + icon -->
            <div class="comment-group flex-layout flex-left last-left" style="padding-top: 0;">
                <textarea id="CPC-comment-<?= $row_count ?>"
                          name="<?= $field_prefix ?>[comments]"
                          class="js-comment-field cols-10"
                          placeholder="Enter comments here"
                          autocomplete="off" rows="1"
                          adderIgnore="true"
                          style="overflow: hidden; word-wrap: break-word; height: 24px;"><?= CHtml::encode($entry->comments) ?></textarea>
                <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
            </div>
        </div>
        <button class="button js-add-comments"
                type="button"
                data-comment-container="#capc-comment-wrapper-<?= $row_count ?> .js-comment-container"
                style="<?= $entry->comments ? "display: none" : ""; ?>">
            <i class="oe-i comments small-icon "></i>
        </button>
    </td>
    <td class="nowrap">
        <input name="<?= $field_prefix ?>[horizontal_value]"
               id="<?= $field_prefix ?>_horizontal_value"
               type="text" placeholder="0" class="cols-8"
               value="<?= CHtml::encode($entry->horizontal_value) ?>"
               data-adder-id="<?= $field_prefix ?>_horizontal_value"
               data-adder-header="<?= $entry->getAttributeLabel('horizontal_value') ?>"
               data-adder-item-set-type="float"
               data-adder-item-set-max="90"
        />
        <?= \CHtml::dropDownList("{$field_prefix}[horizontal_prism_id]", $entry->horizontal_prism_id,
            \CHtml::listData($entry->horizontal_prism_options, 'id', 'name'), [
                'empty' => '- Select -',
                'nowrapper' => true,
                'id' => "{$field_prefix}_horizontal_prism_id",
                'data-adder-header' => "{$entry->getAttributeLabel('horizontal_prism_id')}",
                'data-adder-id' => "{$field_prefix}_horizontal_prism_id",
            ]); ?>
    </td>
    <td>
        <input name="<?= $field_prefix ?>[vertical_value]"
               id="<?= $field_prefix ?>_vertical_value"
               type="text" placeholder="0" class="cols-8"
               value="<?= CHtml::encode($entry->vertical_value) ?>"
               data-adder-id="<?= $field_prefix ?>_vertical_value"
               data-adder-header="<?= $entry->getAttributeLabel('vertical_value') ?>"
               data-adder-item-set-type="float"
               data-adder-item-set-max="50"
        />
        <?= \CHtml::dropDownList("{$field_prefix}[vertical_prism_id]", $entry->vertical_prism_id,
            \CHtml::listData($entry->vertical_prism_options, 'id', 'name'), [
                'empty' => '- Select -',
                'nowrapper' => true,
                'id' => "{$field_prefix}_vertical_prism_id",
                'data-adder-header' => "{$entry->getAttributeLabel('vertical_prism_id')}",
                'data-adder-id' => "{$field_prefix}_vertical_prism_id",
            ]); ?>
    </td>
    <td>
        <?= \CHtml::dropDownList("{$field_prefix}[with_head_posture]", $entry->with_head_posture,
            \CHtml::listData($entry->with_head_posture_options, 'id', 'name'), [
                'empty' => '- Select -',
                'nowrapper' => true,
                'data-adder-header' => $entry->getAttributeLabel('with_head_posture'),
                'id' => "{$field_prefix}_with_head_posture",
            ]); ?>
    </td>
    <td class="nowrap"><button class="button hint green js-edit-row-btn">Edit</button></td>
    <td>
        <i class="oe-i trash"></i>
    </td>
</tr>