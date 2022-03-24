<?php
$radius = 15;
$circumference = $radius * 2 * M_PI;
$offset = $circumference;
if((int)$step->status === $step::STEP_STARTED){
    $total = $step->short_name * 60;
    $elapsed = time() - strtotime($step->start_time);
    $percent =  ($elapsed / $total) * 100;
    if($percent < 100){
        $offset = $circumference - $percent / 100 * $circumference;
    } else {
        $offset = 0;
    }
}
$icon = $step instanceof PathwayStep ? $step->type->large_icon : $step->step_type->large_icon;
?>
<span
    class="oe-pathstep-btn <?= "$status_class {$step->type->type}" ?>"
    data-pathstep-id="<?= $step instanceof PathwayStep ? $step->id : null ?>"
    data-pathstep-type-id="<?= $step instanceof PathwayTypeStep ? $step->id : null ?>"
    data-patient-id="<?= $pathway->worklist_patient->patient_id ?>"
    data-visit-id="<?= $pathway->worklist_patient_id ?>"
    data-pathway-id="<?= $pathway->id ?>"
    data-timestamp-start="<?=strtotime($step->start_time)?>"
    data-step-data='<?=json_encode($step->toJSON())?>'
>
    <span class="step<?= $icon ? " {$icon}" : '' ?>">
        <?= !$icon ? $step->short_name : '' ?>
    </span>
    <svg class="progress-ring" viewBox="0 0 34 34">
        <circle 
            fill="transparent"
            r="<?=$radius?>" cx="17" cy="17" 
            style="stroke-dasharray: <?=$circumference?>, <?=$circumference?>; stroke-dashoffset: <?=$offset?>px;">
        </circle>
    </svg>
    <button 
        class="green hint js-ps-popup-btn" 
        data-action="next"
        style="display: none;"
        data-special-action='{"active":"startTimer"}'
        data-pathstep-id="<?= $step->id ?>"
    >
        Start timer
    </button>
</span>
