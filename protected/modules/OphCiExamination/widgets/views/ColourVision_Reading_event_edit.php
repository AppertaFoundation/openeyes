<?php
/**
 * @var string $field_prefix
 * @var \OEModule\OphCiExamination\models\OphCiExamination_ColourVision_Reading $reading
 * @var \OEModule\OphCiExamination\widgets\ColourVision $this
 */
?>
<tr class="colourvisionReading" data-key="<?php echo $row_count ?>">
    <td>
        <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?= $reading->id ?>" />
        <select name="<?= $field_prefix ?>[method_id]"
                id="<?= "{$field_prefix}_method_id" ?>"
                required="required"
                data-adder-header="<?= $reading->getAttributeLabel('method') ?>"
                data-adder-id="<?= "method_id" ?>"
        >
            <option value="">- Please Select -</option>
            <?php foreach ($this->getMethods() as $method) { ?>
                <option value="<?= $method->id ?>"
                    <?= $reading->method && $reading->method->id === $method->id ? "selected" : "" ?>
                    data-filter-value="<?= $method->id ?>"
                ><?= $method ?></option>
            <?php } ?>
        </select>
    </td>
    <td>
        <select name="<?= $field_prefix ?>[value_id]"
                required="required"
                data-adder-header="<?= $reading->getAttributeLabel('value') ?>"
                data-adder-id="<?= "value_id" ?>"
                data-adder-requires-item-set="method_id"
        >
                <option value="">- Please Select -</option>
                <?php foreach ($this->getReadingValues() as $v) { ?>
                    <option value="<?php echo $v->id?>"
                        <?= ($v->id == $reading->value_id) ? "selected" : "" ?>
                        data-filter-value="<?=$v->method_id ?>"
                    ><?php echo $v->name?></option>
                <?php } ?>
        </select>
    </td>
    <td>
        <?= \CHtml::dropDownList("{$field_prefix}[correctiontype_id]", $reading->correctiontype_id,
            \CHtml::listData($reading->correctiontype_options, 'id', 'name'), [
                'empty' => '- Select -',
                'nowrapper' => true,
                'data-adder-header' => $reading->getAttributeLabel('correctiontype_id'),
                'id' => "{$field_prefix}_correctiontype_id_{$row_count}"
            ]); ?>
    </td>
    <td>
        <i class="oe-i trash"></i>
    </td>
</tr>