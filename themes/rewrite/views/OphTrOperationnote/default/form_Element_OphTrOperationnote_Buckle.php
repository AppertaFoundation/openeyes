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

<section class="element <?php echo $element->elementType->class_name?> on-demand<?php if (@$ondemand) {?> hidden<?php }?><?php if ($this->action->id == 'update' && !$element->event_id) {?> missing<?php }?>"
	data-element-type-id="<?php echo $element->elementType->id ?>"
	data-element-type-class="<?php echo $element->elementType->class_name ?>"
	data-element-type-name="<?php echo $element->elementType->name ?>"
	data-element-display-order="<?php echo $element->elementType->display_order ?>">
	<?php if ($this->action->id == 'update' && !$element->event_id) {?>
		<div class="alert-box alert">This element is missing and needs to be completed</div>
	<?php }?>

	<header class="element-header">
		<h3 class="element-title"><?php  echo $element->elementType->name; ?></h3>
	</header>

	<?php
	$layoutColumns=$form->layoutColumns;
	$form->layoutColumns=array('label'=>3,'field'=>9);
	?>
	<div class="element-fields">
		<div class="row buckle">
			<div class="fixed column">
				<?php
				$this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
					'doodleToolBarArray' => array(
						0 => 'CircumferentialBuckle','EncirclingBand','RadialSponge','BuckleSuture','DrainageSite',
					),
					'onReadyCommandArray' => array(
						array('addDoodle', array('BuckleOperation')),
						array('deselectDoodles', array()),
					),
					'idSuffix'=>'Buckle',
					'side'=>$element->getSelectedEye()->getShortName(),
					'mode'=>'edit',
					'width'=>300,
					'height'=>300,
					'model'=>$element,
					'attribute'=>'eyedraw',
					'offsetX' => 10,
					'offsetY' => 10,
				));
				?>
			</div>
			<div class="fluid column">
				<?php echo $form->hiddenInput($element, 'report', $element->report)?>
				<?php echo $form->dropDownList($element, 'drainage_type_id', CHtml::listData(OphTrOperationnote_DrainageType::model()->findAll(), 'id', 'name'),array('empty'=>'- Please select -'))?>
				<?php echo $form->radioBoolean($element, 'drain_haem')?>
				<?php echo $form->radioBoolean($element, 'deep_suture')?>
				<?php echo $form->textArea($element, 'comments', array('rows' => 4, 'cols' => 60))?>
			</div>
		</div>
	</div>
</section>

<?php $form->layoutColumns=$layoutColumns;?>
