<?php
/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

if (count($element->entries) > 0) {
    ?>
    <table>
        <thead>
        <tr>
            <th><?= $element->getElementTypeName() ?></th>
            <th>Range</th>
            <th>BO</th>
            <th>BI</th>
            <th>BU</th>
            <th>BD</th>
        </tr>
        </thead>
        <tbody>
        <?php

        foreach ($element->entries as $entry) {
            ?>
            <tr>
                <td rowspan="2">
                    <?= $entry->display_labelled_prism_over_eye ?>
                    <?= $entry->correctiontype ?? '' ?>
                    <?= $entry->display_labelled_with_head_posture ?>
                </td>
                <td>Near</td>
                <td><?= $entry->near_bo ?></td>
                <td><?= $entry->near_bi ?></td>
                <td><?= $entry->near_bu ?></td>
                <td><?= $entry->near_bd ?></td>
            </tr>
            <tr>
                <td>Distance</td>
                <td><?= $entry->distance_bo ?></td>
                <td><?= $entry->distance_bi ?></td>
                <td><?= $entry->distance_bu ?></td>
                <td><?= $entry->distance_bd ?></td>
            </tr>
            <?php
        };

        if ($element->comments) {
            ?>
            <tr>
                <td colspan="6">
                    <?= $element->comments ?>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
    <?php
} ?>