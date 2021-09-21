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

/** @var $reading NinePositions_Reading */
/** @var $side string */
/** @var $gaze_type string */

$movement_for_gaze = $reading->getMovementForGazeType($side, $gaze_type) ?: NinePositions_MovementForGaze::model();
$field_prefix .= "[movements][{$side}_{$gaze_type}]"
?>
<?php if ($movement_for_gaze->id) { ?>
    <input type="hidden" name="<?= "{$field_prefix}[id]" ?>" value="<?= $movement_for_gaze->id ?>" />
<?php } ?>
<input type="hidden" name="<?= "{$field_prefix}[eye_id]" ?>" value="<?= $side === 'right' ? \Eye::RIGHT : \Eye::LEFT ?>" />
<input type="hidden" name="<?= "{$field_prefix}[gaze_type]" ?>" value="<?= $gaze_type ?>" />
<?= $form->dropDownList(
    $movement_for_gaze,
    'movement_id',
    \CHtml::listData(
        $movement_for_gaze->movement_options,
        'id',
        'name'
    ),
    [
        'name' => "{$field_prefix}[movement_id]",
        'empty' => '-',
        'nowrapper' => true
    ]
); ?>
