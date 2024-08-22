<div class="element-data full-width">
    <?php
    $last = end($assigned_psds);
    foreach ($assigned_psds as $key => $assigned_psd) {
        if ($assigned_psd->anyAssociatedEventDeleted()) {
            continue;
        }
        $appointment_details = $assigned_psd->getAppointmentDetails();
        $assignment_type_name = $assigned_psd->getAssignmentTypeAndName();
        $is_active = $assigned_psd->active;
        $is_relevant = $assigned_psd->isrelevant;
        $grey_out_section = !$is_relevant || !$is_active ? 'fade' : null;
        $deleted_tag = $assigned_psd->getDeletedUI();
        ?>
    <div class="order-block">
        <div class="flex row">
            <div class="flex-l">
                <!-- rely on status class: todo | active | done -->
                <div class="drug-admin-box inline <?=$assigned_psd->getStatusDetails()['css']?>">
                    <?=$assignment_type_name['type']?>
                </div>&emsp;
                <div class="large-text">
                    <?=$assignment_type_name['name']?>
                    <span class="js-appt-details">
                        <?=$appointment_details['appt_details_dom']?>
                    </span>
                </div>
            </div>
            <div class="flex-r">
                <?=$deleted_tag?>
                <?=$appointment_details['valid_date_dom']?>
            </div>
        </div>
        <div class="flex">
            <div class="cols-11">
                <table class="cols-full">
                    <colgroup>
                        <col class="cols-4">
                        <col class="cols-1">
                        <col class="cols-1">
                        <col class="cols-2">
                        <col class="cols-2">
                        <col class="cols-2">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Drug</th>
                            <th>Dose</th>
                            <th>Route</th>
                            <th>Administered by</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $entries = $assigned_psd->assigned_meds;
                    foreach ($entries as $entry_key => $entry) {
                        list(
                            "administered_ts" => $administered_ts,
                            "administered_nhs" => $administered_nhs,
                            "administered_time" => $administered_time,
                            "administered_time_ui" => $administered_time_ui,
                            "administered_user" => $administered_user,
                            "state_css" => $state_css,
                        ) = $entry->getAdministerDetails();
                        ?>
                        <tr class="<?=$grey_out_section;?>">
                            <td>
                                <?php if (!$is_active && !$entry->administered) {?>
                                    <del>
                                <?php } ?>
                                <?=$entry->medication->getLabel(true)?>
                                <?php if (!$is_active && !$entry->administered) {?>
                                    </del>
                                <?php } ?>
                            </td>
                            <td><?=$entry->dose?> <?=$entry->dose_unit_term?></td>
                            <td>
                            <?php if ($entry->route->isEyeRoute()) {?>
                            <!-- rely on med route -->
                            <span class="oe-eye-lat-icons">
                                <i class="oe-i laterality <?=intval($entry->laterality) === MedicationLaterality::RIGHT ? 'R' : 'NA';?> small pad"></i>
                                <i class="oe-i laterality <?=intval($entry->laterality) === MedicationLaterality::LEFT ? 'L' : 'NA';?> small pad"></i>
                            </span>
                            <?php } else {?>
                                <?=$entry->route;?>
                            <?php }?>
                            </td>
                            <td>
                                <?=$administered_user;?>
                            </td>
                            <td>
                                <?=$administered_nhs;?>
                            </td>
                            <td>
                                <?=$administered_time;?>
                            </td>
                            <td>
                                <i class="oe-i medium pad <?=$state_css?>"></i>
                            </td>
                        </tr>
                    <?php }?>
                    </tbody>
                </table>
                <div class="row">
                    <?php if ($assigned_psd->comment) {?>
                    <i class="oe-i comments-who small pad-right js-has-tooltip" data-tooltip-content="<small>User comment by </small><br><?=$assigned_psd->comment->commented_user->getFullName()?>"></i>
                    <span class="user-comment"><?=\OELinebreakReplacer::replace($assigned_psd->comment)?></span>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php if ($last !== $assigned_psd) {?>
            <hr class="divider">
        <?php }?>
    </div>
    <?php } ?>
</div>
