<?php
/**
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
<table class="standard">
    <thead>
        <tr>
            <th><?= $report->getPatientIdentifierPrompt() ?></th>
            <th><?= $report->getAttributeLabel('all_ids') ?></th>
            <th>Patient’s Surname</th>
            <th>Patient’s First name</th>
            <th>Patient’s DOB</th>
            <th>Patient’s Post code</th>
            <th>Date of Prescription</th>
            <th>Drug name</th>
            <th>Drug Dose</th>
            <th>Drug Frequency</th>
            <th>Drug Duration</th>
            <th>Drug Route</th>
            <th>Dispense condition</th>
            <th>Dispense location</th>
            <th>Laterality</th>
            <th>Prescribed Clinician’s name</th>
            <th>Prescribed Clinician’s Job-role</th>
            <th>Prescription event date</th>
            <th>Preservative Free</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($report->items)) :?>
            <tr>
                <td colspan="6">No drugs were found with the selected search criteria.</td>
            </tr>
        <?php else : ?>
            <?php foreach ($report->items as $drug) :?>
                <tr>
                    <td><?= $drug['identifier']?></td>
                    <td><?= $drug['all_ids'] ?></td>
                    <td><?= $drug['last_name']?></td>
                    <td><?= $drug['first_name']?></td>
                    <td><?= $drug['dob'] ? date('j M Y', strtotime($drug['dob'])) : 'Unknown'?></td>
                    <td><?= $drug['postcode']?></td>
                    <td><?= date('j M Y', strtotime($drug['created_date']))?> <?= substr($drug['created_date'], 11, 5)?></td>
                    <td><?= $drug['preferred_term']?></td>
                    <td><?= $drug['dose']. ' '.$drug['dose_unit']?></td>
                    <td><?= $drug['frequency']?></td>
                    <td><?= $drug['duration']?></td>
                    <td><?= $drug['route']?></td>
                    <td><?= $drug['dispense_condition']?></td>
                    <td><?= $drug['dispense_location']?></td>
                    <td><?= $drug['laterality']?></td>
                    <td><?= $drug['user_first_name'].' '.$drug['user_last_name']; ?></td>
                    <td><?= $drug['role']; ?></td>
                    <td><?= date('j M Y', strtotime($drug['event_date']))?> <?= substr($drug['event_date'], 11, 5)?></td>
                    <td><?= $drug['preservative_free'] ? 'Yes' : 'No'; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
