<?php
/**
 *
 */
$isInAnotherInterventionTrial = TrialPatient::isPatientInInterventionTrial(
    $this->patient,
    $this->trial->id !== null ? $this->trial->id : null
);
$trialsShortlistedInto = TrialPatient::getTrialCount($this->patient, TrialPatientStatus::SHORTLISTED_CODE);
$trialsAcceptedInto = TrialPatient::getTrialCount($this->patient, TrialPatientStatus::ACCEPTED_CODE);
$trialsRejectedFrom = TrialPatient::getTrialCount($this->patient, TrialPatientStatus::REJECTED_CODE);
?>
<div class="trial-count trial-shortlist" style="<?= $trialsShortlistedInto == 0 ? 'display:none;' : '' ?>"><em>Shortlisted</em> <?= $trialsShortlistedInto?></div>
<div class="trial-count" style="<?= $trialsAcceptedInto == 0 ? 'display:none;' : '' ?>"><em>Accepted</em> <?= $trialsAcceptedInto?></div>
<div class="trial-count" style="<?= $trialsRejectedFrom == 0 ? 'display:none;' : '' ?>"><em>Rejected</em> <?= $trialsRejectedFrom?></div>
