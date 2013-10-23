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

<h4><?php echo $element->elementType->name ?></h4>

<div class="cols2">
	<div class="right">
		<?php
		$this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
			'idSuffix'=>'Cataract',
			'side'=>$element->eye->getShortName(),
			'mode'=>'view',
			'width'=>200,
			'height'=>200,
			'model'=>$element,
			'attribute'=>'eyedraw',
		));
		?>
		<?php
		$this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
			'idSuffix'=>'Position',
			'side'=>$element->eye->getShortName(),
			'mode'=>'view',
			'width'=>200,
			'height'=>200,
			'model'=>$element,
			'attribute'=>'eyedraw2',
		));
		?>
	</div>
	<div class="left">
		<table class="subtleWhite normalText">
			<tbody>
				<tr>
					<td width="30%"><?php echo CHtml::encode($element->getAttributeLabel('incision_site_id'))?>:</td>
					<td><span class="big"><?php echo $element->incision_site->name?></span></td>
				</tr>
				<tr>
					<td><?php echo CHtml::encode($element->getAttributeLabel('length'))?>:</td>
					<td><span class="big"><?php echo $element->length?></span></td>
				</tr>
				<tr>
					<td><?php echo CHtml::encode($element->getAttributeLabel('meridian'))?>:</td>
					<td><span class="big"><?php echo $element->meridian?></span></td>
				</tr>
				<tr>
					<td><?php echo CHtml::encode($element->getAttributeLabel('incision_type_id'))?>:</td>
					<td><span class="big"><?php echo $element->incision_type->name?></span></td>
				</tr>
				<tr>
					<td><?php echo CHtml::encode($element->getAttributeLabel('report2'))?>:</td>
					<td><span class="big"><?php echo CHtml::encode($element->report2)?></span></td>
				</tr>
				<tr>
					<td><?php echo CHtml::encode($element->getAttributeLabel('predicted_refraction'))?>:</td>
					<td><span class="big"><?php echo CHtml::encode($element->predicted_refraction)?></span></td>
				</tr>
				<?php if ($element->getSetting('fife')) {?>
					<tr>
						<td><?php echo CHtml::encode($element->getAttributeLabel('intraocular_solution_id'))?>:</td>
						<td><span class="big"><?php echo $element->intraocular_solution ? $element->intraocular_solution->name : 'Not specified'?></span></td>
					</tr>
					<tr>
						<td><?php echo CHtml::encode($element->getAttributeLabel('skin_preparation_id'))?>:</td>
						<td><span class="big"><?php echo $element->skin_preparation ? $element->skin_preparation->name : 'Not specified'?></span></td>
					</tr>
				<?php }?>
			</tbody>
		</table>
	</div>
</div>

<div class="colsX clearfix">
	<div class="colThird">
		<h4>Cataract report</h4>
		<div class="eventHighlight medium">
			<h4>
				<?php foreach (explode(chr(10),CHtml::encode($element->report)) as $line) {?>
					<?php echo $line?><br/>
				<?php }?>
			</h4>
		</div>
	</div>

	<div class="colThird">
		<h4>Cataract devices</h4>
		<div class="eventHighlight medium">
			<?php if (!$element->operative_devices) {?>
				<h4>None</h4>
			<?php } else {?>
				<h4>
					<?php foreach ($element->operative_devices as $device) {?>
						<?php echo $device->name?><br/>
					<?php }?>
				</h4>
			<?php }?>
		</div>
	</div>

	<div class="colThird">
		<h4>Cataract complications</h4>
		<div class="eventHighlight medium">
			<?php if (!$element->complications && !$element->complication_notes) {?>
				<h4>None</h4>
			<?php } else {?>
				<h4>
					<?php foreach ($element->complications as $complication) {?>
						<?php echo $complication->name?><br/>
					<?php }?>
					<?php echo CHtml::encode($element->complication_notes)?>
				</h4>
			<?php }?>
		</div>
	</div>
</div>
