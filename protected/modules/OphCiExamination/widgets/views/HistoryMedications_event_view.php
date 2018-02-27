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
?>
<script type="text/javascript" src="<?= $this->getJsPublishedPath('HistoryMedications.js') ?>"></script>
<?php $el_id =  CHtml::modelName($element) . '_element'; ?>

<div class="element-data full-width" id="<?=$el_id?>">
  <div class="data-row">
    <div class="flex-layout flex-top">
      <div class="cols-1">Current &nbsp;
        <i class="oe-i history medium pad" id="js-stopped-meds-btn"></i>
      </div>
      <div class="cols-10">
        <div id="js-listview-meds-current-pro" style>
            <?php if ($element->currentOrderedEntries) { ?>
            <ul class="dslash-list">
                <?php foreach ($element->currentOrderedEntries as $entry) { ?>
                  <li><?= $entry->getMedicationDisplay() ?></li>
                <?php } ?>
            </ul>
            <?php } else { ?>
                No current medications.
            <?php } ?>
        </div>
        <div class id="js-listview-meds-current-full" style="display:none;">
          <table>
            <colgroup>
              <col class="cols-4">
              <col class="cols-3">
            </colgroup>
            <tbody>
              <?php if ($element->currentOrderedEntries) {
                  foreach ($element->currentOrderedEntries as $entry) {
                    ?>
                    <tr>
                      <td><?= $entry->getMedicationDisplay() ?></td>
                      <td> <?= $entry->getAdministrationDisplay() ?  $entry->getAdministrationDisplay() : ''?></td>
                      <td class="nowrap"><i class="oe-i start small pad"></i><?= $entry->getStartDateDisplay() ? $entry->getStartDateDisplay() : ''?></td>
                      <td></td>
                      <td></td>
                    </tr>
              <?php }
              } ?>
            </tbody>
          </table>
        </div>
    </div>
      <i class="oe-i small pad js-listview-expand-btn expand" data-list="meds-current"></i>
  </div>
    <!-- flex-layout -->
    <div class="divider"></div>
    <div class="flex-layout flex-top" id="js-meds-stopped">
        <div class="cols-1"> Stopped </div>
        <div class="cols-10">
            <div id="js-listview-meds-stopped-pro" style>
                <ul class="dslash-list">
                    <?php foreach ($element->stoppedOrderedEntries as $entry) { ?>
                        <li><?= $entry->getMedicationDisplay() ?><i class="oe-i triangle small pad"></i></li>
                    <?php } ?>
                </ul>
            </div>
          <div class id="js-listview-meds-stopped-full" style="display: none;">
            <table>
              <colgroup>
                <col class="cols-4">
                <col class="cols-3">
              </colgroup>
              <tbody>
              <?php foreach ($element->stoppedOrderedEntries as $entry) {
                      ?>
                    <tr>
                      <td><i class="oe-i stop small pad"></i><?= $entry->getMedicationDisplay() ?></td>
                      <td> <?= $entry->getAdministrationDisplay() ?  $entry->getAdministrationDisplay() : ''?></td>
                      <td class="nowrap"><i class="oe-i start small pad"></i><?= $entry->getStartDateDisplay() ? $entry->getStartDateDisplay() : ''?></td>
                      <td class="nowrap"><i class="oe-i stop small pad"></i><?= $entry->getStopDateDisplay() ? $entry->getStopDateDisplay() : ''?></td>
                      <td><?= $entry->getStopReasonDisplay() ? $entry->getStopReasonDisplay() : ''?></td>
                    </tr>
                  <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      <i class="oe-i small pad js-listview-expand-btn expand" data-list="meds-stopped"></i>
    </div>
    <!-- flex-layout -->
</div>
  <!-- data-row -->
<script type="text/javascript">
  $(document).ready(function() {
    new OpenEyes.OphCiExamination.HistoryMedicationsViewController({
      element: $('#<?= $el_id ?>')
    });
  });
</script>


