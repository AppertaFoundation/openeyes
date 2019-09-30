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
        <col class="cols-5">
    </colgroup>
    <thead>
    <tr>
        <th class="empty"></th>
        <th>Dose (unit)</th>
        <th>Eye</th>
        <th>Frequency</th>
        <th>Until</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($entries as $entry) : ?>
        <?php /** @var \EventMedicationUse $entry */ ?>
        <tr>
            <td><?= $entry->getMedicationDisplay(true) ?></td>
            <td><?= $entry->dose . ($entry->dose_unit_term ? (' ' . $entry->dose_unit_term) : '') ?></td>
            <td>
                <?php
                if ($laterality = $entry->getLateralityDisplay()) {
                    \Yii::app()->controller->widget('EyeLateralityWidget', array('laterality' => $laterality));
                } else {
                    echo "N/A";
                }
                ?>
            </td>
            <td>
                <?= $entry->frequency ? $entry->frequency : ''; ?>
            </td>
            <td><?= $entry->getEndDateDisplay('Ongoing'); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
