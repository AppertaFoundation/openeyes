<?php if ($is_prescriber && $is_new && !$is_confirmed) { ?>
    <?php
        $today = \Helper::convertDate2NHS(date('Y-m-d'), ' ');
        $dates = array_map(function ($appt) {
            return date('Y-m-d', strtotime($appt->when));
        }, $available_appointments);
        $has_appointment_today = array_search(date('Y-m-d'), $dates) !== false;
    ?>
<div class="da-order-options js-assign-order-ctn">
    <div class="help">
        <?=$help_info ? : '{{{help_info}}}'?>
    </div>
    <div class="assign-for" <?=$is_record_admin ? 'style="display:none"' : ''?>>
        <span class="fade">Assign order for </span>
        <fieldset class="restrict-data-height rows-5">
            <?php if (!$available_appointments || !$has_appointment_today) { ?>
            <label class="highlight">
                <input
                    type="radio"
                    class="js-assignment-options unbooked is-order"
                    data-for-today="1"
                    data-appt-date="Today"
                    data-appt-valid-date="<?=$today;?>"
                    data-appt-time="<?=date('H:i');?>"
                    data-appt-clinic="Unbooked Appointment"
                    checked
                >
                <span class="highlighter good">
                    Today
                    <span class="fade nowrap">
                        <small>at</small> <?=date('H:i');?>
                    </span>Unbooked Appointment
                </span>
            </label>
            <?php } ?>
            <?php foreach ($available_appointments as $appt) {
                $appt_date = \Helper::convertMySQL2NHS($appt->when, ' ');
                $valid_appt_date = \Helper::convertMySQL2NHS($appt->when, ' ');
                $time = date('H:i', strtotime($appt->when));
                $for_today = false;
                if ($appt_date === $today) {
                    $for_today = true;
                    $appt_date = 'Today';
                    $assigned_appt = $assigned_appt ? : $appt->id;
                }
                ?>
            <label class="highlight">
                <input
                    type="radio"
                    class="js-assignment-options is-order"
                    value="<?=$appt->id?>"
                    data-for-today="<?=$for_today;?>"
                    data-appt-date="<?=$appt_date;?>"
                    data-appt-valid-date="<?=$valid_appt_date;?>"
                    data-appt-time="<?=$time;?>"
                    data-appt-clinic="<?=$appt->worklist->name;?>"
                    <?=intval($appt->id) === intval($assigned_appt) ? 'checked' : ''?>
                    <?=intval($appt->id) === intval($assigned_appt) ? 'data-assigned=1' : ''?>
                >
                <span class="<?=intval($appt->id) === intval($assigned_appt) ? 'highlighter good' : '';?>">
                    <?= $appt_date;?>
                    <span class="fade nowrap">
                        <small>at</small> <?=$time;?>
                    </span><?=$appt->worklist->name;?>
                </span>
            </label>
            <?php } ?>
        </fieldset>
    </div>
    <div class="actions">
        <button class="green hint js-confirm-preset">
            <?= $btn_text ? : '{{btn_text}}';?>
        </button>
        <button class="red hint js-cancel-preset">Cancel & remove</button>
    </div>
</div>
<?php }?>
