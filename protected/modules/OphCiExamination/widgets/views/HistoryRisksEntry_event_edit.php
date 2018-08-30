<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
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
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OEModule\OphCiExamination\models\HistoryRisksEntry;

?>

<?php

if (!isset($values)) {
    $values = array(
        'id' => $entry->id,
        'risk_id' => $entry->risk_id,
        'risk_display' => $entry->risk ? $entry->risk->name : '',
        'has_risk' => $entry->has_risk,
        'other' => $entry->other,
        'comments' => $entry->comments,
    );
}
?>
<tr data-key="<?= $row_count ?>">
  <td>
    <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?= $values['id'] ?>"/>
      <?php
      echo CHtml::hiddenField($field_prefix . '[risk_id]', $values['risk_id']);
      echo CHtml::hiddenField($field_prefix . '[other]', $values['other']); ?>
    <label class="risk-display" data-id="<?= $values['risk_id'] ?>"
           data-label="<?= $values['risk_display'] ?>"><?= $values['risk_display']; ?></label>
    <span class="<?= $model_name ?>_other_wrapper" style="display: <?= $values['other'] ?: 'none' ?>">
        <?php echo CHtml::textField($field_prefix . '[other]', $values['other'],
            array('class' => 'other-type-input', 'autocomplete' => Yii::app()->params['html_autocomplete'])) ?>
    </span>
  </td>
  <td id="OEModule_OphCiExamination_models_HistoryRisks_entries_<?= $row_count ?>_risk_id_error">
      <?php if($removable) {
          echo CHtml::hiddenField($field_prefix . '[has_risk]' , (string) HistoryRisksEntry::$PRESENT);
      } else { ?>
    <label class="inline highlight">
        <?php echo CHtml::radioButton($field_prefix . '[has_risk]',
            $values['has_risk'] === (string)HistoryRisksEntry::$PRESENT,
            array('value' => HistoryRisksEntry::$PRESENT)); ?>
      Yes
    </label>
    <label class="inline highlight">
        <?php echo CHtml::radioButton($field_prefix . '[has_risk]',
            $values['has_risk'] === (string)HistoryRisksEntry::$NOT_PRESENT,
            array('value' => HistoryRisksEntry::$NOT_PRESENT)); ?>
      No
    </label>
    <label class="inline highlight">
        <?php echo CHtml::radioButton($field_prefix . '[has_risk]', $posted_not_checked,
            array('value' => HistoryRisksEntry::$NOT_CHECKED)); ?>
      Not checked
    </label>
      <?php } ?>
  </td>
  <td>
    <button
        id="<?= strtr($field_prefix, '[]', '__') ?>_comment_button"
        type="button"
        class="button js-add-comments"
        style="<?php if ($values['comments']): ?>visibility: hidden;<?php endif; ?>"
        data-comment-container="#<?= strtr($field_prefix, '[]', '__') ?>_comment_container">
      <i class="oe-i comments small-icon"></i>
    </button>
    <span class="comment-group js-comment-container"
          id="<?= strtr($field_prefix, '[]', '__') ?>_comment_container"
          style="<?php if (!$values['comments']): ?>display: none;<?php endif; ?>"
          data-comment-button="#<?= strtr($field_prefix, '[]', '__') ?>_comment_button">
      <input type="text" class="js-comment-field" name="<?= $field_prefix ?>[comments]"
             value="<?= $values['comments'] ?>" id="<?= strtr($field_prefix, '[]', '__') ?>_comments"/>
      <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
    </span>
  </td>
    <?php if ($removable) : ?>
      <td>
        <i class="oe-i trash"></i>
      </td>
    <?php else: ?>
      <td>read only</td>
    <?php endif; ?>
</tr>
