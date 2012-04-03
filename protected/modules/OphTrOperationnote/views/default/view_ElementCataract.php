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

<h4 class="elementTypeName"><?php echo $element->elementType->name ?></h4>

<div class="view">

	<div class="col1">
		<div class="label"><?php echo CHtml::encode($element->getAttributeLabel('incision_site_id')); ?></div>
		<div class="eventHighlight"><?php echo $element->incision_site->name ?></div>
	</div>

	<div class="col1">
		<div class="label"><?php echo CHtml::encode($element->getAttributeLabel('length')); ?></div>
		<div class="eventHighlight"><?php echo $element->length; ?></div>
	</div>

	<div class="col1">
		<div class="label"><?php echo CHtml::encode($element->getAttributeLabel('meridian')); ?></div>
		<div class="eventHighlight"><?php echo $element->meridian; ?></div>
	</div>

	<div class="col1">
		<div class="label"><?php echo CHtml::encode($element->getAttributeLabel('incision_type_id')); ?></div>
		<div class="eventHighlight"><?php echo $element->incision_type->name ?></div>
	</div>

	<div class="col1">
		<div class="label"><?php echo CHtml::encode($element->getAttributeLabel('report')); ?></div>
		<div class="eventHighlight"><?php echo $element->report ?></div>
	</div>

	<h4>Complications</h4>
	<?php if (!$element->complications) {?>
		None
	<?php }else{
		foreach ($element->complications as $complication) {?>
			<?php echo $complication?><br/>
		<?php }
	}?>

	<div class="clearfix">
		<div class="left" style="width:60%;">
			<?php
			$this->widget('application.modules.eyeDraw.OEEyeDrawWidget', array(
				'identifier'=> 'Cataract',
				'template' => 'horizontal1',
				'side'=>'R',
				'mode'=>'view',
				'size'=>200,
				'model'=>$element,
				'attribute'=>'eyedraw',
				'doodleToolBarArray'=>array(),
				'onLoadedCommandArray'=>array(
					array('addDoodle', array('AntSeg')),
					array('deselectDoodles', array()),
				),
			));
			?>
			<?php
			$this->widget('application.modules.eyeDraw.OEEyeDrawWidget', array(
				'identifier'=> 'Position',
				'side'=>'R',
				'mode'=>'view',
				'size'=>200,
				'model'=>$element,
				'attribute'=>'eyedraw2',
				'doodleToolBarArray'=>array(),
				'onLoadedCommandArray'=>array(
					array('addDoodle', array('OperatingTable')),
					array('addDoodle', array('Surgeon')),
					array('deselectDoodles', array()),
				),
			));
			?>
		</div>
		<div class="right" style="width:40%;">
			<ul style="list-style-type: none; width:100%">
				<?php foreach (explode(',',$element->report) as $item) {?>
					<li>
						<?php echo trim($item)?>
					</li>
				<?php }?>
			</ul>
		</div>
	</div>
</div>
