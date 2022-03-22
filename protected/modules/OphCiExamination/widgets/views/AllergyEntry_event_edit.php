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
 * @var $entry AllergyEntry
 */

use OEModule\OphCiExamination\models\AllergyEntry;

?>

<?php
if (!isset($values)) {
    $values = array(
        'id' => $entry->id,
        'allergy_id' => $entry->allergy_id,
        'reactions' => $entry->reactions,
        'allergy_display' => $entry->displayallergy,
        'other' => $entry->other,
        'comments' => $entry->comments,
        'has_allergy' => $entry->has_allergy,
    );
}
$other_text_field_classes = $entry->hasErrors('other') ? 'highlighted-error error' : '';
?>

<tr class="row-<?= $row_count; ?><?php echo !$removable ? " read-only" : ''; ?>" data-key="<?= $row_count; ?>">
    <td id="<?= $model_name ?>_entries_<?= $row_count ?>_allergy_has_allergy">
        <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?= $values['id'] ?>"/>
        <input type="hidden" name="<?= $field_prefix ?>[other]" value="<?= $values['other'] ?>"/>
        <span class="js-other-allergy" style="display: none;">
            <?= CHtml::textField($field_prefix . '[other]', $values['other'], array('autocomplete' => 'off', 'class' => $other_text_field_classes));?>
        </span>
        <span class="js-not-other-allergy">
            <?= $values['allergy_display'];?>
        </span>
        <input type="hidden" name="<?= $field_prefix ?>[allergy_id]" value="<?= $values['allergy_id'] ?>"/>
    </td>
    <td class="reaction-selection">
        <?php
            echo CHtml::dropDownList(
                $row_count . '_reaction_selection',
                null,
                CHtml::listData(
                    OphCiExaminationAllergyReaction::model()->bydisplayorder()->findAllByAttributes(array('active' => '1')),
                    'id',
                    'name'
                ),
                ['class' => 'cols-10', 'empty' => 'Add reaction']
            );
            ?>
        <ul id="<?=$field_prefix?>[reactions]" class="oe-multi-select inline">
            <?php
            foreach ($entry->reactions as $reaction) {
                echo "<li>
                            $reaction->name
                            <i class=\"oe-i remove-circle small-icon pad-left\"></i>
                            <input type=\"hidden\" name=\"OEModule_OphCiExamination_models_Allergies[entries][$row_count][reactions][]\" value=\"$reaction->id\">
                        </li>";
            }
            ?>
        </ul>
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
              data-hide-method = "display"
                        style="<?php if ($values['comments']) :
                            ?>display: none;<?php
                               endif; ?>"
        >
            <i class="oe-i comments small-icon"></i>
        </button>
    </div>
    </td>
    <td>
        <?php if ($removable) {
            if ($values['has_allergy'] === (string)AllergyEntry::$NOT_PRESENT) { ?>
                <label class="inline highlight">
                    <?=\CHtml::radioButton(
                        $field_prefix . '[has_allergy]',
                        $values['has_allergy'] === (string)AllergyEntry::$PRESENT,
                        array('value' => AllergyEntry::$PRESENT)
                    ); ?>
                    yes
                </label>
                <label class="inline highlight">
                    <?=\CHtml::radioButton(
                        $field_prefix . '[has_allergy]',
                        $values['has_allergy'] === (string)AllergyEntry::$NOT_PRESENT,
                        array('value' => AllergyEntry::$NOT_PRESENT)
                    ); ?>
                    no
                </label>
            <?php } else {
                echo CHtml::hiddenField($field_prefix . '[has_allergy]', (string)AllergyEntry::$PRESENT);
            }
        } else { ?>
            <label class="inline highlight">
                <?=\CHtml::radioButton(
                    $field_prefix . '[has_allergy]',
                    $values['has_allergy'] === (string)AllergyEntry::$NOT_CHECKED,
                    array('value' => AllergyEntry::$NOT_CHECKED)
                ); ?>
                Not checked
            </label>
            <label class="inline highlight">
                <?=\CHtml::radioButton(
                    $field_prefix . '[has_allergy]',
                    $values['has_allergy'] === (string)AllergyEntry::$PRESENT,
                    array('value' => AllergyEntry::$PRESENT)
                ); ?>
                yes
            </label>
            <label class="inline highlight">
                <?=\CHtml::radioButton(
                    $field_prefix . '[has_allergy]',
                    $values['has_allergy'] === (string)AllergyEntry::$NOT_PRESENT,
                    array('value' => AllergyEntry::$NOT_PRESENT)
                ); ?>
                no
            </label>
        <?php } ?>
    </td>


    <?php if ($removable) : ?>
      <td><i class="oe-i trash"></i></td>
    <?php else : ?>
        <td>
            <i class="js-has-tooltip oe-i info small pad right"
               data-tooltip-content="<?= $values['allergy_display'] . " is mandatory to collect."; ?>"></i>
        </td>
    <?php endif; ?>
</tr>
