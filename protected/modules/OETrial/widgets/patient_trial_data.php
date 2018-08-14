<?php
/**
 * Created by PhpStorm.
 * User: fivium-isaac
 * Date: 10/08/18
 * Time: 2:53 PM
 */
$isInAnotherInterventionTrial = TrialPatient::isPatientInInterventionTrial($data,
    $this->trialContext !== null ? $this->trialContext->id : null);
$shortlistedTrials = TrialPatient::getTrialCount($data, TrialPatient::STATUS_SHORTLISTED);
$acceptedTrials = TrialPatient::getTrialCount($data, TrialPatient::STATUS_ACCEPTED);
$rejectedTrials = TrialPatient::getTrialCount($data, TrialPatient::STATUS_REJECTED);
$trialPatient = $this->trialContext !== null ? TrialPatient::getTrialPatient($data, $this->trialContext->id) : null;

if ($isInAnotherInterventionTrial) {
    $warnings[] = 'This patient is already in an Intervention trial';
}

$previousTreatmentType = TrialPatient::getLastPatientTreatmentType($data, $this->trialContext !== null ? $this->trialContext->id : null);
if ($previousTreatmentType === TrialPatient::TREATMENT_TYPE_INTERVENTION) {
    $warnings[] = 'Patient has undergone intervention treatment in a previous trial.';
}

$inTrial = $this->trialContext !== null ? TrialPatient::model()->exists(
    'patient_id = :patientId AND trial_id = :trialId',
    array(
        ':patientId' => $data->id,
        ':trialId' => $this->trialContext->id,
    )
) : null;
?>

    <?php if ($this->trialContext !== null && (
            $shortlistedTrials !== '0'
            || $acceptedTrials !== '0'
            || $rejectedTrials !== '0'
            || TrialPatient::isPatientInInterventionTrial($data, $this->trialContext->id))): ?>
      <div class="row data-row">
        <div class="box large-3 column">
          <div class="row data-row">
            <div class="large-4 column">
                <?php if ($shortlistedTrials !== '0'): ?>
                  <div class="trial-count shortlisted" onmouseover="showQuicklook(this);" onmouseleave="hideQuicklook(this);">
                      <?php echo $shortlistedTrials; ?>
                  </div>
                  <span class="quicklook">
              This patient is shortlisted in <?php echo $shortlistedTrials === '1' ? $shortlistedTrials . ' trial' : $shortlistedTrials . ' trials'; ?>.
            </span>
                <?php else: ?>
                  <div class="trial-count"></div>
                <?php endif; ?>
            </div>
            <div class="large-4 column">
                <?php if ($acceptedTrials !== '0'): ?>
                  <div class="trial-count accepted" onmouseover="showQuicklook(this);" onmouseleave="hideQuicklook(this);">
                      <?php echo $acceptedTrials; ?>
                  </div>
                  <span class="quicklook">
                    This patient has been accepted in <?php echo $acceptedTrials === '1' ? $acceptedTrials . ' trial' : $acceptedTrials . ' trials'; ?>.
                  </span>
                <?php else: ?>
                  <div class="trial-count"></div>
                <?php endif; ?>
            </div>
            <div class="large-4 column">
                <?php if ($rejectedTrials !== '0'): ?>
                  <div class="trial-count rejected" onmouseover="showQuicklook(this);" onmouseleave="hideQuicklook(this);">
                      <?php echo $rejectedTrials; ?>
                  </div>
                  <span class="quicklook">
                    This patient has been rejected from <?php echo $rejectedTrials === '1' ? $rejectedTrials . ' trial' : $rejectedTrials . ' trials'; ?>.
                  </span>
                <?php else: ?>
                  <div class="trial-count"></div>
                <?php endif; ?>
            </div>
          </div>
        </div>
        <div class="current-status large-9 column end">
            <?php if (TrialPatient::isPatientInInterventionTrial($data, $this->trialContext->id)): ?>
              <h3>Participated in Intervention Trial</h3>
            <?php endif; ?>
            <?php if ($inTrial): ?>
              <h3><?php echo $trialPatient->getStatusForDisplay(); ?></h3>
            <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>

<?php if ($this->trialContext !== null &&
Trial::checkTrialAccess(Yii::app()->user, $this->trialContext->id, UserTrialPermission::PERMISSION_EDIT)
) {
$inOtherTrials = TrialPatient::model()->exists(
    'patient_id = :patientId AND trial_id != :trialId',
    array(
        ':patientId' => $data->id,
        ':trialId' => $this->trialContext->id,
    )
);

?>


  <a id="add-to-trial-link-<?php echo $data->id; ?>"
     href="javascript:void(0)" <?php echo $inTrial ? 'style="display:none"' : ''; ?>
     onclick="addPatientToTrial(<?php echo $data->id; ?>, <?php echo $this->trialContext->id; ?>)">
    Add to trial shortlist
  </a>
  <a id="remove-from-trial-link-<?php echo $data->id; ?>"
     href="javascript:void(0)" <?php echo !($inTrial && TrialPatient::getTrialPatient($data, $this->trialContext->id)->patient_status === TrialPatient::STATUS_SHORTLISTED) ? 'style="display:none"' : ''; ?>
     onclick="removePatientFromTrial(<?php echo $data->id; ?>, <?php echo $this->trialContext->id; ?>)">
    Remove from trial shortlist
  </a>
    <?php
} ?>
