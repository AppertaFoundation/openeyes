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
    $is_new_record = isset($diagnosis) && $diagnosis->isNewRecord ? true : false;

    $mandatory = !$removable;
?>

<tr data-key="<?=$row_count?>" class="<?=$model_name ?>_row" style="height:50px; <?= ($values['has_disorder'] == SystemicDiagnoses_Diagnosis::$NOT_PRESENT && !$mandatory) ? 'display: none;' : '' ?>">
    <td style="width:270px;">
        <input type="hidden" name="<?= $field_prefix ?>[id][]" value="<?=$values['id'] ?>" />

        <input type="text" class="diagnoses-search-autocomplete"
               id="diagnoses_search_autocomplete_<?=$row_count?>" style="display: none"
               <?php if(isset($diagnosis)):?>
                    data-saved-diagnoses='<?php echo json_encode(array(
                            'id' => $values['id'],
                            'name' => $values['disorder_display'],
                            'disorder_id' => $values['disorder_id']), JSON_HEX_APOS); ?>'

               <?php endif; ?>
        >
        <input type="hidden" name="<?= $field_prefix ?>[disorder_id][]" value="">
    </td>

  <?php if (!$removable): ?>
  <td class="<?= $model_name ?>_sides" style="white-space:nowrap">
      <?php if($values['side']=='Right'||$values['side']=='Both'){ ?>
        <i class="oe-i laterality R small pad"></i>
      <?php } ?>
  </td>
  <td class="<?= $model_name ?>_sides" style="white-space:nowrap">
  <?php if($values['side']=='Left'||$values['side']=='Both'){ ?>
        <i class="oe-i laterality L small pad"></i>
      <?php } ?>
  </td>
  <td></td>
  <td></td>
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
          <input id="systemic-diagnoses-datepicker-<?= $row_count; ?>" style="width:90px" placeholder="dd/mm/yyyy"  name="<?= $model_name ?>[date][]" value="<?=$values['date'] ?>" >
        <?php endif; ?>
    </td>

    <?php if($removable) : ?>
      <td></td>
      <td>
        <i class="oe-i trash"></i>
      </td>
    <?php else: ?>
      <td>read only <i class="oe-i info small pad"></i></td>
      <td></td>
    <?php endif; ?>
</tr>
