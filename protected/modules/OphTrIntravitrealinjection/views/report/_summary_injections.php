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
            <th><?= $report->getPatientIdentifierPrompt() ?></th>
            <th><?= Patient::model()->getAttributeLabel('first_name')?></th>
            <th><?= Patient::model()->getAttributeLabel('last_name')?></th>
            <th><?= Patient::model()->getAttributeLabel('gender')?></th>
            <th><?= Patient::model()->getAttributeLabel('dob')?></th>
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
            <th><?= $report->getAttributeLabel('all_ids') ?></th>
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
                    <td><?= $injection['injection_date']?></td>
                    <td><?= $injection['patient_identifier']?></td>
                    <td><?= $injection['patient_firstname']?></td>
                    <td><?= $injection['patient_surname']?></td>
                    <td><?= $injection['patient_gender']?></td>
                    <td><?= $injection['patient_dob']?></td>
                    <td><?= $injection['eye']?></td>
                    <td><?= $injection['site_name']?></td>
                    <td><?= $injection['left_drug']?></td>
                    <td><?= $injection['left_injection_number']?></td>
                    <td><?= $injection['right_drug']?></td>
                    <td><?= $injection['right_injection_number']?></td>
                    <?php if ($report->pre_va) {?>
                        <td><?= $injection['left_preinjection_va']?></td>
                        <td><?= $injection['right_preinjection_va']?></td>
                    <?php }?>
                    <?php if ($report->post_va) {?>
                        <td><?= $injection['left_postinjection_va']?></td>
                        <td><?= $injection['right_postinjection_va']?></td>
                    <?php }?>
                    <td><?= $injection['pre_antisept_drug_left']?></td>
                    <td><?= $injection['pre_antisept_drug_right']?></td>
                    <td><?= $injection['given_by_left']?></td>
                    <td><?= $injection['given_by_right']?></td>
                    <td><?= $injection['lens_status_left']?></td>
                    <td><?= $injection['lens_status_right']?></td>
                    <td><?= $injection['diagnosis_left']?></td>
                    <td><?= $injection['diagnosis_right']?></td>
                    <td><?= $injection['all_ids'] ?></td>
                </tr>
            <?php }?>
        <?php }?>
    </tbody>
</table>
