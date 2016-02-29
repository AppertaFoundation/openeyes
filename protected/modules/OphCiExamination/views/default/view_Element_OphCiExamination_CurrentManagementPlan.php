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

$iop = $element->getLatestIOP($this->patient);

$exam_api = Yii::app()->moduleAPI->get('OphCiExamination');
$targetIOP = $exam_api->getTargetIOP($this->patient);
?>
<div class="element-data element-eyes row">
	<script type="text/javascript">
		var previous_iop = <?php echo json_encode($iop);?>;
	</script>
	<div class="element-eye right-eye column">
		<div class="data-row">
			<div class="data-value">
				<?php if ($element->hasRight()) {
    ?>
					<div id="div_OEModule_OphCiExamination_models_Element_OphCiExamination_CurrentManagementPlan_right_iop_id" class="row field-row">
						<div class="large-5 column"><label>IOP:</label></div>
						<div class="large-7 column end" id="OEModule_OphCiExamination_models_Element_OphCiExamination_CurrentManagementPlan_right_iop"><?php
                            echo ($iop == null) ? 'N/A': $iop['rightIOP'].' mmHg';
    if (isset($targetIOP['right']) && !is_null($targetIOP['right']) && $iop['rightIOP'] > $targetIOP['right']) {
        ?> <span class="iop_notification error">*** IOP above target ***</span> <?php

    }
    ?></div>
					</div>
					<div class="row data-row">
						<div class="large-5 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('right_glaucoma_status_id'))?></div></div>
						<div class="large-7 column end"><div class="data-value"><?php echo $element->right_glaucoma_status ? $element->right_glaucoma_status->name : 'None'?></div></div>
					</div>
					<div class="row data-row">
						<div class="large-5 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('right_drop-related_prob_id'))?></div></div>
						<div class="large-7 column end"><div class="data-value"><?php echo $element->{'right_drop-related_prob'} ? $element->{'right_drop-related_prob'}->name : 'None'?></div></div>
					</div>
					<div class="row data-row">
						<div class="large-5 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('right_drops_id'))?></div></div>
						<div class="large-7 column end"><div class="data-value"><?php echo $element->right_drops ? $element->right_drops->name : 'None'?></div></div>
					</div>
					<div class="row data-row">
						<div class="large-5 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('right_surgery_id'))?></div></div>
						<div class="large-7 column end"><div class="data-value"><?php echo $element->right_surgery ? $element->right_surgery->name : 'N/A'?></div></div>
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
					<div id="div_OEModule_OphCiExamination_models_Element_OphCiExamination_CurrentManagementPlan_left_iop_id" class="row field-row">
						<div class="large-5 column"><label>IOP:</label></div>
						<div class="large-7 column end" id="OEModule_OphCiExamination_models_Element_OphCiExamination_CurrentManagementPlan_left_iop"><?php
                            echo ($iop == null) ? 'N/A' : $iop['leftIOP'].' mmHg';
    if (isset($targetIOP['left']) && !is_null($targetIOP['left']) && $iop['leftIOP'] > $targetIOP['left']) {
        ?> <span class="iop_notification error">*** IOP above target ***</span> <?php

    }
    ?></div>
					</div>
					<div class="row data-row">
						<div class="large-5 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('left_glaucoma_status_id'))?></div></div>
						<div class="large-7 column end"><div class="data-value"><?php echo $element->left_glaucoma_status ? $element->left_glaucoma_status->name : 'None'?></div></div>
					</div>
					<div class="row data-row">
						<div class="large-5 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('left_drop-related_prob_id'))?></div></div>
						<div class="large-7 column end"><div class="data-value"><?php echo $element->{'left_drop-related_prob'} ? $element->{'left_drop-related_prob'}->name : 'None'?></div></div>
					</div>
					<div class="row data-row">
						<div class="large-5 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('left_drops_id'))?></div></div>
						<div class="large-7 column end"><div class="data-value"><?php echo $element->left_drops ? $element->left_drops->name : 'None'?></div></div>
					</div>
					<div class="row data-row">
						<div class="large-5 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('left_surgery_id'))?></div></div>
						<div class="large-7 column end"><div class="data-value"><?php echo $element->left_surgery ? $element->left_surgery->name : 'N/A'?></div></div>
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
