<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 *
 * @var $this \OEModule\OphCiExamination\controllers\ReportController
 * @var $report OphCiExamination_ReportAE
 */

?>
<table class="standard">
    <thead>
    <tr>
        <th>Date</th>
        <th><?= $report->getPatientIdentifierPrompt() ?></th>
        <th>DOB</th>
        <th>Patient's Name</th>
        <th>Clinician</th>
        <th>Job role</th>
        <th>Priority</th>
        <th>Outcome</th>
        <th>Link</th>
    </tr>
    </thead>
    <tbody>
    <?php if (empty($report->items)) { ?>
        <tr>
            <td colspan="6">
                No records were found.
            </td>
        </tr>
    <?php } else { ?>
        <?php foreach ($report->items as $item) { ?>
            <tr>
                <td><?= date('j M Y', strtotime($item['event_date'])) ?></td>
                <td><?= $item['hos_num'] ?></td>
                <td><?= date('j M Y', strtotime($item['dob'])) ?></td>
                <td><?= $item['name'] ?></td>
                <td><?= $item['clinician'] ?></td>
                <td><?= $item['role'] ?></td>
                <td><?= $item['priority'] ?></td>
                <td>
                    <?= $item['followup_status']
                    . ($item['discharge_status'] ? (' - ' . $item['discharge_status']): '')
                    . ($item['discharge_destination'] ? (' - ' . $item['discharge_destination']) : '') ?>
                </td>
                <td><?= CHtml::link('view', '/OphCiExamination/default/view/' . $item['id']) ?></td>
            </tr>
        <?php } ?>
    <?php } ?>
    </tbody>
</table>
