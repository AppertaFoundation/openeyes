<?php
/* @var TrialController $this */
/* @var Trial $trial */
/* @var CActiveDataProvider[] $dataProviders * */
/* @var string $sort_by */
/* @var string $sort_dir */

$hasEditPermissions = Trial::checkTrialAccess(Yii::app()->user, $trial->id, UserTrialPermission::PERMISSION_EDIT);
$hasManagePermissions = Trial::checkTrialAccess(Yii::app()->user, $trial->id, UserTrialPermission::PERMISSION_MANAGE);
?>

<h1 class="badge">Trial</h1>
<div class="box">
  <div class="row">
    <div class="large-9 column">
      <div class="box admin">
          <?php
          $this->widget('zii.widgets.CBreadcrumbs', array(
              'links' => $this->breadcrumbs,
          ));
          ?>

          <?php if ($trial->trial_type === Trial::TRIAL_TYPE_INTERVENTION): ?>
            <div class="alert-box alert with-icon">
              This is an Intervention Trial. Participants of this Trial cannot be accepted into other Intervention
              Trials
            </div>
          <?php endif; ?>

          <?php if (!$trial->is_open): ?>
            <div class="alert-box alert with-icon">This Trial has been closed. You will need to reopen it before you
              can make any changes.
            </div>
          <?php endif; ?>
        <input type="hidden" name="YII_CSRF_TOKEN" id="csrf_token"
               value="<?php echo Yii::app()->request->csrfToken ?>"/>
        <div class="row">
          <div class="large-9 column">
            <h1 style="display: inline"><?php echo CHtml::encode($trial->name.'.'); ?></h1>
            <h3 style="display: inline"><?php echo CHtml::encode('Principal Investigator: ' . $trial->principalUser->getFullName()); ?></h3>
          </div>
          <div class="large-3 column">
              <?php echo $trial->getStartedDateForDisplay(); ?>
              <?php if ($trial->started_date !== null): ?>
                &mdash; <?php echo $trial->getClosedDateForDisplay() ?>
              <?php endif; ?>
          </div>
        </div>

          <?php if ($trial->description !== ''): ?>
            <div class="row">
              <div class="large-12 column">
                <p><?php echo CHtml::encode($trial->description); ?></p>
              </div>
            </div>
          <?php endif; ?>

          <?php if ($trial->external_data_link !== ''): ?>
            <div class="row">
              <div class="large-12 column">
                <p>
                    <?php echo $trial->getAttributeLabel('external_data_link') ?>
                    <?php echo CHtml::link(CHtml::encode($trial->external_data_link),
                        CHtml::encode($trial->external_data_link), array('target' => '_blank')); ?>
                </p>
              </div>
            </div>
          <?php endif; ?>

          <?php if ($hasManagePermissions): ?>
            <br/>
              <?php if ($trial->is_open): ?>
                  <?php echo CHtml::beginForm(array('close'), 'post', array('id' => 'close-trial')); ?>
                  <?php echo CHtml::hiddenField('id', $trial->id); ?>
                  <?php echo CHtml::submitButton('Close Trial', array('id' => 'close-trial-submit')); ?>
                  <?php echo CHtml::endForm(); ?>
              <?php else: ?>
                  <?php echo CHtml::beginForm(array('reopen'), 'post', array('id' => 'reopen-trial')); ?>
                  <?php echo CHtml::hiddenField('id', $trial->id); ?>
                  <?php echo CHtml::submitButton('Re-open Trial', array('id' => 'reopen-trial-submit')); ?>
                  <?php echo CHtml::endForm(); ?>
              <?php endif; ?>
          <?php endif; ?>
      </div>
    </div>
      <?php $this->renderPartial('_trialActions', array('trial' => $trial)); ?>
  </div>
</div>

<div class="box">
  <div class="row">
    <div class="large-9 column">
      <div class="box admin">

          <?php $this->renderPartial('_patientList', array(
              'trial' => $trial,
              'renderTreatmentType' => true,
              'title' => 'Accepted Participants',
              'dataProvider' => $dataProviders[TrialPatient::STATUS_ACCEPTED],
              'sort_by' => $sort_by,
              'sort_dir' => $sort_dir,
          )); ?>

          <?php $this->renderPartial('_patientList', array(
              'trial' => $trial,
              'renderTreatmentType' => false,
              'title' => 'Shortlisted Participants',
              'dataProvider' => $dataProviders[TrialPatient::STATUS_SHORTLISTED],
              'sort_by' => $sort_by,
              'sort_dir' => $sort_dir,
          )); ?>

          <?php $this->renderPartial('_patientList', array(
              'trial' => $trial,
              'renderTreatmentType' => false,
              'title' => 'Rejected Participants',
              'dataProvider' => $dataProviders[TrialPatient::STATUS_REJECTED],
              'sort_by' => $sort_by,
              'sort_dir' => $sort_dir,
          )); ?>

      </div>

    </div><!-- /.large-9.column -->
  </div>
</div>

<?php
$assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.assets'), false, -1);
Yii::app()->getClientScript()->registerScriptFile($assetPath . '/js/toggle-section.js');
?>

<script type="application/javascript">

  function changePatientStatus(object, trial_patient_id, new_status) {

    $('#action-loader-' + trial_patient_id).show();
    $.ajax({
      url: '<?php echo Yii::app()->controller->createUrl('/OETrial/trialPatient/changeStatus'); ?>/',
      data: {id: trial_patient_id, new_status: new_status, YII_CSRF_TOKEN: $('#csrf_token').val()},
      type: 'POST',
      success: function (response) {
        if (response === '<?php echo TrialPatient::STATUS_CHANGE_CODE_OK; ?>') {
          window.location.reload(false);
        } else if (response === '<?php echo TrialPatient::STATUS_CHANGE_CODE_ALREADY_IN_INTERVENTION; ?>') {
          new OpenEyes.UI.Dialog.Alert({
            content: "You can't accept this participant into your Trial because that participant has already been accepted into another Intervention trial."
          }).open();
        } else {
          alert("Unknown response code: " + response_code);
        }
      },
      error: function (response) {
        $('#action-loader-' + trial_patient_id).hide();
        new OpenEyes.UI.Dialog.Alert({
          content: "Sorry, an internal error occurred and we were unable to change the participant status.\n\nPlease contact support for assistance."
        }).open();
      },
    });
  }

  function onExternalTrialIdentifierChange(trial_patient_id) {
    $('#ext-trial-id-actions-' + trial_patient_id).show('fast');
  }

  function cancelExternalTrialIdentifier(trial_patient_id) {
    var oldExternalId = $('#external-trial-id-hidden-' + trial_patient_id).val();
    $('#ext-trial-id-form-' + trial_patient_id).val(oldExternalId);
    $('#ext-trial-id-actions-' + trial_patient_id).hide('fast');
  }

  function saveExternalTrialIdentifier(trial_patient_id) {
    var external_id = $('#ext-trial-id-form-' + trial_patient_id).val();

    $('#ext-trial-id-loader-' + trial_patient_id).show();

    $.ajax({
      url: '<?php echo Yii::app()->controller->createUrl('/OETrial/trialPatient/updateExternalId'); ?>',
      data: {id: trial_patient_id, new_external_id: external_id, YII_CSRF_TOKEN: $('#csrf_token').val()},
      type: 'POST',
      complete: function (response) {
        $('#ext-trial-id-loader-' + trial_patient_id).hide();
      },
      success: function (response) {
        $('#ext-trial-id-hidden-' + trial_patient_id).val(external_id);
        $("#ext-trial-id-actions-" + trial_patient_id).hide('fast');
      },
      error: function (response) {
        new OpenEyes.UI.Dialog.Alert({
          content: "Sorry, an internal error occurred and we were unable to change the external trial identifier.\n\nPlease contact support for assistance."
        }).open();
      }
    });
  }

  function onTreatmentTypeChange(trial_patient_id) {
    $('#treatment-type-actions-' + trial_patient_id).show('fast');
  }

  function cancelTreatmentType(trial_patient_id) {
    var oldTreatmentType = $('#treatment-type-hidden-' + trial_patient_id).val();
    $('#treatment-type-' + trial_patient_id).val(oldTreatmentType);
    $('#treatment-type-actions-' + trial_patient_id).hide('fast');
  }

  function updateTreatmentType(trial_patient_id) {

    var treatment_type = $('#treatment-type-' + trial_patient_id).val();

    $('#treatment-type-loader-' + trial_patient_id).show();

    $.ajax({
      url: '<?php echo Yii::app()->controller->createUrl('/OETrial/trialPatient/updateTreatmentType'); ?>',
      data: {id: trial_patient_id, treatment_type: treatment_type, YII_CSRF_TOKEN: $('#csrf_token').val()},
      type: 'POST',
      complete: function (response) {
        $('#treatment-type-loader-' + trial_patient_id).hide();
      },
      success: function (response) {
        $('#treatment-type-hidden-' + trial_patient_id).val(treatment_type);
        $('#treatment-type-actions-' + trial_patient_id).hide('fast');
      },
      error: function (response) {
        new OpenEyes.UI.Dialog.Alert({
          content: "Sorry, an internal error occurred and we were unable to change the treatment type.\n\nPlease contact support for assistance."
        }).open();
      }
    });
  }

  $(document).ready(function () {
    $(".icon-alert-warning").hover(function () {
        $(this).siblings(".warning").show('fast');
      },
      function () {
        $(this).siblings(".warning").hide('fast');
      }
    );

    $('#close-trial-submit').click(function (e) {
      var confirmDialog = new OpenEyes.UI.Dialog.Confirm({
        title: "Close Trial",
        content: "Are you sure you want to close this trial?"
      });

      confirmDialog.content.on('click', '.ok', function () {
        $('#close-trial').submit();
      });

      confirmDialog.open();
      return false;
    });

    $('#reopen-trial-submit').click(function () {
      var confirmDialog = new OpenEyes.UI.Dialog.Confirm({
        title: "Re-open Trial",
        content: "Are you sure you want to re-open this trial?"
      });

      confirmDialog.content.on('click', '.ok', function () {
        $('#reopen-trial').submit();
      });

      confirmDialog.open();
      return false;
    });
  });

  function removePatientFromTrial(trial_patient_id, patient_id, trial_id) {

    $('#action-loader-' + trial_patient_id).show();

    $.ajax({
      url: '<?php echo Yii::app()->createUrl('/OETrial/trial/removePatient'); ?>',
      data: {id: trial_id, patient_id: patient_id, YII_CSRF_TOKEN: $('#csrf_token').val()},
      type: 'POST',
      result: function (response) {
        $('#action-loader-' + trial_patient_id).hide();
      },
      success: function (response) {
        window.location.reload(false);
      },
      error: function (response) {
        new OpenEyes.UI.Dialog.Alert({
          content: "Sorry, an internal error occurred and we were unable to remove the patient from the trial.\n\nPlease contact support for assistance."
        }).open();
      }
    });
  }
</script>
