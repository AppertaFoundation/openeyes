<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
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
 */

?>
<table class="standard borders <?php echo isset($table_class) ? $table_class : "" ?>">
    <colgroup>
        <col class="cols-6">
    </colgroup>
    <thead>
    <tr>
        <th class="empty"></th>
        <th>Dose (unit)</th>
        <th >Eye</th>
        <th>Frequency</th>
        <th>Until</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($entries as $entry) : ?>
        <?php /** @var \EventMedicationUse $entry */ ?>
        <tr>
            <td>
                <?= $entry->getMedicationDisplay(true) ?>
                <?= !empty($entry->comments) ? ('<br /><br /><i>Comment: </i>' . $entry->comments) : ''?>
            </td>
            <td><?= $entry->dose . ($entry->dose_unit_term ? (' ' . $entry->dose_unit_term) : '') ?></td>
            <td>
                <?php
                if ($laterality = $entry->getLateralityDisplay(true)) {
                    echo $laterality;
                } else {
                    echo "n/a";
                }
                ?>
            </td>
            <td>
                <?= $entry->frequency ? $entry->frequency : ''; ?>
            </td>
            <td><?= $entry->getEndDateDisplay('Ongoing'); ?></td>
        </tr>
        <?php if ($entry->taper_support) : ?>
                    <?php foreach ($entry->tapers as $taper) : ?>
                        <?php /** @var \OphDrPrescription_ItemTaper $taper */ ?>
        <tr class="meds-taper col-gap">
            <td><i class="oe-i child-arrow small no-click pad "></i><span class="fade"><em>then</em></span></td>
            <td><?php echo is_numeric($taper->dose) ? ($taper->dose . " " . $entry->dose_unit_term) : $taper->dose ?>
                </td>
            <td class="nowrap">
                <!-- no needed in taper -->
            </td>
            <td><?= $taper->frequency->term ?></td>
            <td class="nowrap"></td>
        </tr>
    <?php endforeach; ?>
                <?php endif; ?>
    <?php endforeach; ?>
    </tbody>
</table>
