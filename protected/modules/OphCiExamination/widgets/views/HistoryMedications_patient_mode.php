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
$model_name = CHtml::modelName($element);
?>
<div class="group">
  <div class="label">Systemic Medications</div>
  <div class="data">
      <?php if (!$current && !$stopped) { ?>
        <p>No medications recorded.</p>
      <?php } else { ?>
        <table id="<?= $model_name ?>_entry_table">
          <tbody>
          <?php if ($current) { ?>
            <tr>
              <th class="cols-7">Current</th>
            </tr>
              <?php foreach ($current as $entry) {
                  if ($entry['route_id'] != 1) { ?>
                    <tr>
                      <td><?= $entry->getMedicationDisplay() ?></td>
                      <td><?php $laterality = $entry->getLateralityDisplay(); ?>
                        <i class="oe-i laterality small <?php echo $laterality == 'R' || $laterality == 'B' ? 'R' : 'NA' ?>"></i>
                        <i class="oe-i laterality small <?php echo $laterality == 'L' || $laterality == 'B' ? 'L' : 'NA' ?>"></i>
                      </td>
                      <td><?= $entry->getDatesDisplay() ?></td>
                    </tr>
                  <?php }
              }
          } ?>
          </tbody>
        </table>
        <table>
          <thead>
          <tr>
            <th class="cols-7">Stopped</th>
            <th>
              <i class="oe-i small js-patient-expand-btn pad expand"></i>
            </th>
          </tr>
          </thead>
          <tbody style="display: none;">
          <?php if ($stopped) { ?>
              <?php foreach ($stopped as $entry) {
                  if ($entry['route_id'] != 1) { ?>
                    <tr>
                      <td><?= $entry->getMedicationDisplay() ?></td>
                      <td><?= $entry->getDatesDisplay() ?></td>
                      <td><?php if ($entry->prescription_item) { ?>
                          <a href="<?= $this->getPrescriptionLink($entry) ?>"><span class="js-has-tooltip fa fa-eye"
                                                                                    data-tooltip-content="View prescription"></span></a>
                          <?php } ?></td>
                    </tr>
                  <?php }
              }
          } ?>
          </tbody>
        </table>
      <?php } ?>
  </div>
</div>
</div><!-- popup-overflow -->

<!-- oe-popup-overflow handles scrolling if data overflow height -->
<div class="oe-popup-overflow quicklook-data-groups">

  <div class="group">
    <div class="label">Eye medications</div>
    <div class="data">
        <?php if (!$current && !$stopped) { ?>
          <p>No medications recorded.</p>
        <?php } else { ?>
          <table id="<?= $model_name ?>_entry_table">
            <thead>
            <tr>
              <th class="cols-7">Current</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($current) { ?>
                <?php foreach ($current as $entry) {
                    if ($entry['route_id'] == 1) { ?>
                      <tr>
                        <td><?= $entry->getMedicationDisplay() ?></td>
                        <td>
                            <?php $laterality = $entry->getLateralityDisplay(); ?>
                          <i class="oe-i laterality small <?php echo $laterality == 'R' || $laterality == 'B' ? 'R' : 'NA' ?>"></i>
                          <i class="oe-i laterality small <?php echo $laterality == 'L' || $laterality == 'B' ? 'L' : 'NA' ?>"></i>
                        </td>
                        <td><?= $entry->getDatesDisplay() ?></td>
                      </tr>
                    <?php }
                }
            } ?>
            </tbody>
          </table>
          <table>
            <thead>
            <tr>
              <th class="cols-7">Stopped</th>
              <th>
                <i class="oe-i small pad js-patient-expand-btn expand"></i>
              </th>
            </tr>
            </thead>
              <?php if ($stopped) { ?>
            <tbody style="display: none;">
            <?php foreach ($stopped as $entry) {
                if ($entry['route_id'] == 1) { ?>
                  <tr>
                    <td><?= $entry->getMedicationDisplay() ?></td>
                    <td><?= $entry->getDatesDisplay() ?></td>
                    <td><?php if ($entry->prescription_item) { ?>
                        <a href="<?= $this->getPrescriptionLink($entry) ?>">
                          <span class="js-has-tooltip fa fa-eye" data-tooltip-content="View prescription"></span>
                        </a>
                        <?php } ?>
                    </td>
                  </tr>
                <?php }
            }
              } ?>
            </tbody>
          </table>
        <?php } ?>
    </div>
  </div>