<?php
/**
 * @var $this TrialContext
 */
?>
<span class="js-add-remove-participant"
      data-patient-id="<?= $this->patient->id ?>"
      data-trial-id="<?= $this->trial->id ?>"
>
<a class="js-add-to-trial"
   style="<?= $this->isPatientInTrial() ? 'display:none;' : '' ?>"
>Add to trial</a>
<a class="js-remove-from-trial"
   style="<?= $this->isPatientInTrial() ? '' : 'display:none;' ?>"
>Remove from trial</a>
</span>
