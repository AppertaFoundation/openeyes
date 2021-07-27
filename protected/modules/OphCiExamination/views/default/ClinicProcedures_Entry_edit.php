<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version. OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details. You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled
 * COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

if (!isset($values)) {
    $values = [
        'id' => $entry->id,
        'procedure' => $entry->procedure->term,
        'procedure_id' => $entry->procedure->id,
        'eye_id' => $entry->eye_id,
        'outcome_time' => $entry->outcome_time,
        'date' => $entry->date,
        'comments' => $entry->comments,
    ];
}
?>

<tr class="row-<?= $row_count ?>" id="<?= $model_name ?>">
    <td>
        <?= $values['procedure'] ?>
        <input type="hidden" name="<?= $field_prefix ?>[procedure_id]" value="<?= $values['procedure_id'] ?>" />
    </td>
    <td>
        <?php
        $this->widget('application.widgets.EyeSelector', [
            'inputNamePrefix' => $field_prefix,
            'selectedEyeId' => $values['eye_id'],
        ]);
        ?>
    </td>
    <td>
        <input class="date"
               id="clinic-procedures-datepicker-<?= $row_count ?>"
               name="<?= $field_prefix ?>[date]"
               value="<?= $values['date'] ?>"
               placeholder="dd Mth YYYY" autocomplete="off">
    </td>
    <td>
        <input class="fixed-width-medium"
               type="time"
               name="<?= $field_prefix ?>[outcome_time]"
               value="<?= $values['outcome_time'] ?>" autocomplete="false">
    </td>
    <td>
        <div class="cols-full align-left">
            <button class="button js-add-comments">
                <i class="oe-i comments small-icon"></i>
            </button>
            <div class="js-input-comments cols-full" style="display: none;">
                <div class=" flex-layout flex-left">
                    <textarea placeholder="Comments" autocomplete="off" rows="1"
                              class="cols-full" name="<?= $field_prefix ?>[comments]"></textarea>
                    <i class="oe-i remove-circle small-icon pad-left  js-remove-add-comments"></i>
                </div>
            </div>
        </div>
    </td>
    <td>
        <i class="oe-i trash remove_item"></i>
    </td>
</tr>
