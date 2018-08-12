<?php
/* @var $this TrialController */
/* @var $model Trial */
/* @var $form CActiveForm */
?>

<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'trial-form',
        'enableAjaxValidation' => true,
    )); ?>

  <p class="note text-right">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

  <div class="row field-row">
    <div class="large-2 column">
        <?php echo $form->labelEx($model, 'name'); ?>
    </div>
    <div class="large-6 column end">
        <?php echo $form->textField($model, 'name', array('size' => 64, 'maxlength' => 64)); ?>
        <?php echo $form->error($model, 'name'); ?>
    </div>
  </div>
  <div class="row field-row">
    <div class="large-2 column">
        <?php echo $form->labelEx($model, 'external_data_link'); ?>
    </div>
    <div class="large-6 column end">
        <?php echo $form->urlField($model, 'external_data_link',
            array('size' => 100, 'maxlength' => 255, 'onblur' => 'checkUrl(this)')); ?>
        <?php echo $form->error($model, 'external_data_link'); ?>
    </div>
  </div>
  <div class="row field-row">
    <div class="large-2 column">
        <?php echo $form->labelEx($model, 'description'); ?>
    </div>
    <div class="large-6 column end">
        <?php echo $form->textArea($model, 'description'); ?>
        <?php echo $form->error($model, 'description'); ?>
    </div>
  </div>

    <?php if (!$model->getIsNewRecord()): ?>
      <div class="row field-row">
        <div class="large-2 column"><?php echo $form->labelEx($model, 'started_date'); ?></div>
        <div class="large-2 column">
            <?php
            if ((bool)strtotime($model->started_date)) {
                $dob = new DateTime($model->started_date);
                $model->started_date = $dob->format('d/m/Y');
            } else {
                $model->started_date = str_replace('-', '/', $model->started_date);
            }
            ?>
            <?php echo $form->textField($model, 'started_date'); ?>
            <?php echo $form->error($model, 'started_date'); ?>
        </div>
        <div class="large-2 column end"><label><i>(dd/mm/yyyy)</i></label></div>
      </div>

      <div class="row field-row">
        <div class="large-2 column"><?php echo $form->labelEx($model, 'closed_date'); ?></div>
        <div class="large-2 column">
            <?php
            if ((bool)strtotime($model->closed_date)) {
                $dob = new DateTime($model->closed_date);
                $model->closed_date = $dob->format('d/m/Y');
            } else {
                $model->closed_date = str_replace('-', '/', $model->closed_date);
            }
            ?>
            <?php echo $form->textField($model, 'closed_date'); ?>
            <?php echo $form->error($model, 'closed_date'); ?>
        </div>
        <div class="large-2 column end"><label><i>(dd/mm/yyyy)</i></label></div>
      </div>

    <?php endif; ?>

  <div class="row field-row">
    <div class="large-2 column">
        <?php echo $form->labelEx($model, 'trial_type'); ?>
    </div>
    <div class="large-2 column end">
        <?php foreach (Trial::model()->getTrialTypeOptions() as $trial_type => $type_label): ?>

          <label>
              <?php echo $form->radioButton($model, 'trial_type',
                  array('value' => $trial_type, 'uncheckValue' => null)); ?>
              <?php echo $type_label; ?>
          </label>
        <?php endforeach; ?>
    </div>
  </div>

  <div class="row buttons text-right">
      <?php echo CHtml::submitButton($model->getIsNewRecord() ? 'Create' : 'Save'); ?>
  </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->

<script>
  function checkUrl(urlField) {
    var urlText = urlField.value;
    if (urlText && urlText.indexOf("http") === -1) {
      urlText = "http://" + urlText;
    }

    urlField.value = urlText;
    return urlField;
  }
</script>