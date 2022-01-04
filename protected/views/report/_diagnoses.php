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
            <th><?= $report->getPatientIdentifierPrompt() ?></th>
            <th><?php echo Patient::model()->getAttributeLabel('dob')?></th>
            <th><?php echo Patient::model()->getAttributeLabel('first_name')?></th>
            <th><?php echo Patient::model()->getAttributeLabel('last_name')?></th>
            <th>Date</th>
            <th>Diagnoses</th>
            <th>Patient IDs</th>
        </tr>
    <tbody>
        <?php if (empty($report->diagnoses)) {?>
            <tr>
                <td colspan="6">
                    No patients were found with the selected search criteria.
                </td>
            </tr>
        <?php } else {?>
            <?php foreach ($report->diagnoses as $ts => $diagnosis) {
                foreach ($diagnosis['diagnoses'] as $_diagnosis) { ?>
                    <tr>
                        <td><?= $diagnosis['identifier'] ?></td>
                        <td><?= $diagnosis['dob'] ? date('j M Y', strtotime($diagnosis['dob'])) : 'Unknown' ?></td>
                        <td><?= $diagnosis['first_name'] ?></td>
                        <td><?= $diagnosis['last_name'] ?></td>
                        <td><?= isset($_diagnosis['date']) ? date('j M Y', strtotime($_diagnosis['date'])) : date('j M Y', $ts) ?></td>
                        <td>
                            <?= $_diagnosis['eye'] . ' ' . $_diagnosis['disorder'] . ' (' . $_diagnosis['type'] . ')'; ?>
                        </td>
                        <td>
                            <?= $diagnosis['all_ids'] ?>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    </tbody>
</table>
