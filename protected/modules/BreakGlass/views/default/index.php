<?php
$this->pageTitle = 'Break Glass';
$settings = new SettingMetadata();
$tech_support_provider = Yii::App()->params['tech_support_provider'] ? htmlspecialchars(Yii::App()->params['tech_support_provider']) : htmlspecialchars($settings->getSetting('tech_support_provider'));
$tech_support_url = Yii::App()->params['tech_support_url'] ? htmlspecialchars(Yii::App()->params['tech_support_url']) : htmlspecialchars($settings->getSetting('tech_support_url'))
?>

<div class="oe-full-content use-full-screen flex-layout">
  <div class="oe-full-main cols-center">
    <h2>Access Record</h2>
    <p>You have requested access to the records of:</p>
    <table class="standard">
      <tbody>
        <tr>
          <td>Name</td>
          <td><?= $patient->getFullName(); ?></td>
        </tr>
        <tr>
          <td>CHI</td>
          <td><?= $patient->getNhsnum(); ?></td>
        </tr>
        <tr>
          <td>Date of birth</td>
          <td><?= $patient->getDOB(); ?></td>
        </tr>
        <tr>
          <td>Address</td>
          <td><?= $patient->getSummaryAddress(); ?></td>
        </tr>
        <tr>
          <td>Health Board</p></td>
          <td><?= $patient_hb ?? "Unknown Health Board"; ?></td>
        </tr>
      </tbody>
    </table>
    <br/>
    <p>This level of access is only permitted in support of direct care.</p>
    <p>The patient has been informed and agreed for you to access their record.</p>
    <br/>
    <br/>
    <p>Why do you need to view this information?</p>
    
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'breakglassform',
        'enableAjaxValidation' => false,
      )); ?>
      <?php echo $form->error($model, 'reason', array('class' => 'alert-box error')); ?>
      <div style="text-align: center; margin: 20px">
        <?php echo $form->dropDownList(
            $model,
            'reason',
            $model->getReasons(),
            array(
              'empty' => 'Select',
              'class' => 'inline',
            ),
        ); ?>

        <div class="longreason" <?= $model->reason === 'Other' ? '' : 'style="display: none;"' ?>>
          <br/>
          <p>Please type your reason:</p>
          <?php echo $form->error($model, 'longreason', array('class' => 'alert-box error')); ?>
          <?php echo $form->textArea(
            $model,
            'longreason',
            array(
                'autocomplete' => 'off',
                'rows'=>5,
                'cols'=>65,
            )
          ); ?>
        </div>
      </div>
      <br/>
      <br/>
      <p>I confirm I am authorised to view this record.</p>
      <p>I understand this will be recorded and may be audited.</p>

      <div class="button-stack">
          <button type="submit" id="breakglass_confirm" class="button hint green">Access Record</button>
          <a href="/"><button type="button" id="breakglass_reject" class="button hint">Cancel</button></a>
      </div>

    <?php $this->endWidget(); ?>
  </div>

  <script>
    $('#BreakGlassModel_reason').change(function() {
      if (this.value === 'Other') {
        $('.longreason').show();
      } else {
        $('.longreason').hide();
      }
    });
  </script>
</div>