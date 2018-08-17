<?php
/* @var $this TrialController */
/* @var $model Trial */
/* @var $form CActiveForm */
?>

<nav class="event-header ">
    <?php if (!$model->getIsNewRecord()): ?>
        <?= CHtml::link('View', $this->createUrl('view', array('id' => $model->id)),
            array('class' => 'button header-tab')) ?>
    <?php endif; ?>
  <a class="button header-tab selected">Edit</a>

  <div class="buttons-right">
    <button class="button header-tab green" name="save" type="submit" form="trial-form">
        <?= $model->getIsNewRecord() ? 'Create' : 'Save' ?>
    </button>

      <?= CHtml::link(
          'Cancel',
          $model->getIsNewRecord() ? $this->createUrl('index') : $this->createUrl('view', array('id' => $model->id)),
          array('class' => 'button header-tab red')
      ) ?>
  </div>
</nav>

<main class="main-event edit">
  <h2 class="event-title"><?= $model->getIsNewRecord() ? 'Create Trial' : 'Edit Trial' ?></h2>

  <section class="element edit full edit-xxx">
    <div class="element-fields">
        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'trial-form',
            'enableAjaxValidation' => true,
        )); ?>

        <?= $form->errorSummary($model) ?>
      <div class="row field-row">
        <div class="large-2 column">
            <?= $form->labelEx($model, 'name') ?>
        </div>
        <div class="large-6 column end">
            <?= $form->textField($model, 'name', array('size' => 64, 'maxlength' => 64)) ?>
            <?= $form->error($model, 'name') ?>
        </div>
      </div>
      <div class="row field-row">
        <div class="large-2 column">
            <?= $form->labelEx($model, 'external_data_link') ?>
        </div>
        <div class="large-6 column end">
            <?= $form->urlField($model, 'external_data_link',
                array('size' => 100, 'maxlength' => 255, 'onblur' => 'checkUrl(this)')); ?>
            <?= $form->error($model, 'external_data_link') ?>
        </div>
      </div>
      <div class="row field-row">
        <div class="large-2 column">
            <?= $form->labelEx($model, 'description') ?>
        </div>
        <div class="large-6 column end">
            <?= $form->textArea($model, 'description') ?>
            <?= $form->error($model, 'description') ?>
        </div>
      </div>

        <?php if (!$model->getIsNewRecord()): ?>
          <div class="row field-row">
            <div class="large-2 column"><?= $form->labelEx($model, 'started_date') ?></div>
            <div class="large-2 column">
                <?php
                if ((bool)strtotime($model->started_date)) {
                    $dob = new DateTime($model->started_date);
                    $model->started_date = $dob->format('d/m/Y');
                } else {
                    $model->started_date = str_replace('-', '/', $model->started_date);
                }
                ?>
                <?= $form->textField($model, 'started_date') ?>
                <?= $form->error($model, 'started_date') ?>
            </div>
            <div class="large-2 column end"><label><i>(dd/mm/yyyy)</i></label></div>
          </div>

          <div class="row field-row">
            <div class="large-2 column"><?= $form->labelEx($model, 'closed_date') ?></div>
            <div class="large-2 column">
                <?php
                if ((bool)strtotime($model->closed_date)) {
                    $dob = new DateTime($model->closed_date);
                    $model->closed_date = $dob->format('d/m/Y');
                } else {
                    $model->closed_date = str_replace('-', '/', $model->closed_date);
                }
                ?>
                <?= $form->textField($model, 'closed_date') ?>
                <?= $form->error($model, 'closed_date') ?>
            </div>
            <div class="large-2 column end"><label><i>(dd/mm/yyyy)</i></label></div>
          </div>

        <?php endif; ?>

      <div class="row field-row">
        <div class="large-2 column">
            <?= $form->labelEx($model, 'trial_type') ?>
        </div>
        <div class="large-2 column end">
            <?php foreach (TrialType::model()->findAll() as $trial_type): ?>
              <label>
                  <?php echo $form->radioButton($model, 'trial_type_id',
                      array('value' => $trial_type->id, 'uncheckValue' => null)); ?>
                  <?= $trial_type->name ?>
              </label>
            <?php endforeach; ?>
        </div>
      </div>

      <div class="row buttons text-right">

      </div>

        <?php $this->endWidget(); ?>
    </div>
  </section>
</main>


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
