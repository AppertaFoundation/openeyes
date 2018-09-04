<?php
/**
* OpenEyes.
*
* (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
* (C) OpenEyes Foundation, 2011-2013
* This file is part of OpenEyes.
* OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
* OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
* You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
*
* @link http://www.openeyes.org.uk
*
* @author OpenEyes <info@openeyes.org.uk>
* @copyright Copyright (c) 2011-2013, OpenEyes Foundation
* @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
*/
?>

<?php
if (!isset($values)) {
    $values = array(
        'id' => $diagnosis->id,
        'disorder_id' => $diagnosis->disorder_id,
        'disorder_display' => $diagnosis->disorder ? $diagnosis->disorder->term : '',
        'eye_id' => $diagnosis->eye_id,
        'date' => $diagnosis->date,
        'date_display' => \Helper::formatFuzzyDate($diagnosis->date),
        'is_principal' => $diagnosis->principal
    );
}
if (isset($values['date']) && strtotime($values['date'])) {
    list($start_sel_year, $start_sel_month, $start_sel_day) = explode('-', $values['date']);
} else if( isset($values['event_date']) && strtotime($values['event_date']) ) {

    // there is event_date but no diagnosis date, this is an update with new diagnoses element
    list($start_sel_year, $start_sel_month, $start_sel_day) = explode('-', $values['event_date']);
    $values['date'] = "$start_sel_year-$start_sel_month-$start_sel_day";
} else {
    // no event_date, no diagnosis date, it seems this is a new event
    $start_sel_day = $start_sel_month = $start_sel_year = null;
}

?>
<tr data-key="<?=$row_count?>" class="<?=$field_prefix ?>_row">
    <td>
        <?=$values['disorder_display'];?>
        <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?=$values['id'] ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[row_key]" value="<?=$row_count?>" />

        <input type="hidden"
               class="savedDiagnosis"
               name="<?= $field_prefix ?>[disorder_id]"
               value="<?=$values['disorder_id']?>"
        >
    </td>

    <?php $this->widget('application.widgets.EyeSelector', [
        'inputNamePrefix' => $field_prefix,
        'selectedEyeId' => $values['eye_id'],
        'hideNotAvailableOption' => isset($hide_not_available_eye_option) ? $hide_not_available_eye_option : false,
    ]); ?>

    <td>
        <?=\CHtml::radioButton("principal_diagnosis_row_key", $values['is_principal'] == 1, ['value' => $row_count]); ?>
    </td>
    <td>
          <input id="diagnoses-datepicker-<?= $row_count; ?>"
                 style="width:90px"
                 placeholder="yyyy-mm-dd"
                 name="<?= $field_prefix ?>[date]"
                 autocomplete="off"
                 value="<?= $values['date'] ?>"
          >
          <i class="js-has-tooltip oe-i info small pad right"
             data-tooltip-content="You can enter date format as yyyy-mm-dd, or yyyy-mm or yyyy.">
          </i>

    </td>
    <td class="edit-column">
        <?php if ($removable) : ?>
          <a href="#" class="removeDiagnosis" rel="<?php echo $values['disorder_id'] ?>">
            <i class="oe-i trash"></i>
          </a>
        <?php else : ?>
            read only
        <?php endif; ?>
    </td>
</tr>
