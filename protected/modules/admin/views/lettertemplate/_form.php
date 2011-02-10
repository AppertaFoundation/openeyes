<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'lettertemplate-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'specialty_id'); ?>
		<?php echo $form->dropDownList($model,'specialty_id',$model->getSpecialtyOptions()); ?>
		<?php echo $form->error($model,'specialty_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>64)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'contacttype_id'); ?>
		<?php echo $form->dropDownList($model,'contacttype_id',$model->getContacttypeOptions()); ?>
		<?php echo $form->error($model,'contacttype_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'text'); ?>
		<?php echo $form->textArea($model,'text',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'text'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'cc'); ?>
		<?php echo $form->textField($model,'cc',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'cc'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->