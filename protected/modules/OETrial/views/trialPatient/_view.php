<?php

/**
 * @var $this TrialPatientController
 * @var TrialPermission $permission
 * @var $data TrialPatient
 * @var $renderTreatmentType bool
 */

$isInAnotherInterventionTrial = TrialPatient::isPatientInInterventionTrial($data->patient, $data->trial_id);

$warnings = array();
foreach ($data->patient->getWarnings(true) as $warn) {
    $warnings[] = "{$warn['long_msg']}: {$warn['details']}";
}

if ($isInAnotherInterventionTrial) {
    $warnings[] = $data->trial->trialType->code === TrialType::INTERVENTION_CODE ? 'Patient is already in an Intervention trial' : 'Patient is in an intervention trial';
}

$previousTreatmentType = TrialPatient::getLastPatientTreatmentType($data->patient, $data->trial_id);
if ($previousTreatmentType && $previousTreatmentType->code === TreatmentType::INTERVENTION_CODE) {
    $warnings[] = 'Patient has undergone intervention treatment in a previous trial.';
}

?>
<tr class="js-trial-patient" data-trial-patient-id="<?= $data->id ?>">
        <?php
      /** @var $patientPanel PatientPanel */
        $patientPanel = $this->createWidget(
            'application.widgets.PatientPanel',
            array(
              'patient' => $data->patient,
              'layout' => 'list',
              'list_mode' => true,
              'selected_site_id' => $this->selectedSiteId,
            )
        );
        $patientPanel->render('PatientPanel');
        ?>
  <td> <!-- External Reference -->
        <?php
        if ($permission->can_edit) {
            echo CHtml::textField(
                "ext-trial-id-form-$data->id",
                $data->external_trial_identifier,
                array(
                  'class' => 'js-external-trial-identifier',
                )
            ); ?>

            <?= CHtml::hiddenField(
                "external-trial-id-hidden-$data->id",
                $data->external_trial_identifier,
                array(
                  'class' => 'js-hidden-external-trial-identifier',
                )
            ) ?>
        <div class="js-external-trial-identifier-actions" style="display: none;">
          <a class="js-save-external-trial-identifier button">Save</a>
          <a class="js-cancel-external-trial-identifier button">Cancel</a>
          <span class="js-spinner-as-icon" style="display: none;"><i class="spinner as-icon"></i></span>
        </div>
            <?php
        } else {
            echo CHtml::encode($data->external_trial_identifier);
        } ?>

  </td>
    <?php if ($renderTreatmentType && !$data->trial->is_open && $data->trial->trialType->code === TrialType::INTERVENTION_CODE) : ?>
      <td> <!-- Treatment Type -->
          <?php if ($permission->can_edit) :
                echo CHtml::dropDownList(
                    'treatment-type',
                    $data->treatment_type_id,
                    TreatmentType::getOptions(),
                    array(
                      'id' => "treatment-type-$data->id",
                      'data-trial-patient-id' => $data->id,
                      'class' => 'js-treatment-type',
                    )
                );
                echo CHtml::hiddenField(
                    "treatment-type-hidden-$data->id",
                    $data->treatment_type_id,
                    array(
                      'class' => 'js-hidden-treatment-type',
                    )
                );
                ?>
            <div class="js-treatment-type-actions" style="display: none;">
              <a class="js-save-treatment-type">Save</a>
              <a class="js-cancel-treatment-type">Cancel</a>
              <span class="js-spinner-as-icon" style="display: none;"><i class="spinner as-icon"></i></span>
            </div>
            <?php else : /* can't edit */
                echo $data->treatmentType->name;
          endif; ?>
      </td>
    <?php endif; ?>

    <td> <!-- Comment -->
        <div class="add-data-actions">
            <button id="trial_patient_comment_button" type="button" <?php if ($data->comment != null) echo 'class="hint green"'; ?> >
                <i class="oe-i comments small-icon"></i>
            </button>
        </div>
    </td>
    <td> <!-- Accept/Reject/Shortlist actions -->
        <?php if ($permission->can_edit && $data->trial->is_open) : ?>
<!--          disable the button if the patient is deleted so that it cannot be accepted into the trial.-->
            <?php if ($data->status->code === TrialPatientStatus::SHORTLISTED_CODE && !$data->patient->isDeleted()) : ?>
            <button href="javascript:void(0)"
               onclick="changePatientStatus(this, <?= $data->id ?>, '<?= TrialPatientStatus::ACCEPTED_CODE ?>')"
               class="accept-patient-button button hint green"
                <?php if ($data->trial->trialType->code === TrialType::INTERVENTION_CODE && $isInAnotherInterventionTrial) :
                    ?>style="color: #ad1515;"<?php
                endif; ?> >
              Accept
            </button>
            <?php endif; ?>

            <?php if (in_array(
                $data->status->code,
                [TrialPatientStatus::SHORTLISTED_CODE, TrialPatientStatus::ACCEPTED_CODE],
                true
            )) : ?>
            <button href="javascript:void(0)"
               onclick="changePatientStatus(this, <?= $data->id ?>, '<?= TrialPatientStatus::REJECTED_CODE ?>')"
               class="accept-patient-button button hint red">Reject
            </button>
            <?php endif; ?>

            <?php if ($data->status->code === TrialPatientStatus::REJECTED_CODE) : ?>
                <?php if (!$data->patient->isDeleted()) : ?>
                <span>
                    <button href="javascript:void(0)"
                       onclick="changePatientStatus(this, <?= $data->id ?>, '<?= TrialPatientStatus::SHORTLISTED_CODE ?>')"
                       class="accept-patient-button button hint blue">Shortlist
                    </button>
                </span>
                <?php endif; ?>

            <button href="javascript:void(0)"
                    class="accept-patient-button button hint red"
               onclick="removePatientFromTrial(<?= $data->id ?>, <?= $data->patient_id ?>, <?= $data->trial_id ?>)">
              Remove
            </button>
            <?php endif; ?>

        <img class="loader" id="action-loader-<?= $data->id ?>"
             src="<?= Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>"
             alt="loading..." style="display: none;"/>
        <?php endif; ?>
  </td>
</tr>
<!--Comment Section-->
<tr class="js-trial-patient-comment-tr" data-trial-patient-id="<?= $data->id ?>" style="display:none">
    <td colspan="6">
        <div id="trial-patient-comments"  class="cols-full flex-layout flex-center" >
            <?php
            echo CHtml::textArea(
                "cmt-trial-id-$data->id",
                $data->comment,
                array(
                    'class' => 'js-comment-trial-patient cols-full column',
                    'rows'=>4,
                    'style'=>'width:90%',
                )
            ); ?>
            <?= CHtml::hiddenField(
                "cmt-trial-id-hidden-$data->id",
                $data->comment,
                array(
                    'class' => 'js-hidden-comment-trial-patient',
                )
            ) ?>
            <i class="oe-i remove-circle small-icon pad-left  js-remove-trial-patient-comments"></i>
        </div>
    </td>
    <td class="js-comment-trial-patient-actions" style="display: none; text-align: center;">
            <button class="js-save-comment-trial-patient button hint green" style=" text-align: center" >Save</button>
            <button class="js-cancel-comment-trial-patient button hint red" style="text-align: center">Cancel</button>
            <span class="js-spinner-as-icon" style="display: none;"><i class="spinner as-icon"></i></span>
    </td>

</tr>
