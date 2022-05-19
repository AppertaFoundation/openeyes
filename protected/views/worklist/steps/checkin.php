<?php
/**
 * @var $worklist_patient Pathway
 * @var $step PathwayStep|PathwayTypeStep
 * @var $partial bool
 */
$is_requested = $step instanceof PathwayTypeStep || (int)$step->status === PathwayStep::STEP_REQUESTED;
?>
<div class="slide-open">
    <?php if (isset($worklist_patient)) { ?>
    <div class="patient">
        <?= strtoupper($worklist_patient->patient->last_name) . ', ' . $worklist_patient->patient->first_name . ' (' . $worklist_patient->patient->title . ')'?>
    </div>
        <?php if ($step instanceof PathwayStep && $step->start_time && !$worklist_patient->pathway->did_not_attend) { ?>
            <h3 class="title">
                Arrived <small>at</small> <?= DateTime::createFromFormat('Y-m-d H:i:s', $step->start_time)->format('H:i') ?>
            </h3>
        <?php } elseif ($worklist_patient->pathway && $worklist_patient->pathway->did_not_attend) { ?>
            <h3 class="title">
                Did not attend
            </h3>
        <?php }
    } else { ?>
        <h3 class="title">Check In</h3>
    <?php } ?>
    <?php if (!$partial) { ?>
    <div class="step-actions">
        <?php if (isset($worklist_patient)) {
            if ((int)$step->status === PathwayStep::STEP_REQUESTED) { ?>
                <button class="green hint js-ps-popup-btn" data-action="done">
                    Check-in
                </button>
                <button class="blue hint js-ps-popup-btn" data-action="DNA">
                    DNA
                </button>
            <?php } elseif ((int)$step->status === PathwayStep::STEP_COMPLETED) { ?>
                <button class="green hint js-ps-popup-btn js-pathway-undo-check-in" data-step-id=<?= $step->id ?> data-action="undocheckin">
                    Undo Check-in
                </button>
            <?php }
        } ?>
        <button class="blue i-btn left hint js-ps-popup-btn" data-action="left"<?= !$is_requested ? ' style="display: none;"' : ''?>>
        </button>
        <button class="blue i-btn right hint js-ps-popup-btn" data-action="right"<?= !$is_requested ? ' style="display: none;"' : ''?>>
        </button>
        <button class="red i-btn trash hint js-ps-popup-btn" data-action="remove"<?= !$is_requested ? ' style="display: none;"' : ''?>>
        </button>
    </div>
    <?php } ?>
</div>
