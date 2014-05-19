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


			<div id="add_family_history">

				<?php
				$form = $this->beginWidget('FormLayout', array(
						'id'=>'add-family_history',
						'enableAjaxValidation'=>false,
						'htmlOptions' => array('class'=>'sliding form add-data'),
						'action'=>array('patient/addFamilyHistory'),
						'layoutColumns'=>array(
								'label' => 3,
								'field' => 9
						),
				))?>

				<fieldset class="field-row">

					<legend><strong>Add family history</strong></legend>

					<input type="hidden" name="edit_family_history_id" id="edit_family_history_id" value="" />
					<input type="hidden" name="patient_id" value="<?php echo $this->patient->id?>" />


					<?php echo CHtml::dropDownList('occupation_id','- Please select -', CHtml::listData(SocialHistoryOccupation::model()->findAll(array('order'=> 'display_order asc')),'id','name'))?>
					<?php echo CHtml::dropDownList('driving_status_id','- Please select -', CHtml::listData(SocialHistoryDrivingStatus::model()->findAll(array('order'=> 'display_order asc')),'id','name'))?>
					<?php echo CHtml::dropDownList('smoking_status_id','- Please select -', CHtml::listData(SocialHistorySmokingStatus::model()->findAll(array('order'=> 'display_order asc')),'id','name'))?>
					<?php echo CHtml::dropDownList('accommodation_id','- Please select -', CHtml::listData(SocialHistoryAccommodation::model()->findAll(array('order'=> 'display_order asc')),'id','name'))?>
					<?php echo CHtml::textArea ('comments')?>
					<?php echo CHtml::textField( 'type_of_job')?>
					<?php echo CHtml::dropDownList('carer','test',CHtml::listData(SocialHistoryAccommodation::model()->findAll(array('order'=> 'display_order asc')),'id','name'))?>
					<?php echo CHtml::textArea ( 'alcohol_intake')?>
					<?php echo CHtml::dropDownList('substance_misuse','test',CHtml::listData(SocialHistoryAccommodation::model()->findAll(array('order'=> 'display_order asc')),'id','name'))?>

					<div class="buttons">
						<img src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" class="add_family_history_loader" style="display: none;" />
						<button type="submit" class="secondary small btn_save_family_history">
							Save
						</button>
						<button class="warning small btn_cancel_family_history">
							Cancel
						</button>
					</div>

				</fieldset>
				<?php $this->endWidget()?>
			</div>
	</div>
</section>

<!-- Confirm deletion dialog -->
<div id="confirm_remove_family_history_dialog" title="Confirm remove family history" style="display: none;">
	<div id="delete_family_history">
		<div class="alert-box alert with-icon">
			<strong>WARNING: This will remove the family history from the patient record.</strong>
		</div>
		<p>
			<strong>Are you sure you want to proceed?</strong>
		</p>
		<div class="buttons">
			<input type="hidden" id="family_history_id" value="" />
			<button type="submit" class="warning small btn_remove_family_history">Remove family history</button>
			<button type="submit" class="secondary small btn_cancel_remove_family_history">Cancel</button>
			<img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
		</div>
	</div>
</div>

