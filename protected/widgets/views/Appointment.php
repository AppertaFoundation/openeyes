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

<!--Beginning of the side view-->
<?php if (isset($is_popup)) { ?>
    <div class="restrict-data-height rows-10">
        <table class="patient-appointments">
            <colgroup>
                <col class="cols-1">
                <col class="cols-6">
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
    </div>
    <?php if ($past_worklist_patients_count != 0) { ?>
        <div class="collapse-data">
            <div class="collapse-data-header-icon expand js-get-past-appointments" data-patient-id="<?=$this->patient->id?>">
                <h3>Past Appointments <small>(<?= $past_worklist_patients_count?>)</small></h3>
            </div>
            <div class="collapse-data-content">
                <div class="restrict-data-shown">
                    <div class="element-data full-width restrict-data-height rows-10"><div class="element-data full-width restrict-data-height rows-10">
                        <!-- restrict data height, overflow will scroll -->
                        <table class="patient-appointments">
                            <colgroup>
                                <col class="cols-1">
                                <col class="cols-6">
                                <col class="cols-2">
                                <col class="cols-3">
                            </colgroup>
                            <tbody class="js-past-appointments-body" data-patient-id="<?=$this->patient->id?>">

                            </tbody>
                        </table>
                        <i class="js-past-appointments-spinner spinner" title="Loading..." style="display: none"></i>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <!--End of the side view-->

<?php } else { ?>
    <div class="restrict-data-height rows-10">
        <table class="patient-appointments">
            <colgroup>
                <col class="cols-2">
                <col class="cols-5">
                <col class="cols-2">
                <col class="cols-3">
            </colgroup>
            <tbody>
            <?php
            $this->controller->renderPartial('//default/appointment_entry_tbody', array('worklist_patients' => $worklist_patients))?>
            </tbody>
        </table>
    </div>
    <?php if ($past_worklist_patients_count != 0) { ?>
        <div class="collapse-data">
        <div class="collapse-data-header-icon expand js-get-past-appointments" data-patient-id="<?=$this->patient->id?>">
                Past Appointments
            <small>(<?= $past_worklist_patients_count?>)</small>
            </div>
            <div class="collapse-data-content">
                <div class="restrict-data-shown">
                    <div class="restrict-data-content rows-10">
                        <!-- restrict data height, overflow will scroll -->
                        <table class="patient-appointments">
                            <colgroup>
                                <col class="cols-1">
                                <col class="cols-6">
                                <col class="cols-2">
                                <col class="cols-3">
                            </colgroup>
                        <tbody class="js-past-appointments-body" data-patient-id="<?=$this->patient->id?>">

                            </tbody>
                        </table>
                    <i class="js-past-appointments-spinner spinner" title="Loading..." style="display: none"></i>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } ?>

<script type="text/javascript">
        $('.js-get-past-appointments[data-patient-id="<?=$this->patient->id?>"]').on('click', function () {
            let patientId = $(this).parent().find('.js-past-appointments-body').data('patientId');
            if (!$('.js-past-appointments-body[data-patient-id="' + patientId + '"]').find('tr').length) {

                $.ajax({
                    url: '/patient/getPastWorklistPatients/',
                    data: {patient_id: patientId},
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        if (data.past_worklist_tbody) {
                            $('.js-past-appointments-body[data-patient-id="' + patientId + '"]').html(data.past_worklist_tbody);
                        }
                    },
                    beforeSend: function () {
                        $('.js-past-appointments-spinner').show();
                    },
                    complete: function () {
                        $('.js-past-appointments-spinner').hide();
                    }
                });
            }
        })
</script>
