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

?>

<?php
if (!isset($values)) {
    $values = array(
        'id' => $entry->id,
        'drug_id' => $entry->drug_id,
        'medication_drug_id' => $entry->medication_drug_id,
        'medication_name' => $entry->medication_name,
        'start_date' => $entry->start_date,
        'end_date' => $entry->end_date,
        'medicationDisplay' => $entry->getMedicationDisplay()
    );
}

if (isset($values['start_date']) && strtotime($values['start_date'])) {
    list($start_sel_year, $start_sel_month, $start_sel_day) = explode('-', $values['start_date']);
} else {
    $start_sel_day = $start_sel_month = null;
    $start_sel_year = date('Y');
    $values['start_date'] = $start_sel_year . '-00-00'; // default to the year displayed in the select dropdowns
}
if (isset($values['end_date']) && strtotime($values['end_date'])) {
    list($end_sel_year, $end_sel_month, $end_sel_day) = explode('-', $values['end_date']);
} else {
    $end_sel_day = $end_sel_month = null;
    $end_sel_year = date('Y');
}
?>

<tr data-key="<?=$row_count?>">
    <td>
        <fieldset class="row field-row fuzzy-date">
            <input type="hidden" name="<?= $field_prefix ?>[start_date]" value="<?=$values['start_date'] ?>" />
            <div class="large-2 column">
                <label>Start:</label>
            </div>
            <div class="large-10 column end">
                <?php $this->render('application.views.patient._fuzzy_date_fields', array('sel_day' => $start_sel_day, 'sel_month' => $start_sel_month, 'sel_year' => $start_sel_year)) ?>
            </div>
        </fieldset>
        <button class="button small warning stop-medication date-control" <?php if ($values['end_date']) {?>style="display: none;"<?php } ?>>stop</button>
        <span class="stop-date-wrapper" <?php if (!$values['end_date']) {?>style="display: none;"<?php } ?>>
            <fieldset class="row field-row fuzzy-date">
                <input type="hidden" name="<?= $field_prefix ?>[end_date]" value="<?=$values['end_date'] ?>" />
                <div class="large-2 column">
                    <label>Stop:</label>
                </div>
                <div class="large-10 column end">
                    <?php $this->render('application.views.patient._fuzzy_date_fields', array('sel_day' => $end_sel_day, 'sel_month' => $end_sel_month, 'sel_year' => $end_sel_year)) ?>
                </div>
            </fieldset>
            <button class="button small warning cancel-stop-medication date-control">cancel stop</button>
        </span>
    </td>
    <td><span class="medication-display"><span class="medication-name"></span> <a href="#" class="medication-rename"><i class="fa fa-times-circle" aria-hidden="true" title="Change medication"></i></a></span>
        <input type="hidden" name="<?= $field_prefix ?>[drug_id]" value="<?= $values['drug_id'] ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[medication_drug_id]" value="<?= $values['medication_drug_id'] ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[medication_name]" value="<?= $values['medication_name'] ?>" />
        <input type="text" name="<?= $field_prefix ?>[medication_search]" class="search" placeholder="Type to search" /></td>
    <td>administration</td>
    <td class="edit-column">
        <button class="button small warning remove" <?php if (!$removable) {?>style="display: none;"<?php } ?>>remove</button>
    </td>
</tr>
