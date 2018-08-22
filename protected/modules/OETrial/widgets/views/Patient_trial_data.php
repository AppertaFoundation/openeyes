<?php
/**
 *
 */
$isInAnotherInterventionTrial = TrialPatient::isPatientInInterventionTrial(
    $this->patient,
    $this->trial->id !== null ? $this->trial->id : null
);
$trialsShortlistedInto = TrialPatient::getTrialCount($this->patient, TrialPatient::STATUS_SHORTLISTED);
$trialsAcceptedInto = TrialPatient::getTrialCount($this->patient, TrialPatient::STATUS_ACCEPTED);
$trialsRejectedFrom = TrialPatient::getTrialCount($this->patient, TrialPatient::STATUS_REJECTED);
?>

<?php if($trialsShortlistedInto > 0):?><div class="trial-count"><em>Shortlisted</em> <?= $trialsShortlistedInto?></div><?php endif;?>
<?php if($trialsAcceptedInto > 0):?><div class="trial-count"><em>Accepted</em> <?= $trialsAcceptedInto?></div><?php endif;?>
<?php if($trialsRejectedFrom > 0):?><div class="trial-count"><em>Rejected</em> <?= $trialsRejectedFrom?></div><?php endif;?>
