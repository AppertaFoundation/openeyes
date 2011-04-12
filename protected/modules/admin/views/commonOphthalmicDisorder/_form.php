<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'common-ophthalmic-disorder-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'disorder_id'); ?>
<?php
if (isset($model->disorder)) {
	$term = $model->disorder->term;
} else {
	$term = '';
}

$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
    'name'=>'term',
    'value' => $term,
    'sourceUrl'=>array('//disorder/disorders'),
    'options'=>array(
        'minLength'=>'4',
    ),
    'htmlOptions'=>array(
        'style'=>'height:20px;',
        'value' => 'foo'
    ),
));
?>		<?php echo $form->error($model,'disorder_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'specialty_id'); ?>
		<?php echo $form->dropDownList($model,'specialty_id',$model->getSpecialtyOptions()); ?>
		<?php echo $form->error($model,'specialty_id'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->