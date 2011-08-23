<?php
if (!Yii::app()->user->isGuest) {
	if (!empty(Yii::app()->session['user'])) {
		$user = Yii::app()->session['user'];
	} else {
		$user = User::model()->findByPk(Yii::app()->user->id);
		Yii::app()->session['user'] = $user;
	} ?>
<div id="user_info">
You are logged in as: <strong><?php echo $user->first_name . ' ' . $user->last_name; ?></strong>

<?php $this->widget('zii.widgets.CMenu',array(
	'items'=>array(
		array('label'=>'Dashboard', 'url'=>array('/site/index'), 'visible'=>!Yii::app()->user->isGuest),
		array('label'=>'Theatre Management', 'url'=>array('/theatre'), 'visible'=>!Yii::app()->user->isGuest),
		// @todo: turn this on once we have account settings to manage
		array('label'=>'Account Settings', 'url'=>array('#'), 'visible'=>false),
		array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
		array('label'=>'Logout', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest)
	),
	'id' => 'navlist',
)); ?>
<br />
<?php
if ($this->showForm) {
	echo 'Selected firm: ';
	$form=$this->beginWidget('CActiveForm', array(
		'id'=>'base-form',
		'enableAjaxValidation'=>false,
		'action' => Yii::app()->createUrl('site')
	));
	echo CHtml::dropDownList('selected_firm_id', $this->selectedFirmId, $this->firms);
	$this->endWidget();
}
?>
</div>
<div class="clear"></div>
<?php
}