<?php

/**
 * @var $step PathwayStep|PathwayTypeStep
 */
$is_step_instance = $step instanceof PathwayStep;

if ($is_step_instance) {
    $is_last_step = $step->id === $step->pathway->requested_steps[count($step->pathway->requested_steps) - 1]->id;
    $is_first_requested_step = $step->id === $step->pathway->requested_steps[0]->id;
} else {
    $is_last_step = $step->id === $step->pathway_type->default_steps[count($step->pathway_type->default_steps) - 1]->id;
    $is_first_requested_step = $step->id === $step->pathway_type->default_steps[0]->id;
}

?>
<div class="slide-open">
    <?php if ($is_step_instance) { ?>
        <div class="patient">
            <?= strtoupper($patient->last_name) . ', ' . $patient->first_name . ' (' . $patient->title . ')'?>
        </div>
    <?php } ?>
    <h3 class="title">Path decision</h3>
    <?php if (isset($worklist_patient)) { ?>
        <div class="step-content">
            <h4>Assess patient and decide suitable path at this point</h4>
            <p>added by <b><?= $step->created_user->getFullName() ?></b></p>
        </div>
        <?php
            $this->renderPartial(
                'step_components/_comment',
                array(
                    'partial' => $partial,
                    'model' => $step,
                    'visit' => $worklist_patient
                )
            );
    } else { ?>
        <div class="step-content">
            <h4>Assess patient and decide suitable path at this point</h4>
        </div>
    <?php } ?>
    <div class="step-actions">
        <button class="blue i-btn left hint js-ps-popup-btn" data-action="left"<?= $is_first_requested_step ? ' disabled' : ''?>></button>
        <button class="blue i-btn right hint js-ps-popup-btn" data-action="right"<?= $is_last_step ? ' disabled' : ''?>></button>
        <button class="red i-btn trash hint js-ps-popup-btn" data-action="remove"></button>
    </div>
</div>
<?php if (!$partial) { ?>
<div class="close-icon-btn">
    <i class="oe-i remove-circle medium-icon"></i>
</div>
<?php } ?>