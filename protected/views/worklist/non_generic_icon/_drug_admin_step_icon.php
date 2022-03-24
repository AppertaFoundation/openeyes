<?php
$assignment_id = $step->getState('assignment_id');
$icon = $step instanceof PathwayStep ? $step->type->large_icon : $step->step_type->large_icon;
$type = $step instanceof PathwayStep ? $step->type->type : $step->step_type->type
?>
<span
    class="oe-pathstep-btn <?= "$status_class {$type}" ?>"
    data-pathstep-id="<?= $step instanceof PathwayStep ? $step->id : null ?>"
    data-pathstep-type-id="<?= $step instanceof PathwayTypeStep ? $step->id : null ?>"
    data-patient-id="<?= $pathway->worklist_patient->patient_id ?>"
    data-assignment-id = "<?=$assignment_id?>"
    data-visit-id="<?= $pathway->worklist_patient_id ?>"
    data-pathway-id="<?= $pathway->id ?>">
    <span class="step<?= $icon ? " {$icon}" : '' ?>">
        <?= !$icon ? $step->short_name : '' ?>
    </span>
</span>