<?php
/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @var \OEModule\OphCiExamination\models\Synoptophore $element
 * @var \OEModule\OphCiExamination\widgets\Synoptophore $this
 */
?>

<script type="text/javascript" src="<?= $this->getJsPublishedPath("Synoptophore.js") ?>"></script>

<?php $model_name = CHtml::modelName($element); ?>
<div id="<?= $model_name ?>_form">
    <?php echo $form->hiddenInput($element, 'eye_id', false, ['class' => 'sideField']); ?>

    <div class="element-both-eyes flex-layout">
        <div class="cols-4">
            <label class="inline">
                <?= $element->getAttributeLabel('angle_from_primary'); ?>
            </label>
            <?php foreach ($element::ANGLES_FROM_PRIMARY as $angle) { ?>
                <label class="inline highlight">
                    <?=\CHtml::radioButton($model_name . '[angle_from_primary]', (string) $element->angle_from_primary === (string) $angle, [
                        'value' => $angle,
                        'id' => "{$model_name}_at_risk_{$angle}"
                    ]); ?>
                    <?= $angle ?>°
                </label>
            <?php } ?>
        </div>
        <!-- comments wrapper -->
        <div id="synoptophore-comments" class="cols-full js-comment-container"
            data-comment-button="#synoptophore-comment-button"
            <?php
            if (!$element->comments) {
                echo 'style="display: none;"';
            }
            ?>>
            <!-- comment-group, textarea + icon -->
            <div class="comment-group flex-layout flex-left">
                <textarea placeholder="Comments" autocomplete="off" rows="1"
                          id="<?= $model_name ?>_comments"
                          name="<?= $model_name ?>[comments]"
                          class="js-input-comments cols-full "><?= CHtml::encode($element->comments) ?></textarea>
                <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
            </div>
        </div>
        <button id="synoptophore-comment-button" class="button js-add-comments"
                data-comment-container="#synoptophore-comments" type="button"
                <?php
                if ($element->comments) {
                    echo 'style="display: none;"';
                }
                ?>><i class="oe-i comments small-icon "></i></button>
    </div>

    <div class="element-eyes">

        <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) { ?>
            <div class="<?= $eye_side ?>-eye <?= $page_side ?> js-element-eye" data-side="<?= $eye_side ?>"
                 id="<?= $model_name ?>_<?= $eye_side ?>_readings_form">
                <div class="active-form" style="<?= $element->hasEye($eye_side) ? '' : 'display: none;' ?>">
                    <div class="remove-side"><i class="oe-i remove-circle small"></i></div>
                    <h3><?= ucfirst($eye_side) ?> Eye Fixation</h3>
                    <table class="cols-full last-left">
                        <colgroup>
                            <col class="cols-4" span="3">
                        </colgroup>
                        <tbody>
                        <?php
                        $i = 0;
                        foreach ($this->getAllReadingGazeTypes() as $row) { ?>
                            <tr class="col-gap">
                                <?php foreach ($row as $gaze_type) {
                                    $field_prefix = "{$model_name}[{$eye_side}_readings][$i]";
                                    $reading = $element->getReadingForSideByGazeType($eye_side, $gaze_type);
                                    ?>
                                    <td class="gaze-container gaze-type-<?= $gaze_type ?>"
                                        data-gaze-type="<?= $gaze_type ?>">
                                        <div class="flex-layout">
                                            <input type="hidden" name="<?= $field_prefix ?>[gaze_type]"
                                                   value="<?= $gaze_type ?>" data-adder-gaze-type="true"
                                                <?= $reading ? "" : 'disabled="disabled"' ?> />
                                            <input type="hidden" name="<?= $field_prefix ?>[horizontal_angle]"
                                                   data-adder-input-id="horizontal_angle"
                                                   value="<?= $reading ? CHtml::encode($reading->horizontal_angle) : "" ?>"
                                                <?= $reading ? "" : 'disabled="disabled"' ?> />
                                            <input type="hidden" name="<?= $field_prefix ?>[direction_id]"
                                                   data-adder-input-id="direction"
                                                   value="<?= $reading ? CHtml::encode($reading->direction_id) : "" ?>"
                                                <?= $reading ? "" : 'disabled="disabled"' ?>/>
                                            <input type="hidden" name="<?= $field_prefix ?>[vertical_power]"
                                                   data-adder-input-id="vertical_power"
                                                   value="<?= $reading ? CHtml::encode($reading->vertical_power) : "" ?>"
                                                <?= $reading ? "" : 'disabled="disabled"' ?>/>
                                            <input type="hidden" name="<?= $field_prefix ?>[deviation_id]"
                                                   data-adder-input-id="deviation"
                                                   value="<?= $reading ? CHtml::encode($reading->deviation_id) : "" ?>"
                                                <?= $reading ? "" : 'disabled="disabled"' ?> />
                                            <input type="hidden" name="<?= $field_prefix ?>[torsion]"
                                                   data-adder-input-id="torsion"
                                                   value="<?= $reading ? CHtml::encode($reading->torsion) : "" ?>"
                                                <?= $reading ? "" : 'disabled="disabled"' ?> />

                                            <div class="data-value"><?= $reading ?></div>
                                            <button class="button hint green thin"
                                                    data-adder-trigger="true"
                                                    data-gaze-type="<?= $gaze_type ?>"
                                                <?= $reading ? 'style="display:none";' : '' ?>>
                                                <i class="oe-i plus pro-theme"></i></button>
                                            <button class="button hint thin"
                                                    data-remove-reading="true"
                                                    data-gaze-type="<?= $gaze_type ?>"
                                                <?= $reading ? '' : 'style="display:none";' ?>>
                                                <i class="oe-i trash "></i></button>
                                    <?php
                                    $i++;
                                }
                                ?>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>

                </div><!-- end of active-form --><!-- inactive form -->
                <div class="inactive-form" style="<?= !$element->hasEye($eye_side) ? '' : 'display: none;' ?>">
                    <div class="add-side">
                        <a href="#">Add <?= $eye_side ?> side</a>
                    </div>
                </div>
                <script type="text/javascript">
                    $(document).ready(function () {
                        new OpenEyes.OphCiExamination.SynoptophoreController({
                            container: document.querySelector('#<?= $model_name ?>_<?= $eye_side ?>_readings_form'),
                            directionOptions: <?= $this->getJsonDirectionOptions() ?>,
                            deviationOptions: <?= $this->getJsonDeviationOptions() ?>,
                            headers: <?= $this->getJsonHeaders() ?>
                        });
                    });
                </script>
            </div>
        <?php } ?>
    </div><!-- element eyes -->
</div>