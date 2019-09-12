<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<tr data-parent-med-id="<?=$data_med_id?>" data-taper="<?= $taper_count ?>" class="prescription-tapier">
    <td style="padding-left: 4%"><i class="oe-i child-arrow small no-click pad"></i><em class="fade">then</em></td>
    <?= \CHtml::activeHiddenField($taper, 'id', ['class' => 'js-input']); ?>
    <?php if (!is_null($data_med)): ?>
        <?= \CHtml::activeHiddenField($data_med, 'id', ['class' => 'js-input']); ?>
    <?php else: ?>
        <input class="js-input" name="Medication[id]" id="Medication_id" type="hidden" value="<?= $data_med_id ?>">
    <?php endif; ?>
    <td>
        <span data-type="dose" data-id="<?= $taper->dose ? $taper->dose : ''; ?>" class="js-text"><?= $taper->dose ? $taper->dose : '-'; ?></span>
        <?= \CHtml::activeTextField($taper, 'dose', ['class' => 'js-input cols-full', 'style' => 'display:none', 'id' => null]); ?>
    </td>
    <td colspan="2"></td>
    <td>
        <span data-type="frequency_id" data-id="<?= $taper->frequency ? $taper->frequency_id : ''; ?>" class="js-text"><?= $taper->frequency ? $taper->frequency->term : '-'; ?></span>
        <?= \CHtml::activeDropDownList($taper, 'frequency_id',
            $frequency_options,
            ['class' => 'js-input cols-full', 'style' => 'display:none', 'empty' => '-- select --', 'id' => null]); ?>
    </td>
    <td>
        <span data-type="duration_id" data-id="<?= $taper->duration ? $taper->duration_id : ''; ?>" class="js-text"><?= $taper->duration ? $taper->duration->name : '-'; ?></span>
        <?= \CHtml::activeDropDownList($taper, 'duration_id',
            $duration_options,
            ['class' => 'js-input', 'style' => 'display:none', 'empty' => '-- select --', 'id' => null]); ?>
    </td>
    <td class="actions" style="text-align:end">
        <a data-action_type="remove" style="display: none" class="js-remove-taper"><i class="oe-i trash"></i></a>
    </td>
</tr>
