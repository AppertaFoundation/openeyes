<?php
/* @var $this TrialPatientController */
/* @var $data TrialPatient */
/* @var $userPermission integer */
/* @var $renderTreatmentType bool */

$isInAnotherInterventionTrial = TrialPatient::isPatientInInterventionTrial($data->patient, $data->trial_id);
$canEditPatient = Trial::checkTrialAccess(Yii::app()->user, $data->trial_id, UserTrialPermission::PERMISSION_EDIT);


$warnings = array();
foreach ($data->patient->getWarnings(true) as $warn) {
    $warnings[] = "{$warn['long_msg']}: {$warn['details']}";
}

if ($isInAnotherInterventionTrial) {
    $warnings[] = $data->trial->trial_type === Trial::TRIAL_TYPE_INTERVENTION ? 'Patient is already in an Intervention trial' : 'Patient is in an intervention trial';
}

$previousTreatmentType = TrialPatient::getLastPatientTreatmentType($data->patient, $data->trial_id);
if ($previousTreatmentType === TrialPatient::TREATMENT_TYPE_INTERVENTION) {
    $warnings[] = 'Patient has undergone intervention treatment in a previous trial.';
}

?>
<tr>
  <td> <!-- Warnings -->
      <?php if (count($warnings) > 0): ?>
        <span class="warning">
          <span class="icon icon-alert icon-alert-warning"></span>
          <span class="quicklook warning">
            <ul>
              <li>
                <?php echo implode('</li><li>', $warnings) ?>
              </li>
            </ul>
          </span>
        </span>
      <?php endif; ?>
  </td>
  <td> <!-- Name -->
      <?php echo CHtml::link(
          CHtml::encode($data->patient->last_name . ', ' . $data->patient->first_name . ($data->patient->is_deceased ? ' (Deceased)' : '')),
          array('/patient/view', 'id' => $data->patient->id),
          array('target' => '_blank')
      ); ?>
  </td>
  <td> <!-- Gender -->
      <?php echo $data->patient->getGenderString(); ?>
  </td>
  <td> <!-- Age -->
      <?php echo $data->patient->getAge(); ?>
  </td>
  <td> <!-- Ethnicity -->
      <?php echo CHtml::encode($data->patient->getEthnicGroupString()); ?>
  </td>
  <td> <!-- External Reference -->
      <?php
      if ($canEditPatient) {
          echo CHtml::textField(
              "ext-trial-id-form-$data->id",
              $data->external_trial_identifier,
              array(
                  'onkeyup' => "onExternalTrialIdentifierChange($data->id)",
              )
          ); ?>

          <?php echo CHtml::hiddenField("external-trial-id-hidden-$data->id", $data->external_trial_identifier); ?>
        <div id="ext-trial-id-actions-<?php echo $data->id; ?>" style="display:none;">
          <a href="javascript:void(0)" onclick="saveExternalTrialIdentifier(<?php echo $data->id; ?>)">Save</a>
          <a href="javascript:void(0)" onclick="cancelExternalTrialIdentifier(<?php echo $data->id; ?>)">Cancel</a>
          <img id="ext-trial-id-loader-<?php echo $data->id; ?>" class="loader"
               src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>"
               alt="loading..." style="display: none;"/>
        </div>
          <?php
      } else {
          echo CHtml::encode($data->external_trial_identifier);
      } ?>

  </td>
    <?php if ($renderTreatmentType && !$data->trial->is_open && $data->trial->trial_type === Trial::TRIAL_TYPE_INTERVENTION): ?>
      <td> <!-- Treatment Type -->
          <?php if ($canEditPatient):

              echo CHtml::dropDownList(
                  'treatment-type',
                  $data->treatment_type,
                  TrialPatient::getTreatmentTypeOptions(),
                  array(
                      'id' => "treatment-type-$data->id",
                      'data-trial-patient-id' => $data->id,
                      'onchange' => "onTreatmentTypeChange($data->id)",
                  )
              );
              echo CHtml::hiddenField("treatment-type-hidden-$data->id", $data->treatment_type);
              ?>
            <div id="treatment-type-actions-<?php echo $data->id; ?>" style="display: none">
              <a href="javascript:void(0)" onclick="updateTreatmentType(<?php echo $data->id; ?>)">Save</a>
              <a href="javascript:void(0)" onclick="cancelTreatmentType(<?php echo $data->id; ?>)">Cancel</a>
              <img id="treatment-type-loader-<?php echo $data->id; ?>"
                   src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>" alt="Working..."
                   class="hidden"/>
            </div>
          <?php else: /* can't edit */
              echo $data->getTreatmentTypeForDisplay();
          endif; ?>
      </td>
    <?php endif; ?>

  <td> <!-- Diagnoses and Medication show/hide actions -->
      <?php if ($data->patient->diagnoses): ?>
        <div>
          <a href="javascript:void(0)"
             data-show-label="Show Diagnoses" data-hide-label="Hide Diagnoses"
             onclick="toggleSection(this, '#collapse-section_<?php echo $data->patient->id . '_diagnoses'; ?>');">Show&nbsp;Diagnoses
          </a>
        </div>
      <?php endif; ?>
      <?php if (count($data->patient->medications) > 0): ?>
        <div>
          <a href="javascript:void(0)"
             data-show-label="Show Medications" data-hide-label="Hide Medications"
             onclick="toggleSection(this, '#collapse-section_<?php echo $data->patient->id . '_medication'; ?>');">Show&nbsp;Medications
          </a>
        </div>
      <?php endif; ?>
  </td>
  <td> <!-- Accept/Reject/Shortlist actions -->
      <?php if ($canEditPatient && $data->trial->is_open): ?>

          <?php if ($data->patient_status === TrialPatient::STATUS_SHORTLISTED): ?>

          <div>
            <a href="javascript:void(0)"
               onclick="changePatientStatus(this, <?php echo $data->id; ?>, '<?php echo TrialPatient::STATUS_ACCEPTED; ?>')"
               class="accept-patient-link"
               <?php if ($data->trial->trial_type === Trial::TRIAL_TYPE_INTERVENTION && $isInAnotherInterventionTrial): ?>style="color: #ad1515;"<?php endif; ?> >
              Accept
            </a>
          </div>
          <?php endif; ?>

          <?php if ($data->patient_status === TrialPatient::STATUS_SHORTLISTED || $data->patient_status === TrialPatient::STATUS_ACCEPTED): ?>
          <div>
            <a href="javascript:void(0)"
               onclick="changePatientStatus(this, <?php echo $data->id; ?>, '<?php echo TrialPatient::STATUS_REJECTED; ?>')"
               class="accept-patient-link">Reject
            </a>
          </div>
          <?php endif; ?>

          <?php if ($data->patient_status === TrialPatient::STATUS_REJECTED): ?>
          <div style="white-space: nowrap;">
          <span>
            <a href="javascript:void(0)"
               onclick="changePatientStatus(this, <?php echo $data->id; ?>, '<?php echo TrialPatient::STATUS_SHORTLISTED; ?>')"
               class="accept-patient-link">Re-Shortlist
            </a>
          </span>

          </div>
          <div>
            <a href="javascript:void(0)"
               onclick="removePatientFromTrial(<?php echo $data->id; ?>, <?php echo $data->patient_id; ?>, <?php echo $data->trial_id; ?>)">
              Remove
            </a>
          </div>
          <?php endif; ?>

        <img class="loader" id="action-loader-<?php echo $data->id; ?>"
             src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>"
             alt="loading..." style="display: none;"/>
      <?php endif; ?>
  </td>
</tr>
<!-- Collapsible diagnoses section -->
<tr id="collapse-section_<?php echo $data->patient->id . '_diagnoses'; ?>" style="display:none">
  <td colspan="9">

    <table>
      <thead>
      <tr>
        <th>Diagnosis</th>
        <th><?php echo Firm::contextLabel(); ?></th>
        <th>Date</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach ($data->patient->diagnoses as $diagnosis): ?>
        <tr>
          <td><?php echo CHtml::encode($diagnosis) . ' (' . ($diagnosis->principal == 1 ? 'Principal' : 'Secondary') . ')'; ?></td>
          <td><?php echo $diagnosis->element_diagnoses->event ? CHtml::encode($diagnosis->element_diagnoses->event->episode->firm->getNameAndSubspecialty()) : 'Unknown'; ?></td>
          <td><?php echo $diagnosis->element_diagnoses->event ? CHtml::encode(Helper::convertDate2NHS($diagnosis->element_diagnoses->event->event_date)) : 'Unknown'; ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>

  </td>
</tr>
<!-- Collapsible medication section -->
<tr id="collapse-section_<?php echo $data->patient->id . '_medication'; ?>" style="display:none">
  <td colspan="9">

    <table>
      <thead>
      <tr>
        <th>Medication</th>
        <th>Administration</th>
        <th>Date From</th>
        <th>Date To</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach ($data->patient->medications as $medication): ?>
        <tr>
          <td><?php echo $medication->getMedicationDisplay(); ?></td>
          <td><?php echo $medication->getAdministrationDisplay(); ?></td>
          <td><?php echo Helper::formatFuzzyDate($medication->start_date); ?></td>
          <td><?php echo isset($medication->end_date) ? Helper::formatFuzzyDate($medication->end_date) : ''; ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>

  </td>
</tr>
