<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'site-element-type-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'possible_element_type_id'); ?>
		<?php echo $form->textField($model,'possible_element_type_id',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'possible_element_type_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'specialty_id'); ?>
		<?php echo $form->textField($model,'specialty_id',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'specialty_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'view_number'); ?>
		<?php echo $form->textField($model,'view_number',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'view_number'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'default'); ?>
		<?php echo $form->textField($model,'default'); ?>
		<?php echo $form->error($model,'default'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'first_in_episode'); ?>
		<?php echo $form->textField($model,'first_in_episode'); ?>
		<?php echo $form->error($model,'first_in_episode'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->