<?php
$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->getClientScript();
$cs->registerCoreScript('jquery');
$cs->registerScriptFile($baseUrl.'/js/jquery.watermark.min.js');
$this->pageTitle=Yii::app()->name . ' - Login';
$this->layout = 'simple';
?>
		<h2 class="alert">Please login</h2>

		<div id="login-form" class="form_greyBox">
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'loginform',
			'enableAjaxValidation'=>false,
		))?>
		<?php echo $form->error($model,'password'); ?>
		<?php //<form action="/site/login" method="post"> ?>

			<div class="loginRow bigInput">
				<?php echo CHtml::activeLabel($model,'username', array('label'=>'Username:')); ?>
				<?php echo $form->textField($model,'username',array('tabindex' => 1)); ?>
				<a href="#" tabindex="5"><span class="small">Forgotten your username?</span></a>
			</div>

			<div class="loginRow bigInput">
				<?php echo CHtml::activeLabel($model,'password', array('label'=>'Password:')); ?>
				<?php echo $form->passwordField($model,'password',array('tabindex' => 2)); ?>
				<a href="#" tabindex="6"><span class="small">Forgotten your password?</span></a>
			</div>

			<div class="row">
				<?php echo CHtml::activeLabel($model,'siteId', array('label'=>'Site:')); ?>
				<?php echo $form->dropDownList($model, 'siteId', $sites, array('tabindex' => 3)); ?>
				<?php echo $form->error($model,'siteId'); ?>
			</div>

			<div class="row">
				<button type="submit" name="yt0" value="" class="btn_login ir" tabindex="4">Login</button>
			</div>

		<?php $this->endWidget(); ?>
	</div><!-- #login-form -->
	<script type="text/javascript">
		$('input[id=LoginForm_username]').watermark('enter username');
		$('input[id=LoginForm_password]').watermark('enter password');

		if ($('#LoginForm_username').val() == '') {
			$('#LoginForm_username').focus();
		} else {
			$('#LoginForm_password').select().focus();
		}
	</script>
