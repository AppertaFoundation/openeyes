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
 *
 * @var $entry OphCiExamination_Investigation_Entry
 */

use OEModule\OphCiExamination\models\OphCiExamination_Investigation_Entry;

if (!isset($values)) {
    $values = array(
        'id' => $entry->id,
        'investigation_code' => $entry->investigation_code,
        'investigation_code_name' => \OEModule\OphCiExamination\models\OphCiExamination_Investigation_Codes::model()->findByPk($entry->investigation_code)->name,
        'last_modified_user_id' => $entry->last_modified_user_id,
        'last_modified_user_name' => User::model()->findByPk($entry->last_modified_user_id)->getFullNameAndTitle(),
        'date' => $entry->date,
        'time' => $entry->time,
        'comments' => $entry->comments
    );
}
?>

<tr class="row-<?= $row_count; ?>-investigation_entry" data-key="<?= $row_count; ?>">
    <td id="<?= $model_name ?>_entries_<?= $row_count ?>_investigation_name">
        <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?= $values['id'] ?>"/>
        <input type="hidden" name="<?= $field_prefix ?>[investigation_code]" value="<?= $values['investigation_code'] ?>"/>
        <?=$values['investigation_code_name']?>
    <td>
        <input class="date investigation-entry-date"
               id="investigation-entry-datepicker-<?= $row_count ?>"
               data-pmu-format="d b Y"
               data-hidden-input-selector="#investigation-entry-date-<?= $row_count; ?>"
               name="<?= $field_prefix ?>[date]"
               value="<?= strtotime($values['date']) ? date(Helper::NHS_DATE_FORMAT, strtotime($values['date'])) : $values['date'] ?>"
               placeholder="dd Mth YYYY" autocomplete="off">
    </td>
    <td>
        <input class="fixed-width-large"
               id="<?= $model_name ?>_entries_<?= $row_count ?>_time"
               type="time"
               name="<?= $field_prefix ?>[time]"

               value="<?= $values['time'] ?>" autocomplete="false">
    </td>
    <td id="<?= $model_name ?>_entries_<?= $row_count ?>_last_modified_user">
        <input type="hidden" name="<?= $field_prefix ?>[last_modified_user_id]" value="<?= $values['last_modified_user_id'] ?>"/>
        <i class="oe-i info small pad-right  js-has-tooltip" data-tt-type="basic"
                       data-tooltip-content="by <?= $values['last_modified_user_name'] ?>"></i>
    </td>
    <td>
        <input type="hidden" name="<?= $field_prefix ?>[comments]" value="<?= $values['comments'] ?>"/>
        <div class="cols-full">
            <div class="js-comment-container flex-layout flex-left"
                 id="<?= $model_name ?>_entries_<?= $row_count ?>_comments"
                 style="<?php if (!$values['comments']) :
                        ?>display: none;<?php
                        endif; ?>"
                 data-comment-button="#<?= $model_name ?>_entries_<?= $row_count ?>_comments_button">
                <?= CHtml::textArea($field_prefix . '[comments]', $values['comments'], [
                    'class' => 'js-comment-field autosize cols-full',
                    'rows' => '1',
                    'placeholder' => 'Comments',
                    'autocomplete' => 'off',
                        ]) ?>
                <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
            </div>
            <button id="<?= $model_name ?>_entries_<?= $row_count ?>_comments_button"
                    class="button js-add-comments"
                    data-comment-container="#<?= $model_name ?>_entries_<?= $row_count ?>_comments"
                    type="button"
                    data-hide-method = "display"
                    style="<?php if ($values['comments']) :
                        ?>display: none;<?php
                           endif; ?>">
                <i class="oe-i comments small-icon"></i>
            </button>
        </div>
    </td>
    <td><i class="oe-i trash"></i></td>
</tr>
