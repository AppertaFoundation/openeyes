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
    $this->controller->renderPartial('//default/appointment_entry_tbody', array('worklist_patients' => $worklist_patients))?>
    </tbody>
</table>
<?php if ($past_worklist_patients_count != 0) { ?>
    <div class="collapse-data">
        <div class="collapse-data-header-icon expand js-get-past-appointments">
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
                        <tbody class="js-past-appointments-body">

                        </tbody>
                    </table>
                    <i class="js-past-appointments-spinner spinner" title="Loading..." style="display: none"></i>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<script type="text/javascript">
    $('.js-get-past-appointments').on('click',() =>{
        if(!$('.js-past-appointments-body').find('tr').length) {

            $.ajax({
                url: '/patient/getPastWorklistPatients/',
                data: {patient_id:  OE_patient_id},
                type: "GET",
                dataType: "json",
                success: function (data) {
                    if(data.past_worklist_tbody) {
                        $('.js-past-appointments-body').html(data.past_worklist_tbody);
                    }
                },
                beforeSend: function () {
                    $('.js-past-appointments-spinner').show();
                },
                complete: function() {
                    $('.js-past-appointments-spinner').hide();
                }
            });
        }
    })
</script>
