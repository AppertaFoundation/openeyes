<?php
    $icon = 'waiting';
    $cbx_val = 0;
    $cbx_attr = null;
    $cbx_icon = 'padlock';
    $administered_timestamp = null;
    $administered_time = null;
    $administered_user_id = null;
    $administered_user_name = null;
    $label_css = 'highlight inline';
    $disabled_css = $med_obj && $med_obj->administered ? 'disabled' : null;
?>
<?php if (!$med_obj ) { ?>
        <td></td>
<?php } else {
        $lat_icon = null;
    if (isset($side)) {
        $lat_icon = "<i class='oe-i eyelat-{$icon_css} small no-click pad-right'></i>";
    }
        $administer_interaction = "<input class='js-administer-cbk' value='$cbx_val' name='Assignment[entries][$index][administered]' type='checkbox'>$med_obj->dose_unit_term";
        $adminiter_unlock_display = "<i class='oe-i $cbx_icon small no-click pad-right'></i>{$lat_icon}<em class='fade'>$med_obj->dose_unit_term</em>";
    if ($med_obj->administered) {
        $icon = 'tick';
        $cbx_val = 1;
        $cbx_attr = 'checked';
        $cbx_icon = 'tick';
        $administered_timestamp = strtotime($med_obj->administered_time) * 1000;
        $administered_time = $med_obj->formatAdministerTime();
        $administered_user_id = $med_obj->administered_by;
        $administered_user_name = $med_obj->administered_user->getFullName();
        $label_css = null;
        $administer_interaction = "$administered_user_name<br><small>at </small>$administered_time<input value='$cbx_val' name='Assignment[entries][$index][administered]' type='hidden'>";
        if (!$for_administer) {
            $administer_interaction = $lat_icon . $administer_interaction;
        }
        $adminiter_unlock_display = $administer_interaction;
    }
    ?>
    <input type="hidden" name="Assignment[entries][<?=$index?>][assignment_id]" value="<?=$assignment->id?>">
    <input type="hidden" name="Assignment[entries][<?=$index?>][medication_id]" value="<?=$med_obj->medication_id?>">
    <input type="hidden" name="Assignment[entries][<?=$index?>][laterality]" value="<?=isset($lat_id) ? $lat_id : null?>">
    <input type="hidden" name="Assignment[entries][<?=$index?>][dose]" value="<?=$med_obj->dose?>">
    <input type="hidden" name="Assignment[entries][<?=$index?>][dose_unit_term]" value="<?=$med_obj->dose_unit_term?>">
    <input type="hidden" name="Assignment[entries][<?=$index?>][route_id]" value="<?=$med_obj->route_id?>">
    <?php if ($for_administer) {?>
    <td <?= !isset($side) ? 'colspan="2"' : ''?>>
        <div class="flex-l">
            <?=$lat_icon?>
            <label class="<?=$label_css?>">
                <?=$administer_interaction?>
                <input type="hidden" class="js-administer-end" name="Assignment[entries][<?=$index?>][administered_time]" value="<?=$administered_timestamp?>">
                <input type="hidden" class="js-administer-user" name="Assignment[entries][<?=$index?>][administered_by]" value="<?=$administered_user_id?>">
            </label>
        </div>
    </td>
    <?php } else { ?>
    <td <?= !isset($side) ? 'colspan="2"' : ''?>>
        <?= $adminiter_unlock_display?>
    </td>
    <?php } ?>
<?php } ?>