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
        'is_glaucoma' => isset($diagnosis->disorder)? (strpos(strtolower($diagnosis->disorder->term), 'glaucoma')) !== false : false,
        'eye_id' => $diagnosis->eye_id,
        'date' => $diagnosis->date,
        'time' => $diagnosis->time,
        'date_display' => \Helper::formatFuzzyDate($diagnosis->date),
        'is_principal' => $diagnosis->principal
    );
}
if (!isset($values['date']) || !strtotime($values['date'])) {
    if (isset($values['event_date']) && strtotime($values['event_date'])) {
        // there is event_date but no diagnosis date, this is an update with new diagnoses element
        list($start_sel_year, $start_sel_month, $start_sel_day) = explode('-', $values['event_date']);
        $values['date'] = "$start_sel_year-$start_sel_month-$start_sel_day";
    }
}
?>
<tr data-key="<?=$row_count?>" class="<?=$field_prefix ?>_row" id="<?= $model_name . '_diagnoses_entries_row_' . $row_count ?>">
    <td>
        <?=$values['disorder_display'];?>
        <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?=$values['id'] ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[row_key]" value="<?=$row_count?>" />
        <input type="hidden" name="glaucoma_diagnoses[]" value="<?=isset($values['is_glaucoma']) ? $values['is_glaucoma'] : 'false'?>" />

        <input type="hidden"
               class="savedDiagnosis"
               name="<?= $field_prefix ?>[disorder_id]"
               value="<?=$values['disorder_id']?>"
        >

        <div class='condition-secondary-to-wrapper' style='display:none;'>
            <div>Associated diagnosis:</div>
            <select class='condition-secondary-to'>
                <option>Please select</option>
            </select>
        </div>
    </td>

    <?php if (isset($is_template) && $is_template) { ?>
        <td class='nowrap'>
            <span class='oe-eye-lat-icons'>
                <label class="inline highlight">
                <input class="js-right-eye" data-eye-side="right" type="checkbox" value="1" {{#right_eye_checked}} checked="checked"{{/right_eye_checked}} name="<?= $field_prefix ?>[right_eye]" id="<?= $model_name ?>_entries_{{row_count}}_right_eye" /> R</label>
                <label class="inline highlight">
                <input class="js-left-eye" data-eye-side="left" type="checkbox" value="1" {{#left_eye_checked}} checked="checked"{{/left_eye_checked}} name="<?= $field_prefix ?>[left_eye]" id="<?= $model_name ?>_entries_{{row_count}}_left_eye" /> L</label >
            </span>
        </td>
    <?php } else {
        $this->widget('application.widgets.EyeSelector', [
            'inputNamePrefix' => $field_prefix,
            'selectedEyeId' => $values['eye_id'],
            'template' => "<td class='nowrap'><span class='oe-eye-lat-icons'>{Right}{Left}</span></td>"
        ]);
    }
    ?>

    <td>
        <?php if (isset($is_template) && $is_template) {  ?>
            <input value="<?= $row_count ?>" {{#is_principal}} checked="checked"{{/is_principal}} type="radio" name="principal_diagnosis_row_key" id="principal_diagnosis_row_key" />
        <?php } else {
            echo \CHtml::radioButton("principal_diagnosis_row_key", $values['is_principal'] == 1, ['value' => $row_count]);
        }
        ?>
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
    <td>
        <input class="fixed-width-medium"
               type="time"
               name="<?= $field_prefix ?>[time]"
               value="<?= $values['time'] ?>"/>
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
