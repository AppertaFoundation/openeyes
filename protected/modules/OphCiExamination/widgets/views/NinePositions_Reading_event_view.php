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

use OEModule\OphCiExamination\models\NinePositions_AlignmentForGaze;
use OEModule\OphCiExamination\models\NinePositions_MovementForGaze;

/**
 * @var \OEModule\OphCiExamination\models\NinePositions_Reading $reading
 * @var \OEModule\OphCiExamination\widgets\NinePositions $this
 * @var array $data_list
 */

/**
 * DVD field is always displayed when enabled, but otherwise will only be rendered if the reading
 * has a value stored for left or right. The data list will only contain values that have been
 * recorded, so the settings for the configurable attributes do not need to be checked in view mode.
 */
?>

<div class="collapse-group highlight">
    <div class="collapse-group-content">
        <?php if (count($data_list)) { ?>
        <div class="data-group">
            <ul class="dot-list large">
                <?php foreach ($data_list as $data_item) { ?>
                    <li><?= $data_item ?></li>
                <?php } ?>
            </ul>
        </div><!-- datagroup -->
    <?php } ?>

        <div class="data-group">
        <!--
        9 positions tables
        look value | ocularmovement | canvas | ocularmovement | look value | ocularmovement | canvas | ocularmovement | look value
        -->
        <table class="nine-positions">
            <tbody>
            <?php if ($this->isReadingAttributeEnabled($this::ENABLE_DVD) || !empty($reading->right_dvd) || !empty($reading->left_dvd)) { ?>
                <tr>
                    <td colspan="4"><?= $reading->getAttributeLabel('right_dvd') ?>: <?=  CHtml::encode($reading->right_dvd ?: '-') ?></td>
                    <td colspan="5" class="right-align"><?= $reading->getAttributeLabel('left_dvd') ?>: <?=  CHtml::encode($reading->left_dvd ?: '-') ?></td>
                </tr>
            <?php } ?>

            <!-- top -->
            <tr>
                <td>
                    <?= $this->renderReadingAlignment($reading, NinePositions_AlignmentForGaze::RIGHT_UP); ?>
                </td>
                <td>
                    <?= $this->renderReadingMovement($reading, 'right', NinePositions_MovementForGaze::RIGHT_UP); ?>
                </td>
                <td rowspan="3">
                    <?php
                    $this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
                        'idSuffix' => 'right_ninepositions_reading_' . $reading->id,
                        'side' => 'R',
                        'mode' => 'view',
                        'width' => 175,
                        'height' => 175,
                        'model' => $reading,
                        'attribute' => 'right_eyedraw',
                        'toggleScale' => 0.72
                    ));
                    ?>
                </td>
                <td>
                    <?= $this->renderReadingMovement($reading, 'right', NinePositions_MovementForGaze::LEFT_UP); ?>
                </td>
                <td>
                    <?= $this->renderReadingAlignment($reading, NinePositions_AlignmentForGaze::CENTER_UP); ?>
                </td>
                <td>
                    <?= $this->renderReadingMovement($reading, 'left', NinePositions_MovementForGaze::RIGHT_UP); ?>
                </td>
                <td rowspan="3">
                    <!-- canvas placeholder -->
                    <?php
                    $this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
                        'idSuffix' => 'left_ninepositions_reading_' . $reading->id,
                        'side' => 'L',
                        'mode' => 'view',
                        'width' => 175,
                        'height' => 175,
                        'model' => $reading,
                        'attribute' => 'left_eyedraw',
                        'toggleScale' => 0.72
                    ));
                    ?>
                </td>
                <td>
                    <?= $this->renderReadingMovement($reading, 'left', NinePositions_MovementForGaze::LEFT_UP); ?>
                </td>
                <td>
                    <?= $this->renderReadingAlignment($reading, NinePositions_AlignmentForGaze::LEFT_UP); ?>
                </td>
            </tr>
            <!-- mid -->
            <tr>
                <td>
                    <?= $this->renderReadingAlignment($reading, NinePositions_AlignmentForGaze::RIGHT_MID); ?>
                </td>
                <td>
                    <?= $this->renderReadingMovement($reading, 'right', NinePositions_MovementForGaze::RIGHT_MID); ?>
                </td>
                <!-- canvas -->

                <td>
                    <?= $this->renderReadingMovement($reading, 'right', NinePositions_MovementForGaze::LEFT_MID); ?>
                </td>
                <td>
                    <?= $this->renderReadingAlignment($reading, NinePositions_AlignmentForGaze::CENTER_MID); ?>
                </td>
                <td>
                    <?= $this->renderReadingMovement($reading, 'left', NinePositions_MovementForGaze::RIGHT_MID); ?>
                </td>

                <!-- canvas -->

                <td>
                    <?= $this->renderReadingMovement($reading, 'left', NinePositions_MovementForGaze::LEFT_MID); ?>
                </td>
                <td>
                    <?= $this->renderReadingAlignment($reading, NinePositions_AlignmentForGaze::LEFT_MID); ?>
                </td>
            </tr>
            <!-- bot -->
            <tr>
                <td>
                    <?= $this->renderReadingAlignment($reading, NinePositions_AlignmentForGaze::RIGHT_DOWN); ?>
                </td>
                <td>
                    <?= $this->renderReadingMovement($reading, 'right', NinePositions_MovementForGaze::RIGHT_DOWN); ?>
                </td>

                <!-- canvas -->

                <td>
                    <?= $this->renderReadingMovement($reading, 'right', NinePositions_MovementForGaze::LEFT_DOWN); ?>
                </td>
                <td>
                    <?= $this->renderReadingAlignment($reading, NinePositions_AlignmentForGaze::CENTER_DOWN); ?>
                </td>
                <td>
                    <?= $this->renderReadingMovement($reading, 'left', NinePositions_MovementForGaze::RIGHT_DOWN); ?>
                </td>

                <!-- canvas -->

                <td>
                    <?= $this->renderReadingMovement($reading, 'left', NinePositions_MovementForGaze::LEFT_DOWN); ?>
                </td>
                <td>
                    <?= $this->renderReadingAlignment($reading, NinePositions_AlignmentForGaze::LEFT_DOWN); ?>
                </td>
            </tr>
            <tr class="divider">
                <td>
                    <?= $this->renderReadingAlignment($reading, NinePositions_AlignmentForGaze::HEAD_TILT_RIGHT); ?>
                </td>
                <td>

                </td>
                <td>

                </td>
                <td>

                </td>
                <td>
                    <?= $this->renderReadingAlignment($reading, NinePositions_AlignmentForGaze::NEAR); ?>
                </td>
                <td>

                </td>
                <td>

                </td>
                <td>

                </td>
                <td>
                    <?= $this->renderReadingAlignment($reading, NinePositions_AlignmentForGaze::HEAD_TILT_LEFT); ?>
                </td>
            </tr>
            </tbody>
        </table>

    </div> <!-- data-group -->
    </div>
</div>

