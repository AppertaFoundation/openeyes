<?php
foreach ($worklist_patients as $worklist_patient) {
    $time = date('H:i', strtotime($worklist_patient->when));
    $date = \Helper::convertDate2NHS($worklist_patient->worklist->start);
    $worklist_name = $worklist_patient->worklist->name;
    $worklist_status = $worklist_patient->getWorklistPatientAttribute('Status');
    $event = $did_not_attend_events[$worklist_patient->id] ?? null;
    ?>
    <tr>
        <td><span class="time"><?= $time ?></span></td>
        <td><?= $worklist_name ?></td>
        <td><span class="oe-date"><?= $date ?></span></td>
        <td>
            <?php if (isset($worklist_status)) { ?>
                <?= $worklist_status->attribute_value ?>
            <?php } elseif ($event && $event->eventType && $event->eventType->class_name === "OphCiDidNotAttend") { ?>
                Did not attend.
            <?php } ?>
        </td>
    </tr>
<?php } ?>