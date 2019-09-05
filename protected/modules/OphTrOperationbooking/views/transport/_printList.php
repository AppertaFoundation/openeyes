<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div id="printable">
    <table>
        <thead>
            <tr>
                <th>Hospital number</th>
                <th>Patient</th>
                <th>TCI date</th>
                <th>Admission time</th>
                <th>Site</th>
                <th>Ward</th>
                <th>Method</th>
                <th>Firm</th>
                <th>Subspecialty</th>
                <th>DTA</th>
                <th>Priority</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($operations as $operation) {?>
                <tr>
                    <td style="width: 53px;"><?php echo $operation->event->episode->patient->hos_num?></td>
                    <td>
                        <?php echo '<strong>'.trim(strtoupper($operation->event->episode->patient->last_name)).'</strong>, '.trim($operation->event->episode->patient->first_name)?>
                    </td>
                    <td style="width: 83px;"><?php echo date('j-M-Y', strtotime($operation->latestBooking->session_date))?></td>
                    <td style="width: 73px;"><?php echo $operation->latestBooking->session_start_time?></td>
                    <td style="width: 95px;"><?php echo $operation->latestBooking->theatre->site->shortName?></td>
                    <td style="width: 170px;"><?php echo $operation->latestBooking->ward->name?></td>
                    <td style="width: 53px;"><?php echo $operation->transportStatus?></td>
                    <td style="width: 43px;"><?php echo $operation->event->episode->firm->pas_code?></td>
                    <td style="width: 53px;"><?php echo $operation->event->episode->firm->serviceSubspecialtyAssignment->subspecialty->ref_spec?></td>
                    <td style="width: 80px;"><?php echo $operation->NHSDate('decision_date')?></td>
                    <td><?php echo $operation->priority->name?></td>
                </tr>
            <?php }?>
        </tbody>
    </table>
</div>
<script>
    window.print();
</script>
