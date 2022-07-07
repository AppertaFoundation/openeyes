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
<?php
$frequency_options = array();
foreach ($element->getFrequencyOptions() as $k => $v) {
    $frequency_options[$v->id] = $v->term . " (" . $v->code . ")";
}

/** @var OphDrPrescription_ItemTaper $entry */
?>

<tr class="js-taper-row meds-taper col-gap" data-parent-key="<?=$row_count?>" data-taper-key="<?=$taper_count?>">
    <td><i class="oe-i child-arrow small no-click pad"></i><em class="fade">then</em></td>
    <td>
        <input class="cols-2 js-dose input-validate numbers-only decimal" style="display: inline-block;" id="<?= $model_name ?>_entries_<?= $row_count ?>_taper_<?= $taper_count ?>_dose"  type="text" name="<?= $field_prefix ?>[dose]" value="<?= $entry->dose ?>" placeholder="Dose" />
        <?= CHtml::dropDownList($field_prefix . '[frequency_id]', $entry->frequency_id, $frequency_options, array('empty' => '-Frequency-', 'class' => 'js-frequency cols-8')) ?>
    </td>
    <td>
        <?=\CHtml::dropDownList(
            $field_prefix . '[duration_id]',
            $entry->duration_id,
            CHtml::listData(MedicationDuration::model()->activeOrPk([$entry->duration_id])->findAll(array('order' => 'display_order')), 'id', 'name'),
            array('empty' => '- Select -', 'class' => 'cols-full js-duration')
        ) ?>
    </td>
    <td></td>
    <td>
        <i class="oe-i trash js-remove-taper"></i>
    </td>
</tr>
