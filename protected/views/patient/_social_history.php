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
				<td><?= CHtml::encode($social_history->getAttributeLabel('occupation_id')) ?></td>
				<td><?php echo CHtml::encode($social_history->occupation->name)?></td>
			</tr>
		<?php }
		if (@!empty($social_history->type_of_job)){ ?>
			<tr>
				<td><?= CHtml::encode($social_history->getAttributeLabel('type_of_job')) ?></td>
				<td><?php echo CHtml::encode($social_history->type_of_job)?></td>
			</tr>
		<?php }
		if (!empty($social_history->driving_statuses)) {?>
			<tr>
				<td class="driving_statuses"><?= CHtml::encode($social_history->getAttributeLabel('driving_statuses')) ?></td>
				<td>
					<?php foreach ($social_history->driving_statuses as $item) {?>
						<?php echo $item->name?><br/>
					<?php }?>
				</td>
			</tr>
		<?php }
		if (isset($social_history->smoking_status)){ ?>
			<tr>
				<td><?= CHtml::encode($social_history->getAttributeLabel('smoking_status_id')) ?></td>
				<td><?php echo CHtml::encode($social_history->smoking_status->name)?></td>
			</tr>
		<?php }
		if (isset($social_history->accommodation)){ ?>
			<tr>
				<td><?= CHtml::encode($social_history->getAttributeLabel('accommodation_id')) ?></td>
				<td><?php echo CHtml::encode($social_history->accommodation->name)?></td>
			</tr>
		<?php }
		if (@!empty($social_history->comments)){ ?>
			<tr>
				<td><?= CHtml::encode($social_history->getAttributeLabel('comments')) ?></td>
				<td><?php echo CHtml::encode($social_history->comments)?></td>
			</tr>
		<?php }
		if (isset($social_history->carer)){ ?>
			<tr>
				<td><?= CHtml::encode($social_history->getAttributeLabel('carer_id')) ?></td>
				<td><?php echo CHtml::encode($social_history->carer->name)?></td>
			</tr>
		<?php }
		if (isset($social_history->alcohol_intake)){ ?>
			<tr>
				<td><?= CHtml::encode($social_history->getAttributeLabel('alcohol_intake')) ?></td>
				<td><?php echo CHtml::encode($social_history->alcohol_intake)?> units/week</td>
			</tr>
		<?php }
		if (isset($social_history->substance_misuse)){ ?>
			<tr>
				<td><?= CHtml::encode($social_history->getAttributeLabel('substance_misuse')) ?></td>
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
					<label for="occupation_id"><?= CHtml::encode($social_history->getAttributeLabel('occupation_id')) ?>:</label>
				</div>
				<div class="<?php echo $form->columns('field');?>">
					<?php echo CHtml::activeDropDownList($social_history, 'occupation_id' ,CHtml::listData(SocialHistoryOccupation::model()->findAll(array('order'=> 'display_order asc')),'id','name'),array('empty'=>'- Select -'))?>
				</div>
			</div>
			<div class="field-row row" id="social_history_type_of_job_show_hide" <?php if(@!$social_history->type_of_job->name=='Other (specify)'){?>style="display:none"<?php }?>>
				<div class="<?php echo $form->columns('label');?>">
					<label for="type_of_job"><?= CHtml::encode($social_history->getAttributeLabel('type_of_job')) ?>:</label>
				</div>
				<div class="<?php echo $form->columns('field');?>">
					<?php echo CHtml::activeTextField($social_history, 'type_of_job')?>
				</div>
			</div>
			<div class="field-row row">
				<div class="<?php echo $form->columns('label');?>">
					<label for="driving_statuses"><?= CHtml::encode($social_history->getAttributeLabel('driving_statuses')) ?>:</label>
				</div>
				<div class="<?php echo $form->columns('field');?>">
					<input type="hidden" name="SocialHistory[driving_statuses]" value="" />
					<?php
						$this->widget('application.widgets.MultiSelectList', array(
							'element' => $social_history,
							'field' => 'SocialHistory[driving_statuses]',
							'relation' => 'driving_status_assignments',
							'relation_id_field' => 'driving_status_id',
							'options' => CHtml::listData(SocialHistoryDrivingStatus::model()->findAll(array('order'=>'display_order asc')),'id','name'),
							'default_options' => array(),
							'htmlOptions' => array('empty' => '- Select -','label' => $social_history->getAttributeLabel('driving_statuses'),'nowrapper' => true),
							'hidden' => false,
							'inline' => false,
							'noSelectionsMessage' => null,
							'showRemoveAllLink' => false,
							'sorted' => false,
							'layoutColumns' => array('field' => 4)
						));
					?>
				</div>
			</div>
			<div class="field-row row">
				<div class="<?php echo $form->columns('label');?>">
					<label for="relative_id"><?= CHtml::encode($social_history->getAttributeLabel('smoking_status_id')) ?>:</label>
				</div>
				<div class="<?php echo $form->columns('field');?>">
					<?php echo CHtml::activeDropDownList($social_history,'smoking_status_id',CHtml::listData(SocialHistorySmokingStatus::model()->activeOrPk($social_history->smoking_status_id)->findAll(array('order'=> 'display_order asc')),'id','name'),array('empty'=>'- Select -'))?>
				</div>
			</div>
			<div class="field-row row">
				<div class="<?php echo $form->columns('label');?>">
					<label for="relative_id"><?= CHtml::encode($social_history->getAttributeLabel('accommodation_id')) ?>:</label>
				</div>
				<div class="<?php echo $form->columns('field');?>">
					<?php echo CHtml::activeDropDownList($social_history, 'accommodation_id',CHtml::listData(SocialHistoryAccommodation::model()->findAll(array('order'=> 'display_order asc')),'id','name'),array('empty'=>'- Select -'))?>
				</div>
			</div>
			<div class="field-row row">
				<div class="<?php echo $form->columns('label');?>">
					<label for="comments"><?= CHtml::encode($social_history->getAttributeLabel('comments')) ?>:</label>
				</div>
				<div class="<?php echo $form->columns('field');?>">
					<?php echo CHtml::activeTextArea($social_history,'comments')?>
				</div>
			</div>
			<div class="field-row row">
				<div class="<?php echo $form->columns('label');?>">
					<label for="carer_id"><?= CHtml::encode($social_history->getAttributeLabel('carer_id')) ?>:</label>
				</div>
				<div class="<?php echo $form->columns('field');?>">
					<?php echo CHtml::activeDropDownList($social_history, 'carer_id',CHtml::listData(SocialHistoryCarer::model()->findAll(array('order'=> 'display_order asc')),'id','name'),array('empty'=>'- Select -'))?>
				</div>
			</div>
			<div class="field-row row">
				<div class="<?php echo $form->columns('label');?>">
					<label for="relative_id"><?= CHtml::encode($social_history->getAttributeLabel('alcohol_intake')) ?>:</label>
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
					<label for="carer_id"><?= CHtml::encode($social_history->getAttributeLabel('substance_misuse')) ?>:</label>
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
			smokingStatus = $('#SocialHistory_smoking_status_id'),
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
			alertText = [];
			if(occupationIsOther() && textJobType.val() == '') {
				alertText.push("Please specify the 'Type of Job' for the employment status of 'Other'.")
			}
			if(!smokingStatus.val()) {
				alertText.push("Please specify the smoking status")
			}
			if(alertText.length > 0) {
				new OpenEyes.UI.Dialog.Alert({
					content: alertText.join("\n")
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
