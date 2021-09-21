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
        <?= CHtml::hiddenField(CHtml::modelName($model) . "[display_order][]", $data_id) ?>
        <?= CHtml::hiddenField(CHtml::modelName($model) . "[id][]", $data_id) ?>
    </td>
    <td><?= $name ?></td>
    <td><?= $display_order ?></td>
    <td><i class="oe-i <?= ($is_active ? 'tick' : 'remove') ?> small"></i></td>
</tr>