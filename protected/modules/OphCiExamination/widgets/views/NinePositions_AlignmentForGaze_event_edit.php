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

/** @var \OEModule\OphCiExamination\models\NinePositions_Reading $reading */
/** @var string $gaze_type */

$field_prefix .= "[alignments][$gaze_type]";
$gaze_alignment = $reading->getAlignmentForGazeType($gaze_type);

?>
<div class=" js-gaze-container" data-gaze-type="<?= $gaze_type ?>">
    <div class="ninepositions-look-value">
        <!-- preserve the alignment id in editing -->
        <?php if ($gaze_alignment && $gaze_alignment->id) { ?>
            <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?= $gaze_alignment->id ?>" />
        <?php }?>
        <input type="hidden" name="<?= $field_prefix ?>[gaze_type]"
               value="<?= $gaze_type ?>"
               data-adder-gaze-type="true"
               <?= $gaze_alignment ? "" : 'disabled="disabled"' ?>
        />
        <input type="hidden" name="<?= $field_prefix ?>[horizontal_angle]"
               value="<?= $gaze_alignment ? CHtml::encode($gaze_alignment->horizontal_angle) : '' ?>"
               data-adder-input-id="horizontal_angle"
               <?= $gaze_alignment ? "" : 'disabled="disabled"' ?>
        />
        <input type="hidden" name="<?= $field_prefix ?>[horizontal_prism_position]"
               value="<?= $gaze_alignment ?  CHtml::encode($gaze_alignment->horizontal_prism_position) : '' ?>"
               data-adder-input-id="horizontal_prism_position"
               <?= $gaze_alignment ? "" : 'disabled="disabled"' ?>
        />
        <input type="hidden" name="<?= $field_prefix ?>[horizontal_e_deviation_id]"
               value="<?= $gaze_alignment ?  CHtml::encode($gaze_alignment->horizontal_e_deviation_id) : '' ?>"
               data-adder-input-id="horizontal_e_deviation_id"
               <?= $gaze_alignment ? "" : 'disabled="disabled"' ?>
        />
        <input type="hidden" name="<?= $field_prefix ?>[horizontal_x_deviation_id]"
               value="<?= $gaze_alignment ?  CHtml::encode($gaze_alignment->horizontal_x_deviation_id) : '' ?>"
               data-adder-input-id="horizontal_x_deviation_id"
               <?= $gaze_alignment ? "" : 'disabled="disabled"' ?>
        />
        <input type="hidden" name="<?= $field_prefix ?>[vertical_angle]"
               value="<?= $gaze_alignment ?  CHtml::encode($gaze_alignment->vertical_angle) : '' ?>"
               data-adder-input-id="vertical_angle"
               <?= $gaze_alignment ? "" : 'disabled="disabled"' ?>
        />
        <input type="hidden" name="<?= $field_prefix ?>[vertical_prism_position]"
               value="<?= $gaze_alignment ?  CHtml::encode($gaze_alignment->vertical_prism_position) : '' ?>"
               data-adder-input-id="vertical_prism_position"
               <?= $gaze_alignment ? "" : 'disabled="disabled"' ?>
        />
        <input type="hidden" name="<?= $field_prefix ?>[vertical_deviation_id]"
               value="<?= $gaze_alignment ?  CHtml::encode($gaze_alignment->vertical_deviation_id) : '' ?>"
               data-adder-input-id="vertical_deviation_id"
               <?= $gaze_alignment ? "" : 'disabled="disabled"' ?>
        />
        <div class="ninepositions-look-h js-display-horizontal"><?= $gaze_alignment ? $gaze_alignment->display_horizontal : '' ?></div>
        <div class="ninepositions-look-v js-display-vertical"><?= $gaze_alignment ? $gaze_alignment->display_vertical : '' ?></div>
    </div>
    <button class="button hint green thin"
            data-adder-trigger="true"
            data-gaze-type="<?= $gaze_type ?>"
            <?= $gaze_alignment ? 'style="display:none";' : '' ?>><i class="oe-i plus pro-theme"></i></button>
    <button class="button thin"
            data-remove-reading="true"
            data-gaze-type="<?= $gaze_type ?>"
            <?= $gaze_alignment ? '' : 'style="display:none";' ?>><i class="oe-i trash"></i></button>
</div>