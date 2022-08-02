<?php

/**
 * @var $step PathwayStep|PathwayTypeStep
 * @var $visit WorklistPatient
 * @var $partial bool
 * @var $assignment OphDrPGDPSD_Assignment
 * @var $interactive int
 * @var $for_administer bool
 */
$is_step_instance = $step instanceof PathwayStep;
$assigned_meds = $assignment->getAssignedMeds();
$eye_meds = $assigned_meds['eye'];
$other_meds = $assigned_meds['other'];
$top_right_icon = $partial ? 'expand small-icon' : 'remove-circle medium-icon';
$is_first_requested_step = true;
$is_last_step = true;
if ($is_step_instance) {
    $assignment_status = $assignment->getStatusDetails(false, $step);
    $is_assignment_complete = (int)$step->status === PathwayStep::STEP_COMPLETED;
    $has_event = false;
    $index = 0;
    $patient_name = $assignment->patient ? $assignment->patient->getHSCICName() : null;
} else {
    $is_assignment_complete = false;
}

if ($is_step_instance) {
    if (count($step->pathway->requested_steps)) {
        $is_last_step = $step->isLastRequestedStep();
        $is_first_requested_step = $step->isFirstRequestedStep();
    }
} else {
    $is_last_step = $step->id === $step->pathway_type->default_steps[count($step->pathway_type->default_steps) - 1]->id;
    $is_first_requested_step = $step->id === $step->pathway_type->default_steps[0]->id;
}
?>
<div class="slide-open" data-status-dict='<?=$assignment->getStatusDetails(true)?>'>
    <form id="worklist-administration-form" method="POST" action='/OphDrPGDPSD/PSD/unlockPSD'>
        <?php if ($is_step_instance) { ?>
            <div class="patient"><?=$patient_name?></div>
        <?php } ?>
        <h3 class="title"><?=$assignment->getAssignmentTypeAndName()['name']?></h3>
        <?php if (isset($visit)) { ?>
            <input type="hidden" class="no-clear" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>
            <input type="hidden" name="step_id" value="<?= $is_step_instance ? $step->id : null?>"/>
            <input type="hidden" name="step_type_id" value="<?= !$is_step_instance ? $step->id : null ?>"/>
            <input type="hidden" name="visit_id" value="<?= $visit->id ?>"/>
            <input type="hidden" name="Assignment[patient_id]" value="<?= $patient_id ?>">
            <input type="hidden" name="Assignment[assignment_id]" value="<?=$assignment->id?>">
            <input type="hidden" name="Assignment[worklist_patient_id]" value="<?=$assignment->visit_id?>">
            <input type="hidden" name="Assignment[has_event]" value="<?=$has_event?>">
            <input type="hidden" class="js-current-icon-class" value="<?=$assignment_status['css']?>">
        <?php } ?>
        <div class="step-content">
            <?php if ($assignment->comment) {?>
                <div class="small-row">
                    <i class="oe-i comments-who small pad-right js-has-tooltip" data-tooltip-content="<small>User comment by </small><br><?=$assignment->comment->commented_user->getFullName()?>"></i>
                    <span class="user-comment"><?=$assignment->comment?></span>
                </div>
            <?php }?>
            <?php if ($assignment->worklist_patient) {?>
            <small class="fade">Valid: <?=\Helper::convertDate2NHS($assignment->worklist_patient->worklist->end)?></small>
            <?php } ?>
            <hr class="divider" />
            <table>
                <colgroup>
                    <col class="cols-2">
                    <col class="cols-5">
                    <col class="cols-5">
                </colgroup>
                <tbody>
                    <?php foreach ($eye_meds as $eye_med) {?>
                    <tr>
                        <td>
                            <span class="oe-eye-lat-icons">
                                <i class="oe-i laterality <?=isset($eye_med['Right']) ? 'R' : 'NA'?> small pad"></i>
                                <i class="oe-i laterality <?=isset($eye_med['Left']) ? 'L' : 'NA'?> small pad"></i>
                            </span>
                        </td>
                        <td><?=$eye_med['term']?></td>
                        <td><?=$eye_med['dose_unit']?></td>
                    </tr>
                        <?php if (!$partial) {?>
                    <tr>
                        <td><i class="js-administer-icon oe-i <?=$eye_med['administration_status']?> small no-click pad"></i></td>
                            <?php
                            foreach (['Right', 'Left'] as $side) {
                                $eye_med_obj = isset($eye_med[$side]) ? $eye_med[$side] : null;
                                $lat_id = isset($eye_med[$side]) ? $eye_med[$side]->laterality : null;
                                $icon_css = isset($eye_med[$side]) ? $side[0]: null;
                                $index = $eye_med_obj ? $eye_med_obj->id : $index;
                                $this->renderPartial(
                                    'application.modules.OphDrPGDPSD.views.pathstep.pathstep_view_admin',
                                    array(
                                        'assignment' => $assignment,
                                        'med_obj' => $eye_med_obj,
                                        'side' => $side,
                                        'lat_id' => $lat_id,
                                        'icon_css' => $icon_css,
                                        'index' => $index,
                                        'for_administer' => $for_administer
                                    )
                                );

                                if (!$eye_med_obj) {
                                    $index++;
                                }
                            }
                            ?>
                    </tr>
                        <?php } ?>
                    <?php } ?>

                    <?php
                    foreach ($other_meds as $other_med) {
                        $med_obj = $other_med['obj'];
                        $index = $med_obj && $med_obj->id ? $med_obj->id : $index;
                        ?>
                    <tr>
                        <td><?=$med_obj->route?></td>
                        <td><?=$other_med['term']?></td>
                        <td><?=$other_med['dose_unit']?></td>
                    </tr>
                        <?php if (!$partial) {?>
                    <tr>
                        <td><i class="js-administer-icon oe-i <?=$other_med['administration_status']?> small no-click pad"></i></td>
                                <?php
                                    $this->renderPartial(
                                        'application.modules.OphDrPGDPSD.views.pathstep.pathstep_view_admin',
                                        array(
                                            'assignment' => $assignment,
                                            'med_obj' => $med_obj,
                                            'index' => $index,
                                            'for_administer' => $for_administer
                                        )
                                    );
                                if (!$med_obj) {
                                    $index++;
                                }
                                ?>
                    </tr>
                        <?php } ?>
                    <?php } ?>

                </tbody>
            </table>
        </div>

        <?php if (!$partial && !$is_assignment_complete && $interactive) {?>
        <div class="step-actions">
            <?php if (isset($visit)) { ?>
                <?php if ($for_administer) {?>
                    <button class="green hint js-confirm-admin" data-action="confirm_da">Confirm Administration</button>
                    <button class="blue hint js-cancel-admin" data-action="cancel_da">Cancel</button>
                <?php } else {?>
                <div class="oe-user-pin">
                    <input class="user-pin-entry" name="pincode" type="password" maxlength="6" minlength="6" inputmode="numeric" placeholder="*******">
                    <button type="submit" class="try-pin js-unlock" data-action="unlock_da" disabled>Unlock</button>
                </div>
                <button class="blue i-btn left hint js-ps-popup-btn" data-action="left"<?= $is_first_requested_step ? ' disabled' : ''?>></button>
                <button class="blue i-btn right hint js-ps-popup-btn" data-action="right"<?= $is_last_step ? ' disabled' : ''?>></button>
                <button class="red i-btn trash hint js-ps-popup-btn" data-action="remove"></button>
                <?php } ?>
            <?php } ?>
        </div>
        <?php }?>
    </form>
    <?php if ($is_step_instance) { ?>
        <div class="step-status <?=$assignment_status['css']?>"><?=$assignment_status['text']?></div>
    <?php } ?>
</div>
<div class="close-icon-btn"><i class="oe-i <?=$top_right_icon?>"></i></div>