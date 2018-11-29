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

  <td colspan="4">
      <?php
      /** @var $patientPanel PatientPanel */
      $patientPanel = $this->createWidget('application.widgets.PatientPanel',
          array(
              'patient' => $data->patient,
              'layout' => 'list',
              'list_mode' => true,
          )
      );
      $patientPanel->render('PatientPanel');
      ?>
  </td>
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
          <a class="js-save-external-trial-identifier">Save</a>
          <a class="js-cancel-external-trial-identifier">Cancel</a>
          <span class="js-spinner-as-icon" style="display: none;"><i class="spinner as-icon"></i></span>
        </div>
          <?php
      } else {
          echo CHtml::encode($data->external_trial_identifier);
      } ?>

  </td>
    <?php if ($renderTreatmentType && !$data->trial->is_open && $data->trial->trialType->code === TrialType::INTERVENTION_CODE): ?>
      <td> <!-- Treatment Type -->
          <?php if ($permission->can_edit):

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
              echo CHtml::hiddenField("treatment-type-hidden-$data->id", $data->treatment_type_id,
                  array(
                      'class' => 'js-hidden-treatment-type',
                  ));
              ?>
            <div class="js-treatment-type-actions" style="display: none;">
              <a class="js-save-treatment-type">Save</a>
              <a class="js-cancel-treatment-type">Cancel</a>
              <span class="js-spinner-as-icon" style="display: none;"><i class="spinner as-icon"></i></span>
            </div>
          <?php else: /* can't edit */
              echo $data->treatmentType->name;
          endif; ?>
      </td>
    <?php endif; ?>

  <td> <!-- Accept/Reject/Shortlist actions -->
      <?php if ($permission->can_edit && $data->trial->is_open): ?>

          <?php if ($data->status->code === TrialPatientStatus::SHORTLISTED_CODE): ?>

          <div>
            <a href="javascript:void(0)"
               onclick="changePatientStatus(this, <?= $data->id ?>, '<?= TrialPatientStatus::ACCEPTED_CODE ?>')"
               class="accept-patient-link"
               <?php if ($data->trial->trialType->code === TrialType::INTERVENTION_CODE && $isInAnotherInterventionTrial): ?>style="color: #ad1515;"<?php endif; ?> >
              Accept
            </a>
          </div>
          <?php endif; ?>

          <?php if (in_array($data->status->code,
              [TrialPatientStatus::SHORTLISTED_CODE, TrialPatientStatus::ACCEPTED_CODE], true)): ?>
          <div>
            <a href="javascript:void(0)"
               onclick="changePatientStatus(this, <?= $data->id ?>, '<?= TrialPatientStatus::REJECTED_CODE ?>')"
               class="accept-patient-link">Reject
            </a>
          </div>
          <?php endif; ?>

          <?php if ($data->status->code === TrialPatientStatus::REJECTED_CODE): ?>
          <div style="white-space: nowrap;">
          <span>
            <a href="javascript:void(0)"
               onclick="changePatientStatus(this, <?= $data->id ?>, '<?= TrialPatientStatus::SHORTLISTED_CODE ?>')"
               class="accept-patient-link">Re-Shortlist
            </a>
          </span>

          </div>
          <div>
            <a href="javascript:void(0)"
               onclick="removePatientFromTrial(<?= $data->id ?>, <?= $data->patient_id ?>, <?= $data->trial_id ?>)">
              Remove
            </a>
          </div>
          <?php endif; ?>

        <img class="loader" id="action-loader-<?= $data->id ?>"
             src="<?= Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>"
             alt="loading..." style="display: none;"/>
      <?php endif; ?>
  </td>
</tr>
