<?php
/**
 * @var $pathway Pathway
 * @var $patient Patient
 * @var $partial bool
 */
?>
<div class="slide-open">
    <div class="patient">
        <?= strtoupper($patient->last_name) . ', ' . $patient->first_name . ' (' . $patient->title . ')'?>
    </div>
    <?php if ($pathway->start_time && !$pathway->did_not_attend) { ?>
        <h3 class="title">
            Arrived <small>at</small> <?= DateTime::createFromFormat('Y-m-d H:i:s', $pathway->start_time)->format('h:i') ?>
        </h3>
    <?php } elseif ($pathway->did_not_attend) { ?>
        <h3 class="title">
            Did not attend
        </h3>
    <?php } ?>
    <?php if (!$partial && (int)$pathway->status === Pathway::STATUS_LATER) { ?>
    <div class="step-actions">
        <button class="green hint js-ps-popup-btn" data-action="done">
            Check-in
        </button>
        <button class="blue hint js-ps-popup-btn" data-action="DNA">
            DNA
        </button>
    </div>
    <?php } ?>
</div>
