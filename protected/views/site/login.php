<?php
$this->pageTitle = Yii::app()->name . ' - Login';
?>

<div class="oe-login">
  <div class="login">
    <h1>OpenEyes 3.0</h1>
    <div class="user">

        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'loginform',
            'enableAjaxValidation' => false,
        )); ?>

        <?php echo $form->error($model, 'password', array('class' => 'alert-box alert')); ?>
        <?php echo $form->textField($model, 'username', array(
            'autocomplete' => Yii::app()->params['html_autocomplete'],
            'placeholder' => 'Username',
        )); ?>

        <?php echo $form->passwordField($model, 'password',
            array('autocomplete' => 'off', 'placeholder' => 'Password')); ?>

      <img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>"
           alt="loading..." style="display:none"/>

      <button type="submit" id="login_button" class="green hint">
        Login
      </button>

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
              <?php echo CHtml::link('Help Documentation', Yii::app()->params['help_url'],
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
