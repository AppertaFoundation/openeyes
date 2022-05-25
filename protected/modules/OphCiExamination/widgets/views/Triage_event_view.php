<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php $triage = $element->triage; ?>
<div class="element-data full-width">
    <div class="cols-9">
        <table class="cols-full last-left">
            <colgroup>
                <col class="cols-4">
            </colgroup>
            <tbody>
            <tr>
                <th>Chief Complaint</th>
                <td>
                    <div class="flex-l row large-text">
                        <?php $this->widget('EyeLateralityWidget', ['laterality' => $triage->eye]) ?>
                        <?= $triage->getChiefComplaint() ?: 'None' ?>
                    </div>
                </td>
            </tr>
            <tr>
                <th>Time</th>
                <td>
                    <small>at</small>
                    <?php echo date('H:i', strtotime($triage->time));?>
                </td>
            </tr>
            <tr>
                <th>Treat as</th>
                <td><?= $triage->treat_as_adult ? 'Adult' : 'Paediatric' ?></td>
            </tr>
            <tr>
                <th>Priority</th>
                <td>
                    <i class="oe-i small pad circle-<?= $triage->priority->label_colour ?>"></i>
                    <?= $triage->priority->description ?>
                </td>
            </tr>
            <tr>
                <th>Under the care of a different eye unit</th>
                <td>
                    <span <?= !$triage->site ? 'class="none"' : '' ?>><?= $triage->site ? $triage->site->name : 'N/A' ?></span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>