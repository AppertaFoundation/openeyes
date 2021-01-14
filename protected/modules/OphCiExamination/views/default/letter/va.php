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
 * @val array['method'] $left
 *            ['value']
 * @val array['method'] $right
 *            ['value']
 * @val string $va_unit
 */
$LR_readings = array();
$left = $left ?: array();
$right = $right ?: array();
$va_unit = $va_unit ? "({$va_unit})" : '';
foreach ($left as $method => $value) {
    $LR_readings[$method] = array('left' => $value);
}
foreach ($right as $method => $value) {
    if (array_key_exists($method, $right)) {
        $LR_readings[$method]['right'] = $value;
    } else {
        $LR_readings[$method] = array('right' => $value);
    }
}

if (empty($LR_readings)) {
    return;
}
?>

<table>
  <thead>
  <tr>
    <th><?= $title ?> <?= $va_unit ?></th>
    <th>Right Eye</th>
    <th>Left Eye</th>
  </tr>
  </thead>
  <tbody>
    <?php foreach ($LR_readings as $method => $readings) : ?>
    <tr>
      <td><?= $method ?></td>
      <td><?= array_key_exists('right', $readings) ? $readings['right'] : ' - ' ?></td>
      <td><?= array_key_exists('left', $readings) ? $readings['left'] : ' - ' ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
