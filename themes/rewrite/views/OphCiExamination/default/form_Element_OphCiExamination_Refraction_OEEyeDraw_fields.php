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
<fieldset class="row field-row">
	<legend class="large-3 column">
		<?php echo $element->getAttributeLabel($side.'_sphere')?>:
	</legend>
	<div class="large-9 column">
		<?php Yii::app()->getController()->renderPartial('_segmented_field', array('element' => $element, 'field' => $side.'_sphere'), false, false)?>
	</div>
</fieldset>
<fieldset class="row field-row">
	<legend class="large-3 column">
		<?php echo $element->getAttributeLabel($side.'_cylinder')?>:
	</legend>
	<div class="large-9 column">
		<?php Yii::app()->getController()->renderPartial('_segmented_field', array('element' => $element, 'field' => $side.'_cylinder'), false, false)?>
	</div>
</fieldset>
<div class="row field-row">
	<div class="large-3 column">
		<label for="<?php echo get_class($element).'_'.$side.'_axis';?>">
			<?php echo $element->getAttributeLabel($side.'_axis')?>:
		</label>
	</div>
	<div class="large-6 column end">
		<?php echo CHtml::activeTextField($element, $side.'_axis', array('class' => 'axis'))?>
	</div>
</div>
<div class="row field-row">
	<div class="large-3 column">
		<label for="<?php echo get_class($element).'_'.$side.'_type_id';?>">
			<?php echo $element->getAttributeLabel($side.'_type_id')?>:
		</label>
	</div>
	<div class="large-6 column end">
		<?php echo CHtml::activeDropDownList($element, $side.'_type_id', OphCiExamination_Refraction_Type::model()->getOptions(), array('class' => 'refractionType'))?>
		<?php echo CHtml::activeTextField($element, $side.'_type_other')?>
	</div>
</div>
