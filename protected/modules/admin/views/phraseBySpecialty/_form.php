<div class="form">

<?php 
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'phrase-by-specialty-form',
	'enableAjaxValidation'=>false,
)); 

if (isset($_GET['section_id'])) {
	$model->section_id = $_GET['section_id'];
}
?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'phrase'); ?>
		<?php echo $form->textArea($model,'phrase',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'phrase'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'section_id'); ?>
		<?php echo $form->dropDownList($model,'section_id',CHtml::listData(Section::Model()->getAllByType('Exam'), 'id', 'name')); ?>
		<?php echo $form->error($model,'section_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'display_order'); ?>
		<?php echo $form->textField($model,'display_order',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'display_order'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'specialty_id'); ?>
		<?php echo $form->dropDownList($model,'specialty_id',CHtml::listData(Specialty::Model()->findAll(), 'id', 'name')); ?>
		<?php echo $form->error($model,'specialty_id'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
