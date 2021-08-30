<div class="element-data full-width">
    <?php
    $last = end($assigned_psds);
    foreach ($assigned_psds as $key => $assigned_psd) {
            $appointment_details = $assigned_psd->getAppointmentDetails();
            $assignment_type_name = $assigned_psd->getAssignmentTypeAndName();
            $grey_out_section = $assigned_psd->isrelevant ? null : 'fade';
        ?>
    <div class="order-block <?=$grey_out_section;?>">
        <div class="flex row">
            <div class="flex-l">
                <!-- rely on status class: todo | active | done -->
                <div class="drug-admin-box inline <?=$assigned_psd->getStatusDetails()['css']?>">
                    <?=$assignment_type_name['type']?>
                </div>
                <div class="large-text">
                    <?=$assignment_type_name['name']?>
                    <span class="js-appt-details">
                        <?=$appointment_details['appt_details_dom']?>
                    </span>
                </div>
            </div>
            <div class="flex-r">
                <?=$appointment_details['valid_date_dom']?>
            </div>
        </div>
        <div class="flex">
            <div class="cols-11">
                <table class="cols-full">
                    <colgroup>
                        <col class="cols-4">
                        <col class="cols-2">
                        <col class="cols-1">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Drug</th>
                            <th>Dose</th>
                            <th>Route</th>
                            <th>Administered by</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th><i class="oe-i tick small no-click pad"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $entries = $assigned_psd->assigned_meds;
                    foreach ($entries as $entry_key => $entry) {
                        extract($entry->getAdministerDetails());
                        ?>
                        <tr>
                            <td><?=$entry->medication->getLabel(true)?></td>
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
                                <!-- <small class="fade">Waiting to administer</small> -->
                            </td>
                            <td>
                                <?=$administered_nhs;?>
                            </td>
                            <td>
                                <?=$administered_time;?>
                            </td>
                            <td>
                                <i class="oe-i medium pad <?=$css?>"></i>
                            </td>
                        </tr>
                    <?php }?>
                    </tbody>
                </table>
                <div class="row">
                    <?php if ($assigned_psd->comment) {?>
                    <i class="oe-i comments-who small pad-right js-has-tooltip" data-tooltip-content="<small>User comment by </small><br><?=$assigned_psd->comment->commented_user->getFullName()?>"></i>
                    <span class="user-comment"><?=$assigned_psd->comment?></span>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php if($last !== $assigned_psd) {?>
            <hr class="divider">
        <?php }?>
    </div>
    <?php } ?>
</div>