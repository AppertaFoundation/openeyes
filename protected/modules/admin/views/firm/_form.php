<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'firm-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'service_specialty_assignment_id'); ?>
		<?php echo $form->dropDownList($model,'service_specialty_assignment_id',$model->getServiceSpecialtyOptions()); ?>
		<?php echo $form->error($model,'service_specialty_assignment_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'pas_code'); ?>
		<?php echo $form->textField($model,'pas_code',array('size'=>4,'maxlength'=>4)); ?>
		<?php echo $form->error($model,'pas_code'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>40,'maxlength'=>40)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->