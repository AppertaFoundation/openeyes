<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<table class="prescriptions">
    <thead>
    <tr>
        <th>Drug</th>
        <th>Dose</th>
        <th>Route</th>
        <th>Frequency</th>
        <th>Duration</th>
        <th>Dispense Condition/Location</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($element->items as $key => $item) {
        /** @var OphDrPrescription_Item $item */
        /* TODO need to rewrite Patient::hasDrugAllergy
        $additional_class = $this->patient->hasDrugAllergy($item->drug_id) ? ' allergyWarning' : '';
        */
        $additional_class = "";
        $additional_class .= (($key % 2) == 0) ? 'even' : ' odd';
        ?>
        <tr class="prescription-item<?= $additional_class ?>">
            <td class="prescription-label">
                <?php echo $item->refMedication->preferred_term; ?>
                <?php if(!is_null($item->comments)): ?>
                    <br/>
                    <i><?php echo CHtml::encode($item->comments); ?></i>
                <?php endif; ?>
            </td>
            <td><?php echo $item->dose . " " . $item->dose_unit_term; ?></td>
            <td><?php echo $item->route->term ?>
                <?php if ($item->laterality) {
                    echo ' (' . $item->refMedicationLaterality->name . ')';
                } ?>
            </td>
            <td><?php echo $item->frequency->term; ?></td>
            <td><?php echo $item->duration ? $item->drugDuration->name : '' ?></td>
            <td><?php echo $item->dispense_condition->name." / ".$item->dispense_location->name ?></td>
        </tr>
        <?php foreach ($item->tapers as $taper) { ?>
            <tr class="prescription-tapier <?php echo (($key % 2) == 0) ? 'even' : 'odd'; ?>">
                <td class="prescription-label"><span>then</span></td>
                <td><?php echo is_numeric($taper->dose) ? ($taper->dose . " " . $item->dose_unit_term) : $taper->dose ?></td>
                <td></td>
                <td><?php echo $taper->frequency->term ?></td>
                <td><?php echo $taper->duration->name ?></td>
            </tr>
        <?php }
    } ?>
    </tbody>
</table>

<input type="hidden" id="et_ophdrprescription_draft" value="<?php echo $element->draft ?>"/>
<input type="hidden" id="et_ophdrprescription_print" value="<?php echo $element->print ?>"/>

<?php if ($element->comments) { ?>
    <h3 class="element-title">
        <?php echo CHtml::encode($element->getAttributeLabel('comments')) ?>
    </h3>
    <div class="element-data">
        <div class="data-row">
            <div class="data-value">
                <?php echo $element->textWithLineBreaks('comments') ?>
            </div>
        </div>
    </div>
<?php } ?>
