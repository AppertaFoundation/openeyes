<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<tr data-parent-med-id="<?=$set_item_medication_id?>" id="medication_set_item_taper_<?= $data_parent_key ?>_<?= $taper_count ?>" data-taper="<?= $taper_count ?>" class="prescription-taper<?= is_null($set_item_medication) ? ' new' : ''?>">
    <td style="padding-left: 4%"><i class="oe-i child-arrow small no-click pad"></i><em class="fade">then</em></td>
    <?= \CHtml::activeHiddenField($taper, 'id', ['class' => 'js-input', 'name' => "MedicationSetAutoRuleMedicationTaper[$data_parent_key][$taper_count][id]"]); ?>

    <td class="js-input-wrapper">
        <?= \CHtml::activeTextField($taper, 'dose', [
            'class' => 'js-input cols-full',
            'id' => null,
            'name' => "MedicationSetAutoRuleMedicationTaper[$data_parent_key][$taper_count][dose]"
            ]); ?>
    </td>
    <td colspan="2"></td>
    <td class="js-input-wrapper">
        <?= \CHtml::activeDropDownList(
            $taper,
            'frequency_id',
            $frequency_options,
            [
                'class' => 'js-input cols-full',
                'empty' => '-- select --',
                'id' => null,
                'name' => "MedicationSetAutoRuleMedicationTaper[$data_parent_key][$taper_count][frequency_id]",
            ]
        ); ?>
    </td>
    <td class="js-input-wrapper">
        <?= \CHtml::activeDropDownList(
            $taper,
            'duration_id',
            $duration_options,
            [
                'class' => 'js-input cols-full',
                'empty' => '-- select --',
                'id' => null,
                'name' => "MedicationSetAutoRuleMedicationTaper[$data_parent_key][$taper_count][duration_id]"
            ]
        ); ?>
    </td>

    <?php if ($is_prescription_set) : ?>
    <!-- tapers are using their parent settings -->
    <td></td>
    <td></td>
    <?php endif; ?>

    <td class="actions" style="text-align:end">
        <a data-action_type="remove" class="js-remove-taper"><i class="oe-i trash"></i></a>
    </td>
</tr>
