<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>

<div class="eventHighlight">
	<div class="grid-view">
		<table id="prescription_items">
			<thead>
				<tr>
					<th>Drug</th>
					<th>Dose</th>
					<th>Route</th>
					<th>Frequency</th>
					<th>Duration</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($element->items as $key => $item) { ?>
				<tr	class="prescriptionItem<?php if ($this->patient->hasAllergy($item->drug_id)) { ?> allergyWarning<?php } ?> <?php echo (($key % 2) == 0) ? 'even' : 'odd'; ?>">
					<td class="prescriptionLabel"><?php echo $item->drug->tallmanlabel; ?></td>
					<td><?php echo $item->dose ?></td>
					<td><?php echo $item->route->name ?><?php if ($item->route_option) { echo ' ('.$item->route_option->name.')'; } ?></td>
					<td><?php echo $item->frequency->name ?></td>
					<td><?php echo $item->duration->name ?></td>
				</tr>
				<?php foreach ($item->tapers as $taper) { ?>
				<tr class="prescriptionTaper <?php echo (($key % 2) == 0) ? 'even' : 'odd'; ?>">
					<td class="prescriptionLabel"><span>then</span></td>
					<td><?php echo $taper->dose ?></td>
					<td></td>
					<td><?php echo $taper->frequency->name ?></td>
					<td><?php echo $taper->duration->name ?></td>
				</tr>
				<?php	} } ?>
			</tbody>
		</table>
	</div>
</div>
<input type="hidden" id="et_ophdrprescription_draft" value="<?php echo $element->draft?>" />
<input type="hidden" id="et_ophdrprescription_print" value="<?php echo $element->print?>" />
<?php if ($element->comments) { ?>
<h4>
	<?php echo CHtml::encode($element->getAttributeLabel('comments'))?>
</h4>
<div class="eventHighlight comments">
	<h4>
		<?php echo $element->textWithLineBreaks('comments')?>
	</h4>
</div>
<?php } ?>
