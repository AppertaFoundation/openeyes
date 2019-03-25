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
<tr>
    <td>
        <?php $this->widget('MedicationInfoBox', array('medication_id' => $entry->medication_id)); ?>
        <?= $entry->getMedicationDisplay(true) ?>
        <?php if($entry->prescribe): ?>
        &nbsp;<i class="oe-i drug-rx small" title="Prescribed"></i>
        <?php endif; ?>
    </td>
    <td>
        <?php echo $entry->getAdministrationDisplay(); ?>
    </td>
    <td><?php echo $entry->duration ? $entry->drugDuration->name : '' ?></td>
    <td>
        <?php if($entry->dispense_condition_id) { echo $entry->dispense_condition->name . " / " . $entry->dispense_location->name; } ?>
    </td>
    <td><?php $laterality = $entry->getLateralityDisplay(); ?>
        <i class="oe-i laterality small <?php echo $laterality == 'R' || $laterality == 'B' ? 'R' : 'NA' ?>"></i>
        <i class="oe-i laterality small <?php echo $laterality == 'L' || $laterality == 'B' ? 'L' : 'NA' ?>"></i>
    </td>
    <td><?= $entry->getStartDateDisplay() ?></td>
</tr>
<?php if($entry->taper_support): ?>
    <?php foreach ($entry->tapers as $taper): ?>
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
    <?php endforeach; ?>
<?php endif; ?>
