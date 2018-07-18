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
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<?php

if (isset($entry->start_date) && strtotime($entry->start_date)) {
    list($start_sel_year, $start_sel_month, $start_sel_day) = explode('-', $entry->start_date);
} else {
    $start_sel_day = $start_sel_month = null;
    $start_sel_year = date('Y');
    $entry->start_date = $start_sel_year . '-00-00'; // default to the year displayed in the select dropdowns
}
if (isset($entry->end_date) && strtotime($entry->end_date)) {
    list($end_sel_year, $end_sel_month, $end_sel_day) = explode('-', $entry->end_date);
} else {
    $end_sel_day = $end_sel_month = null;
    $end_sel_year = date('Y');
}
?>

<tr data-key="<?=$row_count?>" class="<?=$field_prefix ?>_row <?= $entry->originallyStopped ? 'originally-stopped' : ''?>">
    <td>
        <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?=$entry->id ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[prescription_item_id]" value="<?=$entry->prescription_item_id ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[originallyStopped]" value="<?=$entry->originallyStopped ?>" />
      <span class="medication-display">
        <span class="medication-name">
          <?= $entry->getMedicationDisplay() ?>
        </span>
      </span>
      <input type="hidden" name="<?= $field_prefix ?>[drug_id]" value="<?= $entry->drug_id ?>" />
        <?php if ($entry->originallyStopped) { ?>
          <i class="oe-i stop small pad"></i>
        <?php } ?>
    </td>
  <td>
    <div class="row">
      <input type="hidden" name="<?= $field_prefix ?>[dose]" value="<?= $entry->dose ?>" />
      <input type="hidden" name="<?= $field_prefix ?>[frequency_id]" value="<?= $entry->frequency_id ?>" />
      <input type="hidden" name="<?= $field_prefix ?>[route_id]" value="<?= $entry->route_id ?>" />
      <input type="hidden" name="<?= $field_prefix ?>[option_id]" value="<?= $entry->option_id ?>" />
        <?= $entry->getAdministrationDisplay() ?>
    </div>
  </td>
  <td>
      <fieldset class="row field-row fuzzy-date">
        <input type="hidden" name="<?= $field_prefix ?>[start_date]" value="<?= $entry->start_date? $entry->start_date: date('Y-m-d') ?>" />
        <i class="oe-i start small pad"></i>
          <?= Helper::convertMySQL2NHS($entry->start_date) ?>
      </fieldset>
  </td>
  <td>
        <fieldset class="row field-row fuzzy-date">
            <div class="large-11 column end">
                <?php if (!$entry->end_date) { ?>
                  <input id="datepicker_2_<?=$row_count?>"
                         name="<?= $field_prefix ?>[end_date]" value="<?= $entry->end_date? $entry->end_date: date('Y-m-d') ?>"
                         style="width:80px" placeholder="yyyy-mm-dd">
                  <i class="js-has-tooltip oe-i info small pad right" data-tooltip-content="You can enter date format as yyyy-mm-dd, or yyyy-mm or yyyy."></i>
                <?php } else { ?>
                  <i class="oe-i start small pad"></i>
                    <?=Helper::formatFuzzyDate($entry->end_date) ?>
                  <input type="hidden" name="<?= $field_prefix ?>[end_date]" value="<?= $entry->end_date? $entry->end_date: date('Y-m-d') ?>" />
                <?php } ?>
            </div>
        </fieldset>
    </td>
    <td>
        <?= CHtml::dropDownList($field_prefix . '[stop_reason_id]', $entry->stop_reason_id, $stop_reason_options, array('empty' => '-?-', 'class'=>'cols-full')) ?>
    </td>

    <td class="text-center">
      <i class="oe-i info small pad js-has-tooltip" data-tooltip-content=
      "This medication was prescribed through OpenEyes.<?= $entry->prescriptionNotCurrent() ? ' The prescription has been altered since this entry was recorded.' : ''; ?>"></i>
    </td>
</tr>
