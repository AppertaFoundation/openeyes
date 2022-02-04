<div class="oe-login">
    <div class="login">
        <h1>OpenEyes e-Sign</h1>
        <div class="user">
            <?php $form = $this->beginWidget('CActiveForm', array(
                'id' => 'loginform',
                'enableAjaxValidation' => false,
            )); ?>
                <?php echo $form->error($model, 'pin', array('class' => 'alert-box alert')); ?>
                <?php echo $form->hiddenField($model, 'user_id') ?>
                <?php echo $form->passwordField($model, 'pin', array('autocomplete' => 'off', 'placeholder' => 'Enter PIN...', 'class' => 'large', 'autofocus' => true)); ?>
                <button type="submit" id="login_button" class="green hint">Link device</button>
            <?php $this->endWidget(); ?>
        </div>
        <div class="info">
            Enter your PIN to link this device to your OpenEyes
        </div>
    </div>
</div>
