<?php
	$this->pageTitle=Yii::app()->name . ' - Login';
	$this->layout = 'simple';
?>

<div class="container content">
	<h1 class="badge">Please login</h1>
	<div class="row">
		<div class="large-11 small-11 small-centered large-centered column">

			<?php $form = $this->beginWidget('CActiveForm', array(
				'id'=>'loginform',
				'enableAjaxValidation'=>false,
				'htmlOptions'=>array(
					'class'=>'form panel login'
				)
			))?>

				<?php echo $form->error($model,'password'); ?>


				<?php /*

			<div class="loginRow bigInput">
				<?php echo CHtml::activeLabel($model,'username', array('label'=>'Username:')); ?>
				<?php echo $form->textField($model,'username',array('tabindex' => 1)); ?>

			</div>

			<div class="loginRow bigInput">
				<?php echo CHtml::activeLabel($model,'password', array('label'=>'Password:')); ?>
				<?php echo $form->passwordField($model,'password',array('tabindex' => 2, 'autocomplete' => 'off')); ?>
				<?php if (Yii::app()->params['auth_source'] == 'BASIC') {?>
					<a href="#" tabindex="6"><span class="small">Forgotten your password?</span></a>
				<?php }?>
			</div>
*/?>

				<div class="row field-row">
					<div class="small-4 column">
						<?php echo CHtml::activeLabel($model,'username', array('label'=>'Username:')); ?>
					</div>
					<div class="small-8 column">
						<?php echo $form->textField($model,'username',array('tabindex' => 1, 'placeholder' => 'Enter username...')); ?>
						<?php if (Yii::app()->params['auth_source'] == 'BASIC') {?>
							<a href="#" tabindex="5"><span class="small">Forgotten your username?</span></a>
						<?php }?>
					</div>
				</div>

				<div class="row field-row">
					<div class="small-4 column">
						<?php echo CHtml::activeLabel($model,'password', array('label'=>'Password:')); ?>
					</div>
					<div class="small-8 column">
						<?php echo $form->passwordField($model,'password',array('tabindex' => 2, 'autocomplete' => 'off', 'placeholder' => 'Enter password...')); ?>
						<?php if (Yii::app()->params['auth_source'] == 'BASIC') {?>
							<a href="#" tabindex="6"><span class="small">Forgotten your password?</span></a>
						<?php }?>
					</div>
				</div>


				<div class="row field-row text-right">
					<div class="small-12 column">

						<img class="loader" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="margin-right: 10px; display: none;" />

						<button id="login_button" type="submit" tabindex="2">
							Login
						</button>
					</div>
				</div>
			<?php $this->endWidget(); ?>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('input[id=LoginForm_username]').watermark('enter username');
	$('input[id=LoginForm_password]').watermark('enter password');

	if ($('#LoginForm_username').val() == '') {
		$('#LoginForm_username').focus();
	} else {
		$('#LoginForm_password').select().focus();
	}
	handleButton($('#login_button'));
</script>


<?php
/*
		<h2 class="alert">Please login</h2>

		<div id="login-form" class="form_greyBox">

		<?php //<form action="/site/login" method="post"> ?>

			<div class="loginRow bigInput">
				<?php echo CHtml::activeLabel($model,'username', array('label'=>'Username:')); ?>
				<?php echo $form->textField($model,'username',array('tabindex' => 1)); ?>
				<?php if (Yii::app()->params['auth_source'] == 'BASIC') {?>
					<a href="#" tabindex="5"><span class="small">Forgotten your username?</span></a>
				<?php }?>
			</div>

			<div class="loginRow bigInput">
				<?php echo CHtml::activeLabel($model,'password', array('label'=>'Password:')); ?>
				<?php echo $form->passwordField($model,'password',array('tabindex' => 2, 'autocomplete' => 'off')); ?>
				<?php if (Yii::app()->params['auth_source'] == 'BASIC') {?>
					<a href="#" tabindex="6"><span class="small">Forgotten your password?</span></a>
				<?php }?>
			</div>

			<div class="row">
				<img class="loader" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="margin-right: 10px; display: none;" />
				<button id="login_button" type="submit" name="yt0" class="classy blue tall" tabindex="2"><span class="button-span button-span-blue">Login</span></button>
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

		handleButton($('#login_button'));
	</script>
*/?>