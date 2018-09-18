<?php
/* @var $this TrialPatientController */
/* @var $data TrialPatient */
/* @var $renderTreatmentType bool */

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
<tr>
  <td> <!-- Warnings -->
      <?php if (count($warnings) > 0): ?>
        <span class="warning">
          <span class="icon icon-alert icon-alert-warning"></span>
          <span class="quicklook warning">
            <ul>
              <li>
                <?= implode('</li><li>', $warnings) ?>
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
      <?= $data->patient->getGenderString() ?>
  </td>
  <td> <!-- Age -->
      <?= $data->patient->getAge() ?>
  </td>
  <td> <!-- Ethnicity -->
      <?= CHtml::encode($data->patient->getEthnicGroupString()) ?>
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

          <?= CHtml::hiddenField("external-trial-id-hidden-$data->id", $data->external_trial_identifier) ?>
        <div id="ext-trial-id-actions-<?= $data->id ?>" style="display:none;">
          <a href="javascript:void(0)" onclick="saveExternalTrialIdentifier(<?= $data->id ?>)">Save</a>
          <a href="javascript:void(0)" onclick="cancelExternalTrialIdentifier(<?= $data->id ?>)">Cancel</a>
          <img id="ext-trial-id-loader-<?= $data->id ?>" class="loader"
               src="<?= Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>"
               alt="loading..." style="display: none;"/>
        </div>
          <?php
      } else {
          echo CHtml::encode($data->external_trial_identifier);
      } ?>

  </td>
    <?php if ($renderTreatmentType && !$data->trial->is_open && $data->trial->trialType->code === TrialType::INTERVENTION_CODE): ?>
      <td> <!-- Treatment Type -->
          <?php if ($canEditPatient):

              echo CHtml::dropDownList(
                  'treatment-type',
                  $data->treatment_type,
                  TreatmentType::getOptions(),
                  array(
                      'id' => "treatment-type-$data->id",
                      'data-trial-patient-id' => $data->id,
                      'onchange' => "onTreatmentTypeChange($data->id)",
                  )
              );
              echo CHtml::hiddenField("treatment-type-hidden-$data->id", $data->treatment_type);
              ?>
            <div id="treatment-type-actions-<?= $data->id ?>" style="display: none">
              <a href="javascript:void(0)" onclick="updateTreatmentType(<?= $data->id ?>)">Save</a>
              <a href="javascript:void(0)" onclick="cancelTreatmentType(<?= $data->id ?>)">Cancel</a>
              <img id="treatment-type-loader-<?= $data->id ?>"
                   src="<?= Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>" alt="Working..."
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
             onclick="toggleSection(this, '#collapse-section_<?= $data->patient->id . '_diagnoses' ?>');">Show&nbsp;Diagnoses
          </a>
        </div>
      <?php endif; ?>
      <?php if (count($data->patient->medications) > 0): ?>
        <div>
          <a href="javascript:void(0)"
             data-show-label="Show Medications" data-hide-label="Hide Medications"
             onclick="toggleSection(this, '#collapse-section_<?= $data->patient->id . '_medication' ?>');">Show&nbsp;Medications
          </a>
        </div>
      <?php endif; ?>
  </td>
  <td> <!-- Accept/Reject/Shortlist actions -->
      <?php if ($canEditPatient && $data->trial->is_open): ?>

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

          <?php if (in_array($data->status->code, [TrialPatientStatus::SHORTLISTED_CODE, TrialPatientStatus::ACCEPTED_CODE], true)): ?>
          <div>
            <a href="javascript:void(0)"
               onclick="changePatientStatus(this, <?= $data->id ?>, '<?= TrialPatientStatus::REJECTED_CODE ?>')"
               class="accept-patient-link">Reject
            </a>
          </div>
          <?php endif; ?>

          <?php if ($data->status->code  === TrialPatientStatus::REJECTED_CODE): ?>
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
<!-- Collapsible diagnoses section -->
<tr id="collapse-section_<?= $data->patient->id . '_diagnoses' ?>" style="display:none">
  <td colspan="9">

    <table>
      <thead>
      <tr>
        <th>Diagnosis</th>
        <th><?= Firm::contextLabel() ?></th>
        <th>Date</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach ($data->patient->diagnoses as $diagnosis): ?>
        <tr>
          <td><?= CHtml::encode($diagnosis) . ' (' . ($diagnosis->principal == 1 ? 'Principal' : 'Secondary') . ')' ?></td>
          <td><?= $diagnosis->element_diagnoses->event ? CHtml::encode($diagnosis->element_diagnoses->event->episode->firm->getNameAndSubspecialty()) : 'Unknown' ?></td>
          <td><?= $diagnosis->element_diagnoses->event ? CHtml::encode(Helper::convertDate2NHS($diagnosis->element_diagnoses->event->event_date)) : 'Unknown' ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>

  </td>
</tr>
<!-- Collapsible medication section -->
<tr id="collapse-section_<?= $data->patient->id . '_medication' ?>" style="display:none">
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
          <td><?= $medication->getMedicationDisplay() ?></td>
          <td><?= $medication->getAdministrationDisplay() ?></td>
          <td><?= Helper::formatFuzzyDate($medication->start_date) ?></td>
          <td><?= isset($medication->end_date) ? Helper::formatFuzzyDate($medication->end_date) : '' ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>

  </td>
</tr>
