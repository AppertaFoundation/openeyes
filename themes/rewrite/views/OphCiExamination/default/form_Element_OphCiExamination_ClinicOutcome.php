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
<div id="div_<?php echo get_class($element)?>_status"
	class="eventDetail">
	<div class="label">
		<?php echo $element->getAttributeLabel('status_id') ?>:
	</div>
	<div class="data">
		<?php
		$html_options = array('empty'=>'- Please select -', 'options' => array());
		foreach (OphCiExamination_ClinicOutcome_Status::model()->findAll(array('order'=>'display_order')) as $opt) {
			$html_options['options'][(string) $opt->id] = array('data-followup' => $opt->followup);
		}
		echo CHtml::activeDropDownList($element,'status_id', CHtml::listData(OphCiExamination_ClinicOutcome_Status::model()->findAll(array('order'=>'display_order')),'id','name'), $html_options)?>
	</div>
</div>
<div id="div_<?php echo get_class($element)?>_followup"
	class="eventDetail"
	<?php if (!($element->status && $element->status->followup)) { ?>
	style="display: none;"
	<?php }?>
	>
	<div class="label">
		<?php echo $element->getAttributeLabel('followup_quantity')?>:
	</div>
	<div class="data">
		<?php
		$html_options = array('empty'=>'- Please select -', 'options' => array());
		echo CHtml::activeDropDownList($element,'followup_quantity', $element->getFollowUpQuantityOptions(), $html_options)?>
	</div>
	<div class="data">
		<?php
		$html_options = array('empty'=>'- Please select -', 'options' => array());
		echo CHtml::activeDropDownList($element,'followup_period_id', CHtml::listData(Period::model()->findAll(array('order'=>'display_order')),'id','name'), $html_options)?>
	</div>
	<div class="data" style="margin-left: 1em;">
		<?php echo CHtml::activeCheckBox($element,'community_patient')?>
		<?php echo $element->getAttributeLabel('community_patient')?>
	</div>
</div>
<div id="div_<?php echo get_class($element)?>_role"
	class="eventDetail"
	<?php if (!($element->status && $element->status->followup)) { ?>
	style="display: none;"
	<?php }?>>
	<div class="label">
		<?php echo $element->getAttributeLabel('role')?>:
	</div>
	<div class="data">
		<?php
		$html_options = array('empty'=>'- Please select -', 'options' => array());
		echo CHtml::activeDropDownList($element, 'role_id',
			CHtml::listData(OphCiExamination_ClinicOutcome_Role::model()->findAll(array('order'=>'display_order')),'id', 'name'),
			$html_options) ?>
	</div>
	<div class="data">
		<?php echo CHtml::activeTextField($element, 'role_comments')?>
	</div>
</div>
<script type="text/javascript">
		var Element_OphCiExamination_ClinicOutcome_templates = {
		<?php foreach (OphCiExamination_ClinicOutcome_Template::model()->findAll() as $template) { ?>
		"<?php echo $template->id?>": {
			"clinic_outcome_status_id": <?php echo $template->clinic_outcome_status_id ?>,
			"followup_quantity": "<?php echo $template->followup_quantity ?>",
			"followup_period_id": "<?php echo $template->followup_period_id ?>"
		},
		<?php } ?>
		};
</script>
