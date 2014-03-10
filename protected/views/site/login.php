<?php
	$this->pageTitle=Yii::app()->name . ' - Login';
?>

<div class="container content">
	<h1 class="badge">Please login</h1>
	<div class="row">
		<div class="large-11 large-centered column">

			<?php $form = $this->beginWidget('CActiveForm', array(
				'id'=>'loginform',
				'enableAjaxValidation'=>false,
				'htmlOptions'=>array(
					'class'=>'form panel login'
				)
			))?>

				<?php echo $form->error($model,'password',array('class'=>'alert-box alert')); ?>

				<div class="row field-row">
					<div class="large-4 column">
						<?php echo CHtml::activeLabel($model,'username', array('label'=>'Username:','class'=>'align')); ?>
					</div>
					<div class="large-8 column">
						<?php echo $form->textField($model,'username',array('placeholder'=>'Enter username...','class'=>'large')); ?>
					</div>
				</div>

				<div class="row field-row">
					<div class="large-4 column">
						<?php echo CHtml::activeLabel($model,'password', array('label'=>'Password:','class'=>'align')); ?>
					</div>
					<div class="large-8 column">
						<?php echo $form->passwordField($model,'password',array('autocomplete'=>'off','placeholder'=>'Enter password...','class'=>'large')); ?>
					</div>
				</div>

				<div class="row field-row text-right">
					<div class="large-12 column">

						<img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display:none" />

						<button type="submit" id="login_button" class="primary long">
							Login
						</button>
					</div>
				</div>
			<?php $this->endWidget(); ?>
		</div>
	</div>
</div>

<script type="text/javascript">
	if ($('#LoginForm_username').val() == '') {
		$('#LoginForm_username').focus();
	} else {
		$('#LoginForm_password').select().focus();
	}
	handleButton($('#login_button'));
</script>