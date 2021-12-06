<?php

/**
 * @var $step PathwayStep|PathwayTypeStep
 * @var $patient Patient
 */
$is_step_instance = $step instanceof PathwayStep;
$selected_preset = VisualFieldTestPreset::model()->findByPk($step->getState('preset_id'));
$selected_test_type = $selected_preset->testType ?? VisualFieldTestType::model()->findByPk($step->getState('test_type_id'));
$selected_test_option = $selected_preset->option ?? VisualFieldTestOption::model()->findByPk(
    $step->getState('test_option')
);

$test_types = VisualFieldTestType::model()->activeOrPk($selected_test_type->id)->findAll();
$test_presets = VisualFieldTestPreset::model()->findAllAtLevel(ReferenceData::LEVEL_INSTITUTION);
$test_options = VisualFieldTestOption::model()->activeOrPk($selected_test_option->id)->findAll();

$laterality = $step->getState('laterality');
$is_config = (int)$step->status === PathwayStep::STEP_CONFIG;
$is_requested = (int)$step->status === PathwayStep::STEP_REQUESTED || $is_config;
$is_requested_only = (int)$step->status === PathwayStep::STEP_REQUESTED;
if ($is_step_instance) {
    $current_time = new DateTime();
    $wait_time = $step->start_time ? $current_time->diff(DateTime::createFromFormat('Y-m-d H:i:s', $step->start_time))->format('%i') : null;
}
?>
<div class="slide-open">
    <?php if ($is_step_instance) { ?>
        <div class="patient"><?= strtoupper($patient->last_name) . ', ' . $patient->first_name . ' (' . $patient->title . ')'?></div>
    <?php } ?>
        <h3 class="title">Visual Fields</h3>
        <div class="step-content">
            <form id="visual-fields-form"<?= !$is_config ? ' style="display: none;' : null ?>>
                <fieldset>
                    <label class="highlight inline">
                        <input name="eyelat_select_R" value="2" type="checkbox" <?= (int)$laterality === Eye::RIGHT || (int)$laterality === Eye::BOTH ? "checked" : "" ?>/>
                        <span class="oe-eye-lat-icons">
                            <i class="oe-i laterality R small pad"></i>
                        </span>
                    </label>
                    <label class="highlight inline">
                        <input name="eyelat_select_L" value="1" type="checkbox" <?= (int)$laterality === Eye::LEFT || (int)$laterality === Eye::BOTH ? "checked" : "" ?>/>
                        <span class="oe-eye-lat-icons">
                            <i class="oe-i laterality L small pad"></i>
                        </span>
                    </label>
                </fieldset>
                <hr class="divider"/>
                <fieldset>
                    <?php foreach ($test_presets as $preset) { ?>
                        <label class="highlight">
                            <input value="<?= $preset->id ?>" name="preset_id" type="radio" <?= ($selected_preset && $selected_preset->id === $preset->id) ? 'selected' : null ?>/>
                            <?= $preset->name ?>
                        </label>
                    <?php } ?>
                    <label class="highlight">
                        <input value="" name="preset_id" type="radio" <?= (!$selected_preset) ? 'selected' : null ?>/>
                        Custom field settings
                    </label>
                </fieldset>
                <div class="js-field-custom" style="display: none;">
                    <hr class="divider"/>
                    <table>
                        <colgroup>
                            <col class="cols-4">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th>Test type</th>
                            <td>
                                <fieldset>
                                    <?php foreach ($test_types as $type) { ?>
                                        <label class="highlight inline">
                                            <input value="<?= $type->id ?>" name="test_type_id" type="radio"/>
                                            <?= $type->short_name ?>
                                        </label>
                                    <?php } ?>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <th>SITA Algorithm</th>
                            <td>
                                <fieldset>
                                    <?php foreach ($test_options as $option) { ?>
                                        <label class="highlight inline">
                                            <input value="<?= $option->id ?>" name="test_option_id" type="radio"/>
                                            <?= $option->short_name ?>
                                        </label>
                                    <?php } ?>
                                </fieldset>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </form>
            <span class="oe-eye-lat-icons"<?= $is_config ? ' style="display: none;"' : null ?>>
                <?php if ($laterality & Eye::RIGHT) { ?>
                    <i class="oe-i laterality R medium pad"></i>
                <?php } ?>
                <?php if ($laterality & Eye::LEFT) { ?>
                    <i class="oe-i laterality L medium pad"></i>
                <?php } ?>
            </span>
            <hr class="divider"<?= $is_config ? ' style="display: none;"' : null ?>/>
            <table<?= $is_config ? ' style="display: none;"' : null ?>>
                <tr>
                    <th>Test type</th>
                   <td><?= $selected_test_type->short_name ?></td>
                </tr>
                <tr>
                    <th>STA</th>
                    <td><?= $selected_test_option->short_name ?></td>
                </tr>
            </table>
        </div>
        <?php if (!$partial) { ?>
            <div class="step-actions">
                <?php if ($is_step_instance) { ?>
                    <button
                            class="green hint <?= $is_config ? 'js-change-visual-fields' : 'js-ps-popup-btn'?>"
                            data-action="next"<?= (int)$step->status === PathwayStep::STEP_COMPLETED ? 'style="display: none;"' : ''?>>
                        <?php if ((int)$step->status === PathwayStep::STEP_CONFIG) {
                            echo 'Set options';
                        } else { ?>
                            <?= (int)$step->status === PathwayStep::STEP_STARTED ? 'Complete' : 'Start' ?>
                        <?php } ?>
                    </button>
                    <button
                            class="blue hint js-ps-popup-btn"
                            data-action="prev"<?= $is_config ? 'style="display: none;"' : ''?>>
                        <?php if ((int)$step->status === PathwayStep::STEP_COMPLETED) {
                            echo 'Undo complete';
                        } elseif ((int)$step->status === PathwayStep::STEP_STARTED) {
                            echo 'Cancel';
                        } else {
                            echo 'Change';
                        }?>
                    </button>
                <?php } ?>
                <button class="blue i-btn left hint js-ps-popup-btn" data-action="left"<?= !$is_requested_only ? 'style="display: none;"' : ''?>>
                </button>
                <button class="blue i-btn right hint js-ps-popup-btn" data-action="right"<?= !$is_requested_only ? 'style="display: none;"' : ''?>>
                </button>
                <button class="red i-btn trash hint js-ps-popup-btn" data-action="remove"<?= !$is_requested ? 'style="display: none;"' : ''?>>
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
        <?php } ?>
</div>
<?php if (!$partial) { ?>
    <div class="close-icon-btn">
        <i class="oe-i remove-circle medium-icon"></i>
    </div>
<?php } ?>
