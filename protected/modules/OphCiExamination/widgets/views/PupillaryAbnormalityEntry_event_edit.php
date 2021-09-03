<?php
/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OEModule\OphCiExamination\models\PupillaryAbnormalityEntry;
/** @var PupillaryAbnormalityEntry $entry */
/** @var string $field_prefix */
?>

<?php
if (!isset($values)) {
    $values = array(
        'id' => $entry->id,
        'abnormality_id' => $entry->abnormality_id,
        'abnormality_display' => $entry->displayabnormality,
        'comments' => $entry->comments,
        'has_abnormality' => $entry->has_abnormality,
    );
}
?>

<tr id="<?= $model_name ?>_entries" class="row-<?= $row_count; ?><?= !$removable ? " read-only" : ''; ?>"
    data-key="<?= $row_count; ?>">
    <td>
        <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?= $values['id'] ?>"/>
        <?= $values['abnormality_display']; ?>
        <input type="hidden" name="<?= $field_prefix ?>[abnormality_id]" value="<?= $values['abnormality_id'] ?>"/>
        <input type="hidden" name="<?= $field_prefix ?>[eye_id]" value="<?= $eye_id ?>"/>
    </td>
    <td id="<?= $model_name ?>_entries_<?= $side . '_' . $row_count ?>_has_abnormality" class="nowrap">
        <?php if ($removable) {
            if ($values['has_abnormality'] === (string)PupillaryAbnormalityEntry::$NOT_PRESENT) { ?>
                <label class="inline highlight">
                    <?= \CHtml::radioButton($field_prefix . '[has_abnormality]',
                        $values['has_abnormality'] === (string)PupillaryAbnormalityEntry::$PRESENT,
                        array('value' => PupillaryAbnormalityEntry::$PRESENT)); ?>
                    yes
                </label>
                <label class="inline highlight">
                    <?= \CHtml::radioButton($field_prefix . '[has_abnormality]',
                        $values['has_abnormality'] === (string)PupillaryAbnormalityEntry::$NOT_PRESENT,
                        array('value' => PupillaryAbnormalityEntry::$NOT_PRESENT)); ?>
                    no
                </label>
            <?php } else {
                echo CHtml::hiddenField($field_prefix . '[has_abnormality]', (string)PupillaryAbnormalityEntry::$PRESENT);
            }
        } else { ?>
            <label class="inline highlight">
                <?= \CHtml::radioButton(
                    $field_prefix . '[has_abnormality]',
                    $values['has_abnormality'] === (string)PupillaryAbnormalityEntry::$NOT_CHECKED,
                    [
                        'value' => $entry::$NOT_CHECKED,
                        'id' => "{$field_prefix}_has_abnormality_{$entry::$NOT_CHECKED}"
                    ]
                ); ?>
                Not checked
            </label>
            <label class="inline highlight">
                <?= \CHtml::radioButton(
                    $field_prefix . '[has_abnormality]',
                    $values['has_abnormality'] === (string)PupillaryAbnormalityEntry::$PRESENT,
                    [
                        'value' => PupillaryAbnormalityEntry::$PRESENT,
                        'id' => "{$field_prefix}_has_abnormality_{$entry::$PRESENT}"]
                ); ?>
                yes
            </label>
            <label class="inline highlight">
                <?= \CHtml::radioButton(
                    $field_prefix . '[has_abnormality]',
                    $values['has_abnormality'] === (string)$entry::$NOT_PRESENT,
                    [
                        'value' => $entry::$NOT_PRESENT,
                        'id' => "{$field_prefix}_has_abnormality_{$entry::$NOT_PRESENT}"
                    ]
                ); ?>
                no
            </label>
        <?php } ?>
    </td>
    <td>
        <div class="cols-full">
            <div class="js-comment-container flex-layout flex-left"
                 id="<?= CHtml::getIdByName($field_prefix . '[comment_container]') ?>"
                 style="<?php if (!$values['comments']) :
                        ?>display: none;<?php
                        endif; ?>"
                 data-comment-button="#<?= CHtml::getIdByName($field_prefix . '[comments]') ?>_button">
                <?= CHtml::textArea($field_prefix . '[comments]', $values['comments'], [
                    'class' => 'js-comment-field autosize cols-full',
                    'rows' => '1',
                    'placeholder' => 'Comments',
                    'autocomplete' => 'off',
                        ]) ?>
                <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
            </div>
            <button id="<?= CHtml::getIdByName($field_prefix . '[comments]') ?>_button"
                    class="button js-add-comments"
                    data-comment-container="#<?= CHtml::getIdByName($field_prefix . '[comment_container]') ?>"
                    type="button"
                    data-hide-method="display"
                    style="<?php if ($values['comments']) :
                        ?>display: none;<?php
                           endif; ?>"
            >
                <i class="oe-i comments small-icon"></i>
            </button>
        </div>
    </td>


    <?php if ($removable) : ?>
        <td><i class="oe-i trash"></i></td>
    <?php else : ?>
        <td>
            <i class="js-has-tooltip oe-i info small pad right"
               data-tooltip-content="<?= $values['abnormality_display'] . " is mandatory to collect."; ?>"></i>
        </td>
    <?php endif; ?>

</tr>
