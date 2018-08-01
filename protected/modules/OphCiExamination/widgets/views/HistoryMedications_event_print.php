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
<br>
<div class="element-data row clearfix">
    <div class="data-group">
        <div class="cols-2 column">
            <label>Current:</label>
        </div>
        <div class="cols-10 column end">
            <?php foreach ($element->currentOrderedEntries as $entry): ?>
              <div class="cols-12 column">
                <span class="detail"><strong><?= $entry->getMedicationDisplay() ?></strong><?= $entry->getAdministrationDisplay() ? ', ' . $entry->getAdministrationDisplay() : ''?><?= $entry->getDatesDisplay() ? ', ' . $entry->getDatesDisplay() : ''?> //</span>
              </div>
            <?php endforeach; ?>
        </div>
    </div>
    <br>
    <div class="data-group">
        <div class="cols-2 column">
            <label>Stopped:</label>
        </div>
        <div class="cols-10 column end">
            <?php foreach ($element->stoppedOrderedEntries as $entry): ?>
              <div class="cols-12 column">
                <span class="detail"><strong><?= $entry->getMedicationDisplay() ?></strong><?= $entry->getAdministrationDisplay() ? ', ' . $entry->getAdministrationDisplay() : ''?><?= $entry->getDatesDisplay() ? ', ' . $entry->getDatesDisplay() : ''?> //</span>
              </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

