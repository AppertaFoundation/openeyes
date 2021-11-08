<?php

/**
 * @var $pathway Pathway
 * @var $patient Patient
 * @var $partial bool
 */
$current_time = new DateTime();
$acceptable_wait_time = $pathway->getAcceptableWaitTime();
if (count($pathway->completed_steps) > 0) {
    $start_time = DateTime::createFromFormat(
        'Y-m-d H:i:s',
        $pathway->completed_steps[array_key_last(
            $pathway->completed_steps
        )]->end_time
    );
} else {
    $start_time = DateTime::createFromFormat(
        'Y-m-d H:i:s',
        $pathway->start_time
    );
}

$end_time = new DateTime();
$wait_time = floor(($end_time->getTimestamp() - $start_time->getTimestamp()) / 60);
?>
<div class="slide-open">
    <div class="patient"><?= strtoupper($patient->last_name) . ', ' . $patient->first_name . ' (' . $patient->title . ')'?></div>
    <h3 class="title">Waiting - (<?= $wait_time ?> mins)</h3>
    <?php if ($wait_time > $acceptable_wait_time) { ?>
        <div class="step-content"><h4>Patient is delayed</h4></div>
    <?php }?>
    <?php if (!$partial) { ?>
        <div class="step-actions">
        </div>
    <?php } ?>
</div>
