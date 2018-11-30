<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
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
        'allergy_display' => $entry->displayallergy,
        'other' => $entry->other,
        'comments' => $entry->comments,
        'has_allergy' => $entry->has_allergy,
    );
}
?>

<tr class="row-<?= $row_count; ?><?php echo !$removable ? " read-only" : ''; ?>" data-key="<?= $row_count; ?>">
    <td id="<?= $model_name ?>_entries_<?= $row_count ?>_allergy_has_allergy">
			<input type="hidden" name="<?= $field_prefix ?>[id]" value="<?= $values['id'] ?>"/>
			<input type="hidden" name="<?= $field_prefix ?>[other]" value="<?= $values['other'] ?>"/>
			<span class="js-other-allergy">
            <?= CHtml::textField($field_prefix . '[other]', $values['other'], array('autocomplete' => 'off'));?>
        </span>
			<span class="js-not-other-allergy">
            <?= $values['allergy_display'];?>
        </span>
			<input type="hidden" name="<?= $field_prefix ?>[allergy_id]" value="<?= $values['allergy_id'] ?>"/>
			</td>
	<td>
        <?php if ($removable) {
            if ($values['has_allergy'] === (string)AllergyEntry::$NOT_PRESENT) { ?>
                <label class="inline highlight">
                    <?=\CHtml::radioButton($field_prefix . '[has_allergy]',
                        $values['has_allergy'] === (string)AllergyEntry::$PRESENT, array('value' => AllergyEntry::$PRESENT)); ?>
                    yes
                </label>
                <label class="inline highlight">
                    <?=\CHtml::radioButton($field_prefix . '[has_allergy]',
                        $values['has_allergy'] === (string)AllergyEntry::$NOT_PRESENT,
                        array('value' => AllergyEntry::$NOT_PRESENT)); ?>
                    no
                </label>
            <?php } else {
                echo CHtml::hiddenField($field_prefix . '[has_allergy]', (string)AllergyEntry::$PRESENT);
            }
        } else { ?>
        <label class="inline highlight">
            <?=\CHtml::radioButton($field_prefix . '[has_allergy]',
                $values['has_allergy'] === (string)AllergyEntry::$NOT_CHECKED,
                array('value' => AllergyEntry::$NOT_CHECKED)); ?>
          Not checked
        </label>
        <label class="inline highlight">
            <?=\CHtml::radioButton($field_prefix . '[has_allergy]',
                $values['has_allergy'] === (string)AllergyEntry::$PRESENT,
                array('value' => AllergyEntry::$PRESENT)); ?>
          yes
        </label>
        <label class="inline highlight">
            <?=\CHtml::radioButton($field_prefix . '[has_allergy]',
                $values['has_allergy'] === (string)AllergyEntry::$NOT_PRESENT,
                array('value' => AllergyEntry::$NOT_PRESENT)); ?>
          no
        </label>
      <?php } ?>
  </td>
	<td>
    <span class="comment-group js-comment-container"
					id="<?= CHtml::getIdByName($field_prefix . '[comment_container]') ?>"
					style="<?php if (!$values['comments']): ?>display: none;<?php endif; ?>"
					data-comment-button="#<?= CHtml::getIdByName($field_prefix . '[comments]') ?>_button">
                  <?= CHtml::textField($field_prefix . '[comments]', $values['comments'], array(
										'class' => 'js-comment-field',
										'autocomplete' => 'off',
									)) ?>
			<i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
        </span>
		<button id="<?= CHtml::getIdByName($field_prefix . '[comments]') ?>_button"
						class="button js-add-comments"
						data-comment-container="#<?= CHtml::getIdByName($field_prefix . '[comment_container]') ?>"
						type="button"
						style="<?php if ($values['comments']): ?>visibility: hidden;<?php endif; ?>"
		>
			<i class="oe-i comments small-icon"></i>
		</button>
	</td>


    <?php if ($removable) : ?>
      <td><i class="oe-i trash"></i></td>
    <?php else : ?>
        <td>
            Read only
            <i class="js-has-tooltip oe-i info small pad right"
               data-tooltip-content="<?= $values['allergy_display'] . " is mandatory to collect."; ?>"></i>
        </td>
    <?php endif; ?>

</tr>