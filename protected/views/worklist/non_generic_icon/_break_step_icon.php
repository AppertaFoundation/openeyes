<?php
$status_class = (int)$step->status === PathwayStep::STEP_COMPLETED ? "done {$step->type->short_name}-back" : $step->type->short_name;
?>
<span class="oe-pathstep-btn <?= "$status_class {$step->type->type}" ?>" data-pathstep-id="<?= $step->id ?>"
        data-patient-id="<?= $pathway->worklist_patient->patient_id ?>"
        data-visit-id="<?= $pathway->worklist_patient_id ?>"
        data-pathway-id="<?= $pathway->id ?>">
    <span class="step<?= $step->type->large_icon ? " {$step->type->large_icon}" : '' ?>">
        <?= !$step->type->large_icon ? $step->short_name : '' ?>
    </span>
</span>