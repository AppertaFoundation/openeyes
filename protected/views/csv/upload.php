<div class="box admin">
  <div class="errorSummary">
      <?php
      if (isset($errors) and $errors !== null) {
          echo '<pre>';
          echo ArrayHelper::array_dump_html($errors);
          echo '</pre>';
      }
      ?>
  </div>
    <?php
    $form = $this->beginWidget(
        'CActiveForm',
        array(
            'id' => 'upload-form',
            'action' => Yii::app()->createURL('csv/preview', array('context' => $context)),
            'enableAjaxValidation' => false,
            'htmlOptions' => array('enctype' => 'multipart/form-data'),
        )
    );

    echo $form->fileField(new Csv(), 'csvFile');
    echo CHtml::submitButton('Submit');
    $this->endWidget();
    ?>
</div>