<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>

<?php
//var_dump($element);
?>
<div class="element-fields row">
	<div class="large-3 column">
		<label><?php echo $element->getAttributeLabel('topographer_id')?>:</label>
	</div>
	<div class="large-3 column">
	<?php
	$allTopographerDevice = \OEModule\OphCiExamination\models\OphCiExamination_Topographer_device::model()->findAll(array('order' => 'display_order'));
	echo CHtml::dropDownList('topographer_id',
		null,
		CHtml::listData($allTopographerDevice, 'id', 'name'), array('class' => 'MultiSelectList')); ?>
	</div>
	<div class="large-2 column"></div>
	<div class="large-3 column">
		<label><?php echo $element->getAttributeLabel('tomographer_id')?>:</label>
	</div>
	<div class="large-3 column">
		<?php
		$allTomographerDevice = \OEModule\OphCiExamination\models\OphCiExamination_Tomographer_device::model()->findAll(array('order' => 'display_order'));
		echo CHtml::dropDownList('tomographer_id',
			null,
			CHtml::listData($allTomographerDevice, 'id', 'name'), array('class' => 'MultiSelectList')); ?>
	</div>
	<div class="large-2 column"></div>
</div>
<div class="element-fields row">
	<div class="large-3 column">
		<label><?php echo $element->getAttributeLabel('topographer_scan_quality_id')?>:</label>
	</div>
	<div class="large-3 column">
			<?php
			$allScanQuality = \OEModule\OphCiExamination\models\OphCiExamination_Scan_Quality::model()->findAll(array('order' => 'display_order'));
			echo CHtml::dropDownList('topographer_scan_quality_id',
				null,
				CHtml::listData($allScanQuality, 'id', 'name')); ?>
	</div>
	<div class="large-2 column"></div>
	<div class="large-3 column">
		<label><?php echo $element->getAttributeLabel('tomographer_scan_quality_id')?>:</label>
	</div>
	<div class="large-3 column">
		<?php
		$allScanQuality = \OEModule\OphCiExamination\models\OphCiExamination_Scan_Quality::model()->findAll(array('order' => 'display_order'));
		echo CHtml::dropDownList('tomographer_scan_quality_id',
			null,
			CHtml::listData($allScanQuality, 'id', 'name')); ?>
	</div>
	<div class="large-2 column"></div>
</div>

<div class="element-fields element-eyes row">
<?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
<div class="element-eye right-eye column side left" data-side="right">
		<div class="active-form">
			<a href="#" class="icon-remove-side remove-side">Remove side</a>
			<div class="row field-row">
				<div class="large-4 column">
					<label><?php echo $element->getAttributeLabel('right_anterior_k1_value')?>:</label>
				</div>
				<div class="large-4 column">
				<?= $form->textField($element, "right_anterior_k1_value", array('nowrapper' => true, 'size' => 2, 'maxlength' => 2)) ?>
				</div>
				<div class="large-4 column">
				</div>
			</div>
			<div class="row field-row">
				<div class="large-4 column">
					<label><?php echo $element->getAttributeLabel('right_axis_anterior_k1_value')?>:</label>
				</div>
				<div class="large-4 column">
					<?= $form->textField($element, "right_axis_anterior_k1_value", array('nowrapper' => true, 'size' => 3, 'maxlength' => 3)) ?>
				</div>
					<div class="large-4 column">
					</div>
				</div>
			<div class="row field-row">
				<div class="large-4 column">
					<label><?php echo $element->getAttributeLabel('right_anterior_k2_value')?>:</label>
				</div>
				<div class="large-4 column">
					<?= $form->textField($element, "right_anterior_k2_value", array('nowrapper' => true, 'size' => 2, 'maxlength' => 2)) ?>
				</div>
				<div class="large-4 column">
				</div>
			</div>
			<div class="row field-row">
				<div class="large-4 column">
					<label><?php echo $element->getAttributeLabel('right_axis_anterior_k2_value')?>:</label>
				</div>
				<div class="large-4 column">
					<?= $form->textField($element, "right_axis_anterior_k2_value", array('nowrapper' => true, 'size' => 3, 'maxlength' => 3)) ?>
				</div>
				<div class="large-4 column">
				</div>
			</div>
			<div class="row field-row">
				<div class="large-4 column">
					<label><?php echo $element->getAttributeLabel('right_kmax_value')?>:</label>
				</div>
				<div class="large-4 column">
					<?= $form->textField($element, "right_kmax_value", array('nowrapper' => true, 'size' => 2, 'maxlength' => 2)) ?>
				</div>
				<div class="large-4 column">
				</div>
			</div>
			<div class="row field-row">
				<div class="large-4 column">
					<label><?php echo $element->getAttributeLabel('right_posterior_k2_value')?>:</label>
				</div>
				<div class="large-4 column">
					<?= $form->textField($element, "right_posterior_k2_value", array('nowrapper' => true, 'size' => 3, 'maxlength' => 3)) ?>
				</div>
				<div class="large-4 column">
				</div>
			</div>
			<div class="row field-row">
				<div class="large-4 column">
					<label><?php echo $element->getAttributeLabel('right_thinnest_point_pachymetry_value')?>:</label>
				</div>
				<div class="large-4 column">
					<?= $form->textField($element, "right_thinnest_point_pachymetry_value", array('nowrapper' => true, 'size' => 2, 'maxlength' => 2)) ?>
				</div>
				<div class="large-4 column">
				</div>
			</div>
			<div class="row field-row">
				<div class="large-4 column">
					<label><?php echo $element->getAttributeLabel('right_b-a_index_value')?>:</label>
				</div>
				<div class="large-4 column">
					<?= $form->textField($element, "right_b-a_index_value", array('nowrapper' => true, 'size' => 3, 'maxlength' => 3)) ?>
				</div>
				<div class="large-4 column">
				</div>
			</div>
			<div class="row field-row">
				<div class="large-4 column">
					<label><?php echo $element->getAttributeLabel('keratoconus_stage_id')?>:</label>
				</div>
				<div class="large-4 column">
					<?php
					$allKeraStage = \OEModule\OphCiExamination\models\OphCiExamination_Keratoconus_Stage::model()->findAll(array('order' => 'display_order'));
					echo CHtml::dropDownList('keratoconus_stage_id',
						null,
						CHtml::listData($allKeraStage, 'id', 'name')); ?>
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
	<div class="element-eye left-eye column side right" data-side="left">

	<div class="active-form">
		<a href="#" class="icon-remove-side remove-side">Remove side</a>
		<div class="row field-row">
			<div class="large-4 column">
				<label><?php echo $element->getAttributeLabel('left_anterior_k1_value')?>:</label>
			</div>
			<div class="large-4 column">
				<?= $form->textField($element, "left_anterior_k1_value", array('nowrapper' => true, 'size' => 2, 'maxlength' => 2)) ?>
			</div>
			<div class="large-4 column">
			</div>
		</div>
		<div class="row field-row">
			<div class="large-4 column">
				<label><?php echo $element->getAttributeLabel('left_axis_anterior_k1_value')?>:</label>
			</div>
			<div class="large-4 column">
				<?= $form->textField($element, "left_axis_anterior_k1_value", array('nowrapper' => true, 'size' => 3, 'maxlength' => 3)) ?>
			</div>
			<div class="large-4 column">
			</div>
		</div>
		<div class="row field-row">
			<div class="large-4 column">
				<label><?php echo $element->getAttributeLabel('left_anterior_k2_value')?>:</label>
			</div>
			<div class="large-4 column">
				<?= $form->textField($element, "left_anterior_k2_value", array('nowrapper' => true, 'size' => 2, 'maxlength' => 2)) ?>
			</div>
			<div class="large-4 column">
			</div>
		</div>
		<div class="row field-row">
			<div class="large-4 column">
				<label><?php echo $element->getAttributeLabel('left_axis_anterior_k2_value')?>:</label>
			</div>
			<div class="large-4 column">
				<?= $form->textField($element, "left_axis_anterior_k2_value", array('nowrapper' => true, 'size' => 3, 'maxlength' => 3)) ?>
			</div>
			<div class="large-4 column">
			</div>
		</div>
		<div class="row field-row">
			<div class="large-4 column">
				<label><?php echo $element->getAttributeLabel('left_kmax_value')?>:</label>
			</div>
			<div class="large-4 column">
				<?= $form->textField($element, "left_kmax_value", array('nowrapper' => true, 'size' => 2, 'maxlength' => 2)) ?>
			</div>
			<div class="large-4 column">
			</div>
		</div>
		<div class="row field-row">
			<div class="large-4 column">
				<label><?php echo $element->getAttributeLabel('left_posterior_k2_value')?>:</label>
			</div>
			<div class="large-4 column">
				<?= $form->textField($element, "left_posterior_k2_value", array('nowrapper' => true, 'size' => 3, 'maxlength' => 3)) ?>
			</div>
			<div class="large-4 column">
			</div>
		</div>
		<div class="row field-row">
			<div class="large-4 column">
				<label><?php echo $element->getAttributeLabel('left_thinnest_point_pachymetry_value')?>:</label>
			</div>
			<div class="large-4 column">
				<?= $form->textField($element, "left_thinnest_point_pachymetry_value", array('nowrapper' => true, 'size' => 2, 'maxlength' => 2)) ?>
			</div>
			<div class="large-4 column">
			</div>
		</div>
		<div class="row field-row">
			<div class="large-4 column">
				<label><?php echo $element->getAttributeLabel('left_b-a_index_value')?>:</label>
			</div>
			<div class="large-4 column">
				<?= $form->textField($element, "left_b-a_index_value", array('nowrapper' => true, 'size' => 3, 'maxlength' => 3)) ?>
			</div>
			<div class="large-4 column">
			</div>
		</div>
		<div class="row field-row">
			<div class="large-4 column">
				<label><?php echo $element->getAttributeLabel('keratoconus_stage_id')?>:</label>
			</div>
			<div class="large-4 column">
				<?php
				$allKeraStage = \OEModule\OphCiExamination\models\OphCiExamination_Keratoconus_Stage::model()->findAll(array('order' => 'display_order'));
				echo CHtml::dropDownList('keratoconus_stage_id',
					null,
					CHtml::listData($allKeraStage, 'id', 'name')); ?>
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