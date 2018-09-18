<?php
/* @var TrialController $this */
/* @var Trial $trial */
/* @var TrialPermission $permission */
/* @var CActiveDataProvider[] $dataProviders * */
/* @var string $sort_by */
/* @var string $sort_dir */
?>

<?php $this->renderPartial('_trial_header', array(
    'trial' => $trial,
    'title' => CHtml::encode($trial->name),
    'permission' => $permission,
)); ?>


<div class="oe-full-content subgrid oe-worklists">

    <?php $this->renderPartial('_trialActions', array(
        'trial' => $trial,
        'permission' => $permission,
    )); ?>

  <main class="oe-full-main">
    <section class="element edit full">
      <div class="element-fields">

          <?php if ($trial->trialType->code === TrialType::INTERVENTION_CODE): ?>
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
      </div>
    </section>

    <div class="row divider">

      <table class="standard cols-full">
        <colgroup>
          <col class="cols-2">
          <col class="cols-4">
          <col class="cols-2">
          <col class="cols-4">
        </colgroup>
        <tbody>
        <tr class="col-gap">
          <td>Principal Investigator</td>
          <td>
              <?= CHtml::encode($trial->principalUser->getFullName()) ?>
          </td>
          <td>Date</td>
          <td>
              <?= $trial->getStartedDateForDisplay(); ?>
              <?php if ($trial->started_date !== null): ?>
                &mdash; <?= $trial->getClosedDateForDisplay() ?>
              <?php endif; ?>
          </td>
        </tr>
        <?php if ($trial->external_data_link !== ''): ?>
          <tr class="col-gap">
            <td><?= $trial->getAttributeLabel('external_data_link') ?></td>
            <td>
                <?= CHtml::link(CHtml::encode($trial->external_data_link),
                    CHtml::encode($trial->external_data_link), array('target' => '_blank')) ?>
            </td>
          </tr>
        <?php endif; ?>
        <?php if (strlen($trial->description)): ?>
          <tr class="col-gap">
            <td>Description</td>
            <td colspan="3">
                <?= CHtml::encode($trial->description) ?>
            </td>
          </tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>


      <?php $this->renderPartial('_patientList', array(
          'trial' => $trial,
          'permission' => $permission,
          'renderTreatmentType' => true,
          'title' => 'Accepted Participants',
          'dataProvider' => $dataProviders['ACCEPTED'],
          'sort_by' => $sort_by,
          'sort_dir' => $sort_dir,
      )); ?>
      <?php $this->renderPartial('_patientList', array(
          'trial' => $trial,
          'permission' => $permission,
          'renderTreatmentType' => false,
          'title' => 'Shortlisted Participants',
          'dataProvider' => $dataProviders['SHORTLISTED'],
          'sort_by' => $sort_by,
          'sort_dir' => $sort_dir,
      )); ?>
      <?php $this->renderPartial('_patientList', array(
          'trial' => $trial,
          'permission' => $permission,
          'renderTreatmentType' => false,
          'title' => 'Rejected Participants',
          'dataProvider' => $dataProviders['REJECTED'],
          'sort_by' => $sort_by,
          'sort_dir' => $sort_dir,
      )); ?>
  </main>

    <?php
    $assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.assets'), false, -1);
    Yii::app()->getClientScript()->registerScriptFile($assetPath . '/js/toggle-section.js');
    ?>
</div>
<script type="application/javascript">

  function changePatientStatus(object, trial_patient_id, new_status) {

    $('#action-loader-' + trial_patient_id).show();
    $.ajax({
      url: '<?php echo Yii::app()->controller->createUrl('/OETrial/trialPatient/changeStatus'); ?>/',
      data: {id: trial_patient_id, new_status: new_status, YII_CSRF_TOKEN: $('#csrf_token').val()},
      type: 'POST',
      success: function (response) {
        window.location.reload(false);
      },
      error: function (response) {
        $('#action-loader-' + trial_patient_id).hide();
        new OpenEyes.UI.Dialog.Alert({
          content: response
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
