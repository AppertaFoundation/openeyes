<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<table class="patient-appointments">
    <colgroup>
        <col class="cols-2">
        <col class="cols-5">
        <col class="cols-2">
        <col class="cols-3">
    </colgroup>
    <tbody>
    <?php
    foreach ($worklist_patients as $worklist_patient) {
        $time = date('H:i', strtotime($worklist_patient->when));
        $date = \Helper::convertDate2NHS($worklist_patient->worklist->start);
        $worklist_name = $worklist_patient->worklist->name;
        $worklist_status = $worklist_patient->getWorklistPatientAttribute('Status');
        $event = Event::model()->findByAttributes(['worklist_patient_id' => $worklist_patient->id]);
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
    </tbody>
</table>

<?php if ($past_worklist_patients) { ?>
    <table class="patient-appointments">
        <colgroup>
            <col class="cols-2">
            <col class="cols-5">
            <col class="cols-2">
            <col class="cols-3">
        </colgroup>
        <thead>
        <tr>
            <th colspan="2">Past appointments</th>
            <th></th>
            <th>
                <i class="oe-i small pad js-patient-expand-btn expand <?= $pro_theme ?>"></i>
            </th>
        </tr>
        </thead>
        <tbody style="display: none;">
        <?php
        foreach ($past_worklist_patients as $worklist_patient) {
            $time = date('H:i', strtotime($worklist_patient->when));
            $date = \Helper::convertDate2NHS($worklist_patient->worklist->start);
            $worklist_name = $worklist_patient->worklist->name;
            $worklist_status = $worklist_patient->getWorklistPatientAttribute('Status');
            $event = Event::model()->findByAttributes(['worklist_patient_id' => $worklist_patient->id]);
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
        </tbody>
    </table>
<?php } ?>