<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'diagnosis-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'ophthalmic'); ?>
		<?php echo $form->dropDownList($model,'common_ophthalmic_disorder_id',$model->getCommonOphthalmicDisorderOptions($this->firm), array('empty'=>'')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'systemic'); ?>
		<?php echo $form->dropDownList($model,'common_systemic_disorder_id',$model->getCommonSystemicDisorderOptions(), array('empty'=>'')); ?>
	</div>

	<div class="row">
		Or enter a disorder:<br />
<?php
$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
    'name'=>'term',
    'sourceUrl'=>array('disorder/disorders'),
    'options'=>array(
        'minLength'=>'4',
    ),
    'htmlOptions'=>array(
        'style'=>'height:20px;'
    ),
));
?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'location'); ?>
		<?php echo $form->dropDownList($model,'location',$model->getLocationoptions()); ?>
		<?php echo $form->error($model,'location'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->