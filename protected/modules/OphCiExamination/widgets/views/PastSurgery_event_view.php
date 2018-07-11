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
<div class="element-data">
  <div class="data-value">
    <div class="tile-data-overflow">
        <?php if (!$operations || count($operations) === 0) { ?>
          <div class="data-value">No procedures recorded.</div>
        <?php } else { ?>
      <table>
        <colgroup>
          <col>
          <col width="55px">
          <col width="85px">
        </colgroup>
        <tbody> <?php foreach ($operations as $operation) { ?>
          <tr>
            <td>
                <?= array_key_exists('object',
                    $operation) ? $operation['object']->operation : $operation['operation']; ?>
            </td>
              <?php if (array_key_exists('side', $operation) ||
                  (array_key_exists('object', $operation) && $operation['object']->side)): ?>
                <td>
                    <?php $side = array_key_exists('side', $operation) ?
                        $operation['side'] : (array_key_exists('object',
                            $operation) ? $operation['object']->side : ''); ?>
                  <span class="oe-eye-lat-icons">
                    <i class="oe-i laterality <?php echo $side == 'Right' || $side == 'Both' || $side == 'Bilateral' ? 'R' : 'NA' ?> small pad"></i>
                    <i class="oe-i laterality <?php echo $side == 'Left' || $side == 'Both' || $side == 'Bilateral' ? 'L' : 'NA' ?> small pad"></i>
                  </span>
                </td>
              <?php endif; ?>
            <td>
              <div class="oe-date">
                <?= array_key_exists('object',
                    $operation) ? $operation['object']->getDisplayDate() : Helper::convertFuzzyDate2HTML($operation['date']); ?>
              </div>
            </td>
          </tr>
        <?php }
        } ?>
        </tbody>
      </table>
        <?= CHtml::encode($element->comments) ?>
    </div>
  </div>
</div>
