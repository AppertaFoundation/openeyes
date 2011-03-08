<?php
if ($this->showForm) {
?>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'base-form',
	'enableAjaxValidation'=>false,
	'action' => Yii::app()->createUrl('site')
)); ?>

	<?php echo CHtml::dropDownList('selected_firm_id', $this->selectedFirmId, $this->firms); ?>

	<?php echo CHtml::submitButton('Change Firm'); ?>
<?php
}
?>

<?php
	if (!Yii::app()->user->isGuest) {
?>
	Name: <b><?php echo Yii::app()->user->name ?></b>
<?php
}

if ($this->showForm) {
?>
	Selected firm: <b><?php echo($this->firms[$this->selectedFirmId]) ?></b>

<?php $this->endWidget(); ?>

<?php
}