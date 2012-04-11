<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>

<h4><?php echo $element->elementType->name ?></h4>

<div class="cols2">
	<div class="right">
		<?php
		$this->widget('application.modules.eyeDraw.OEEyeDrawWidgetCataract', array(
			'side'=>$element->eye->getShortName(),
			'mode'=>'view',
			'size'=>200,
			'model'=>$element,
			'attribute'=>'eyedraw',
		));
		?>
		<?php
		$this->widget('application.modules.eyeDraw.OEEyeDrawWidgetSurgeonPosition', array(
			'side'=>$element->eye->getShortName(),
			'mode'=>'view',
			'size'=>200,
			'model'=>$element,
			'attribute'=>'eyedraw2',
		));
		?>
	</div>
	<div class="left">
		<div class="eventHighlight">
			<h4><?php echo CHtml::encode($element->getAttributeLabel('incision_site_id'))?>: <?php echo $element->incision_site->name?></h4>
		</div>
		<div class="eventHighlight">
			<h4><?php echo CHtml::encode($element->getAttributeLabel('length'))?>: <?php echo $element->length?></h4>
		</div>
		<div class="eventHighlight">
			<h4><?php echo CHtml::encode($element->getAttributeLabel('meridian'))?>: <?php echo $element->meridian?></h4>
		</div>
		<div class="eventHighlight">
			<h4><?php echo CHtml::encode($element->getAttributeLabel('incision_type_id'))?>: <?php echo $element->incision_type->name?></h4>
		</div>
	</div>
</div>

<h4>Cataract report</h4>
<div class="eventHighlight">
	<?php foreach (explode(chr(10),$element->report) as $line) {?>
		<h4><?php echo $line?></h4>
	<?php }?>
</div>

<h4>Cataract complications</h4>
<div class="eventHighlight">
	<?php if (!$element->complications) {?>
		<h4>None</h4>
	<?php } else {?>
		<?php foreach ($element->complications as $complication) {?>
			<h4><?php echo $complication->name?></h4>
		<?php }?>
	<?php }?>
</div>
