<?php
$this->pageTitle = Yii::app()->name . ' - Login';
?>

<div class="oe-login">
  <div class="login">
    <h1>OpenEyes <?=Yii::App()->params['oe_version']?></h1>
    <div class="user">

        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'loginform',
            'enableAjaxValidation' => false,
        )); ?>

        <?php echo $form->error($model, 'password', array('class' => 'alert-box error')); ?>
        <?php echo $form->textField($model, 'username', array(
            'autocomplete' => Yii::app()->params['html_autocomplete'],
            'placeholder' => 'Username',
        )); ?>

        <?php echo $form->passwordField($model, 'password',
            array('autocomplete' => 'off', 'placeholder' => 'Password')); ?>

      <i class="spinner" style="display:none"></i>

      <button type="submit" id="login_button" class="green hint">Login</button>

      <div class="oe-user-banner">
        <?php $this->renderPartial('//base/_banner_watermark_full'); ?>
      </div>

        <?php $this->endWidget(); ?>
      <!-- user -->
    </div>
    <div class="info">
      <div class="flex-layout">
        <span>Need Help?

            <?php if (Yii::app()->params['helpdesk_email']): ?>
              <?php echo Yii::app()->params['helpdesk_email'] ?>
            <?php endif; ?>

            <?php if (Yii::app()->params['helpdesk_phone']): ?>
              <strong><?php echo Yii::app()->params['helpdesk_phone'] ?></strong>
            <?php endif; ?>

            <?php if (Yii::app()->params['help_url']): ?>
              <?=\CHtml::link('Help Documentation', Yii::app()->params['help_url'],
                      array('target' => '_blank')) ?>
            <?php endif; ?>

        </span>
        <a href="#" onclick="$('#js-openeyes-btn').click();">About</a>
      </div>
      <!-- info -->
    </div>
    <!-- login -->
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
