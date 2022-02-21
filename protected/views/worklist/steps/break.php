<?php

/**
 * @var $step PathwayStep|PathwayTypeStep
 * @var $patient Patient
 */
$is_step_instance = $step instanceof PathwayStep;
if ($is_step_instance) {
    $is_completed = (int)$step->status === PathwayStep::STEP_COMPLETED;
    $duration = null;
    if($step->start_time && $step->end_time){
        $end = DateTime::createFromFormat('Y-m-d H:i:s', $step->end_time);
        $start = DateTime::createFromFormat('Y-m-d H:i:s', $step->start_time);
        $diff = $end->diff($start);
        $duration = $diff->format('%h hrs %I mins');
    }
    $comment = $step->comment->comment ?? null;
}

?>
<div class="slide-open">
    <?php if ($is_step_instance) { ?>
    <div class="patient"><?= strtoupper($patient->last_name) . ', ' . $patient->first_name . ' (' . $patient->title . ')'?></div>
    <?php } ?>
    <h3 class="title">Break in pathway</h3>
    <div class="step-content">
        <?php if ($is_step_instance) {
            if ($is_completed) { ?>
                <table>
                    <colgroup>
                        <col class="cols-9"><col class="cols-3">
                    </colgroup>
                    <thead>
                        <tr><th>Notes</th><th>Duration</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?=$comment?></td>
                            <td><?= $duration ?></td>
                        </tr>
                    </tbody>
                </table>
            <?php } else { ?>
                <?php if (!$partial) { ?>
                    <textarea class="cols-full" rows="6" value="<?=$comment?>"></textarea>
                <?php } else {
                    echo $comment;
                } ?>
            <?php }
        } ?>
        <p>added by <b><?= $step->created_user->getFullName() ?></b></p>
    </div>
    <?php if ($is_step_instance && !$is_completed) { ?>
        <div class="step-status buff">Wait time does not pause!</div>
    <?php } ?>
    <?php if (!$partial) {?>
        <div class="step-actions">
            <?php if ($is_step_instance) { ?>
            <button class="green hint js-ps-popup-btn" data-action="next"<?= $step->status === PathwayStep::STEP_COMPLETED ? 'style="display: none;"' : ''?>>
                Patient is back
            </button>
            <button class="blue hint js-ps-popup-btn" data-action="addNotes"<?= $step->status === PathwayStep::STEP_COMPLETED ? 'style="display: none;"' : ''?>>
                Update
            </button>
            <?php } ?>
            <button class="red i-btn trash hint js-ps-popup-btn" data-action="remove"<?= $is_step_instance && $step->status === PathwayStep::STEP_COMPLETED ? 'style="display: none;"' : ''?>>
            </button>
        </div>
    <?php } ?>
</div>
