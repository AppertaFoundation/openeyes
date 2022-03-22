<?php

/**
 * @var $pathway Pathway
 */
$acceptable_wait_time = $pathway->getAcceptableWaitTime();
?>

<div class="pathway" data-visit-id="<?= $pathway->worklist_patient_id ?>" data-pathway-id="<?= $pathway->id ?>">
    <?php
    // It is assumed that the checkin step is always the first step, regardless of its status.
    if ((int)$pathway->status === Pathway::STATUS_LATER) {
        $status_class = 'todo';
    } else {
        $status_class = 'done';
    }
    $time = $pathway->start_time;
    $formatted_time = $time ? DateTime::createFromFormat('Y-m-d H:i:s', $time)->format('H:i') : null
    ?>
    <span class="oe-pathstep-btn <?= "$status_class process" ?>" data-pathstep-id="checkin"
          data-patient-id="<?= $pathway->worklist_patient->patient_id ?>"
          data-visit-id="<?= $pathway->worklist_patient_id ?>">
        <?php if ($pathway->did_not_attend) { ?>
            <span class="step">DNA</span>
        <?php } else { ?>
            <span class="step i-arr"></span>
        <?php } ?>
        <span class="info"<?= $status_class !== 'done' || $pathway->did_not_attend ? 'style="display: none;"' : '' ?>>
            <?= $formatted_time ?>
        </span>
    </span>
    <?php foreach ($pathway->completed_steps as $step) {
        $status_class = $step->getStatusString();
        $time = $step->start_time;
        $formatted_time = $time ? DateTime::createFromFormat('Y-m-d H:i:s', $time)->format('H:i') : null ?>
        <?php if (in_array($step->type->short_name, PathwayStep::NON_GENERIC_STEP)) {
            $short_name = str_replace(' ', '_', $step->type->short_name);
            $view_file = "_{$short_name}_step_icon";
            $this->renderPartial(
                "//worklist/non_generic_icon/$view_file",
                array('pathway' => $pathway, 'step' => $step, 'status_class' => $status_class)
            );
        } else { ?>
            <span class="oe-pathstep-btn <?= "$status_class {$step->type->type}" ?>" data-pathstep-id="<?= $step->id ?>"
                  data-patient-id="<?= $pathway->worklist_patient->patient_id ?>"
                  data-visit-id="<?= $pathway->worklist_patient_id ?>"
                  data-pathway-id="<?= $pathway->id ?>"
                  data-long-name="<?= $step->long_name ?>">
                <span class="step<?= $step->type->large_icon ? " {$step->type->large_icon}" : '' ?>">
                    <?= !$step->type->large_icon ? $step->short_name : '' ?>
                </span>
                <span class="info">
                    <?= $formatted_time ?>
                </span>
            </span>
            <?php
            $red_flag_count = isset($step->associated_event)
                ? \OEModule\OphCiExamination\models\Element_OphCiExamination_AE_RedFlags::model()
                    ->count(
                        'event_id = :event_id AND nrf_check != 1',
                        array(':event_id' => $step->associated_event->id)
                    )
                : 0;
            if ($red_flag_count > 0) { ?>
            <span class="oe-pathstep-btn buff red-flag" data-pathstep-id="<?= $step->id ?>"
                  data-patient-id="<?= $pathway->worklist_patient->patient_id ?>"
                  data-visit-id="<?= $pathway->worklist_patient_id ?>"
                  data-pathway-id="<?= $pathway->id ?>"
                  data-red-flag="true" >
                <span class="step i-redflag"></span>
            </span>
            <?php }
        }
    } ?>
    <?php foreach ($pathway->started_steps as $step) {
        $status_class = $step->getStatusString();
        $formatted_time = $step->start_time ? DateTime::createFromFormat('Y-m-d H:i:s', $step->start_time)->format(
            'H:i'
        ) : null; ?>
        <?php if (in_array($step->type->short_name, PathwayStep::NON_GENERIC_STEP)) {
            $short_name = str_replace(' ', '_', $step->type->short_name);
            $view_file = "_{$short_name}_step_icon";
            $this->renderPartial(
                "//worklist/non_generic_icon/$view_file",
                array('pathway' => $pathway, 'step' => $step, 'status_class' => $status_class)
            );
        } else { ?>
        <span class="oe-pathstep-btn <?= "$status_class {$step->type->type}" ?>" data-pathstep-id="<?= $step->id ?>"
              data-patient-id="<?= $pathway->worklist_patient->patient_id ?>"
              data-visit-id="<?= $pathway->worklist_patient_id ?>"
              data-pathway-id="<?= $pathway->id ?>"
              data-long-name="<?= $step->long_name ?>">
            <span class="step<?= $step->type->large_icon ? " {$step->type->large_icon}" : '' ?>">
                <?= !$step->type->large_icon ? $step->short_name : '' ?>
            </span>
            <span class="info"><?= $formatted_time ?></span>
        </span>
        <?php }
    }
    if (
        $pathway->start_time
        && !$pathway->end_time
        && count($pathway->started_steps) === 0
    ) {
        $wait_time_since_last_action = $pathway->getWaitTimeSinceLastAction();
        extract($wait_time_since_last_action, EXTR_OVERWRITE);
        ?>
        <span class="oe-pathstep-btn buff <?= $status_class ?>" data-pathstep-id="wait"
              data-patient-id="<?= $pathway->worklist_patient->patient_id ?>"
              data-visit-id="<?= $pathway->worklist_patient_id ?>"
              data-pathway-id="<?= $pathway->id ?>">
            <span class="step <?= $icon_class ?? null ?>"></span>
            <span class="info"><?= $wait_time ?? null ?></span>
        </span>
    <?php }
    foreach ($pathway->requested_steps as $step) {
        $status_class = $step->getStatusString();
        $formatted_time = $step->start_time ? DateTime::createFromFormat('Y-m-d H:i:s', $step->start_time)->format(
            'H:i'
        ) : null; ?>
        <?php if (in_array($step->type->short_name, PathwayStep::NON_GENERIC_STEP)) {
            $short_name = str_replace(' ', '_', $step->type->short_name);
            $view_file = "_{$short_name}_step_icon";
            $this->renderPartial(
                "//worklist/non_generic_icon/$view_file",
                array('pathway' => $pathway, 'step' => $step, 'status_class' => $status_class)
            );
        } else { ?>
        <span class="oe-pathstep-btn <?= "$status_class {$step->type->type}" ?>" data-pathstep-id="<?= $step->id ?>"
              data-patient-id="<?= $pathway->worklist_patient->patient_id ?>"
              data-visit-id="<?= $pathway->worklist_patient_id ?>"
              data-pathway-id="<?= $pathway->id ?>"
              data-long-name="<?= $step->long_name ?>">
            <span class="step<?= $step->type->large_icon ? " {$step->type->large_icon}" : '' ?>">
                <?= !$step->type->large_icon ? $step->short_name : '' ?>
            </span>
            <span class="info" style="display: none;"><?= $formatted_time ?></span>
        </span>
        <?php }
    }
    if ((int)$pathway->status === Pathway::STATUS_DONE) {
        $formatted_time = $pathway->end_time ? DateTime::createFromFormat('Y-m-d H:i:s', $pathway->end_time)->format(
            'H:i'
        ) : date('H:i') ?>
        <span class="oe-pathstep-btn done buff finish" data-pathstep-id="finished"
              data-patient-id="<?= $pathway->worklist_patient->patient_id ?>"
              data-visit-id="<?= $pathway->worklist_patient_id ?>">
            <span class="step i-fin"></span>
            <span class="info"><?= $formatted_time ?></span>
        </span>
    <?php } ?>
</div>
