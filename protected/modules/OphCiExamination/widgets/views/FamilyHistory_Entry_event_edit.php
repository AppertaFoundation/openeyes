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
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php
if (!isset($values)) {
    $values = array(
        'id' => $entry->id,
        'relative_id' => $entry->relative_id,
        'relative_display' => $entry->displayrelative,
        'other_relative' => $entry->other_relative,
        'side_id' => $entry->side_id,
        'side_display' => $entry->side,
        'condition_id' => $entry->condition_id,
        'condition_display' => $entry->displaycondition,
        'other_condition' => $entry->other_condition,
        'comments' => $entry->comments,
    );
}

?>
<tr class="row-<?=$row_count;?><?php if($editable){ echo " read-only"; } ?>" data-key="<?=$row_count;?>">
  <td>
    <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?=$values['id'] ?>" />
    <input type="hidden" name="<?= $field_prefix ?>[relative_id]" value="<?=$values['relative_id'] ?>" />
      <?= $values['relative_display'] ?>

      <div class="<?php echo $is_other_selected ? '' : 'hidden';?> other_relative_wrapper">
        <?php echo CHtml::textField($field_prefix . '[other_relative]', ( $is_other_selected ? $values['other_relative'] : ''), array('class' => 'other_relative_text other-type-input', 'autocomplete' => Yii::app()->params['html_autocomplete']))?>
      </div>
    </td>
    <td>
      <input type="hidden" name="<?= $field_prefix ?>[side_id]" value="<?=$values['side_id'] ?>" />
      <?= $values['side_display'] ?>
    </td>
    <td>
      <input type="hidden" name="<?= $field_prefix ?>[condition_id]" value="<?=$values['condition_id'] ?>" />
      <?= $values['condition_display'] ?>
    </td>
    <td>
        <?php if(!$editable): ?>
            <?= $values['comments'] ?>
        <?php else: ?>
          <div class="cols-full ">
            <button class="button  js-add-comments" data-input="next" style="display: none;" type="button">
              <i class="oe-i comments  small-icon"></i>
            </button>
              <?php echo CHtml::textField($field_prefix . '[comments]', $values['comments'], array('autocomplete' => Yii::app()->params['html_autocomplete'], 'class'=>'js-input-comments'))?>
          </div>
        <?php endif;?>
    </td>

    <?php if ($editable) : ?>
      <td>
        <i class="oe-i trash"></i>
      </td>
    <?php else: ?>
      <td>read only <i class="oe-i info small pad"></i></td>
    <?php endif; ?>
</tr>
