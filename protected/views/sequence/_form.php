<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'sequence-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary(array($model, $firm)); ?>

	<div class="row">
		<?php echo $form->labelEx($firm,'firm_id'); ?>
		<?php echo $form->dropDownList($firm,'firm_id',$firm->getFirmOptions(),
			array('empty' => '')); ?>
		<?php echo $form->error($firm,'firm_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'theatre_id'); ?>
		<?php echo $form->dropDownList($model,'theatre_id',$model->getTheatreOptions(),
			array('empty' => '')); ?>
		<?php echo $form->error($model,'theatre_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'start_date'); ?>
		<?php 
$this->widget('zii.widgets.jui.CJuiDatePicker', array(
	'model'=>$model,
	'attribute'=>'start_date',
    'value'=>$model->start_date,
    // additional javascript options for the date picker plugin
    'options'=>array(
        'showAnim'=>'fold',
		'minDate'=>'new Date()',
		'defaultDate'=>$model->start_date,
		'dateFormat'=>'yy-mm-dd'
    ),
    'htmlOptions'=>array(
        'style'=>'height:20px;'
    ),
)); ?>
		<?php echo $form->error($model,'start_date'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'start_time'); ?>
		<?php echo $form->textField($model,'start_time'); ?>
		<?php echo $form->error($model,'start_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'end_time'); ?>
		<?php echo $form->textField($model,'end_time'); ?>
		<?php echo $form->error($model,'end_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'end_date'); ?>
		<?php 
$this->widget('zii.widgets.jui.CJuiDatePicker', array(
	'model'=>$model,
	'attribute'=>'end_date',
    'value'=>$model->end_date,
    // additional javascript options for the date picker plugin
    'options'=>array(
        'showAnim'=>'fold',
		'minDate'=>'new Date()',
		'defaultDate'=>$model->end_date
    ),
    'htmlOptions'=>array(
        'style'=>'height:20px;'
    ),
)); ?>
		<?php echo $form->error($model,'end_date'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'frequency'); ?>
		<?php echo $form->dropDownList($model,'frequency',$model->getFrequencyOptions()); ?>
		<?php echo $form->error($model,'frequency'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->