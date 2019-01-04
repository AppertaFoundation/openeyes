<?php
/* @var $this DisorderController */
/* @var $model Disorder */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'disorder-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'id'); ?>
		<?php echo $form->textField($model,'id',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'fully_specified_name'); ?>
		<?php echo $form->textField($model,'fully_specified_name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'fully_specified_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'term'); ?>
		<?php echo $form->textField($model,'term',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'term'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'last_modified_user_id'); ?>
		<?php echo $form->textField($model,'last_modified_user_id',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'last_modified_user_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'last_modified_date'); ?>
		<?php echo $form->textField($model,'last_modified_date'); ?>
		<?php echo $form->error($model,'last_modified_date'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'created_user_id'); ?>
		<?php echo $form->textField($model,'created_user_id',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'created_user_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'created_date'); ?>
		<?php echo $form->textField($model,'created_date'); ?>
		<?php echo $form->error($model,'created_date'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'specialty_id'); ?>
		<?php echo $form->textField($model,'specialty_id',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'specialty_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'active'); ?>
		<?php echo $form->textField($model,'active'); ?>
		<?php echo $form->error($model,'active'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->