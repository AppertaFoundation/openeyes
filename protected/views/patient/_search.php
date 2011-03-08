<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'post',
)); ?>
	<div class="row">
		<?php echo $form->label($model,'first_name'); ?>
		<?php echo $form->textField($model,'first_name',array('size'=>40,'maxlength'=>40)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'last_name'); ?>
		<?php echo $form->textField($model,'last_name',array('size'=>40,'maxlength'=>40)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'dob'); ?>
		<?php echo $form->textField($model,'dob'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'gender'); ?>
		<?php echo $form->radioButtonList($model,'gender',array('M'=>'M','F'=>'F'),array(
			'template'=>'{input} {label}', 'separator' => ' &nbsp; ', 'labelOptions'=>array('style'=>'display:inline;float:none;')
		)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'hos_num'); ?>
		<?php echo $form->textField($model,'hos_num',array('size'=>40,'maxlength'=>40)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'nhs_num'); ?>
		<?php echo $form->textField($model,'nhs_num',array('size'=>40,'maxlength'=>40)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->