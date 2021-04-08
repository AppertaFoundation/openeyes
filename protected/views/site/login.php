<?php
$this->pageTitle = ((string)SettingMetadata::model()->getSetting('use_short_page_titles') != "on" ? Yii::app()->name . ' - ' : '') . 'Login';
$settings = new SettingMetadata();
$tech_support_provider = Yii::App()->params['tech_support_provider'] ? htmlspecialchars(Yii::App()->params['tech_support_provider']) : htmlspecialchars($settings->getSetting('tech_support_provider'));
$tech_support_url = Yii::App()->params['tech_support_url'] ? htmlspecialchars(Yii::App()->params['tech_support_url']) : htmlspecialchars($settings->getSetting('tech_support_url'))
?>

<div class="oe-login">
  <div class="login">
    <h1>OpenEyes</h1>
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
      <div class="flex-layout">
        <span class="large-text"> Need Help?&nbsp;
          <?php  $purifier = new CHtmlPurifier(); ?>
          <?php if (Yii::app()->params['helpdesk_phone'] || Yii::app()->params['helpdesk_email']) : ?>
                <?= Yii::app()->params['helpdesk_phone'] ? "<strong>" . $purifier->purify(Yii::app()->params['helpdesk_phone']) . "</strong>" : null ?></strong>
                <?= Yii::app()->params['helpdesk_email'] ? "<br/>" . $purifier->purify(Yii::app()->params['helpdesk_email']) : null ?>
                <?= Yii::app()->params['helpdesk_hours'] ? "<br/> (" . $purifier->purify(Yii::app()->params['helpdesk_hours']) . ")" : null ?>
          <?php elseif ($tech_support_provider) : ?>
            <strong><a href="<?= $tech_support_url ?>" target="_blank"><?= $tech_support_provider ?></a></strong>
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