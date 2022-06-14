<?php

/**
 * @var $step PathwayStep|PathwayTypeStep
 * @var $patient Patient
 * @var $red_flag bool
 * @var $partial bool
 */

use OEModule\OphCiExamination\models\OphCiExamination_AE_RedFlags_Options;
use OEModule\OphCiExamination\models\Element_OphCiExamination_AE_RedFlags;
use OEModule\OphCiExamination\models\OphCiExamination_AE_RedFlags_Options_Assignment;

$is_step_instance = $step instanceof PathwayStep;
$is_requested = (int)$step->status === PathwayStep::STEP_REQUESTED || !$step->status;
if ($is_step_instance) {
    $current_time = new DateTime();
    $wait_time = $step->start_time ? $current_time->diff(DateTime::createFromFormat('Y-m-d H:i:s', $step->start_time))->format('%i') : null;
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
    <?php }
    if (isset($red_flag) && $red_flag) {
        $red_flags_event = Element_OphCiExamination_AE_RedFlags::model()->find('event_id = ? AND nrf_check != 1', array($step->associated_event->id));
        $red_flag_options = OphCiExamination_AE_RedFlags_Options_Assignment::model()->findAll('element_id = ?', array($red_flags_event->id));
        ?>

        <h3 class="title">
            Red flagged at <?= DateTime::createFromFormat('Y-m-d H:i:s', $red_flags_event->last_modified_date)->format('H:i') ?>
        </h3>
        <div class="step-content">
            <table>
                <tbody>
                <?php foreach ($red_flag_options as $red_flag_option) { ?>
                    <tr>
                        <h4>
                            <?= OphCiExamination_AE_RedFlags_Options::model()->find(
                                'id =?',
                                array($red_flag_option->red_flag_id)
                            )->name ?>
                        </h4>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <p>added by <b><?= User::model()->findByPk($red_flags_event->last_modified_user_id)->getFullNameAndTitle()?></b></p>
    <?php } else { ?>
        <h3 class="title"><?= $step->long_name ?></h3>
        <div class="step-content">
            <?php if ($is_step_instance) { ?>
            <table>
                <colgroup>
                    <col class="cols-3">
                    <col class="cols-3">
                    <col class="cols-6">
                </colgroup>
                <thead>
                <tr>
                    <th>State</th>
                    <th>Time</th>
                    <th>Person</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Requested</td>
                    <td><?= DateTime::createFromFormat('Y-m-d H:i:s', $step->created_date)->format('H:i') ?></td>
                    <td>
                        <?= $step->created_user->getFullNameAndTitle() ?>
                    </td>
                </tr>
                <?php if ($step->started_user_id) { ?>
                <tr>
                    <td>Started</td>
                    <td><?= DateTime::createFromFormat('Y-m-d H:i:s', $step->start_time)->format('H:i') ?></td>
                    <td>
                        <?= $step->started_user->getFullNameAndTitle() ?>
                    </td>
                </tr>
                <?php } ?>
                <?php if ($step->completed_user_id) { ?>
                    <tr>
                        <td>Completed</td>
                        <td><?= DateTime::createFromFormat('Y-m-d H:i:s', $step->end_time)->format('H:i') ?></td>
                        <td>
                            <?= $step->completed_user->getFullNameAndTitle() ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <hr class="divider"/>
                <?php if (!$partial) { ?>
                    <div class="step-actions">
                        <button class="green hint js-ps-popup-btn"
                                data-action="goto"
                                data-url="<?= $step->getState('event_view_url') ?>"<?= !$step->associated_event ? ' style="display: none;"' : ''?>>
                            Go to Event
                        </button>
                    </div>
                <?php }
            } else {
                $custom_step_assignment = PathwayStepTypePresetAssignment::model()->find('custom_pathway_step_type_id = :id', [':id' => $step->step_type_id]);
                if ($custom_step_assignment) {
                    $step_type = $custom_step_assignment->standard_pathway_step_type;
                } else {
                    $step_type = $step->step_type;
                }
                if ($step_type->short_name === 'Letter') {
                    $macro = LetterMacro::model()->findByPk($step->getState('macro_id'));
                    ?>
                    <table>
                        <tr>
                            <th>Macro</th>
                            <td><?= $macro->name ?? 'None' ?></td>
                        </tr>
                    </table>
                <?php }
                elseif ($step->step_type->short_name === 'drug admin') {
                    $preset = OphDrPGDPSD_PGDPSD::model()->findByPk($step->getState('preset_id'));
                    $laterality = $step->getState('laterality');
                    ?>
                    <table>
                        <tr>
                            <th>PGD Preset</th>
                            <td><?= $preset->name ?></td>
                        </tr>
                        <tr>
                            <th>Laterality</th>
                            <td>
                                <span class="oe-eye-lat-icons">
                                <?php if ($laterality & Eye::RIGHT) { ?>
                                    <i class="oe-i laterality R medium pad"></i>
                                <?php } ?>
                                <?php if ($laterality & Eye::LEFT) { ?>
                                    <i class="oe-i laterality L medium pad"></i>
                                <?php } ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                <?php }
            } ?>
        </div>
        <div class="step-comments">
            <?php if (isset($worklist_patient) && !$partial) { ?>
            <div class="flex js-comments-edit" style="<?= $step instanceof PathwayStep && $step->comment ? 'display: none;' : '' ?>">
                <div class="cols-11">
                    <input class="cols-full js-step-comments" type="text" maxlength="80" placeholder="Comments"
                    <?= $step instanceof PathwayStep && $step->comment ? 'value="' . $step->comment->comment . '"' : '' ?>/>
                    <div class="character-counter">
                        <span class="percent-bar"
                              style="width: <?= $step instanceof PathwayStep && $step->comment ? strlen($step->comment->comment) / 0.8 : 0 ?>%;"></span>
                    </div>
                </div>
                <i class="oe-i save-plus js-save"></i>
            </div>
            <?php } ?>
            <?php if ($is_step_instance) { ?>
            <div class="flex js-comments-view" style="<?= !$step->comment ? 'display: none;' : '' ?>">
                <div class="cols-11">
                    <i class="oe-i comments small pad-right no-click"></i>
                    <em class="comment"><?= $step->comment->comment ?? '' ?></em>
                </div>
                <?php if (!$partial && (int)$step->status !== PathwayStep::STEP_COMPLETED) { ?>
                    <i class="oe-i medium-icon pencil js-edit"></i>
                <?php } ?>
            </div>
            <?php } ?>
        </div>
        <?php if (!$partial) { ?>
        <div class="step-actions">
            <?php if (isset($worklist_patient)) { ?>
                <button class="green hint js-ps-popup-btn" data-action="next"<?= (int)$step->status === PathwayStep::STEP_COMPLETED ? ' style="display: none;"' : ''?>>
                    <?= $is_step_instance && (int)$step->status === PathwayStep::STEP_STARTED ? 'Complete' : 'Start' ?>
                </button>
                <button class="blue hint js-ps-popup-btn" data-action="prev"<?= $is_requested ? ' style="display: none;"' : ''?>>
                    <?php if ($is_step_instance && (int)$step->status === PathwayStep::STEP_COMPLETED) {
                        echo 'Undo complete';
                    } else {
                        echo 'Cancel';
                    } ?>
                </button>
            <?php } ?>
            <button class="blue i-btn left hint js-ps-popup-btn" data-action="left"<?= !$is_requested ? ' style="display: none;"' : ''?><?= $is_first_requested_step ? ' disabled' : ''?>>
            </button>
            <button class="blue i-btn right hint js-ps-popup-btn" data-action="right"<?= !$is_requested ? ' style="display: none;"' : ''?><?= $is_last_step ? ' disabled' : ''?>>
            </button>
            <button class="red i-btn trash hint js-ps-popup-btn" data-action="remove"<?= !$is_requested ? ' style="display: none;"' : ''?>>
            </button>
        </div>
        <?php } ?>
        <?php if ($is_step_instance) { ?>
            <div class="step-status <?= $step->getStatusString() ?>">
                <?php switch ((int)$step->status) {
                    case PathwayStep::STEP_STARTED:
                        echo 'Currently active';
                        break;
                    case PathwayStep::STEP_COMPLETED:
                        echo 'Completed';
                        break;
                    default:
                        echo 'Waiting to be done';
                        break;
                } ?>
            </div>
        <?php }
    } ?>
</div>
<?php if (!$partial) { ?>
    <div class="close-icon-btn">
        <i class="oe-i remove-circle medium-icon"></i>
    </div>
<?php } ?>
