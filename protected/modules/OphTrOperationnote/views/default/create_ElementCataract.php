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

<div class="<?php echo $element->elementType->class_name?>">
	<h4 class="elementTypeName"><?php echo $element->elementType->name ?></h4>

	<div class="splitElement clearfix">
		<div class="left" style="width:60%;">
			<?php
			$this->widget('application.modules.eyeDraw.OEEyeDrawWidget', array(
				'identifier'=> 'Cataract',
				'template' => 'horizontal1',
				'side'=>'R',
				'mode'=>'edit',
				'size'=>300,
				'model'=>$element,
				'attribute'=>'eyedraw',
				'doodleToolBarArray'=>array('PhakoIncision','SidePort','IrisHook','PCIOL','ACIOL','PI','MattressSuture'),
				'onLoadedCommandArray'=>array(
					array('addDoodle', array('AntSeg')),
					array('deselectDoodles', array()),
				),
			));
			?>
			<?php echo $form->dropDownList($element, 'incision_site_id', CHtml::listData(IncisionSite::model()->findAll(), 'id', 'name'),array('layout' => 'horizontal', 'empty'=>'- Please select -', 'div_style' => 'margin-left: 320px; margin-top: 100px;'))?>
			<?php echo $form->textField($element, 'length', array('layout' => 'horizontal', 'div_style' => 'margin-left: 320px;', 'size' => '10'))?>
			<?php echo $form->textField($element, 'meridian', array('layout' => 'horizontal', 'div_style' => 'margin-left: 320px;', 'size' => '10'))?>
			<?php echo $form->dropDownList($element, 'incision_type_id', CHtml::listData(IncisionType::model()->findAll(), 'id', 'name'),array('layout' => 'horizontal', 'empty'=>'- Please select -', 'div_style' => 'margin-left: 320px;'))?>
			<?php echo $form->checkBox($element, 'vision_blue', array('layout' => 'horizontal', 'div_style' => 'margin-left: 320px;'))?>
			<?php echo $form->textArea($element, 'report', array('rows'=>8,'cols'=>50,'button'=>array('id'=>'generate_report','colour'=>'blue','size'=>'venti','label'=>'Generate',)))?>
		</div>
		<div class="right" style="width:40%;">
			<?php echo $form->dropDownList($element, 'iol_position_id', CHtml::listData(IOLPosition::model()->findAll(), 'id', 'name'),array('empty'=>'- Please select -'))?>
			<?php echo $form->checkBoxArray($element, 'Complications', array('wound_burn','iris_trauma','zonular_dialysis','pc_rupture','decentered_iol','iol_exchange','dropped_nucleus','op_cancelled','corneal_odema','iris_prolapse','zonular_rupture','vitreous_loss','iol_into_vitreous','other_iol_problem','choroidal_haem'),array('column_length' => 8))?>
		</div>
	</div>
</div>
