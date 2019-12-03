<tr class="clickable"
    data-id="<?php echo $data_id ?>"
    data-uri="<?php echo $data_uri ?>"
>
    <td class="reorder">
        <span>↑↓</span>
        <?=\CHtml::activeHiddenField($model, "[$row_count]display_order");?>
        <?=\CHtml::activeHiddenField($model, "[$row_count]id");?>
    <td><?php echo $name?></td>
    <td><?php echo $display_order?></td>
    <td><i class="oe-i <?=($is_active ? 'tick' : 'remove');?> small"></i></td>
</tr>