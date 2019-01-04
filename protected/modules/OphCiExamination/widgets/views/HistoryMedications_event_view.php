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
<?php $el_id = CHtml::modelName($element) . '_element'; ?>

<section class="element view-Eye-Medications tile "
         data-element-type-id="<?php echo $element->elementType->id ?>"
         data-element-type-class="<?php echo $element->elementType->class_name ?>"
         data-element-type-name="Eye Medications"
         data-element-display-order="<?php echo $element->elementType->display_order ?>">
    <header class=" element-header">
        <h3 class="element-title">Eye Medications</h3>
    </header>
    <div class="element-data">
        <div class="data-value">
            <?php if (!$element->currentOrderedEntries) { ?>
                No current medications.
                <br/>
                Stopped Medications : 
                <table>
                    <colgroup>
                        <col>
                        <col width="55px">
                        <col width="85px">
                    </colgroup>
                    <tbody>
                    <?php foreach ($element->stoppedOrderedEntries as $entry) {
                        if ($entry['route_id'] == 1) { ?>
                            <tr>
                                <td><?= $entry->getMedicationDisplay() ?></td>
                                <td><?php $laterality = $entry->getLateralityDisplay(); ?>
                                  <?php $this->widget('EyeLateralityWidget', array('laterality' => $laterality)); ?>
                                </td>
                                <td>
                                    <i class="oe-i info small js-has-tooltip"
                                       data-tooltip-content="<?= $entry->getDoseAndFrequency() ?>"
                                    </i>
                                </td>
                                <td><?= $entry->getStartDateDisplay() ?></td>
                            </tr>
                        <?php }
                    } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <div class="tile-data-overflow">
                    <table>
                        <colgroup>
                            <col>
                            <col width="55px">
                            <col width="85px">
                        </colgroup>
                        <tbody>
                        <?php foreach ($element->currentOrderedEntries as $entry) {
                            if ($entry['route_id'] == 1) { ?>
                                <tr>
                                    <td><?= $entry->getMedicationDisplay() ?></td>
                                    <td><?php $laterality = $entry->getLateralityDisplay(); ?></td>
                                    <td>
                                        <i class="oe-i info small  js-has-tooltip"
                                           data-tooltip-content="<?= $entry->getDoseAndFrequency() ?>"
                                        </i>
                                    </td>
                                    <td><?= $entry->getStartDateDisplay() ?></td>
                                </tr>
                            <?php }
                        } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </div>
    </div>
</section>

<section class=" element view-Systemic-Medications tile"
         data-element-type-id="<?php echo $element->elementType->id ?>"
         data-element-type-class="<?php echo $element->elementType->class_name ?>"
         data-element-type-name="Systemic Medications"
         data-element-display-order="<?php echo $element->elementType->display_order ?>">
    <header class=" element-header">
        <h3 class="element-title">Systemic Medications</h3>
    </header>
    <div class="element-data">
        <div class="data-value">
            <?php if (!$element->orderedEntries) { ?>
                No current medications.
            <?php } else { ?>
                <div class="tile-data-overflow">
                    <table>
                        <colgroup>
                            <col>
                            <col width="55px">
                            <col width="85px">
                        </colgroup>
                        <tbody>
                        <?php foreach ($element->orderedEntries as $entry) {
                            if ($entry['route_id'] != 1) { ?>
                                <tr>
                                    <td><?= $entry->getMedicationDisplay() ?></td>
                                    <td><?php $laterality = $entry->getLateralityDisplay(); ?></td>
                                    <td>
                                        <i class="oe-i info small  js-has-tooltip"
                                           data-tooltip-content="<?= $entry->getDoseAndFrequency() ?>"
                                        </i>
                                    </td>
                                    <td><?= $entry->getStartDateDisplay() ?></td>
                                </tr>
                            <?php }
                        } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </div>
    </div>
</section>


