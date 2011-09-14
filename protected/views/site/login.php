<?php
$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->getClientScript();
$cs->registerCoreScript('jquery');
$cs->registerScriptFile($baseUrl.'/js/jquery.watermark.min.js');
$this->pageTitle=Yii::app()->name . ' - Login';
$this->layout = 'simple';
?>
<div id="login">
	<div class="text">Login to OpenEyes:</div>

	<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'login-form',
		'enableAjaxValidation'=>false,
	));?>
		<?php echo $form->error($model,'password'); ?>

		<div class="row">
			<?php echo CHtml::activeLabel($model,'username', array('label'=>'Username:')); ?>
			<?php echo $form->textField($model,'username'); ?>
		</div>

		<div class="row">
			<?php echo CHtml::activeLabel($model,'password', array('label'=>'Password:')); ?>
			<?php echo $form->passwordField($model,'password'); ?>
		</div>

		<div class="row">
			<?php echo CHtml::activeLabel($model,'siteId', array('label'=>'Site:')); ?>
			<?php echo $form->dropDownList($model, 'siteId', $sites); ?>
			<?php echo $form->error($model,'siteId'); ?>
		</div>

		<div class="row buttons">
			<?php echo CHtml::submitButton(''); ?>
		</div>

	<?php $this->endWidget(); ?>
	</div><!-- form -->

	<div class="contact">Don't have a username and password? <span style="font-weight: normal;">Contact the helpdesk on:</span><br />
		Telephone: <span class="number">ext. 0000</span> Email: <span class="number">helpdesk@openeyes.org.uk</span>
	</div>
</div>
<script type="text/javascript">
	$('input[id=LoginForm_username]').watermark('enter username');
	$('input[id=LoginForm_password]').watermark('enter password');
</script>
