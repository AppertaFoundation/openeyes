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
<?php
/**
 * @val array $readings_by_method
 * @val array $comments
 * @val string $va_unit
 * @val string $title
 */

$va_unit = $va_unit ? "({$va_unit})" : '';

if (empty($readings_by_method) && empty($comments)) {
    return;
}
?>

<table>
    <thead>
    <tr>
        <th><?= $title ?> <?= $va_unit ?></th>
        <th>Right Eye</th>
        <th>Left Eye</th>
        <th>BEO</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($readings_by_method as $method => $readings) { ?>
        <tr>
            <td><?= $method ?></td>
            <td><?= array_key_exists('right', $readings) ? $readings['right'] : ' - ' ?></td>
            <td><?= array_key_exists('left', $readings) ? $readings['left'] : ' - ' ?></td>
            <td><?= array_key_exists('beo', $readings) ? $readings['beo'] : ' - ' ?></td>
        </tr>
    <?php } ?>
    <?php if (!empty($comments)) { ?>
        <tr>
            <td>Comments</td>
            <td><?= $comments['right'] ?? '-' ?></td>
            <td><?= $comments['left'] ?? '-' ?></td>
            <td><?= $comments['beo'] ?? '-' ?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
