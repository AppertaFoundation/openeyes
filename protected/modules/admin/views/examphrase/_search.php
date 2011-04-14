<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'specialty_id'); ?>
		<?php echo $form->dropDownList($model,'specialty_id',$model->getSpecialtyOptions()); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'part'); ?>
		<?php echo $form->dropDownList($model,'part',$model->getPartOptions()); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'phrase'); ?>
		<?php echo $form->textField($model,'phrase',array('size'=>60,'maxlength'=>80)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'display_order'); ?>
		<?php echo $form->textField($model,'display_order',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->
