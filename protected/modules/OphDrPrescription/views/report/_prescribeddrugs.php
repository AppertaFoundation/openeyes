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
            <th>Patient’s no</th>
            <th>Patient’s Surname</th>
            <th>Patient’s First name</th>
            <th>Patient’s DOB</th>
            <th>Patient’s Post code</th>
            <th>Date of Prescription</th>
            <th>Drug name</th>
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
                <?php
                $drugObj = new Drug();
                $drugObj->attributes = $drug;
                ?>
                <tr>
                    <td><?php echo $drug['hos_num']?></td>
                    <td><?php echo $drug['last_name']?></td>
                    <td><?php echo $drug['first_name']?></td>
                    <td><?php echo $drug['dob'] ? date('j M Y', strtotime($drug['dob'])) : 'Unknown'?></td>
                    <td><?php echo $drug['postcode']?></td>
                    <td><?php echo date('j M Y', strtotime($drug['created_date']))?> <?php echo substr($drug['created_date'], 11, 5)?></td>
                    <td><?php echo $drugObj->tallmanLabel?></td>
                    <td><?php echo $drug['user_first_name'].' '.$drug['user_last_name']; ?></td>
                    <td><?php echo $drug['role']; ?></td>
                    <td><?php echo date('j M Y', strtotime($drug['event_date']))?> <?php echo substr($drug['event_date'], 11, 5)?></td>
                    <td><?php echo $drug['preservative_free'] ? 'Yes' : 'No'; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
