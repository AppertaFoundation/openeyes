<?php
/**
 * @var $this TrialContext
 */
?>

<span class="js-add-remove-participant"
      data-patient-id="<?= $this->patient->id ?>"
      data-trial-id="<?= $this->trial->id ?>"
>
<button class="js-add-to-trial button blue hint"
   style="<?= $this->isPatientInTrial() ? 'display:none;' : '' ?>"
>Add to Shortlist</button>

<em style="display: none;font-style: italic" class="oe-list-patient js-remove-from-trial">Added to Shortlist</em>
</span>
