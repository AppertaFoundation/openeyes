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

use OEModule\OphCiExamination\models\NinePositions_MovementForGaze;
use OEModule\OphCiExamination\models\NinePositions_Reading;
use OEModule\OphCiExamination\models\NinePositions_AlignmentForGaze;
use OEModule\OphCiExamination\models\traits\HasWithHeadPosture;
use OEModule\OphCiExamination\widgets\NinePositions;

/* @var string $field_prefix */
/* @var bool $suppress_ed_globals */
/* @var NinePositions $this */
/* @var NinePositions_Reading $reading */

?>

<div class="js-reading collapse-group highlight" data-key="<?= $row_count ?>">
    <div class="header-icon collapse">Reading</div>
    <div class="collapse-group-content">
        <div class="cols-full">
            <!-- DOM simplified for v3 'method' -->
            <div class="flex-layout">
                <div class="method flex-l">
                    <?php if ($this->isReadingAttributeEnabled($this::ENABLE_CORRECTION)) { ?>
                        <label><?= $reading->getAttributeLabel('with_correction') ?></label>
                        <label class="inline highlight">
                            <?=\CHtml::radioButton(
                                $field_prefix . '[with_correction]',
                                (string) $reading->with_correction === (string) NinePositions_Reading::WITH_CORRECTION,
                                [
                                    'value' => NinePositions_Reading::WITH_CORRECTION,
                                    'id' => \CHtml::getIdByName($field_prefix . '[with_correction]') . NinePositions_Reading::WITH_CORRECTION
                                ]
                            ); ?> Yes
                        </label>
                        <label class="inline highlight">
                            <?=\CHtml::radioButton(
                                $field_prefix . '[with_correction]',
                                (string) $reading->with_correction === (string) NinePositions_Reading::WITHOUT_CORRECTION,
                                [
                                    'value' => NinePositions_Reading::WITHOUT_CORRECTION,
                                    'id' => \CHtml::getIdByName($field_prefix . '[with_correction]') . NinePositions_Reading::WITHOUT_CORRECTION
                                ]
                            ); ?> No
                        </label>
                        <span class="tabspace"></span>
                    <?php } ?>
                    <?php if ($this->isReadingAttributeEnabled($this::ENABLE_HEAD_POSTURE)) { ?>
                        <label><?= $reading->getAttributeLabel('with_head_posture') ?></label>
                        <label class="inline highlight ">
                            <?=\CHtml::radioButton(
                                $field_prefix . '[with_head_posture]',
                                (string) $reading->with_head_posture === (string) HasWithHeadPosture::$WITH_HEAD_POSTURE,
                                [
                                    'value' => HasWithHeadPosture::$WITH_HEAD_POSTURE,
                                    'id' => \CHtml::getIdByName($field_prefix . '[with_head_posture]') . HasWithHeadPosture::$WITH_HEAD_POSTURE
                                ]
                            ); ?> Used
                        </label>
                        <label class="inline highlight ">
                            <?=\CHtml::radioButton(
                                $field_prefix . '[with_head_posture]',
                                (string) $reading->with_head_posture === (string) HasWithHeadPosture::$WITHOUT_HEAD_POSTURE,
                                [
                                    'value' => HasWithHeadPosture::$WITHOUT_HEAD_POSTURE,
                                    'id' => \CHtml::getIdByName($field_prefix . '[with_head_posture]') . HasWithHeadPosture::$WITHOUT_HEAD_POSTURE
                                ]
                            ); ?> Not used
                        </label>
                        <span class="tabspace"></span>
                    <?php } ?>

                    <?php if ($this->isReadingAttributeEnabled($this::ENABLE_WONG_SUPINE_POSITIVE)) { ?>
                        <label class="inline highlight ">
                            <?=\CHtml::checkBox(
                                $field_prefix . '[wong_supine_positive]',
                                (string) $reading->wong_supine_positive === '1',
                                ['value' => '1']
                            ); ?> <?= $reading->getAttributeLabel('wong_supine_positive'); ?>
                        </label>
                    <?php } ?>
                    <?php if ($this->isReadingAttributeEnabled($this::ENABLE_HESS_CHART)) { ?>
                        <label class="inline highlight ">
                            <?=\CHtml::checkBox(
                                $field_prefix . '[hess_chart]',
                                (string) $reading->hess_chart === '1',
                                ['value' => '1']
                            ); ?> <?= $reading->getAttributeLabel('hess_chart'); ?>
                        </label>
                    <?php } ?>
                    <label class="inline highlight" for="<?= $field_prefix ?>_full_ocular_movement">
                        <?= \CHtml::checkBox(
                            $field_prefix . '[full_ocular_movement]',
                            $reading->full_ocular_movement ? true : false,
                            ["class" => "js-full-ocular-movement"]
                        ); ?>
                        <?= $reading->getAttributeLabel('full_ocular_movement')  ?>
                    </label>
                </div>

                <div class="cols-4 align-right">
                    <div class="cols-full">
                        <button
                            class="button js-add-comments"
                            id="ninepositions-reading-<?= $row_count ?>-comment-button"
                            data-comment-container="#ninepositions-reading-<?= $row_count ?>-comments"
                            type="button"
                            data-hide-method="display"
                            style="<?= $reading->comments ? "display: none;" : "" ?>"
                        >
                            <i class="oe-i comments small-icon "></i>
                        </button> <!-- comments wrapper -->
                        <div
                            id="ninepositions-reading-<?= $row_count ?>-comments"
                            class="cols-full comment-group js-comment-container"
                            style="<?= $reading->comments ? "" : "display: none;" ?>"
                            data-comment-button="#ninepositions-reading-<?= $row_count ?>-comment-button"
                        >
                            <!-- comment-group, textarea + icon -->
                            <div class=" flex-layout flex-left">
                            <textarea placeholder="<?= $reading->getAttributeLabel('comments') ?>"
                                      rows="1"
                                      class="cols-full js-comment-field"
                                      name="<?= $field_prefix ?>[comments]"
                            ><?=  CHtml::encode($reading->comments) ?></textarea>
                                <i class="oe-i remove-circle small-icon pad-left  js-remove-add-comments" data-hide-method="display"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- layout eyedraw using flex, but maintaining v2 DOM structure -->
            <div class="eyedraw-controls flex-layout flex-center">

                <?php if ($this->isReadingAttributeEnabled($this::ENABLE_DVD)) { ?>
                    <div class="flex-layout">
                        <label><?= $reading->getAttributeLabel('right_dvd'); ?></label>
                        <input type="text" class="fixed-width-medium" name="<?= "{$field_prefix}[right_dvd]" ?>" value="<?=  CHtml::encode($reading->right_dvd) ?>" />
                    </div>
                    <div class="tabspace"></div>
                <?php } ?>

                <div class="right-eyedraw-controls">
                    <div class="flex">
                        <a class="ed-button" href="#" data-function="addDoodle" data-arg="OrthopticShading" data-eye="right">
                            <i class="ed-i icon-ed-OrthopticShading"></i>
                            <span class="label">Orthoptic Shading</span>
                        </a>
                        <a class="ed-button" href="#" data-function="shootOrDrift" data-arg="UpDrift" data-eye="right">
                            <i class="ed-i icon-ed-UpDriftFromLeft"></i>
                            <span class="label">Up/Down Drift</span>
                        </a>
                        <a class="ed-button" href="#" data-function="shootOrDrift" data-arg="UpShoot" data-eye="right">
                            <i class="ed-i icon-ed-UpShootFromLeft"></i>
                            <span class="label">Up/Down Shoot</span>
                        </a>      
                    </div><!-- flex -->
                </div><!-- .right-eyedraw-controls -->

                <!-- pad out the icon groups -->
                <div class="tabspace"></div>

                <div class="center-eyedraw-controls">
                    <div class="flex">
                        <a class="ed-button" href="#" data-function="addPattern" data-arg="APattern">
                            <span class="ed-i icon-ed-APattern"></span>
                            <span class="label">'A' Pattern</span>
                        </a>
                        <a class="ed-button" href="#" data-function="addPattern" data-arg="VPattern">
                            <span class="ed-i icon-ed-VPattern"></span>
                            <span class="label">'V' Pattern</span>
                        </a>
                        <a class="ed-button" href="#" data-function="addPattern" data-arg="">
                            <span class="ed-i icon-ed-NoPattern"></span>
                            <span class="label">No Pattern</span>
                        </a>
                        <a class="ed-button" href="#" data-function="addPattern" data-arg="XPattern">
                            <span class="ed-i icon-ed-XPattern"></span>
                            <span class="label">'X' Pattern</span>
                        </a>
                        <a class="ed-button" href="#" data-function="addPattern" data-arg="YPattern">
                            <span class="ed-i icon-ed-YPattern"></span>
                            <span class="label">'Y' Pattern</span>
                        </a>
                        <a class="ed-button" href="#" data-function="addPattern" data-arg="InverseYPattern">
                            <span class="ed-i icon-ed-InverseYPattern"></span>
                            <span class="label">'Inverse Y' Pattern</span>
                        </a>
                    </div>
                </div><!-- .center-eyedraw-controls -->

                <!-- pad out the icon groups -->
                <div class="tabspace"></div>

                <div class="left-eyedraw-controls">
                    <div class="flex">
                        <a class="ed-button" href="#" data-function="addDoodle" data-arg="OrthopticShading" data-eye="left">
                            <span class="ed-i icon-ed-OrthopticShading"></span>
                            <span class="label">Orthoptic Shading</span>
                        </a>
                        <a class="ed-button" href="#" data-function="shootOrDrift" data-arg="UpDrift" data-eye="left">
                            <span class="ed-i icon-ed-UpDriftFromRight"></span>
                            <span class="label">Up/Down Drift</span>
                        </a>
                        <a class="ed-button" href="#" data-function="shootOrDrift" data-arg="UpShoot" data-eye="left">
                            <span class="ed-i icon-ed-UpShootFromRight"></span>
                            <span class="label">Up/Down Shoot</span>
                        </a>
                    </div>
                </div><!-- left-eyedraw-controls -->

                <?php if ($this->isReadingAttributeEnabled($this::ENABLE_DVD)) { ?>
                    <div class="tabspace"></div>

                    <div class="flex-layout">
                        <label><?= $reading->getAttributeLabel('left_dvd'); ?></label>
                        <input type="text" class="fixed-width-medium" name="<?= "{$field_prefix}[left_dvd]"?>" value="<?=  CHtml::encode($reading->left_dvd) ?>" />
                    </div>
                <?php } ?>


            </div><!-- .eyedraw-controls -->

        </div><!-- cols -->
        <!--
        custom table layout
        look value | ocularmovement | canvas | ocularmovement | look value | ocularmovement | canvas | ocularmovement | look value
        -->
        <table class="nine-positions">
            <tbody>
            <!-- top -->
            <tr>
                <td>
                    <?php
                    $this->render(
                        'NinePositions_AlignmentForGaze_event_edit',
                        array(
                            'reading' => $reading,
                            'gaze_type' => NinePositions_AlignmentForGaze::RIGHT_UP,
                            'form' => $form,
                            'model_name' => CHtml::modelName($element),
                            'row_count' => $row_count,
                            'field_prefix' => $field_prefix
                        )
                    );
                    ?>
                </td>
                <td>
                    <?php
                    $this->render(
                        'NinePositions_MovementForGaze_event_edit',
                        [
                            'reading' => $reading,
                            'side' => 'right',
                            'gaze_type' => NinePositions_MovementForGaze::RIGHT_UP,
                            'form' => $form,
                            'field_prefix' => $field_prefix
                        ]
                    );
                    ?>
                </td>
                <td rowspan="3">
                    <?php
                    $this->widget('application.modules.eyedraw.OEEyeDrawWidget', [
                        'listenerArray' => ['OpenEyes.OphCiExamination.ninePositionsEyedrawListener'],
                        'showDrawingControls' => false,
                        'idSuffix' => 'right_ninepositions_' . $row_count,
                        'attribute' => 'right_eyedraw',
                        'inputName' => $field_prefix . '[right_eyedraw]',
                        'inputId' => $field_prefix . '_right_eyedraw_' . $row_count,
                        'model' => $reading,
                        'suppressGlobalJs' => $suppress_ed_globals,
                        'side' => 'R',
                        'mode' => 'edit',
                        'width' => 175,
                        'height' => 175,
                        'toolbar' => false,
                        'onReadyCommandArray' => [
                            ['addDoodle', ['OrthopticEye']],
                            ['deselectDoodles', []],
                        ],
                    ]); ?>
                </td>
                <td>
                    <?php
                    $this->render(
                        'NinePositions_MovementForGaze_event_edit',
                        [
                            'reading' => $reading,
                            'side' => 'right',
                            'gaze_type' => NinePositions_MovementForGaze::LEFT_UP,
                            'form' => $form,
                            'field_prefix' => $field_prefix
                        ]
                    );
                    ?>
                </td>
                <td>
                    <?php
                    $this->render(
                        'NinePositions_AlignmentForGaze_event_edit',
                        array(
                            'reading' => $reading,
                            'gaze_type' => NinePositions_AlignmentForGaze::CENTER_UP,
                            'form' => $form,
                            'model_name' => CHtml::modelName($element),
                            'row_count' => $row_count,
                            'field_prefix' => CHtml::modelName($element) . "[readings][$row_count]"
                        )
                    );
                    ?>
                </td>
                <td>
                    <?php
                    $this->render(
                        'NinePositions_MovementForGaze_event_edit',
                        [
                            'reading' => $reading,
                            'side' => 'left',
                            'gaze_type' => NinePositions_MovementForGaze::RIGHT_UP,
                            'form' => $form,
                            'field_prefix' => $field_prefix
                        ]
                    );
                    ?>
                </td>
                <td rowspan="3"><!-- canvas placeholder -->
                    <?php
                    $this->widget('application.modules.eyedraw.OEEyeDrawWidget', [
                        'listenerArray' => ['OpenEyes.OphCiExamination.ninePositionsEyedrawListener'],
                        'showDrawingControls' => false,
                        'idSuffix' => 'left_ninepositions_' . $row_count,
                        'attribute' => 'left_eyedraw',
                        'model' => $reading,
                        'inputName' => $field_prefix . '[left_eyedraw]',
                        'inputId' => $field_prefix . '_left_eyedraw_' . $row_count,
                        'suppressGlobalJs' => $suppress_ed_globals,
                        'side' => 'L',
                        'mode' => 'edit',
                        'width' => 175,
                        'height' => 175,
                        'toolbar' => false,
                        'onReadyCommandArray' => [
                            ['addDoodle', ['OrthopticEye']],
                            ['deselectDoodles', []],
                        ],
                    ]); ?>
                </td>
                <td>
                    <?php
                    $this->render(
                        'NinePositions_MovementForGaze_event_edit',
                        [
                            'reading' => $reading,
                            'side' => 'left',
                            'gaze_type' => NinePositions_MovementForGaze::LEFT_UP,
                            'form' => $form,
                            'field_prefix' => $field_prefix
                        ]
                    );
                    ?>
                </td>
                <td>
                    <?php
                    $this->render(
                        'NinePositions_AlignmentForGaze_event_edit',
                        array(
                            'reading' => $reading,
                            'gaze_type' => NinePositions_AlignmentForGaze::LEFT_UP,
                            'form' => $form,
                            'model_name' => CHtml::modelName($element),
                            'row_count' => $row_count,
                            'field_prefix' => CHtml::modelName($element) . "[readings][$row_count]"
                        )
                    );
                    ?>
                </td>
            </tr>
            <!-- mid -->
            <tr>
                <td>
                    <?php
                    $this->render(
                        'NinePositions_AlignmentForGaze_event_edit',
                        array(
                            'reading' => $reading,
                            'gaze_type' => NinePositions_AlignmentForGaze::RIGHT_MID,
                            'form' => $form,
                            'model_name' => CHtml::modelName($element),
                            'row_count' => $row_count,
                            'field_prefix' => CHtml::modelName($element) . "[readings][$row_count]"
                        )
                    );
                    ?>
                </td>
                <td>
                    <?php
                    $this->render(
                        'NinePositions_MovementForGaze_event_edit',
                        [
                            'reading' => $reading,
                            'side' => 'right',
                            'gaze_type' => NinePositions_MovementForGaze::RIGHT_MID,
                            'form' => $form,
                            'field_prefix' => $field_prefix
                        ]
                    );
                    ?>
                </td>

                <!-- canvas -->

                <td>
                    <?php
                    $this->render(
                        'NinePositions_MovementForGaze_event_edit',
                        [
                            'reading' => $reading,
                            'side' => 'right',
                            'gaze_type' => NinePositions_MovementForGaze::LEFT_MID,
                            'form' => $form,
                            'field_prefix' => $field_prefix
                        ]
                    );
                    ?>
                </td>
                <td>
                    <?php
                    $this->render(
                        'NinePositions_AlignmentForGaze_event_edit',
                        array(
                            'reading' => $reading,
                            'gaze_type' => NinePositions_AlignmentForGaze::CENTER_MID,
                            'form' => $form,
                            'model_name' => CHtml::modelName($element),
                            'row_count' => $row_count,
                            'field_prefix' => CHtml::modelName($element) . "[readings][$row_count]"
                        )
                    );
                    ?>
                </td>
                <td>
                    <?php
                    $this->render(
                        'NinePositions_MovementForGaze_event_edit',
                        [
                            'reading' => $reading,
                            'side' => 'left',
                            'gaze_type' => NinePositions_MovementForGaze::RIGHT_MID,
                            'form' => $form,
                            'field_prefix' => $field_prefix
                        ]
                    );
                    ?>
                </td>

                <!-- canvas -->

                <td>
                    <?php
                    $this->render(
                        'NinePositions_MovementForGaze_event_edit',
                        [
                            'reading' => $reading,
                            'side' => 'left',
                            'gaze_type' => NinePositions_MovementForGaze::LEFT_MID,
                            'form' => $form,
                            'field_prefix' => $field_prefix
                        ]
                    );
                    ?>
                </td>
                <td>
                    <?php
                    $this->render(
                        'NinePositions_AlignmentForGaze_event_edit',
                        array(
                            'reading' => $reading,
                            'gaze_type' => NinePositions_AlignmentForGaze::LEFT_MID,
                            'form' => $form,
                            'model_name' => CHtml::modelName($element),
                            'row_count' => $row_count,
                            'field_prefix' => CHtml::modelName($element) . "[readings][$row_count]"
                        )
                    );
                    ?>
                </td>
            </tr>
            <!-- bot -->
            <tr>
                <td>
                    <?php
                    $this->render(
                        'NinePositions_AlignmentForGaze_event_edit',
                        array(
                            'reading' => $reading,
                            'gaze_type' => NinePositions_AlignmentForGaze::RIGHT_DOWN,
                            'form' => $form,
                            'model_name' => CHtml::modelName($element),
                            'row_count' => $row_count,
                            'field_prefix' => CHtml::modelName($element) . "[readings][$row_count]"
                        )
                    );
                    ?>
                </td>
                <td>
                    <?php
                    $this->render(
                        'NinePositions_MovementForGaze_event_edit',
                        [
                            'reading' => $reading,
                            'side' => 'right',
                            'gaze_type' => NinePositions_MovementForGaze::RIGHT_DOWN,
                            'form' => $form,
                            'field_prefix' => $field_prefix
                        ]
                    );
                    ?>
                </td>

                <!-- canvas -->

                <td>
                    <?php
                    $this->render(
                        'NinePositions_MovementForGaze_event_edit',
                        [
                            'reading' => $reading,
                            'side' => 'right',
                            'gaze_type' => NinePositions_MovementForGaze::LEFT_DOWN,
                            'form' => $form,
                            'field_prefix' => $field_prefix
                        ]
                    );
                    ?>
                </td>
                <td>
                    <?php
                    $this->render(
                        'NinePositions_AlignmentForGaze_event_edit',
                        array(
                            'reading' => $reading,
                            'gaze_type' => NinePositions_AlignmentForGaze::CENTER_DOWN,
                            'form' => $form,
                            'model_name' => CHtml::modelName($element),
                            'row_count' => $row_count,
                            'field_prefix' => CHtml::modelName($element) . "[readings][$row_count]"
                        )
                    );
                    ?>
                </td>
                <td>
                    <?php
                    $this->render(
                        'NinePositions_MovementForGaze_event_edit',
                        [
                            'reading' => $reading,
                            'side' => 'left',
                            'gaze_type' => NinePositions_MovementForGaze::RIGHT_DOWN,
                            'form' => $form,
                            'field_prefix' => $field_prefix
                        ]
                    );
                    ?>
                </td>

                <!-- canvas -->

                <td>
                    <?php
                    $this->render(
                        'NinePositions_MovementForGaze_event_edit',
                        [
                            'reading' => $reading,
                            'side' => 'left',
                            'gaze_type' => NinePositions_MovementForGaze::LEFT_DOWN,
                            'form' => $form,
                            'field_prefix' => $field_prefix
                        ]
                    );
                    ?>
                </td>
                <td>
                    <?php
                    $this->render(
                        'NinePositions_AlignmentForGaze_event_edit',
                        array(
                            'reading' => $reading,
                            'gaze_type' => NinePositions_AlignmentForGaze::LEFT_DOWN,
                            'form' => $form,
                            'model_name' => CHtml::modelName($element),
                            'row_count' => $row_count,
                            'field_prefix' => CHtml::modelName($element) . "[readings][$row_count]"
                        )
                    );
                    ?>
                </td>
            </tr>
            <tr class="divider">
                <td>
                    <?php
                    $this->render(
                        'NinePositions_AlignmentForGaze_event_edit',
                        array(
                            'reading' => $reading,
                            'gaze_type' => NinePositions_AlignmentForGaze::HEAD_TILT_RIGHT,
                            'form' => $form,
                            'model_name' => CHtml::modelName($element),
                            'row_count' => $row_count,
                            'field_prefix' => CHtml::modelName($element) . "[readings][$row_count]"
                        )
                    );
                    ?>
                </td>
                <td>
                    Tilt Right
                </td>
                <td>

                </td>
                <td>
                    Near
                </td>
                <td>
                    <?php
                    $this->render(
                        'NinePositions_AlignmentForGaze_event_edit',
                        array(
                            'reading' => $reading,
                            'gaze_type' => NinePositions_AlignmentForGaze::NEAR,
                            'form' => $form,
                            'model_name' => CHtml::modelName($element),
                            'row_count' => $row_count,
                            'field_prefix' => CHtml::modelName($element) . "[readings][$row_count]"
                        )
                    );
                    ?>
                </td>
                <td>

                </td>
                <td>

                </td>
                <td>
                    Tilt Left
                </td>
                <td>
                    <?php
                    $this->render(
                        'NinePositions_AlignmentForGaze_event_edit',
                        array(
                            'reading' => $reading,
                            'gaze_type' => NinePositions_AlignmentForGaze::HEAD_TILT_LEFT,
                            'form' => $form,
                            'model_name' => CHtml::modelName($element),
                            'row_count' => $row_count,
                            'field_prefix' => CHtml::modelName($element) . "[readings][$row_count]"
                        )
                    );
                    ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="cols-full js-reading-buttons">
        <div class="flex-layout flex-right">
            <div class="add-data-actions">
                <button class="button blue hint js-remove-reading">Remove reading</button>
                <button class="button hint green js-add-reading">Add another reading<i class="spinner as-icon js-loader" style="display: none;"></i></button>
            </div>
        </div>
    </div>
</div>
