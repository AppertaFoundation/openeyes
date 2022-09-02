<?php
/**
 * @var $step PathwayStep|PathwayTypeStep
 * @var $patient Patient
 */
$is_step_instance = $step instanceof PathwayStep;
$is_requested = true;
$is_completed = false;

if ($is_step_instance) {
    $hide_non_requested_buttons = (int)$step->status !== PathwayStep::STEP_REQUESTED;
    $hide_non_active_buttons = (int)$step->status !== PathwayStep::STEP_STARTED;
    $hide_non_complete_buttons = (int)$step->status !== PathwayStep::STEP_COMPLETED;
    $is_requested = (int)$step->status === PathwayStep::STEP_REQUESTED || !$step->status;
    $is_completed = (int)$step->status === PathwayStep::STEP_COMPLETED;

    $is_last_step = $step->isLastRequestedStep();
    $is_first_requested_step = $step->isFirstRequestedStep();
} else {
    $is_last_step = $step->id === $step->pathway_type->default_steps[count($step->pathway_type->default_steps) - 1]->id;
    $is_first_requested_step = $step->id === $step->pathway_type->default_steps[0]->id;
}
?>
<div class="slide-open">
    <?php if ($is_step_instance) { ?>
        <div class="patient"><?= strtoupper($patient->last_name) . ', ' . $patient->first_name . ' (' . $patient->title . ')'?></div>
    <?php } ?>
    <h3 class="title"><?= $step->long_name ?></h3>
    <div class="step-content">
        <?php if ($is_completed) {?>
            <table>
                <colgroup>
                    <col class="cols-3">
                    <col class="cols-3">
                    <col class="cols-6">
                </colgroup>
                <thead>
                    <tr><th>State</th><th>Time</th><th>Mins</th></tr>
                </thead>
                <tbody>
                    <tr><td>Requested</td><td><small>at</small> <?=date('H:i', strtotime($step->created_date))?></td><td><?=$step->created_user->getFullName()?></td></tr>
                    <tr><td>Confirmed</td><td><small>at</small> <?=date('H:i', strtotime($step->end_time))?></td><td><?=$step->completed_user->getFullName()?></td></tr>
                </tbody>
            </table>
        <?php } else { ?>
        <h4>Patient is ready for check out</h4>
            <?php if ($is_step_instance) { ?>
        <p>added by <b><?= $step->created_user->contact->getFullName()?></b></p>
            <?php } ?>
        <?php } ?>
    </div>
    <?php if (!$partial) { ?>
    <div class="step-actions">
        <?php if (isset($worklist_patient)) { ?>
            <button 
                class="green hint js-ps-popup-btn" 
                data-pathstep-id="<?= $step instanceof PathwayStep ? $step->id : null ?>"
                data-pathway-id="<?= $step instanceof PathwayStep ? $step->pathway->id : null ?>"
                data-action="checkout"
                <?= $is_completed ? 'style="display: none;"' : ''?>>
                Check out
            </button>
            <button 
                class="<?= $is_completed ? 'blue' : 'red' ?> hint js-ps-popup-btn" 
                data-pathstep-id="<?= $step instanceof PathwayStep ? $step->id : null ?>"
                data-pathway-id="<?= $step instanceof PathwayStep ? $step->pathway->id : null ?>"
                data-action="undo_finish"
                <?= $is_requested ? 'style="display: none;"' : ''?>>
                Undo check out
            </button>
        <?php } ?>
        <button class="blue i-btn left hint js-ps-popup-btn" data-action="left"<?= !$is_requested ? 'style="display: none;"' : ''?><?= $is_first_requested_step ? ' disabled' : ''?>>
        </button>
        <button class="blue i-btn right hint js-ps-popup-btn" data-action="right"<?= !$is_requested ? 'style="display: none;"' : ''?><?= $is_last_step ? ' disabled' : ''?>>
        </button>
        <button class="red i-btn trash hint js-ps-popup-btn" data-action="remove"<?= !$is_requested ? 'style="display: none;"' : ''?>>
        </button>
    </div>
    <?php } ?>
</div>
