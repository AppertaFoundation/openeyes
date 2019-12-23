<?php
if (!isset($values)) {
    $values = array(
        'id' => $model_associated_content->id,
        'is_system_hidden' => $model_associated_content->is_system_hidden,
        'is_print_appended' => $model_associated_content->is_print_appended,
        'method_id' => $model_associated_content->init_method_id,
        'short_code' => $model_associated_content->short_code,
        'title' => $model_associated_content->display_title,
    );
}
$is_system_hidden_checked = ($values['is_system_hidden'] == 1 ? 'CHECKED' : '');
$is_print_appended_checked = ($values['is_print_appended'] == 1 ? 'CHECKED' : '');
?>
<tr class="row-<?=$row_count;?>" data-key="<?= $row_count ?>">
    <input type="hidden" name="<?= $prefix_associated ?>[id]" id="<?= $associated_model_name.'_'.$row_count ?>_id" value="<?= $values['id']?>" />
    <td>
        <input type="checkbox" name="<?= $prefix_associated ?>[is_system_hidden]" id="<?= $associated_model_name.'_'.$row_count ?>_is_system_hidden" <?= $is_system_hidden_checked ?> />
    </td>
    <td>
        <input type="checkbox" name="<?= $prefix_associated ?>[is_print_appended]" id="<?= $associated_model_name.'_'.$row_count ?>_is_print_appended" <?= $is_print_appended_checked ?> {{is_print_appended_js}} />
    </td>
    <td><?=
           CHtml::dropDownList('description', $values['method_id'], CHtml::listData(OphcorrespondenceInitMethod::model()->findAll(array('condition' => 'active=1', 'order' => 'id asc')), 'id', 'description'), array('empty' => '- Select -')) ;
    ?>

        <input type="hidden" name="<?= $prefix_init_method ?>[method_id]" id="<?= $init_method_model_name.'_'.$row_count ?>_method_id" value="<?=$values['method_id']?>" />
    </td>
    <input type="hidden" name="<?= $prefix_init_method ?>[short_code]" id="<?= $init_method_model_name.'_'.$row_count ?>_short_code" value="<?= $values['short_code']  ?>"/>
    <td>
        <input type="text" name="<?= $prefix_init_method ?>[title]" id="<?= $init_method_model_name.'_'.$row_count ?>_title" value="<?= $values['title']  ?>"/>
    </td>
    <td class="edit-column">
        <button class="button small warning remove">remove</button>
    </td>
</tr>