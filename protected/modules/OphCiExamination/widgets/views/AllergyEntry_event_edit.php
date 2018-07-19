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

<tr class="row-<?= $row_count; ?><?php if (!$removable) {
    echo " read-only";
} ?>" data-key="<?= $row_count; ?>">
  <td>
    <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?= $values['id'] ?>"/>
    <input type="hidden" name="<?= $field_prefix ?>[other]" value="<?= $values['other'] ?>"/>
      <?= $values['allergy_display']; ?>
      <?php if ($removable): ?>
        <input type="hidden" name="<?= $field_prefix ?>[allergy_id]" value="<?= $values['allergy_id'] ?>"/>
      <?php endif; ?>
  </td>
  <td id="<?= $model_name ?>_entries_<?= $row_count ?>_allergy_has_allergy">
    <label class="inline highlight">
        <?php echo CHtml::radioButton($field_prefix . '[has_allergy]', $posted_not_checked,
            array('value' => AllergyEntry::$NOT_CHECKED)); ?>
      Not checked
    </label>
    <label class="inline highlight">
        <?php echo CHtml::radioButton($field_prefix . '[has_allergy]',
            $values['has_allergy'] === (string)AllergyEntry::$PRESENT, array('value' => AllergyEntry::$PRESENT)); ?>
      yes
    </label>
    <label class="inline highlight">
        <?php echo CHtml::radioButton($field_prefix . '[has_allergy]',
            $values['has_allergy'] === (string)AllergyEntry::$NOT_PRESENT,
            array('value' => AllergyEntry::$NOT_PRESENT)); ?>
      no
    </label>
  </td>
  <td>
      <?php if (!$removable): ?>
        <input type="hidden" name="<?= $field_prefix ?>[comments]" value="<?= $values['comments'] ?>"/>
          <?= $values['comments'] ?>
      <?php else: ?>
        <div class="cols-full">
          <button id="<?= CHtml::getIdByName($field_prefix . '[comments]') ?>_button"
                  class="button js-add-comments"
                  data-comment-container="#<?= CHtml::getIdByName($field_prefix . '[comment_container]') ?>"
                  type="button"
                  style="<?php if ($values['comments']): ?>visibility: hidden;<?php endif; ?>"
          >
            <i class="oe-i comments small-icon"></i>
          </button>
          <span class="comment-group js-comment-container"
                id="<?= CHtml::getIdByName($field_prefix . '[comment_container]') ?>"
                style="<?php if (!$values['comments']): ?>display: none;<?php endif; ?>"
                data-comment-button="#<?= CHtml::getIdByName($field_prefix . '[comments]') ?>_button">
              <?= CHtml::textField($field_prefix . '[comments]', $values['comments'], array(
                  'class' => 'js-comment-field',
              )) ?>
            <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
            </span>
        </div>
      <?php endif; ?>
  </td>

  <td>
      <?php if ($removable): ?>
        <i class="oe-i trash"></i>
      <?php else: ?>
        read only <i class="oe-i info small pad"></i>
      <?php endif; ?>
  </td>
</tr>