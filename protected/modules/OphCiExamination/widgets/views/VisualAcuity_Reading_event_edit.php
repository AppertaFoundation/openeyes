<?php

use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading;

/**
 * @var OphCiExamination_VisualAcuity_Reading $reading
 * @var \OEModule\OphCiExamination\widgets\VisualAcuity $this
 * @var string $field_prefix
 */
?>
<tr data-key="<?= $row_count ?>">
    <td>
        <?= \CHtml::dropDownList("{$field_prefix}[method_id]", $reading->method_id,
            \CHtml::listData($reading->methodOptions(), 'id', 'name'), [
                'empty' => '- Select -',
                'nowrapper' => true,
                'data-adder-header' => $reading->getAttributeLabel('method_id'),
                'id' => "{$field_prefix}_method_id_{$row_count}",
                'data-adder-id' => "method_id",
                'required' => 'required'
            ]); ?>
    </td>
    <td>
        <input type="text"
               name="<?="{$field_prefix}[unit_id]" ?>"
               value="<?= CHtml::encode($reading->unit_id) ?>"
               id="<?= "{$field_prefix}_unit_id_{$row_count}" ?>"
               data-adder-header="<?= $reading->getAttributeLabel('unit_id') ?>"
               data-adder-id="unit_id"
               data-acuity-field="unit"
               required="required"
        />
    </td>
    <td>
        <input type="text"
               name="<?="{$field_prefix}[value]" ?>"
               value="<?= CHtml::encode($reading->value) ?>"
               id="<?= "{$field_prefix}_value_{$row_count}" ?>"
               <?php if ($this->shouldTrackCviAlert()) { ?>class="va-selector"<?php } ?>
               data-adder-header="<?= $reading->getAttributeLabel('value') ?>"
               data-adder-id="value"
               data-adder-requires-item-set="unit_id"
               data-acuity-field="value"
               required="required"
        />
    </td>
    <td>
        <?= \CHtml::dropDownList("{$field_prefix}[source_id]", $reading->source_id,
            \CHtml::listData($reading->sourceOptions(), 'id', 'name'), [
                'empty' => '- Select -',
                'nowrapper' => true,
                'data-adder-header' => $reading->getAttributeLabel('source_id'),
                'id' => "{$field_prefix}_source_id_{$row_count}"
            ]); ?>
    </td>
    <?php if ($this->readingsHaveFixation()) { ?>
        <td>
            <?= \CHtml::dropDownList("{$field_prefix}[fixation_id]", $reading->fixation_id,
                \CHtml::listData($reading->fixationOptions(), 'id', 'name'), [
                    'empty' => '- Select -',
                    'nowrapper' => true,
                    'data-adder-header' => $reading->getAttributeLabel('fixation_id'),
                    'id' => "{$field_prefix}_fixation_id_{$row_count}"
                ]); ?>
        </td>
    <?php } ?>
    <td>
        <?= \CHtml::dropDownList("{$field_prefix}[occluder_id]", $reading->occluder_id,
            \CHtml::listData($reading->occluderOptions(), 'id', 'name'), [
                'empty' => '- Select -',
                'nowrapper' => true,
                'data-adder-header' => $reading->getAttributeLabel('occluder_id'),
                'id' => "{$field_prefix}_occluder_id_{$row_count}"
            ]); ?>
    </td>
    <td>
        <?= \CHtml::dropDownList("{$field_prefix}[with_head_posture]", $reading->with_head_posture,
            \CHtml::listData($reading->with_head_posture_options, 'id', 'name'), [
                'empty' => '- Select -',
                'nowrapper' => true,
                'data-adder-header' => $reading->getAttributeLabel('with_head_posture'),
                'id' => "{$field_prefix}_with_head_posture_{$row_count}"
            ]); ?>
    </td>
    <td>
        <i class="oe-i trash"></i>
    </td>
</tr>