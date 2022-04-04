<?php
/**
 * @var $worklist_patient Pathway
 * @var $partial bool
 */
?>
<div class="slide-open">
    <div class="patient">
        <?= strtoupper($worklist_patient->patient->last_name) . ', ' . $worklist_patient->patient->first_name . ' (' . $worklist_patient->patient->title . ')'?>
    </div>
    <?php if ($worklist_patient->pathway){
        if ($worklist_patient->pathway->start_time && !$worklist_patient->pathway->did_not_attend) { ?>
            <h3 class="title">
                Arrived <small>at</small> <?= DateTime::createFromFormat('Y-m-d H:i:s', $worklist_patient->pathway->start_time)->format('H:i') ?>
            </h3>
        <?php } elseif ($worklist_patient->pathway->did_not_attend) { ?>
            <h3 class="title">
                Did not attend
            </h3>
        <?php }
    }?>
    <?php if (!$partial && (!$worklist_patient->pathway || (int)$worklist_patient->pathway->status === Pathway::STATUS_LATER)) { ?>
    <div class="step-actions">
        <button class="green hint js-ps-popup-btn" data-action="done">
            Check-in
        </button>
        <button class="blue hint js-ps-popup-btn" data-action="DNA">
            DNA
        </button>
    </div>
    <?php } ?>
    <?php if (!$partial && $worklist_patient->pathway && ((int)$worklist_patient->pathway->status === Pathway::STATUS_STUCK || (int)$worklist_patient->pathway->status === Pathway::STATUS_WAITING)) { ?>
        <div class="step-actions">
            <button class="green hint js-ps-popup-btn js-pathway-undo-check-in" data-pathway-id=<?= $worklist_patient->pathway->id ?> data-action="undocheckin">
                Undo Check-in
            </button>
        </div>
    <?php } ?>
</div>
