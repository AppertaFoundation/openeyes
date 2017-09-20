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

<div class="element-data" id="<?=$el_id?>">
  <div class="row current-kind">
    <div class="large-2 column">
      <label style="white-space: nowrap;">Current:
          <?php if ($element->currentOrderedEntries) { ?><a href="#" class="detail-toggle" data-kind="current"><i class="fa fa-icon fa-expand" aria-hidden="true"></i></a><?php } ?>
          <?php if ($element->stoppedOrderedEntries) { ?><a href="#" class="kind-toggle show" data-kind="stopped" <?php if (!$element->currentOrderedEntries) { ?>style="display: none;"<?php } ?>><i class="fa fa-icon fa-history" aria-hidden="true"></i></a><?php } ?></label>
    </div>
    <div class="large-10 column end">
        <div class="data-value current">
            <?php if ($element->currentOrderedEntries) { ?>
            <ul class="comma-list">
                <?php foreach ($element->currentOrderedEntries as $entry) { ?>
                  <li><span class="simple"><?= $entry->getMedicationDisplay() ?></span>
                      <span class="detail" style="display: none;"><strong><?= $entry->getMedicationDisplay() ?></strong><?= $entry->getAdministrationDisplay() ? ', ' . $entry->getAdministrationDisplay() : ''?><?= $entry->getDatesDisplay() ? ', ' . $entry->getDatesDisplay() : ''?></span></li>
                <?php } ?>
            </ul>
            <?php } else { ?>
                No current medications.
            <?php } ?>
        </div>
    </div>
  </div>
    <div class="row stopped-kind" <?php if ($element->currentOrderedEntries) { ?>style="display: none;"<?php } ?>>
        <div class="large-2 column">
            <label style="white-space: nowrap;">Stopped:
                <?php if ($element->stoppedOrderedEntries) { ?><a href="#" class="detail-toggle" data-kind="stopped"><i class="fa fa-icon fa-expand" aria-hidden="true"></i></a><?php } ?>
                <a href="#" class="kind-toggle remove" data-kind="stopped"><i class="fa fa-icon fa-times" aria-hidden="true"></i></a>
            </label>
        </div>
        <div class="large-10 column end">
            <div class="data-value stopped">
                <ul class="comma-list">
                    <?php foreach ($element->stoppedOrderedEntries as $entry) { ?>
                        <li><span class="simple"><?= $entry->getMedicationDisplay() ?></span>
                            <span class="detail" style="display: none;"><strong><?= $entry->getMedicationDisplay() ?></strong><?= $entry->getAdministrationDisplay() ? ', ' . $entry->getAdministrationDisplay() : ''?><?= $entry->getDatesDisplay() ? ', ' . $entry->getDatesDisplay() : ''?></span></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    new OpenEyes.OphCiExamination.HistoryMedicationsViewController({
      element: $('#<?= $el_id ?>')
    });
  });
</script>


