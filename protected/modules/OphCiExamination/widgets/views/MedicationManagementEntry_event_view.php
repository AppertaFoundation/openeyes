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
<tr class="divider col-gap <?= isset($stopped) && $stopped ? "fade": ""; ?>">
    <td rowspan="2">
        <i class="oe-i <?= $entry_icon ?> small pad-right "></i>
        <?= $entry->getMedicationDisplay(true) ?>
        <?php if ($this->mode !== static::$EVENT_PRINT_MODE) {
            $this->widget('MedicationInfoBox', array('medication_id' => $entry->medication_id));
        } ?>
    </td>
    <td>
        <?php echo $entry->getAdministrationDisplay(); ?>
        <?php $laterality = $entry->getLateralityDisplay(); ?>

        <?php if ($entry->route && $entry->route->has_laterality) {
            $laterality = $entry->getLateralityDisplay(); ?>
            <i class="oe-i laterality small <?php echo $laterality === 'R' || $laterality === 'B' ? 'R' : 'NA' ?>"></i>
            <i class="oe-i laterality small <?php echo $laterality === 'L' || $laterality === 'B' ? 'L' : 'NA' ?>"></i>
        <?php } ?>
    </td>
    <td>

        <?php echo $entry->duration ? $entry->drugDuration->name : '' ?>
        <?php if ($entry->dispense_condition_id) {
            echo $entry->dispense_condition->name . " / " . isset($entry->dispense_location) ? $entry->dispense_location->name : "";
        } ?>
    </td>
    <td>
        <?php if ($entry->prescribe && $entry->prescriptionItem) : ?>
            <i class="oe-i circle-green small js-has-tooltip" data-tooltip-content="Prescribed"></i>
        <?php endif; ?>
    </td>
    <td>
        <?php if ($entry->prescribe && $entry->prescriptionItem) : ?>
            <a href="<?= $entry->getPrescriptionLink(); ?>">
                <i class="oe-i direction-right-circle medium-icon js-has-tooltip" data-tooltip-content="View Prescription"></i>
            </a>
        <?php endif; ?>
    </td>
</tr>
<tr class="no-line col-gap <?=isset($stopped) && $stopped ? "fade": ""; ?>">
    <td>
        <div class="flex-meds-inputs">
                <span>
                <i class="oe-i start small pad"></i>
            <?= $entry->getStartDateDisplay() ?>
                    </span>
            <?php if (isset($stopped) && $stopped) { ?>
                <span>
            <i class="oe-i stop small pad"></i>
                <?= $entry->getEndDateDisplay() ?>
                </span>
            <?php } ?>
        </div>
    </td>
    <td>
        <i class="oe-i comments small no-click pad-right "></i>
        <?php if ($entry->comments) { ?>
            <span><?= $entry->comments; ?></span>
        <?php } else { ?>
            <span class="none">No comments</span>
        <?php } ?>
    </td>
    <td></td>
    <td></td>
</tr>
<?php if ($entry->taper_support) : ?>
    <?php foreach ($entry->tapers as $taper) : ?>
        <tr class="meds-taper col-gap <?= isset($stopped) && $stopped ? "fade" : "" ?>"  >
            <td>
                <i class="oe-i child-arrow small no-click pad"></i>
                <em class="fade">then</em>
            </td>
            <td>
                <?php echo is_numeric($taper->dose) ? ($taper->dose . " " . $entry->dose_unit_term) : $taper->dose ?>
                <?php echo $taper->frequency->term; ?>
            </td>
            <td>
                <?php echo $taper->duration->name; ?>
            </td>
            <td></td>
            <td></td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>
