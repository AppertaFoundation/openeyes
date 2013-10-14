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
<tr data-key="<?php echo $key ?>" class="prescriptionItem<?php if ($patient->hasDrugAllergy($item->drug_id)) { ?> allergyWarning<?php } ?><?php if($item->getErrors()) { ?> errors<?php } ?> <?php echo ($key % 2) ? 'odd' : 'even'; ?>">
	<td class="prescriptionLabel">
		<?php echo $item->drug->tallmanlabel; ?>
		<?php if ($item->id) { ?><input type="hidden" name="prescription_item[<?php echo $key ?>][id]" value="<?php echo $item->id?>" /><?php } ?>
		<input type="hidden" name="prescription_item[<?php echo $key ?>][drug_id]" value="<?php echo $item->drug_id?>" />
	</td>
	<td class="prescriptionItemDose">
		<?php echo CHtml::textField('prescription_item['.$key.'][dose]', $item->dose) ?>
	</td>
	<td>
		<?php echo CHtml::dropDownList('prescription_item['.$key.'][route_id]', $item->route_id, CHtml::listData($item->availableRoutes(), 'id', 'name'), array('empty' => '-- Select --', 'class' => 'drugRoute')); ?>
	</td>
	<td>
		<?php if ($item->route && $options = $item->route->options) {
			echo CHtml::dropDownList('prescription_item['.$key.'][route_option_id]', $item->route_option_id, CHtml::listData($options, 'id', 'name'), array('empty' => '-- Select --'));
		} else {
			echo '-';
		}?>
	</td>
	<td class="prescriptionItemFrequencyId">
		<?php echo CHtml::dropDownList('prescription_item['.$key.'][frequency_id]', $item->frequency_id, CHtml::listData($item->availableFrequencies(), 'id', 'name'), array('empty' => '-- Select --')); ?>
	</td>
	<td class="prescriptionItemDurationId">
		<?php echo CHtml::dropDownList('prescription_item['.$key.'][duration_id]', $item->duration_id, CHtml::listData($item->availableDurations(), 'id', 'name'), array('empty' => '-- Select --')); ?>
	</td>
	<td class="prescriptionItemActions">
		<a class="removeItem"	href="#">Remove</a>
		| <a class="taperItem"	href="#">+Taper</a>
	</td>
</tr>
<?php
	$count = 0;
	foreach ($item->tapers as $taper) {
?>
<tr data-key="<?php echo $key ?>" data-taper="<?php echo $count ?>" class="prescriptionTaper <?php echo ($key % 2) ? 'odd' : 'even'; ?>">
	<td class="prescriptionLabel">
		<span>then</span>
		<?php if ($taper->id) { ?><input type="hidden" name="prescription_item[<?php echo $key ?>][taper][<?php echo $count ?>][id]" value="<?php echo $taper->id?>" /><?php } ?>
	</td>
	<td>
		<?php echo CHtml::textField('prescription_item['.$key.'][taper]['.$count.'][dose]', $taper->dose) ?>
	</td>
	<td></td>
	<td></td>
	<td>
		<?php echo CHtml::dropDownList('prescription_item['.$key.'][taper]['.$count.'][frequency_id]', $taper->frequency_id, CHtml::listData($item->availableFrequencies(), 'id', 'name'), array('empty' => '-- Select --')); ?>
	</td>
	<td>
		<?php echo CHtml::dropDownList('prescription_item['.$key.'][taper]['.$count.'][duration_id]', $taper->duration_id, CHtml::listData($item->availableDurations(), 'id', 'name'), array('empty' => '-- Select --')); ?>
	</td>
	<td class="prescriptionItemActions">
		<a class="removeTaper"	href="#">Remove</a>
	</td>
</tr>
<?php
		$count++;
	}
?>
