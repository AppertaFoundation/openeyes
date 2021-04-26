<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2018
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
<?php /** @var EventMedicationUse $entry */ ?>

<tr class="divider col-gap" data-key="<?= $row_count ?>">
    <td><?php if ($entry_icon) {
        ?><i class="oe-i <?= $entry_icon ?> small pad-right "></i><?php
        } ?><?php if (isset($patient) && $patient->hasDrugAllergy($entry->medication_id)) {
                                                                                                            echo '<i class="oe-i warning small pad js-has-tooltip js-allergy-warning" data-tooltip-content="Allergic to ' . implode(',', $patient->getPatientDrugAllergy($entry->medication_id)) . '"></i>';
        } ?><?= $entry->getMedicationDisplay(true) ?><?php if ($this->mode !== static::$EVENT_PRINT_MODE) {
                                                            $this->widget('MedicationInfoBox', array('medication_id' => $entry->medication_id));
        } ?></td>
    <td><?php echo $entry->getAdministrationDisplay(false); ?><?php if ($route = $entry->getRouteDisplay()) {
                                                                echo ' (' . $entry->getRouteDisplay() . ')';
        } ?></td>
    <td class="nowrap">
        <div class="meds-side-dates">
            <?php $laterality = $entry->getLateralityDisplay(); ?>
            <span class="oe-eye-lat-icons">
                <i class="oe-i laterality small <?php echo $laterality === 'R' || $laterality === 'B' ? 'R' : 'NA' ?> pad"></i>
                <i class="oe-i laterality small <?php echo $laterality === 'L' || $laterality === 'B' ? 'L' : 'NA' ?> pad"></i>
            </span>
           <?= Helper::oeDateAsStr($entry->getStartDateDisplay()) ?><i class="oe-i direction-right small no-click pad"></i>
            <?php if (isset($entry->end_date) && (!$entry->prescriptionItem || $entry->prescriptionItem && $entry->prescriptionItem->prescription->draft === '0')) { ?>
                    <?= Helper::oeDateAsStr($entry->getEndDateDisplay()) ?>
                    </div>
                    <div class="meds-stop-reason">
                    <br><em class="fade"><?= "({$entry->stopReason})" ?></em>
            <?php } else {
                ?><em class="fade">Ongoing</em><?php
            } ?>
            </div>
        </div>
    </td>
    <td>
        <div>   
            <?php echo $entry->duration_id ? $entry->medicationDuration->name . "<i class='oe-i d-slash small-icon no-click'></i>" : '' ?>
            <?php if ($entry->dispense_condition_id) {
                if ($entry->dispense_condition->name === 'Print to {form_type}') {
                    echo str_replace('{form_type}', $form_setting, $entry->dispense_condition->name) . ( $entry->dispense_location->name != 'N/A' ? "<i class='oe-i d-slash small-icon no-click'></i>" . "{$entry->dispense_location->name}" : null);
                } else {
                    echo $entry->dispense_condition->name . "<i class='oe-i d-slash small-icon no-click'></i>" . (isset($entry->dispense_location) ? $entry->dispense_location->name : "");
                }
            } ?>
        </div>
        <?php if ($entry->comments) { ?>
            <i class="oe-i comments-who small pad-right js-has-tooltip" data-tt-type="basic" data-tooltip-content="User comment by <br />Michael Morgan"></i>
            <span class="user-comment"><?= $entry->comments; ?></span>
        <?php } ?>
    </td>
    <td class="nowrap"><?php if ($entry->prescribe && $entry->prescriptionItem) {
                            $is_draft = (int) $entry->prescriptionItem->prescription->draft === 1; ?>
            <i class="oe-i circle-<?= $is_draft ? 'orange' : 'green' ?> small js-has-tooltip" data-tt-type="basic" data-tooltip-content="<?= $is_draft ? 'Draft' : 'Prescribed' ?>"></i>
                       <?php } ?></td>
    <td class="nowrap">
    <!-- no audit trail, use blank spacer icon to maintain table layout alignment -->
    <?php if ($entry->prescription_item_id && !$entry->prescriptionNotCurrent()) { ?>
        <i class="oe-i spacer small pad-right"></i>
        <a href="<?= $this->getPrescriptionLink($entry->prescriptionItem) ?>">
            <span class="oe-i direction-right-circle small-icon js-has-tooltip"
                    data-tooltip-content="View prescription">
            </span>
        </a>
    <?php } ?>
    </td>
</tr>

<?php if ($entry->taper_support) : ?>
    <?php foreach ($entry->tapers as $taper) : ?>
        <tr class="meds-taper col-gap">
            <td><i class="oe-i child-arrow small no-click pad-right "></i><em class="fade">then</em></td>
            <td><?php echo is_numeric($taper->dose) ? ($taper->dose . " " . $entry->dose_unit_term) : $taper->dose ?>
                <?php echo $taper->frequency->term; ?></td>
            <td class="nowrap">
            </td>
            <td><?php echo $taper->duration->name; ?></td>
            <td class="nowrap"><i class="oe-i spacer small pad-right"></i></td>
            <td class="nowrap"><i class="oe-i spacer small pad-right"></i></td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>