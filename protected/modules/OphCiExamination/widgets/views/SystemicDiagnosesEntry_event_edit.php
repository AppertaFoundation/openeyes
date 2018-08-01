<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

use OEModule\OphCiExamination\models\SystemicDiagnoses_Diagnosis;
?>

<?php
if (!isset($values)) {
    $values = array(
        'id' => $diagnosis->id,
        'disorder_id' => $diagnosis->disorder_id,
        'disorder_display' => $diagnosis->disorder ? $diagnosis->disorder->term : '',
        'has_disorder' => $diagnosis->has_disorder,
        'side_id' => $diagnosis->side_id,
        'side_display' => $diagnosis->side ? $diagnosis->side->adjective : 'N/A',
        'date' => $diagnosis->date,
        'date_display' => $diagnosis->getDisplayDate(),
    );
}

    if (isset($values['date']) && strtotime($values['date'])) {
        list($start_sel_year, $start_sel_month, $start_sel_day) = explode('-', $values['date']);
    } else {
        $start_sel_day = $start_sel_month = null;
        $start_sel_year = date('Y');
        $values['date'] = $start_sel_year . '-00-00'; // default to the year displayed in the select dropdowns
    }

    $is_new_record = isset($diagnosis) && $diagnosis->isNewRecord ? true : false;

    $mandatory = !$removable;
?>

<tr data-key="<?=$row_count?>" class="<?=$model_name ?>_row" style="height:50px; <?= ($values['has_disorder'] == SystemicDiagnoses_Diagnosis::$NOT_PRESENT && !$mandatory) ? 'display: none;' : '' ?>">
    <td style="width:270px;">
        <input type="hidden" name="<?= $field_prefix ?>[id][]" value="<?=$values['id'] ?>" />

        <input type="text" class="diagnoses-search-autocomplete"
               id="diagnoses_search_autocomplete_<?=$row_count?>"
               <?php if(isset($diagnosis)):?>
                    data-saved-diagnoses='<?php echo json_encode(array(
                            'id' => $values['id'],
                            'name' => $values['disorder_display'],
                            'disorder_id' => $values['disorder_id']), JSON_HEX_APOS); ?>'
               <?php endif; ?> >
        <input type="hidden" name="<?= $field_prefix ?>[disorder_id][]" value="">
    </td>


  <td id="<?="{$model_name}_{$row_count}_checked_status"?>">
      <?php

      $is_not_checked = $values['has_disorder'] == SystemicDiagnoses_Diagnosis::$NOT_CHECKED;
      $selected = $posted_checked_status ? $posted_checked_status : ($is_not_checked ? null : $values['has_disorder']);

      if($removable) {
          echo '<span>'.SystemicDiagnoses_Diagnosis::getStatusNameEditMode($selected).'</span>';
          echo CHtml::hiddenField($model_name . '[has_disorder][]', $selected);
      }
      else {
          echo CHtml::dropDownList($model_name . '[has_disorder][]', $selected, [
              SystemicDiagnoses_Diagnosis::$NOT_CHECKED => 'Not checked',
              SystemicDiagnoses_Diagnosis::$PRESENT => 'Yes',
              SystemicDiagnoses_Diagnosis::$NOT_PRESENT => 'No',
          ],['empty' => '- Select -']);
      }
      ?>
  </td>

  <?php if (!$removable): ?>
  <td class="<?= $model_name ?>_sides" style="white-space:nowrap">
      <?php if($values['side_id'] == \EYE::RIGHT  ||$values['side_id']=== \EYE::BOTH){ ?>
        <i class="oe-i laterality R small pad"></i>
      <?php } ?>
  </td>
  <td class="<?= $model_name ?>_sides" style="white-space:nowrap">
      <?php if($values['side_id'] == \EYE::LEFT  ||$values['side_id'] == \EYE::BOTH){ ?>
        <i class="oe-i laterality L small pad"></i>
      <?php } ?>
  </td>
  <?php else: ?>
    <input type="hidden" name="<?= $model_name ?>[side_id][]" class="diagnosis-side-value" value="<?=$values['side_id']?>">
      <?php foreach (Eye::model()->findAll(array('order' => 'display_order')) as $eye) {?>
      <td class="<?= $model_name ?>_sides" style="white-space:nowrap">
        <input type="radio"
               name="<?="{$model_name}_diagnosis_side_{$row_count}" ?>"
               value="<?php echo $eye->id?>"
            <?php if($eye->id == $values['side_id']){ echo "checked"; }?>/>
      </td>
      <?php }?>
      <td class="<?= $model_name ?>_sides" style="white-space:nowrap">
      <input type="radio"
             name="<?="{$model_name}_diagnosis_side_{$row_count}" ?>"
          <?php if(empty($values['side_id'])): ?> checked <?php endif; ?>
             value="" />
    </td>
  <?php endif; ?>
    <td>
        <?php if(!$removable) :?>
            <?=Helper::formatFuzzyDate($values['date']) ?>
        <?php else:?>
          <input id="systemic-diagnoses-datepicker-<?= $row_count; ?>" style="width:90px" placeholder="yyyy-mm-dd"  name="<?= $model_name ?>[date][]" value="<?=$values['date'] ?>" autocomplete="off">
          <i class="js-has-tooltip oe-i info small pad right" data-tooltip-content="You can enter date format as yyyy-mm-dd, or yyyy-mm or yyyy."></i>
        <?php endif; ?>
    </td>

    <?php if($removable) : ?>
      <td>
        <i class="oe-i trash"></i>
      </td>
    <?php else: ?>
      <td>read only <i class="oe-i info small pad"></i></td>
    <?php endif; ?>
</tr>
