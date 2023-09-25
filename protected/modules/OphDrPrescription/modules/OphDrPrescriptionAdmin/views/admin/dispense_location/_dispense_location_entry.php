<tr class="clickable"
    data-id="<?php echo $data_id ?>"
    data-uri="<?php echo $data_uri ?>"
    data-can-remove-institution-assignment=<?= json_encode(empty($condition_assignments)) ?>
    data-test="dispense-location-row-<?= $name ?>"
>
    <td>
        <input type="checkbox" name="select[]" value="<?= $data_id ?>"
               id="select[<?= $data_id ?>]" data-test="dispense-location-checkbox"/>
    </td>
    <td class="reorder">
        <span>↑↓</span>
        <?= CHtml::hiddenField(CHtml::modelName($model) . "[display_order][]", $data_id) ?>
        <?= CHtml::hiddenField(CHtml::modelName($model) . "[id][]", $data_id) ?>
    </td>
    <td data-test="dispense-location-name"><?= $name ?></td>
    <td><?= $display_order ?></td>
    <td>
        <i class="oe-i <?= ($is_active ? 'tick' : 'remove') ?> small"></i>
        <?php if (!empty($condition_assignments)) { ?>
        <i class="oe-i info small js-has-tooltip"
            data-tooltip-content="This Dispense Location is assigned to the following Dispense Conditions: <?= implode(", ", array_column($condition_assignments, "name")) ?>. Please remove the assignment before removing it from the current institution."
            data-test="dispense-location-assigned-tooltip"></i>
        <?php } ?>
    </td>
</tr>
