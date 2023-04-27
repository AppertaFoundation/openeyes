<?php
/**
 * @var $visit WorklistPatient
 */
$acceptable_wait_time = Pathway::model()->getAcceptableWaitTime();
$pathway = $visit->pathway;
?>

<div class="pathway" data-visit-id="<?= $visit->id ?>" data-pathway-id="<?= $pathway->id ?? null ?>">
    <?php
    if ($pathway) {
        $check_in_completed = false;
        $checkin_step_type = PathwayStepType::model()->find('short_name = \'checkin\'');

        foreach ($pathway->completed_steps as $step) {
            $check_in_completed = $check_in_completed || (int)$step->step_type_id === (int)$checkin_step_type->id;

            $status_class = $step->getStatusString() . (empty($step->comment) ? '' : ' has-comments');
            if (in_array($step->type->short_name, PathwayStep::NON_GENERIC_STEP)) {
                $short_name = str_replace(' ', '_', $step->type->short_name);
                $view_file = "_{$short_name}_step_icon";
                $this->renderPartial(
                    "//worklist/non_generic_icon/$view_file",
                    array('visit' => $visit, 'step' => $step, 'status_class' => $status_class)
                );
            } else { ?>
                <span class="oe-pathstep-btn <?= "$status_class {$step->type->type}" ?>" data-pathstep-id="<?= $step->id ?>"
                      data-patient-id="<?= $pathway->worklist_patient->patient_id ?>"
                      data-pathstep-type-id=""
                      data-visit-id="<?= $pathway->worklist_patient_id ?>"
                      data-pathway-id="<?= $pathway->id ?>"
                      data-long-name="<?= $step->long_name ?>"
                      data-test="<?= $step->type->large_icon ? explode("-", $step->type->large_icon)[1] . "-step-{$visit->worklist->id}" : '' ?>">
                    <span class="step<?= $step->type->large_icon ? " {$step->type->large_icon}" : '' ?>">
                        <?= !$step->type->large_icon ? $step->short_name : '' ?>
                    </span>
                    <span class="info">
                        <!-- PathwayStep getter: getStepStartTime() -->
                        <?= $step->stepStartTime ?>
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
                      data-pathstep-type-id=""
                      data-visit-id="<?= $pathway->worklist_patient_id ?>"
                      data-pathway-id="<?= $pathway->id ?>"
                      data-red-flag="true" >
                    <span class="step i-redflag"></span>
                </span>
                <?php }
            }
        }
        foreach ($pathway->started_steps as $step) {
            $status_class = $step->getStatusString() . (empty($step->comment) ? '' : ' has-comments'); ?>
            <?php if (in_array($step->type->short_name, PathwayStep::NON_GENERIC_STEP)) {
                $short_name = str_replace(' ', '_', $step->type->short_name);
                $view_file = "_{$short_name}_step_icon";
                $this->renderPartial(
                    "//worklist/non_generic_icon/$view_file",
                    array('visit' => $visit, 'step' => $step, 'status_class' => $status_class)
                );
            } else { ?>
            <span class="oe-pathstep-btn <?= "$status_class {$step->type->type}" ?>" data-pathstep-id="<?= $step->id ?>"
                  data-patient-id="<?= $pathway->worklist_patient->patient_id ?>"
                  data-pathstep-type-id=""
                  data-visit-id="<?= $pathway->worklist_patient_id ?>"
                  data-pathway-id="<?= $pathway->id ?>"
                  data-long-name="<?= $step->long_name ?>"
                  data-test="started-path-step-<?=str_replace(' ', '-', strtolower($step->long_name)) ?>">
                <span class="step<?= $step->type->large_icon ? " {$step->type->large_icon}" : '' ?>">
                    <?= !$step->type->large_icon ? $step->short_name : '' ?>
                </span>
                <span class="info">
                    <!-- PathwayStep getter: getStepStartTime() -->
                    <?= $step->stepStartTime ?>
                </span>
            </span>
            <?php }
        }
        if (
            $check_in_completed &&
            $pathway->start_time
            && !$pathway->end_time
            && count($pathway->started_steps) === 0
        ) {
            $wait_time_since_last_action = $pathway->getWaitTimeSinceLastAction();
            extract($wait_time_since_last_action, EXTR_OVERWRITE);
            ?>
        <span class="oe-pathstep-btn buff <?= $status_class ?>" data-pathstep-id="wait"
              data-pathstep-type-id=""
              data-patient-id="<?= $pathway->worklist_patient->patient_id ?>"
              data-visit-id="<?= $pathway->worklist_patient_id ?>"
              data-pathway-id="<?= $pathway->id ?>"
              data-test="wait-pathstep">
            <span class="step <?= $icon_class ?? null ?>"></span>
            <span class="info"><?= $wait_time ?? null ?></span>
        </span>
        <?php }
        foreach ($pathway->requested_steps as $step) {
            $status_class = $step->getStatusString() . (empty($step->comment) ? '' : ' has-comments'); ?>
            <?php if (in_array($step->type->short_name, PathwayStep::NON_GENERIC_STEP)) {
                $short_name = str_replace(' ', '_', $step->type->short_name);
                $view_file = "_{$short_name}_step_icon";
                $this->renderPartial(
                    "//worklist/non_generic_icon/$view_file",
                    array('visit' => $visit, 'step' => $step, 'status_class' => $status_class)
                );
            } else { ?>
            <span class="oe-pathstep-btn <?= "$status_class {$step->type->type}" ?>" data-pathstep-id="<?= $step->id ?>"
                  data-patient-id="<?= $pathway->worklist_patient->patient_id ?>"
                  data-pathstep-type-id=""
                  data-visit-id="<?= $pathway->worklist_patient_id ?>"
                  data-pathway-id="<?= $pathway->id ?>"
                  data-long-name="<?= $step->long_name ?>"
                  data-test="<?= $step->type->large_icon ? explode("-", $step->type->large_icon)[1] . "-step-{$visit->worklist->id}" : str_replace(' ', '-', strtolower($step->long_name)) ?>">
                <span class="step<?= $step->type->large_icon ? " {$step->type->large_icon}" : '' ?>">
                    <?= !$step->type->large_icon ? $step->short_name : '' ?>
                </span>
                <span class="info" style="display: none;">
                    <!-- PathwayStep getter: getStepStartTime() -->
                    <?= $step->stepStartTime ?>
                </span>
            </span>
            <?php }
        }
    } else {
        foreach ($visit->worklist->worklist_definition->pathway_type->default_steps as $step) {
            $status_class = $step->getStatusString() . (empty($step->comment) ? '' : ' has-comments'); ?>
            <?php if (in_array($step->step_type->short_name, PathwayStep::NON_GENERIC_STEP)) {
                $short_name = str_replace(' ', '_', $step->step_type->short_name);
                $view_file = "_{$short_name}_step_icon";
                $this->renderPartial(
                    "//worklist/non_generic_icon/$view_file",
                    array('visit' => $visit, 'step' => $step, 'status_class' => $status_class)
                );
            } else { ?>
                <span class="oe-pathstep-btn <?= "$status_class {$step->step_type->type}" ?>" data-pathstep-type-id="<?= $step->id ?>"
                      data-pathstep-id=""
                      data-patient-id="<?= $visit->patient_id ?>"
                      data-visit-id="<?= $visit->id ?>"
                      data-pathway-id=""
                      data-long-name="<?= $step->long_name ?>"
                      data-test="<?= $step->step_type->large_icon ? explode("-", $step->step_type->large_icon)[1] . "-step-{$visit->worklist->id }" : str_replace(' ', '-', strtolower($step->long_name)) ?>">
                <span class="step<?= $step->step_type->large_icon ? " {$step->step_type->large_icon}" : '' ?>">
                    <?= !$step->step_type->large_icon ? $step->short_name : '' ?>
                </span>
                <span class="info" style="display: none;"></span>
            </span>
            <?php }
        }
    }
    if ($pathway && (int)$pathway->status === Pathway::STATUS_DONE) {
        $formatted_time = $pathway->end_time ? DateTime::createFromFormat('Y-m-d H:i:s', $pathway->end_time)->format(
            'H:i'
        ) : date('H:i') ?>

        <span class="oe-pathstep-btn done buff finish" data-pathstep-id="finished"
              data-pathstep-type-id=""
              data-patient-id="<?= $pathway->worklist_patient->patient_id ?>"
              data-visit-id="<?= $pathway->worklist_patient_id ?>">
            <span class="step i-fin"></span>
            <span class="info"><?= $formatted_time ?></span>
        </span>
    <?php } ?>
</div>
