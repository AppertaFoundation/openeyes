<?php
/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @var Element_OphDrPrescription_Details $element
 */
?>
<div class="element-data full-width">
    <div class="data-value">
        <div class="cols-11">
            <table class="last-left">
                <thead>
                <tr>
                    <th>Drug</th>
                    <th>Dose</th>
                    <th>Route</th>
                    <th>Frequency</th>
                    <th>Duration</th>
                    <th>Dispense Condition/Location</th>
                    <th>Comments</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($element->items as $key => $item) { ?>
                    <tr class="prescription-item">
                        <td class="priority-text">
                            <?php if (isset($this->patient) && $this->patient->hasDrugAllergy($item->medication_id)) : ?>
                                <i class="oe-i warning small pad js-has-tooltip"
                                   data-tooltip-content="Allergic to <?= implode(',', $this->patient->getPatientDrugAllergy($item->medication_id)) ?>">
                                </i>
                            <?php endif; ?>
                            <?php echo $item->getMedicationDisplay(true); ?>
                            <?php $this->widget('MedicationInfoBox', array('medication_id' => $item->medication_id)); ?>
                            <?=$item->renderPGDInfo();?>
                        </td>
                            
                        <td><?php echo $item->dose . " " . $item->dose_unit_term; ?></td>
                        <td><?php echo $item->route->term ?>
                            <?php if ($item->laterality) {
                                echo ' (' . $item->medicationLaterality->name . ')';
                            } ?>
                        </td>
              <td><?php echo $item->frequency ? $item->frequency->term : '' ?></td>
              <td><?php echo $item->duration_id ? $item->medicationDuration->name : '' ?></td>
                <?php if ($item->dispense_condition->name === 'Print to {form_type}') : ?>
                <td>
                    <?php echo str_replace(
                        '{form_type}',
                        $data['form_setting'],
                        $item->dispense_condition->name
                    ) . " / {$item->dispense_location->name}" ?>
                </td>
                <?php else : ?>
                        <td><?php echo $item->dispense_condition->name . " / " . $item->dispense_location->name ?></td>
                <?php endif; ?>

                        <td class="prescription-label">
                    <?php if ($item->comments !== null) : ?>
                                <i><?= \CHtml::encode($item->comments); ?></i>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php foreach ($item->tapers as $taper) { ?>
              <tr class="prescription-taper <?php echo (($key % 2) === 0) ? 'even' : 'odd'; ?>">
                            <td class="prescription-label">
                                <i class="oe-i child-arrow small no-click pad"></i>
                                <em class="fade">then</em>
                            </td>
                            <td><?php echo is_numeric($taper->dose) ? ($taper->dose . " " . $item->dose_unit_term) : $taper->dose ?></td>
                            <td></td>
                            <td><?php echo $taper->frequency->term ?></td>
                            <td><?php echo $taper->duration->name ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php }
                } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<input type="hidden" id="et_ophdrprescription_draft" value="<?php echo $element->draft ?>"/>
<input type="hidden" id="et_ophdrprescription_print" value="<?php echo $element->print ?>"/>

<?php if ($element->comments) { ?>
<!-- CLose the wrapping section early so that teh commetns section can appear in its own section -->
</section>
<section class="element view full  view-comments">
    <header class="element-header">
        <h3 class="element-title">Comments</h3>
    </header>
    <div class="element-data full-width">
        <div class="data-value">
            <span class="large-text"><?php echo $element->textWithLineBreaks('comments') ?></span>
        </div>
    </div>
    <?php } ?>
