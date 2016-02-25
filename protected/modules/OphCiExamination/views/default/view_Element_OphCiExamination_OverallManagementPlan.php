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
<div class="element-data">
	<div class="row data-row">
		<div class="large-5 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('clinic_interval_id'))?></div></div>
		<div class="large-7 column end"><div class="data-value"><?php echo $element->clinic_internal ? $element->clinic_internal->name : 'None'?></div></div>
	</div>
	<div class="row data-row">
		<div class="large-5 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('photo_id'))?></div></div>
		<div class="large-7 column end"><div class="data-value"><?php echo $element->photo ? $element->photo->name : 'None'?></div></div>
	</div>
	<div class="row data-row">
		<div class="large-5 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('oct_id'))?></div></div>
		<div class="large-7 column end"><div class="data-value"><?php echo $element->oct ? $element->oct->name : 'None'?></div></div>
	</div>
	<div class="row data-row">
		<div class="large-5 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('hfa_id'))?></div></div>
		<div class="large-7 column end"><div class="data-value"><?php echo $element->hfa ? $element->hfa->name : 'None'?></div></div>
	</div>
	<div class="row data-row">
		<div class="large-5 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('gonio_id'))?></div></div>
		<div class="large-7 column end"><div class="data-value"><?php echo $element->gonio ? $element->gonio->name : 'None'?></div></div>
	</div>
	<div class="row data-row">
		<div class="large-5 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('hrt_id'))?></div></div>
		<div class="large-7 column end"><div class="data-value"><?php echo $element->hrt ? $element->hrt->name : 'None'?></div></div>
	</div>
	<div class="row data-row">
		<div class="large-5 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('comments'))?></div></div>
		<div class="large-7 column end"><div class="data-value"><?php echo $element->textWithLineBreaks('comments')?></div></div>
	</div>
</div>
<div class="element-data element-eyes row">
	<div class="element-eye right-eye column">
		<div class="data-row">
			<div class="data-value">
				<?php if ($element->hasRight()) {
    ?>
					<div class="row data-row">
						<div class="large-5 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('right_target_iop_id'))?></div></div>
						<div class="large-7 column end"><div class="data-value" id="OEModule_OphCiExamination_models_Element_OphCiExamination_OverallManagementPlan_right_target_iop_id"><?php echo $element->right_target_iop->name?> mmHg</div></div>
					</div>

				<?php

} else {
    ?>
					Not recorded
				<?php 
}?>
			</div>
		</div>
	</div>
	<div class="element-eye left-eye column">
		<div class="data-row">
			<div class="data-value">
				<?php if ($element->hasLeft()) {
    ?>
					<div class="row data-row">
						<div class="large-5 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('left_target_iop_id'))?></div></div>
						<div class="large-7 column end"><div class="data-value" id="OEModule_OphCiExamination_models_Element_OphCiExamination_OverallManagementPlan_left_target_iop_id"><?php echo $element->left_target_iop->name?> mmHg</div></div>
					</div>
				<?php

} else {
    ?>
					Not recorded
				<?php 
}?>
			</div>
		</div>
	</div>
</div>
