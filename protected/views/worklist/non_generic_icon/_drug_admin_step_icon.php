<?php
$assignment_id = $step->getState('assignment_id');
?>
<span
    class="oe-pathstep-btn <?= "$status_class {$step->type->type}" ?>"
    data-pathstep-id="<?= $step->id ?>"
    data-patient-id="<?= $pathway->worklist_patient->patient_id ?>"
    data-assignment-id = "<?=$assignment_id?>"
    data-visit-id="<?= $pathway->worklist_patient_id ?>"
    data-pathway-id="<?= $pathway->id ?>">
    <span class="step<?= $step->type->large_icon ? " {$step->type->large_icon}" : '' ?>">
        <?= !$step->type->large_icon ? $step->short_name : '' ?>
    </span>
</span>