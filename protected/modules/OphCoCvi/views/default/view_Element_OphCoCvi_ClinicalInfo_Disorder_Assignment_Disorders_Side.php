<?php
/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<table class="standard">
    <colgroup>
        <col class="cols-3">
        <col class="cols-2">
        <col class="cols-2">
        <col class="cols-2">
        <col class="cols-2">
        <col class="cols-2">
    </colgroup>
    <thead>
        <th>Diagnosis</th>
        <th>Main Cause</th>
        <th>ICD 10 Code</th>
        <th>Right Eye</th>
        <th>Left Eye</th>
        <th>Both Eyes</th>
    </thead>
    <tbody>
    <?php
    foreach ($disorder_section->disorders as $disorder) {
        $main_cause = $element->isCviDisorderMainCauseForSide($disorder, 'right');
        ?>
            <tr>
                    <td><?php echo CHtml::encode($disorder->name); ?></td>
                    <td><?php echo ($main_cause) ? 'Yes' : 'No';?></td>
                    <td><?php echo CHtml::encode($disorder->code); ?></td>
                    <td><?php echo ($element->hasCviDisorderForSide($disorder, 'right')) ? 'Yes' : 'No'; ?></td>
                    <td><?php echo ($element->hasCviDisorderForSide($disorder, 'left')) ? 'Yes' : 'No'; ?></td>
                    <td><?php echo ($element->hasCviDisorderForSide($disorder, 'both')) ? 'Yes' : 'No'; ?></td>
            </tr>
        <?php
    }?>
    <tr><td colspan="10">
            <hr class="divider"></td></tr>
    </tbody>
</table>