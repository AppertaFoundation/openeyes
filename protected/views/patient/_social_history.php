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
<section class="box patient-info associated-data js-toggle-container">
<header class="box-header">
	<h3 class="box-title">
		<span class="icon-patient-clinician-hd_flag"></span>
		Social History
	</h3>
	<a href="#" class="toggle-trigger toggle-hide js-toggle">
			<span class="icon-showhide">
			Show/hide this section
			</span>
	</a>
</header>
<div class="js-toggle-body">
<?php if ($this->checkAccess('OprnEditSocialHistory')) {?>
	<?php
	$this->patient->id;
	$social_history = SocialHistory::model()->find('patient_id=?',array($this->patient->id));
	?>
	<table class="plain patient-data">
		<thead>
		<tr>
			<th>Social History</th>
			<th>Status</th>
		</tr>
		</thead>
		<tbody>
		<?php if (isset($social_history->occupation)){ ?>
			<tr>
				<td>Occupation</td>
				<td><?php echo CHtml::encode($social_history->occupation->name)?></td>
			</tr>
		<?php }
		if (@!empty($social_history->type_of_job)){ ?>
			<tr>
				<td>Type of Job</td>
				<td><?php echo CHtml::encode($social_history->type_of_job)?></td>
			</tr>
		<?php }
		if (isset($social_history->driving_status)){ ?>
			<tr>
				<td>Driving Status</td>
				<td><?php echo CHtml::encode($social_history->driving_status->name)?></td>
			</tr>
		<?php }
		if (isset($social_history->smoking_status)){ ?>
			<tr>
				<td>Smoking Status</td>
				<td><?php echo CHtml::encode($social_history->smoking_status->name)?></td>
			</tr>
		<?php }
		if (isset($social_history->accommodation)){ ?>
			<tr>
				<td>Accommodation</td>
				<td><?php echo CHtml::encode($social_history->accommodation->name)?></td>
			</tr>
		<?php }
		if (@!empty($social_history->comments)){ ?>
			<tr>
				<td>Comments</td>
				<td><?php echo CHtml::encode($social_history->comments)?></td>
			</tr>
		<?php }
		if (isset($social_history->carer)){ ?>
			<tr>
				<td>Carer</td>
				<td><?php echo CHtml::encode($social_history->carer->name)?></td>
			</tr>
		<?php }
		if (isset($social_history->alcohol_intake)){ ?>
			<tr>
				<td>Alcohol Intake</td>
				<td><?php echo CHtml::encode($social_history->alcohol_intake)?> units/week</td>
			</tr>
		<?php }
		if (isset($social_history->substance_misuse)){ ?>
			<tr>
				<td>Substance Misuse</td>
				<td><?php echo CHtml::encode($social_history->substance_misuse->name)?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	<div class="box-actions">
		<button  id="btn-add_social_history" class="secondary small">
			Edit
		</button>
	</div>

	<div id="add_social_history" style="display: none;">

		<?php
		$form = $this->beginWidget('FormLayout', array(
			'id'=>'add-previous_operation',
			'enableAjaxValidation'=>false,
			'htmlOptions' => array('class'=>'form add-data'),
			'action'=>array('patient/editSocialHistory'),
			'layoutColumns'=>array(
				'label' => 3,
				'field' => 9
			),
		));

		if(!$social_history)	$social_history = new SocialHistory();
		?>
		<fieldset class="field-row">
			<legend><strong>Social History</strong></legend>
			<input type="hidden" name="edit_operation_id" id="edit_operation_id" value="" />
			<input type="hidden" name="patient_id" value="<?php echo $this->patient->id?>" />
			<div class="field-row row">
				<div class="<?php echo $form->columns('label');?>">
					<label for="occupation_id">Occupation:</label>
				</div>
				<div class="<?php echo $form->columns('field');?>">
					<?php echo CHtml::activeDropDownList($social_history, 'occupation_id' ,CHtml::listData(SocialHistoryOccupation::model()->findAll(array('order'=> 'display_order asc')),'id','name'),array('empty'=>'- Select -'))?>
				</div>
			</div>
			<div class="field-row row" id="social_history_type_of_job_show_hide" <?php if(@!$social_history->type_of_job->name=='Other (specify)'){?>style="display:none"<?php }?>>
				<div class="<?php echo $form->columns('label');?>">
					<label for="type_of_job">Type of Job:</label>
				</div>
				<div class="<?php echo $form->columns('field');?>">
					<?php echo CHtml::activeTextField($social_history, 'type_of_job')?>
				</div>
			</div>
			<div class="field-row row">
				<div class="<?php echo $form->columns('label');?>">
					<label for="relative_id">Driving Status:</label>
				</div>
				<div class="<?php echo $form->columns('field');?>">
					<?php echo CHtml::activeDropDownList($social_history,'driving_status_id',CHtml::listData(SocialHistoryDrivingStatus::model()->findAll(array('order'=> 'display_order asc')),'id','name'),array('empty'=>'- Select -'))?>
				</div>
			</div>
			<div class="field-row row">
				<div class="<?php echo $form->columns('label');?>">
					<label for="relative_id">Smoking Status:</label>
				</div>
				<div class="<?php echo $form->columns('field');?>">
					<?php echo CHtml::activeDropDownList($social_history,'smoking_status_id',CHtml::listData(SocialHistorySmokingStatus::model()->findAll(array('order'=> 'display_order asc')),'id','name'),array('empty'=>'- Select -'))?>
				</div>
			</div>
			<div class="field-row row">
				<div class="<?php echo $form->columns('label');?>">
					<label for="relative_id">Accommodation:</label>
				</div>
				<div class="<?php echo $form->columns('field');?>">
					<?php echo CHtml::activeDropDownList($social_history, 'accommodation_id',CHtml::listData(SocialHistoryAccommodation::model()->findAll(array('order'=> 'display_order asc')),'id','name'),array('empty'=>'- Select -'))?>
				</div>
			</div>
			<div class="field-row row">
				<div class="<?php echo $form->columns('label');?>">
					<label for="comments">Comments:</label>
				</div>
				<div class="<?php echo $form->columns('field');?>">
					<?php echo CHtml::activeTextArea($social_history,'comments')?>
				</div>
			</div>
			<div class="field-row row">
				<div class="<?php echo $form->columns('label');?>">
					<label for="carer_id">Carer:</label>
				</div>
				<div class="<?php echo $form->columns('field');?>">
					<?php echo CHtml::activeDropDownList($social_history, 'carer_id',CHtml::listData(SocialHistoryCarer::model()->findAll(array('order'=> 'display_order asc')),'id','name'),array('empty'=>'- Select -'))?>
				</div>
			</div>
			<div class="field-row row">
				<div class="<?php echo $form->columns('label');?>">
					<label for="relative_id">Alcohol Intake:</label>
				</div>
				<div class="large-2 column">
					<?php echo CHtml::activeTextField($social_history, 'alcohol_intake')?>
				</div>
				<div class="large-3 column end">
					<p>units/week</p>
				</div>
			</div>
			<div class="field-row row">
				<div class="<?php echo $form->columns('label');?>">
					<label for="carer_id">Substance Misuse:</label>
				</div>
				<div class="<?php echo $form->columns('field');?>">
					<?php echo CHtml::activeDropDownList($social_history, 'substance_misuse_id',CHtml::listData(SocialHistorySubstanceMisuse::model()->findAll(array('order'=> 'display_order asc')),'id','name'),array('empty'=>'- Select -'))?>
				</div>
			</div>
			<div class="previous_operations_form_errors alert-box alert hide"></div>
			<div class="buttons">
				<img src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" class="add_previous_operation_loader" style="display: none;" />
				<button type="submit" class="secondary small btn_save_social_history">
					Save
				</button>
				<button class="warning small btn_cancel_social_history">
					Cancel
				</button>
			</div>
		</fieldset>
		<?php $this->endWidget()?>
	</div>
<?php }?>
</div>
</section>

<script type="text/javascript">
$(function () {

    var btnAdd = $('#btn-add_social_history'),
	toggleAdd = $('#add_social_history'),
	btnSave = $('.btn_save_social_history'),
	btnCancel = $('.btn_cancel_social_history'),
	selectOccupation = $('#SocialHistory_occupation_id'),
	toggleJobType = $('#social_history_type_of_job_show_hide'),
	textJobType = $('#SocialHistory_type_of_job'),
	occupationIsOther = function() {
	    return $('#SocialHistory_occupation_id option:selected').text() == 'Other (specify)'; 
	},
	setJobType = function() {
            if (occupationIsOther()) {
		toggleJobType.show();
		textJobType.focus();
            } else {
		toggleJobType.hide();
		textJobType.val('');
            }
	};

    selectOccupation.change(setJobType);
    setJobType();		// need to also update on first run

    btnSave.click(function() {
	if(occupationIsOther() && textJobType.val() == '') {
	    new OpenEyes.UI.Dialog.Alert({
		content: "Please specify the 'Type of Job' for the occupation of 'Other'."
	    }).open();
	    return false;
	}
	return true;
    });

    btnAdd.click(function(event) {
        event.preventDefault();
        toggleAdd.slideToggle('fast');
        btnAdd.attr('disabled', true).addClass('disabled');
    });

    btnCancel.click(function(event) {
        event.preventDefault();
        toggleAdd.slideToggle('fast');
        btnAdd.attr('disabled', false).removeClass('disabled');
    });

});
</script>
