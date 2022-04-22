<?php
$radius = 15;
$circumference = $radius * 2 * M_PI;
$offset = $circumference;

$step_text = $step->short_name;

if ((int)$step->status === $step::STEP_STARTED) {
    $total_minutes = $step->short_name;
    $total_seconds = $total_minutes * 60;

    $elapsed_seconds = time() - strtotime($step->start_time);

    //Ratio is 0.0..1.0
    $ratio = $elapsed_seconds / $total_seconds;
    $ratio_inverted = 1.0 - $ratio;

    if ($ratio < 1.0) {
        $offset = $ratio_inverted * $circumference;
    } else {
        $offset = 0;
    }
    $step_text = ceil($ratio_inverted * $total_minutes);
}
$icon = $step instanceof PathwayStep ? $step->type->large_icon : $step->step_type->large_icon;
$type = $step instanceof PathwayStep ? $step->type->type : $step->step_type->type
?>
<span
    class="oe-pathstep-btn <?= "$status_class $type" ?>"
    data-pathstep-id="<?= $step instanceof PathwayStep ? $step->id : null ?>"
    data-pathstep-type-id="<?= $step instanceof PathwayTypeStep ? $step->id : null ?>"
    data-patient-id="<?= $visit->patient_id ?>"
    data-visit-id="<?= $visit->id ?>"
    data-pathway-id="<?= $visit->pathway->id ?? null ?>"
    data-timestamp-start="<?= $step instanceof PathwayStep ? strtotime($step->start_time) : null ?>"
    data-step-data='<?=json_encode($step->toJSON())?>'
>
    <span class="step<?= $icon ? " {$icon}" : '' ?>">
        <?= !$icon ? $step_text : '' ?>
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
