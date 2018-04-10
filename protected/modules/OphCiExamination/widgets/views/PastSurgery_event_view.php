<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
$widget = $this;
?>
<div class="element-data full-width">
    <div class="data-row">
        <div class="data-value">
          <div class="tile-data-overflow">
            <table>
              <colgroup>
                <col>
                <col width="55px">
                <col width="85px">
              </colgroup>
              <tbody>
              <?php if (!$operations || sizeof($operations)==0) { ?>
                <p>No ophthalmic diagnoses recorded.</p>
              <?php } else {
                  foreach ($operations as $operation) {?>
                    <tr>
                      <td><?= array_key_exists('object', $operation) ? $operation['object']->operation : $operation['operation']; ?></td>
                      <td>
                          <?php $side = array_key_exists('side', $operation) ? $operation['side']: (array_key_exists('object', $operation) ? $operation['object']->side : ''); ?>
                        <i class="oe-i laterality <?php echo  $side=='Right'||$side=='Both'||$side=='Bilateral'? 'R':'NA' ?> small pad"></i>
                        <i class="oe-i laterality <?php echo $side=='Left'||$side=='Both'||$side=='Bilateral'? 'L':'NA' ?> small pad"></i>
                      </td>
                      <td><?= array_key_exists('object', $operation) ? $operation['object']->getDisplayDate() : Helper::formatFuzzyDate($operation['date']); ?></td>
                    </tr>
                  <?php } }?>
              </tbody>
            </table>
          </div>
        </div>
    </div>
</div>
