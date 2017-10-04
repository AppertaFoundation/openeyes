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
        <input type="hidden" name="<?= $field_prefix ?>[originallyStopped]" value="<?=$entry->originallyStopped ?>" />
        <fieldset class="row field-row fuzzy-date">
            <input type="hidden" name="<?= $field_prefix ?>[start_date]" value="<?= $entry->start_date ?>" />
            <div class="large-1 column text-right">
                <a href="#" class="start-medication enable date-control has-tooltip" data-tooltip-content="record a start date for this medication" <?php if ($entry->start_date) {?>style="display: none;"<?php } ?>><i class="fa fa-icon fa-play-circle"></i></a>
                <a href="#" class="start-medication cancel date-control has-tooltip" data-tooltip-content="remove the start date for this medication" <?php if (!$entry->start_date) {?>style="display: none;"<?php } ?>><i class="fa fa-icon fa-times-circle"></i></a>
            </div>
            <div class="large-11 column end">
                <span class="start-date-wrapper" <?php if (!$entry->start_date) {?>style="display: none;"<?php } ?>">
                  <?php $this->render('application.views.patient._fuzzy_date_fields', array('sel_day' => $start_sel_day, 'sel_month' => $start_sel_month, 'sel_year' => $start_sel_year)) ?>
                </span>
            </div>
        </fieldset>

        <fieldset class="row field-row fuzzy-date">
            <input type="hidden" name="<?= $field_prefix ?>[end_date]" value="<?= $entry->end_date ?>" />
            <div class="large-1 column text-right">
                <a href="#" class="stop-medication enable date-control has-tooltip" data-tooltip-content="record a stop date for this medication" <?php if ($entry->end_date) {?>style="display: none;"<?php } ?>><i class="fa fa-icon fa-stop"></i></a>
                <a href="#" class="stop-medication cancel date-control has-tooltip" data-tooltip-content="remove the stop date for this medication" <?php if (!$entry->end_date) {?>style="display: none;"<?php } ?>><i class="fa fa-icon fa-times-circle"></i></a>
            </div>
            <div class="large-11 column end">
              <span class="stop-date-wrapper" <?php if (!$entry->end_date) {?>style="display: none;"<?php } ?>>
                <?php $this->render('application.views.patient._fuzzy_date_fields', array('sel_day' => $end_sel_day, 'sel_month' => $end_sel_month, 'sel_year' => $end_sel_year)) ?>
                <?= CHtml::dropDownList($field_prefix . '[stop_reason_id]', $entry->stop_reason_id, $stop_reason_options, array('empty' => '-Stop Reason-')) ?>
            </span>
            </div>
        </fieldset>
    </td>
    <td>
        <span class="medication-display"><a href="#" class="medication-rename"><i class="fa fa-times-circle" aria-hidden="true" title="Change medication"></i></a> <span class="medication-name"><?= $entry->getMedicationDisplay() ?></span></span>
        <input type="hidden" name="<?= $field_prefix ?>[drug_id]" value="<?= $entry->drug_id ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[medication_drug_id]" value="<?= $entry->medication_drug_id ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[medication_name]" value="<?= $entry->medication_name ?>" />
        <?php $hide_search = strlen($entry->getMedicationDisplay()) > 0; ?>
        <?= $this->getFirm() ?
        CHtml::dropDownList(
            $field_prefix . '[drug_select]',
            '',
            Drug::model()->listBySubspecialtyWithCommonMedications($this->getFirm()->getSubspecialtyID()),
            ($hide_search ? array('empty' => '- Select -', 'style' => 'display: none;') : array('empty' => '- Select -'))
        ) : ''; ?>
        <input type="text" name="<?= $field_prefix ?>[medication_search]" value="<?= $entry->getMedicationDisplay() ?>" class="search" placeholder="Type to search" <?= $hide_search ? 'style="display: none;"': '' ?>/>
    </td>
    <td>
        <div class="row">
          <div class="large-6 column">
            <div class="row">
              <div class="large-3 column"><label>Dose:</label></div>
              <div class="large-9 column end">
                  <input type="hidden" name="<?= $field_prefix ?>[units]" value="<?= $entry->units ?>" />
                  <input type="text" name="<?= $field_prefix ?>[dose]" value="<?= $entry->dose ?>" placeholder="<?= $entry->units ?>" />
              </div>
            </div>
            <div class="row">
              <div class="large-3 column"><label class="has-tooltip" data-tooltip-content="Frequency">Freq.:</label></div>
              <div class="large-9 column end">
                  <?= CHtml::dropDownList($field_prefix . '[frequency_id]', $entry->frequency_id, $frequency_options, array('empty' => '-Select-')) ?>
              </div>
            </div>
          </div>
          <div class="large-6 column end">
            <div class="row">
              <div class="large-3 column"><label>Route:</label></div>
              <div class="large-9 column end">
                  <?= CHtml::dropDownList($field_prefix . '[route_id]', $entry->route_id, $route_options, array('empty' => '-Select-')) ?>
              </div>
            </div>
            <div class="row admin-route-options" <?php if (!$entry->routeOptions()) {?>style="display:none;"<?php } ?>>
              <div class="large-3 column"><label class="has-tooltip" data-tooltip-content="Route Option">Opt.:</label></div>
              <div class="large-9 column end route-option-wrapper">
                  <?= CHtml::dropDownList($field_prefix . '[option_id]',
                      $entry->option_id,
                      CHtml::listData($entry->routeOptions() ?: array(), 'id', 'name'),
                      array('empty' => '-Select-')) ?>
              </div>
            </div>
          </div>
        </div>
    </td>
    <td class="edit-column">
        <button class="button small warning remove" <?php if (!$removable) {?>style="display: none;"<?php } ?>>remove</button>
    </td>
</tr>
