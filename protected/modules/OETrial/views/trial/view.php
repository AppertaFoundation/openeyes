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
        <section class="element edit cols-11">
            <div class="element-fields">
                <?php if ($trial->trialType->code === TrialType::INTERVENTION_CODE) : ?>
                <div class="alert-box alert with-icon">
                  This is an Intervention Trial. Participants of this Trial cannot be accepted into other Intervention
                  Trials
                </div>
                <?php endif; ?>

                <?php if (!$trial->is_open) : ?>
                <div class="alert-box alert with-icon">This Trial has been closed. You will need to reopen it before you
                  can make any changes.
                </div>
                <?php endif; ?>
            </div>
        </section>

        <div class="flex-trial-group js-filter-group">

            <table class="standard">
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
                    <?php $principal_investigators = $trial->getTrialPrincipalInvestigators();
                    foreach ($principal_investigators as $item) {
                        echo $item->user->getFullName().'<br>';
                    }
                    ?>
                </td>
            <tr class="col-gap">
            <td>Date</td>
                <td>
                    <?= $trial->getStartedDateForDisplay(); ?>
                    <?php if ($trial->started_date !== null) : ?>
                    &mdash; <?= $trial->getClosedDateForDisplay() ?>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td>Ethics Number</td>
                <td>
                    <?= $trial->getEthicsNumberForDisplay(); ?>
                </td>
            </tr>
            <?php if ($trial->external_data_link !== '') : ?>
                <tr class="col-gap">
                    <td><?= $trial->getAttributeLabel('external_data_link') ?></td>
                    <td>
                    <?= CHtml::link(
                        CHtml::encode($trial->external_data_link),
                        CHtml::encode($trial->external_data_link),
                        array('target' => '_blank')
                    ) ?>
                    </td>
                </tr>
            <?php endif; ?>
            <?php if (strlen($trial->description)) : ?>
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

        <div class="flex-trial-group js-filter-group" id="accepted-participants">
            <?php $this->renderPartial('_patientList', array(
                'trial' => $trial,
                'permission' => $permission,
                'renderTreatmentType' => true,
                'title' => 'Accepted Participants',
                'dataProvider' => $dataProviders['ACCEPTED'],
                'sort_by' => $sort_by,
                'sort_dir' => $sort_dir,
            )); ?>
        </div>
        <div class="flex-trial-group js-filter-group" id="shortlisted-participants">
            <?php $this->renderPartial('_patientList', array(
                'trial' => $trial,
                'permission' => $permission,
                'renderTreatmentType' => false,
                'title' => 'Shortlisted Participants',
                'dataProvider' => $dataProviders['SHORTLISTED'],
                'sort_by' => $sort_by,
                'sort_dir' => $sort_dir,
            )); ?>
        </div>
        <div class="flex-trial-group js-filter-group" id="rejected-participants">
            <?php $this->renderPartial('_patientList', array(
                'trial' => $trial,
                'permission' => $permission,
                'renderTreatmentType' => false,
                'title' => 'Rejected Participants',
                'dataProvider' => $dataProviders['REJECTED'],
                'sort_by' => $sort_by,
                'sort_dir' => $sort_dir,
            )); ?>
        </div>
    </main>

    <?php
    $assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.assets'), true, -1);
    Yii::app()->getClientScript()->registerScriptFile($assetPath . '/js/toggle-section.js');
    ?>
</div>

<script type="application/javascript">
    $(document).ready(function () {
        $.ajax({
            type: "GET",
            url: "/OETrial/trial/renderPopups",
            data: {
                "trialId" : (<?= $trial->id?>),
                "sort_dir" : '<?= $sort_dir ?>',
                "sort_by" : '<?= $sort_by ?>',
                "TrialPatient_page" : <?= $page ?>,
                YII_CSRF_TOKEN: YII_CSRF_TOKEN
            },
            success: function (resp) {
                $("body.open-eyes.oe-grid").append(resp);
            }
        })
    })
    $('body').on('click', '.collapse-data-header-icon', function () {
        $(this).toggleClass('collapse expand');
        $(this).next('div').toggle();
    });
</script>

<script type="application/javascript">
  $('.js-trails-sort-selector').change(function(e){
        window.location = e.target.value;
  });

  function changePatientStatus(object, trial_patient_id, new_status) {

    $('#action-loader-' + trial_patient_id).show();
    $.ajax({
      url: '<?= Yii::app()->controller->createUrl('/OETrial/trialPatient/changeStatus'); ?>/',
      data: {id: trial_patient_id, new_status: new_status},
      success: function (response) {
        window.location.reload(false);
      },
      error: function (response) {
        //  Changed to meet requirements of CERA-583, there was no popup box shown earlier because content:response throws an error
        var displayError = response.responseText;
        displayError = displayError.substring(displayError.search('<p>'),displayError.indexOf('(') ) + '</p>';
        $('#action-loader-' + trial_patient_id).hide();
        new OpenEyes.UI.Dialog.Alert({
          content: displayError
        }).open();
      },
    });
  }

  $(document).on('keyup', '.js-external-trial-identifier', function () {
    var $container = $(this).closest('.js-trial-patient');
    $container.find('.js-external-trial-identifier-actions').show();
  });

  $(document).on('click', '.js-cancel-external-trial-identifier', function () {
    var $container = $(this).closest('.js-trial-patient');
    var oldExternalId = $container.find('.js-hidden-external-trial-identifier').val();
    $container.find('.js-external-trial-identifier').val(oldExternalId);
    $container.find('.js-external-trial-identifier-actions').hide();
  });

  $(document).on('click', '.js-save-external-trial-identifier', function () {
    var $container = $(this).closest('.js-trial-patient');
    var $actions = $(this).closest('.js-external-trial-identifier-actions');
    var trialPatientId = $(this).closest('.js-trial-patient').data('trial-patient-id');
    var externalId = $container.find('.js-external-trial-identifier').val();
    var $spinner = $actions.find('.js-spinner-as-icon');
    $spinner.show();

    $.ajax({
      url: '<?= Yii::app()->controller->createUrl('/OETrial/trialPatient/updateExternalId'); ?>',
      data: {id: trialPatientId, new_external_id: externalId, YII_CSRF_TOKEN: YII_CSRF_TOKEN},
      type: 'POST',
      complete: function (response) {
        $spinner.hide();
      },
      success: function (response) {
        $container.find('.js-hidden-external-trial-identifier').val(externalId);
        $actions.hide();
      },
      error: function (response) {
        new OpenEyes.UI.Dialog.Alert({
          content: "Sorry, an internal error occurred and we were unable to change the external trial identifier.\n\nPlease contact support for assistance."
        }).open();
      }
    });
  });

  $(document).on('change', '.js-treatment-type', function() {
    $(this).closest('.js-trial-patient').find('.js-treatment-type-actions').show();
  });

  $(document).on('click', '.js-cancel-treatment-type', function() {
    var $container = $(this).closest('.js-trial-patient');
    var oldTreatmentType = $container.find('.js-hidden-treatment-type').val();
    $container.find('.js-treatment-type').val(oldTreatmentType);
    $container.find('.js-treatment-type-actions').hide();
  });

  $(document).on('click', '.js-save-treatment-type', function() {
    var $container = $(this).closest('.js-trial-patient');
    var treatmentType = $container.find('.js-treatment-type').val();
    var $actions = $(this).closest('.js-treatment-type-actions');
    var $spinner = $actions.find('.js-spinner-as-icon');
    $spinner.show();
    var trialPatientId = $container.data('trial-patient-id');

    $.ajax({
      url: '<?= Yii::app()->controller->createUrl('/OETrial/trialPatient/updateTreatmentType'); ?>',
      data: {id: trialPatientId, treatment_type: treatmentType, YII_CSRF_TOKEN: YII_CSRF_TOKEN},
      type: 'POST',
      complete: function (response) {
        $spinner.hide();
      },
      success: function (response) {
        $container.find('.js-hidden-treatment-type').val(treatmentType);
        $actions.hide();
      },
      error: function (response) {
        new OpenEyes.UI.Dialog.Alert({
          content: "Sorry, an internal error occurred and we were unable to change the treatment type.\n\nPlease contact support for assistance."
        }).open();
      }
    });
  });
    $(document).on('input', '.js-comment-trial-patient', function () {
        var $container=$(this).closest('.js-trial-patient-comment-tr');
        $container.find('.js-comment-trial-patient-actions').show();
    });

    $(document).on('click', '.js-cancel-comment-trial-patient', function () {
        var $container=$(this).closest('.js-trial-patient-comment-tr');
        var oldComment = $container.find('.js-hidden-comment-trial-patient').val();
        $container.find('.js-comment-trial-patient').val(oldComment);
        $container.find('.js-comment-trial-patient-actions').hide();
    });

    $(document).on('click', '.js-save-comment-trial-patient', function () {
        var $container=$(this).closest('.js-trial-patient-comment-tr');
        var $containerAttrVal = $(this).closest('.js-trial-patient-comment-tr').attr('data-trial-patient-id');
        var $actions = $(this).closest('.js-comment-trial-patient-actions');
        var trialPatientId = $(this).closest('.js-trial-patient-comment-tr').data('trial-patient-id');
        var comment = $container.find('.js-comment-trial-patient').val();
        var $spinner = $actions.find('.js-spinner-as-icon');
        $spinner.show();

        $.ajax({
            url: '<?= Yii::app()->controller->createUrl('/OETrial/trialPatient/updateComment'); ?>',
            data: {id: trialPatientId, new_comment: comment, YII_CSRF_TOKEN: YII_CSRF_TOKEN},
            type: 'POST',
            complete: function (response) {
                $spinner.hide();
            },
            success: function (response) {
                $container.find('.js-hidden-comment-trial-patient').val(comment);
                $actions.hide();
                // Make the comment button green when there is a comment.
                if (comment != '') {
                    $($("[data-trial-patient-id="+$containerAttrVal+"]")[0]).find("#trial_patient_comment_button").addClass("hint green");
                }
                else {
                    $($("[data-trial-patient-id="+$containerAttrVal+"]")[0]).find("#trial_patient_comment_button").removeClass("hint green");
                }
            },
            error: function (response) {
                new OpenEyes.UI.Dialog.Alert({
                    content: "Sorry, an internal error occurred and we were unable to change the comment.\n\nPlease contact support for assistance."
                }).open();
            }
        });
    });

    $(document).on('click','#trial_patient_comment_button',function () {
        var $container = $(this).closest('.js-trial-patient').next('.js-trial-patient-comment-tr');
        if ($container.is(":visible")) {
            $container.hide();
        }else{
            $container.show();
        }
    });

    $(document).on('click','.js-remove-trial-patient-comments',function () {
        var $container = $(this).closest('.js-trial-patient-comment-tr');
        if ($container.is(":visible")) {
            $container.hide();
        }
    });



  $(function () {
    $(".icon-alert-warning").hover(function () {
        $(this).siblings(".warning").show();
      },
      function () {
        $(this).siblings(".warning").hide();
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
      url: '<?= Yii::app()->createUrl('/OETrial/trial/removePatient'); ?>',
      data: {id: trial_id, patient_id: patient_id, YII_CSRF_TOKEN: YII_CSRF_TOKEN},
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
