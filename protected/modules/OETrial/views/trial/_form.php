<?php
/* @var $this TrialController */
/* @var $trial Trial */
/* @var $form CActiveForm */
?>

<?php $this->renderPartial('_trial_header', array(
    'trial' => $trial,
    'title' => $trial->getIsNewRecord() ? 'Create Trial' : 'Edit Trial',
)); ?>
<div class="oe-full-content subgrid oe-worklists">
  <main class="oe-full-main">


        <?php $form = $this->beginWidget('CActiveForm', array(
          'id' => 'trial-form',
          'enableAjaxValidation' => true,
      )); ?>

        <?php if ($trial->hasErrors()) : ?>
        <div class="alert-box error with-icon">
          <p>Please fix the following input errors:</p>
            <?= $form->errorSummary($trial) ?>
        </div>
        <?php endif; ?>

    <table class="standard cols-full">
      <colgroup>
        <col class="cols-2">
        <col class="cols-4">
      </colgroup>
      <tbody>
      <tr class="col-gap">
        <td>
            <?= $form->labelEx($trial, 'name') ?>
        </td>
        <td>
            <?= $form->textField($trial, 'name', array('size' => 50, 'maxlength' => 200)) ?>
        </td>
      </tr>
      <tr class="col-gap">
          <td>
                <?= $form->labelEx($trial, 'ethics_number') ?>
          </td>
          <td>
                <?= $form->textField($trial, 'ethics_number', array('size' => 50, 'maxlength' => 200)) ?>
          </td>
      </tr>
      <tr class="col-gap">
        <td>
            <?= $form->labelEx($trial, 'external_data_link') ?>
        </td>
        <td>
            <?= $form->urlField($trial, 'external_data_link',
                array('size' => 50, 'onblur' => 'checkUrl(this)')); ?>
        </td>
      </tr>
      <tr>
        <td>

            <?= $form->labelEx($trial, 'description') ?>
        </td>
        <td>

            <?= $form->textArea($trial, 'description', array('cols' => 50)) ?>
        </td>
      </tr>

        <?php if (!$trial->getIsNewRecord()) : ?>
        <tr>
          <td>
            Date Range
          </td>
          <td class="flex-layour cols-full">
              <?php
                $this->widget('application.widgets.DatePicker', array(
                  'element' => $trial,
                  'name' => CHtml::modelName($trial) . '[started_date]',
                  'field' => 'started_date',
                  'options' => array(
                      'style' => 'margin-left:8px',
                      'nowrapper' => true,
                  ),
                  'value' => $trial->started_date,
                ))
                ?>

              <?php
                $this->widget('application.widgets.DatePicker', array(
                  'element' => $trial,
                  'name' => CHtml::modelName($trial) . '[closed_date]',
                  'field' => 'closed_date',
                  'options' => array(
                      'maxDate' => 'today',
                      'style' => 'margin-left:8px',
                      'nowrapper' => true,
                  ),
                  'value' => $trial->closed_date,
                ))
                ?>

          </td>
        </tr>
        <?php endif; ?>
      <tr>
        <td>
            <?= $form->labelEx($trial, 'trial_type_id') ?>
        </td>
        <td>
            <?php foreach (TrialType::model()->findAll() as $trial_type) : ?>
              <label>
                  <?php echo $form->radioButton($trial, 'trial_type_id',
                      array('value' => $trial_type->id, 'uncheckValue' => null)); ?>
                  <?= $trial_type->name ?>
              </label>
            <?php endforeach; ?>
        </td>
      </tr>
      </tbody>
    </table>

        <?php $this->endWidget(); ?>
</div>


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
