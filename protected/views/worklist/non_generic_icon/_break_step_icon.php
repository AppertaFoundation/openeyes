<?php
$status_class = (int)$step->status === PathwayStep::STEP_COMPLETED ? "done {$step->type->short_name}-back" : $step->type->short_name;
$icon = $step instanceof PathwayStep ? $step->type->large_icon : $step->step_type->large_icon;

?>
<span class="oe-pathstep-btn <?= "$status_class {$step->type->type}" ?>"
        data-pathstep-id="<?= $step instanceof PathwayStep ? $step->id : null ?>"
        data-pathstep-type-id="<?= $step instanceof PathwayTypeStep ? $step->id : null ?>"
        data-patient-id="<?= $pathway->worklist_patient->patient_id ?>"
        data-visit-id="<?= $pathway->worklist_patient_id ?>"
        data-pathway-id="<?= $pathway->id ?>">
    <span class="step<?= $icon ? " {$icon}" : '' ?>">
        <?= !$icon ? $step->short_name : '' ?>
    </span>
</span>