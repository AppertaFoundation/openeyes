<?php

/**
 * @var $step PathwayStep|PathwayTypeStep
 * @var $patient Patient
 */
$status = $step instanceof PathwayStep ? (int)$step->status : PathwayStep::STEP_REQUESTED;
$is_step_instance = $step instanceof PathwayStep;
$is_ready_to_start = $status === PathwayStep::STEP_REQUESTED && !in_array($status, array(PathwayStep::STEP_STARTED, PathwayStep::STEP_COMPLETED));
$is_started = $status === PathwayStep::STEP_STARTED && !in_array($status, array(PathwayStep::STEP_REQUESTED, PathwayStep::STEP_COMPLETED));
$is_completed = $status === PathwayStep::STEP_COMPLETED;
?>
<div class="slide-open">
    <?php if ($is_step_instance) { ?>
    <div class="patient"><?= strtoupper($patient->last_name) . ', ' . $patient->first_name . ' (' . $patient->title . ')'?></div>
    <?php } ?>
    <h3 class="title">Hold timer (mins)</h3>
    <?php if (!$partial && !$is_completed) { ?>
    <div class="step-actions">
        <?php if (isset($worklist_patient)) { ?>
        <button 
            class="green hint js-ps-popup-btn" 
            data-action="next"
            <?= $is_ready_to_start ? '' : 'style="display: none;"'?>
            data-special-action='{"active":"startTimer"}'
        >
            Start timer
        </button>
        <button 
            class="blue hint js-ps-popup-btn" 
            data-action="prev"<?= !$is_started ? 'style="display: none;"' : ''?>
            data-special-action='{"todo": "cancelTimer", "done": "cancelTimer"}'
        >
            Cancel timer
        </button>
        <?php } ?>
        <button class="blue i-btn left hint js-ps-popup-btn" data-action="left"<?= !$is_ready_to_start ? 'style="display: none;"' : ''?>>
        </button>
        <button class="blue i-btn right hint js-ps-popup-btn" data-action="right"<?= !$is_ready_to_start ? 'style="display: none;"' : ''?>>
        <button class="red i-btn trash hint js-ps-popup-btn" data-action="remove"<?= !$is_ready_to_start ? 'style="display: none;"' : ''?>>
        </button>
    </div>
    <?php } ?>
</div>
