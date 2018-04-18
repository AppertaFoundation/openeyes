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
<tr data-key="<?=$row_count?>" class="<?=$field_prefix ?>_row" style="height:50px;">
    <td style="width:290px;padding-top:15px;padding-bottom:15px;">

        <input type="hidden" name="<?= $field_prefix ?>[id][]" value="<?=$values['id'] ?>" />

        <input type="text"
               class="diagnoses-search-autocomplete"
               id="diagnoses_search_autocomplete_<?=$row_count?>"
               data-saved-diagnoses='<?php echo json_encode(array(
                    'id' => $values['id'],
                    'name' => $values['disorder_display'],
                    'disorder_id' => $values['disorder_id'])); ?>'

        <input type="hidden" name="<?= $field_prefix ?>[disorder_id][]" value="">
    </td>

    <td>
        <div class="sides-radio-group">
            <?php foreach (Eye::model()->findAll(array('order' => 'display_order')) as $eye) {
                ?>
                <label class="inline">
                    <input type="radio"
                           name="<?= $model_name ?>[eye_id_<?=$row_count?>]"
                           value="<?=$eye->id?>"
                           <?php if ($values['eye_id'] == $eye->id) {?>checked="checked" <?php }?>/> <?php echo $eye->name?>
                </label>
            <?php } ?>
        </div>
    </td>
    <td>
        <input type="radio"
               name="principal_diagnosis"
               value="<?php echo $values['disorder_id']; ?>"
               <?php if ($values['is_principal'] == 1) {?>checked="checked" <?php } ?>/>
    </td>
    <td>
        <fieldset class="row field-row fuzzy-date">
            <input type="hidden" name="<?= $model_name ?>[date][]" value="<?= $values['date'] ?>" />
            <div class="large-12 column end">
                <span class="start-date-wrapper">
                <?php $this->renderPartial('application.views.patient._fuzzy_date_fields', array(
                    'sel_day' => $start_sel_day,
                    'sel_month' => $start_sel_month,
                    'sel_year' => $start_sel_year))
                ?>
                </span>
            </div>
        </fieldset>
    </td>
    <td class="edit-column">
        <?php if($removable) : ?>
            <button class="button small warning remove">remove</button>
        <?php else: ?>
            read only
        <?php endif; ?>
    </td>
</tr>
