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
<table class="standard">
    <thead>
        <tr>
            <th>Date</th>
            <th><?php echo Patient::model()->getAttributeLabel('hos_num')?></th>
            <th><?php echo Patient::model()->getAttributeLabel('first_name')?></th>
            <th><?php echo Patient::model()->getAttributeLabel('last_name')?></th>
            <th><?php echo Patient::model()->getAttributeLabel('gender')?></th>
            <th><?php echo Patient::model()->getAttributeLabel('dob')?></th>
            <th>Eye</th>
            <th>Site</th>
            <th>Left drug</th>
            <th>Left injection no</th>
            <th>Right drug</th>
            <th>Right injection no</th>
            <?php if ($report->pre_va) {?>
                <th>Left pre-injection VA</th>
                <th>Right pre-injection VA</th>
            <?php }?>
            <?php if ($report->post_va) {?>
                <th>Left post-injection VA</th>
                <th>Right post-injection VA</th>
            <?php }?>
            <th>Left Pre-injection Antiseptics</th>
            <th>Right Pre-injection Antiseptics</th>
            <th>Left Injection given by</th>
            <th>Right Injection given by</th>
            <th>Left Lens Status</th>
            <th>Right Lens Status</th>
            <th>Left Diagnosis</th>
            <th>Right Diagnosis</th>
        </tr>
    <tbody>
        <?php if (empty($report->injections)) {?>
            <tr>
                <td colspan="12">
                    No patients were found with the selected search criteria.
                </td>
            </tr>
        <?php } else {?>
            <?php foreach ($report->injections as $ts => $injection) {?>
                <tr>
                    <td><?php echo $injection['injection_date']?></td>
                    <td><?php echo $injection['patient_hosnum']?></td>
                    <td><?php echo $injection['patient_firstname']?></td>
                    <td><?php echo $injection['patient_surname']?></td>
                    <td><?php echo $injection['patient_gender']?></td>
                    <td><?php echo $injection['patient_dob']?></td>
                    <td><?php echo $injection['eye']?></td>
                    <td><?php echo $injection['site_name']?></td>
                    <td><?php echo $injection['left_drug']?></td>
                    <td><?php echo $injection['left_injection_number']?></td>
                    <td><?php echo $injection['right_drug']?></td>
                    <td><?php echo $injection['right_injection_number']?></td>
                    <?php if ($report->pre_va) {?>
                        <td><?php echo $injection['left_preinjection_va']?></td>
                        <td><?php echo $injection['right_preinjection_va']?></td>
                    <?php }?>
                    <?php if ($report->post_va) {?>
                        <td><?php echo $injection['left_postinjection_va']?></td>
                        <td><?php echo $injection['right_postinjection_va']?></td>
                    <?php }?>
                    <td><?php echo $injection['pre_antisept_drug_left']?></td>
                    <td><?php echo $injection['pre_antisept_drug_right']?></td>
                    <td><?php echo $injection['given_by_left']?></td>
                    <td><?php echo $injection['given_by_right']?></td>
                    <td><?php echo $injection['lens_status_left']?></td>
                    <td><?php echo $injection['lens_status_right']?></td>
                    <td><?php echo $injection['diagnosis_left']?></td>
                    <td><?php echo $injection['diagnosis_right']?></td>

                </tr>
            <?php }?>
        <?php }?>
    </tbody>
</table>
