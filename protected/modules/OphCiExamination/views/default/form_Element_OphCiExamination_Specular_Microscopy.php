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
<?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
<div class="element-fields row">
	<div class="large-2 column">
		<label><?php echo $element->getAttributeLabel('specular_microscope_id')?>:</label>
	</div>
	<div class="large-2 column">
		<?php
		$allSpecularMicroscope = \OEModule\OphCiExamination\models\OphCiExamination_Specular_Microscope::model()->findAll(array('order' => 'display_order'));
		echo CHtml::dropDownList('OEModule_OphCiExamination_models_Element_OphCiExamination_Specular_Microscopy[specular_microscope_id]',
			$element->specular_microscope_id,
			CHtml::listData($allSpecularMicroscope, 'id', 'name'), array('class' => 'MultiSelectList')); ?>
	</div>
	<div class="large-2 column"></div>
	<div class="large-2 column">
		<label><?php echo $element->getAttributeLabel('scan_quality_id')?>:</label>
	</div>
	<div class="large-2 column">
		<?php
		$allScanQuality = \OEModule\OphCiExamination\models\OphCiExamination_Scan_Quality::model()->findAll(array('order' => 'display_order'));
		echo CHtml::dropDownList('OEModule_OphCiExamination_models_Element_OphCiExamination_Specular_Microscopy[scan_quality_id]',
			$element->scan_quality_id,
			CHtml::listData($allScanQuality, 'id', 'name'), array('class' => 'MultiSelectList')); ?>
	</div>
	<div class="large-2 column"></div>
</div>
<div class="element-fields element-eyes row">
	<?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
<div class="element-eye right-eye column side left<?php if (!$element->hasRight()) {
?> inactive<?php
}?>" data-side="right">
		<div class="active-form">
			<a href="#" class="icon-remove-side remove-side">Remove side</a>

			<div class="row field-row">
				<div class="large-4 column">
					<label><?php echo $element->getAttributeLabel('right_endothelial_cell_density_value')?>:</label>
				</div>
				<div class="large-4 column">
				<?= $form->textField($element, "right_endothelial_cell_density_value", array('nowrapper' => true, 'size' => 6, 'maxlength' => 4)) ?>
				</div>
				<div class="large-4 column">
				</div>
			</div>

			<div class="row field-row">
				<div class="large-4 column">
					<label><?php echo $element->getAttributeLabel('right_coefficient_variation_value')?>:</label>
				</div>
				<div class="large-4 column">
					<?= $form->textField($element, "right_coefficient_variation_value", array('nowrapper' => true, 'size' => 6, 'maxlength' => 6)) ?>
				</div>
					<div class="large-4 column">
					</div>
				</div>
		</div>
		<div class="inactive-form">
			<div class="add-side">
				<a href="#">
					Add right side <span class="icon-add-side"></span>
				</a>
			</div>
		</div>
	</div>
<div class="element-eye left-eye column side right<?php if (!$element->hasLeft()) {
?> inactive<?php
}?>" data-side="left">
		<div class="active-form">
			<a href="#" class="icon-remove-side remove-side">Remove side</a>
			<div class="row field-row">
				<div class="large-4 column">
					<label><?php echo $element->getAttributeLabel('left_endothelial_cell_density_value')?>:</label>
				</div>
				<div class="large-4 column">
					<?= $form->textField($element, "left_endothelial_cell_density_value", array('nowrapper' => true, 'size' => 6, 'maxlength' => 4)) ?>
				</div>
				<div class="large-4 column">
				</div>
			</div>
			<div class="row field-row">
				<div class="large-4 column">
					<label><?php echo $element->getAttributeLabel('left_coefficient_variation_value')?>:</label>
				</div>
				<div class="large-4 column">
					<?= $form->textField($element, "left_coefficient_variation_value", array('nowrapper' => true, 'size' => 6, 'maxlength' => 6)) ?>
				</div>
				<div class="large-4 column">
				</div>
			</div>
			</div>
		<div class="inactive-form">
			<div class="add-side">
				<a href="#">
					Add left side <span class="icon-add-side"></span>
				</a>
			</div>
		</div>
	</div>
</div>