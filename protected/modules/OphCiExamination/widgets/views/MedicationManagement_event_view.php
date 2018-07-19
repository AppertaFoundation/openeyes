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
<?php /** @var \OEModule\OphCiExamination\models\MedicationManagement $element */ ?>
<?php $el_id =  CHtml::modelName($element) . '_element'; ?>

<div class="element-data" id="<?=$el_id?>">
    <div class="row continued-kind">
        <div class="large-2 column">
            <label style="white-space: nowrap;">Continued:</label>
        </div>
        <div class="large-10 column end">
            <div class="data-value current">
                <?php if (!empty($entries = $element->getContinuedEntries())) { ?>
                    <ul class="comma-list">
                        <?php foreach ($entries as $entry) { ?>
                            <li><span class="detail" style="display: inline;"><strong><?= $entry->getMedicationDisplay()  ?></strong><?= $entry->getAdministrationDisplay() ? ', ' . $entry->getAdministrationDisplay() : ''?><?= $entry->getDatesDisplay() ? ', ' . $entry->getDatesDisplay() : ''?></span></li>
                        <?php } ?>
                    </ul>
                <?php } else { ?>
                    No continued medications.
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="row stopped-kind">
        <div class="large-2 column">
            <label style="white-space: nowrap;">Stopped:</label>
        </div>
        <div class="large-10 column end">
            <div class="data-value stopped">
                <?php if (!empty($entries = $element->getStoppedEntries())) { ?>
                    <ul class="comma-list">
                        <?php foreach ($entries as $entry) { ?>
                            <li><span class="detail" style="display: inline;"><strong><?= $entry->getMedicationDisplay()  ?></strong><?= $entry->getAdministrationDisplay() ? ', ' . $entry->getAdministrationDisplay() : ''?><?= $entry->getDatesDisplay() ? ', ' . $entry->getDatesDisplay() : ''?></span></li>
                        <?php } ?>
                    </ul>
                <?php } else { ?>
                    No stopped medications.
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="row prescribed-kind">
        <div class="large-2 column">
            <label style="white-space: nowrap;">Prescribed:</label>
        </div>
        <div class="large-10 column end">
            <div class="data-value prescribed">
                <?php if (!empty($entries = $element->getPrescribedEntries())) { ?>
                    <ul class="comma-list">
                        <?php foreach ($entries as $entry) { ?>
                            <li><span class="detail" style="display: inline;"><strong><?= $entry->getMedicationDisplay()  ?></strong><?= $entry->getAdministrationDisplay() ? ', ' . $entry->getAdministrationDisplay() : ''?><?= $entry->getDatesDisplay() ? ', ' . $entry->getDatesDisplay() : ''?></span></li>
                        <?php } ?>
                    </ul>
                <?php } else { ?>
                    No prescribed medications.
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="row prescribed-kind">
        <div class="large-2 column">
            <label style="white-space: nowrap;">Other:</label>
        </div>
        <div class="large-10 column end">
            <div class="data-value prescribed">
                <?php if (!empty($entries = $element->getOtherEntries())) { ?>
                    <ul class="comma-list">
                        <?php foreach ($entries as $entry) { ?>
                            <li><span class="detail" style="display: inline;"><strong><?= $entry->getMedicationDisplay()  ?></strong><?= $entry->getAdministrationDisplay() ? ', ' . $entry->getAdministrationDisplay() : ''?><?= $entry->getDatesDisplay() ? ', ' . $entry->getDatesDisplay() : ''?></span></li>
                        <?php } ?>
                    </ul>
                <?php } else { ?>
                    No medications.
                <?php } ?>
            </div>
        </div>
    </div>
</div>