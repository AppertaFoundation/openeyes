<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<table class="standard">
    <thead>
        <tr>
            <th><?= $report->getPatientIdentifierPrompt() ?></th>
            <th><?php echo Patient::model()->getAttributeLabel('dob')?></th>
            <th><?php echo Patient::model()->getAttributeLabel('first_name')?></th>
            <th><?php echo Patient::model()->getAttributeLabel('last_name')?></th>
            <th><?php echo Patient::model()->getAttributeLabel('gender')?></th>
            <th>Site</th>
            <th>Consultant's name</th>
            <th>Date</th>
            <th>Type</th>
            <th>Status</th>
            <th>Patient IDs</th>
            <th>Link</th>
        </tr>
    <tbody>
        <?php if (empty($report->letters)) {?>
            <tr>
                <td colspan="6">
                    No letters were found with the selected search criteria.
                </td>
            </tr>
        <?php } else {?>
            <?php foreach ($report->letters as $letter) {?>
                <tr>
                    <td><?= $letter['identifier']?></td>
                    <td><?= $letter['dob'] ? date('j M Y', strtotime($letter['dob'])) : 'Unknown'?></td>
                    <td><?= $letter['first_name']?></td>
                    <td><?= $letter['last_name']?></td>
                    <td><?= $letter['gender']?></td>
                    <td><?= isset($letter['name']) ? $letter['name'] : 'N/A'; ?></td>
                    <td><?= $letter['cons_first_name'] ." ". $letter['cons_last_name']; ?></td>
                    <td><?= date('j M Y', strtotime($letter['created_date']))?> <?php echo substr($letter['created_date'], 11, 5)?></td>
                    <td><?= $letter['type']?></td>
                    <td><?= ucfirst($letter['status']);?></td>
                    <td><?= $letter['all_ids'] ?></td>
                    <td><a href="<?= $letter['link']?>">view</a></td>
                </tr>
            <?php }?>
        <?php }?>
    </tbody>
</table>