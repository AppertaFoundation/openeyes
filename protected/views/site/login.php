<?php
$this->pageTitle = ((string)SettingMetadata::model()->getSetting('use_short_page_titles') != "on" ? Yii::app()->name . ' - ' : '') . 'Login';
$settings = new SettingMetadata();
$tech_support_provider = Yii::App()->params['tech_support_provider'] ? htmlspecialchars(Yii::App()->params['tech_support_provider']) : htmlspecialchars($settings->getSetting('tech_support_provider'));
$tech_support_url = Yii::App()->params['tech_support_url'] ? htmlspecialchars(Yii::App()->params['tech_support_url']) : htmlspecialchars($settings->getSetting('tech_support_url'));
?>

<div class="oe-login">

  <div class="login">
    <h1><b>OpenEyes&#8482;</b></h1>
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

      <?php echo $form->passwordField(
          $model,
          'password',
          array(
            'autocomplete' => 'off',
            'placeholder' => 'Password'
          )
      ); ?>

      <i class="spinner" style="display:none"></i>

      <button type="submit" id="login_button" class="green hint">Login</button>

      <div class="oe-user-banner">
        <?php $this->renderPartial('//base/_banner_watermark_full'); ?>
      </div>

      <?php $this->endWidget(); ?>
      <!-- user -->
    </div>
    <div class="info">
      <center>
      <a href="http://apperta.org" target="_blank"><img src="<?php echo Yii::app()->assetManager->createUrl('img/logo/logo.png') ?>" alt="Apperta Foundation CIC Logo" width="30%" style="padding-right:20px"></a>
       <a href="https://digitalpublicgoods.net/who-we-are/" target="_blank"><img src="<?php echo Yii::app()->assetManager->createUrl('img/logo/dpga_logo_2.svg') ?>" alt="Digital Public Good Alliance Logo" width="30%" style="padding-right:20px">
       <a href="https://digitalhealthatlas.org/" target="_blank"><img src="<?php echo Yii::app()->assetManager->createUrl('img/logo/logo-dha.svg') ?>" alt="Digital Health Atlas Logo" width="30%"></a></br></br>
        Copyright&#169; Apperta Foundation CIC <?= date('Y') ?>
    </center>
        <!--<a href="#" onclick="$('#js-openeyes-btn').click();">About</a>-->
      
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
