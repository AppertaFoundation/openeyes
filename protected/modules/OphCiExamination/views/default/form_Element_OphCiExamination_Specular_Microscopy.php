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
<div class="element-fields">
	<div class="field-row row">
		<div class="large-12 column">
				<div id="add_allergy">

					<input type="hidden" name="patient_id" value="<?php echo $this->patient->id ?>"/>

					<div class="row field-row">
						<div class="<?php echo $form->columns('label'); ?>">
							<?php echo $element->getAttributeLabel('specular_microscope_id')?>:
						</div>
						<div class="<?php echo $form->columns('field'); ?>">
							<?php
							$allSpecularMicroscope = \OEModule\OphCiExamination\models\OphCiExamination_Specular_Microscope::model()->findAll(array('order' => 'display_order'));
							echo CHtml::dropDownList('specular_microscope_id',
								null,
								CHtml::listData($allSpecularMicroscope, 'id', 'name')); ?>
						</div>
					</div>
					<div class="row field-row">
						<div class="<?php echo $form->columns('label'); ?>">
							<?php echo $element->getAttributeLabel('scan_quality_id')?>:
						</div>
						<div class="<?php echo $form->columns('field'); ?>">
							<?php
							$allScanQuality = \OEModule\OphCiExamination\models\OphCiExamination_Scan_Quality::model()->findAll(array('order' => 'display_order'));
							echo CHtml::dropDownList('scan_quality_id',
								null,
								CHtml::listData($allScanQuality, 'id', 'name')); ?>
						</div>
					</div>
					<div class="row field-row">
						<div class="<?php echo $form->columns('label'); ?>">
							<?php echo $element->getAttributeLabel('endothelial_cell_density_value')?>:
						</div>
						<div class="<?php echo $form->columns('field'); ?>">
							<?php
							$allAllergies = \Allergy::model()->findAll(array('order' => 'display_order', 'condition' => 'active=1'));
							echo CHtml::dropDownList('allergy_id',
								null,
								CHtml::listData($allAllergies, 'id', 'name'), array('empty' => '-- Select --')); ?>
						</div>
					</div>
					<div class="row field-row">
						<div class="<?php echo $form->columns('label'); ?>">
							<?php echo $element->getAttributeLabel('coefficient_variations_value')?>:
						</div>
						<div class="<?php echo $form->columns('field'); ?>">
							<?php
							$allAllergies = \Allergy::model()->findAll(array('order' => 'display_order', 'condition' => 'active=1'));
							echo CHtml::dropDownList('allergy_id',
								null,
								CHtml::listData($allAllergies, 'id', 'name'), array('empty' => '-- Select --')); ?>
						</div>
					</div>




					<div class="buttons large-12 column">
						<img src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>" class="add_allergy_loader" style="display: none;"/>
						<button type="button" class="secondary small btn_save_allergy right">Add</button>
					</div>
				</div>
		</div>
	</div>
</div>
