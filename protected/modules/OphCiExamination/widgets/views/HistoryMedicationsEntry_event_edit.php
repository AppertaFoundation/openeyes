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
 */

?>

<?php

if (!isset($values)) {
    $values = array(
        'id' => $entry->id,
        'drug_id' => $entry->drug_id?:'',
        'medication_drug_id' => $entry->medication_drug_id? :'' ,
        'medication_name' =>$entry->getMedicationDisplay()? :'',
    );
}

if (isset($entry->start_date) && strtotime($entry->start_date)) {
    list($start_sel_year, $start_sel_month, $start_sel_day) = array_pad(explode('-', $entry->start_date), 3,0);
}else {
    $start_sel_day = $start_sel_month = null;
    $start_sel_year = date('Y');
}
if (isset($entry->end_date) && strtotime($entry->end_date)) {
    list($end_sel_year, $end_sel_month, $end_sel_day) = array_pad(explode('-', $entry->end_date), 3,0);
} else {
    $end_sel_day = $end_sel_month = null;
    $end_sel_year = date('Y');
}
?>

<tr data-key="<?=$row_count?>"
    style="display: <?= $entry->originallyStopped ? 'none' : ''?>"
    class="<?=$field_prefix ?>_row <?= $entry->originallyStopped ? 'originally-stopped' : ''?>">
    <td>
      <span class="medication-display">
        <span class="medication-name">
          <?= $values['medication_name'] ?>
        </span>
      </span>
      <?php if ($entry->originallyStopped) { ?>
        <i class="oe-i stop small pad"></i>
      <?php } ?>
        <input type="hidden" name="<?= $field_prefix ?>[drug_id]" value="<?= $values['drug_id'] ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[medication_drug_id]" value="<?= $values['medication_drug_id'] ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[medication_name]" value="<?= $values['medication_name'] ?>" />
    </td>
        <?php
        $select_options = Drug::model()->listBySubspecialtyWithCommonMedications($this->getFirm()->getSubspecialtyID(), true);
        $html_options = ['empty' => '- Select -'];
        $hide_search = strlen($entry->getMedicationDisplay()) > 0;
        if($hide_search){
            $html_options['style'] = 'display: none;';
        }

        foreach($select_options as $select_option){
            $html_options['options'][$select_option['id']] = [
                'data-tags' => implode(',', $select_option['tags']),
                'data-tallmanlabel' => $select_option['name'],
            ];
        }
        ?>

    <td>
      <input type="hidden" name="<?= $field_prefix ?>[units]" value="<?= $entry->units ?>" />
      <input class="cols-2" type="text" name="<?= $field_prefix ?>[dose]" value="<?= $entry->dose ?>" placeholder="Dose" />
        <?= CHtml::dropDownList($field_prefix . '[frequency_id]', $entry->frequency_id, $frequency_options, array('empty' => '-Select-', 'class'=>'cols-3')) ?>
        <?= CHtml::dropDownList($field_prefix . '[route_id]', $entry->route_id, $route_options, array('empty' => '-Select-', 'class'=>'cols-4')) ?>
        <?= CHtml::dropDownList($field_prefix . '[option_id]',
                  $entry->option_id,
                  CHtml::listData($entry->routeOptions() ?: array(), 'id', 'name'),
                  array('empty' => '-Select-', 'class'=>'cols-2 admin-route-options', 'style'=>$entry->routeOptions()?'':'display:none' ));  ?>

    </td>
    <td>
      <fieldset class="data-group fuzzy-date">
      <?php if (!$entry->start_date||$removable) { ?>
        <input id="datepicker_1_<?=$row_count?>" name="<?= $field_prefix ?>[start_date]"
               value="<?= $entry->start_date ? $entry->start_date : (Yii::app()->request->isPostRequest ?  "" : date('Y-m-d')) ?>"
               style="width:80px" placeholder="yyyy-mm-dd" autocomplete="off">
        <i class="js-has-tooltip oe-i info small pad right" data-tooltip-content="You can enter date format as yyyy-mm-dd, or yyyy-mm or yyyy."></i>
      <?php } else { ?>
        <i class="oe-i start small pad"></i>
          <?=Helper::formatFuzzyDate($entry->start_date) ?>
        <input type="hidden" name="<?= $field_prefix ?>[start_date]" value="<?= $entry->start_date ?>" />

      <?php } ?>
    <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?=$entry->id ?>" />
    <input type="hidden" name="<?= $field_prefix ?>[originallyStopped]" value="<?=$entry->originallyStopped ?>" />
    </fieldset>
    </td>
  <td>
    <fieldset class="data-group fuzzy-date">
        <?php if (!$entry->end_date||$removable) { ?>
          <input id="datepicker_2_<?=$row_count?>" name="<?= $field_prefix ?>[end_date]" value="<?= $entry->end_date ?>" style="width:80px" placeholder="yyyy-mm-dd" autocomplete="off">
          <i class="js-has-tooltip oe-i info small pad right" data-tooltip-content="You can enter date format as yyyy-mm-dd, or yyyy-mm or yyyy."></i>
        <?php } else { ?>
          <i class="oe-i start small pad"></i>
            <?=Helper::formatFuzzyDate($entry->end_date) ?>
          <input type="hidden" name="<?= $field_prefix ?>[end_date]" value="<?= $entry->end_date ?>" />
        <?php } ?>
    </fieldset>
  </td>
    <td>
        <?= CHtml::dropDownList($field_prefix . '[stop_reason_id]', $entry->stop_reason_id, $stop_reason_options, array('empty' => '-Stop Reason-')) ?>
    </td>
    <td>
        <?php if ($removable) { ?>
            <i class="oe-i trash"></i>
        <?php } else { ?>
            <i class="oe-i info small-icon"></i>
        <?php } ?>
    </td>
</tr>
