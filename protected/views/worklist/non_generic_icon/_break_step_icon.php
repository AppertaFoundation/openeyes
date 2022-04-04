<?php
$status_class = (int)$step->status === PathwayStep::STEP_COMPLETED ? "done {$step->type->short_name}-back" : $step->type->short_name;
$icon = $step instanceof PathwayStep ? $step->type->large_icon : $step->step_type->large_icon;
$type = $step instanceof PathwayStep ? $step->type->type : $step->step_type->type
?>
<span class="oe-pathstep-btn <?= "$status_class $type" ?>"
        data-pathstep-id="<?= $step instanceof PathwayStep ? $step->id : null ?>"
        data-pathstep-type-id="<?= $step instanceof PathwayTypeStep ? $step->id : null ?>"
        data-patient-id="<?= $visit->patient_id ?>"
        data-visit-id="<?= $visit->id ?>"
        data-pathway-id="<?= $visit->pathway->id ?? null ?>">
    <span class="step<?= $icon ? " {$icon}" : '' ?>">
        <?= !$icon ? $step->short_name : '' ?>
    </span>
</span>