<?php
/**
 *
 */
$isInAnotherInterventionTrial = TrialPatient::isPatientInInterventionTrial(
    $this->patient,
    $this->trial->id !== null ? $this->trial->id : null
);
$shortlistedTrials = TrialPatient::getTrialCount($this->patient, TrialPatient::STATUS_SHORTLISTED);
$acceptedTrials = TrialPatient::getTrialCount($this->patient, TrialPatient::STATUS_ACCEPTED);
$rejectedTrials = TrialPatient::getTrialCount($this->patient, TrialPatient::STATUS_REJECTED);
$trialPatient = @TrialPatient::getTrialPatient($this->patient, $this->trial->id);
?>

<?php if($shortlistedTrials > 0):?><div class="trial-count"><em>Shortlisted</em> <?= $shortlistedTrials?></div><?php endif;?>
<?php if($acceptedTrials > 0):?><div class="trial-count"><em>Accepted</em> <?= $acceptedTrials?></div><?php endif;?>
<?php if($rejectedTrials > 0):?><div class="trial-count"><em>Rejected</em> <?= $rejectedTrials?></div><?php endif;?>
