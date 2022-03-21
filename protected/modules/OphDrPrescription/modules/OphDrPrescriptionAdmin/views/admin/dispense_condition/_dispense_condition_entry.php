<tr class="clickable"
    data-id="<?php echo $data_id ?>"
    data-uri="<?php echo $data_uri ?>"
>
    <td>
        <input type="checkbox" name="select[]" value="<?= $data_id ?>"
               id="select[<?= $data_id ?>]"/>
    </td>
    <td class="reorder">
        <span>↑↓</span>
        <?=CHtml::hiddenField(CHtml::modelName($model) . "[display_order][]", $data_id) ?>
        <?=CHtml::hiddenField(CHtml::modelName($model) . "[id][]", $data_id) ?>
    </td>
    <td><?= $name ?></td>
    <td><?= $display_order ?></td>
    <td>
        <?php if ($is_active) {?>
            <i class="oe-i tick small"></i>
        <?php } else {?>
            <i class="oe-i remove small"></i>
            <i class="oe-i info small pad js-has-tooltip" data-tooltip-content="Please add this to current institution before associating any dispense locations"></i>
        <?php } ?>
    </td>
</tr>