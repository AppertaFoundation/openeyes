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
        'id' => $diagnosis->id,
        'disorder_id' => $diagnosis->disorder_id,
        'disorder_display' => $diagnosis->disorder ? $diagnosis->disorder->term : '',
        'side_id' => $diagnosis->side_id,
        'side_display' => $diagnosis->side ? $diagnosis->side->adjective : 'None',
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

?>

<tr data-key="<?=$row_count?>" class="<?=$field_prefix ?>_row" style="height:50px;">
    <td style="width:290px;">

        <input type="hidden" name="<?= $field_prefix ?>[id][]" value="<?=$values['id'] ?>" />

        <input type="text"
               class="diagnoses-search-autocomplete"
               id="diagnoses_search_autocomplete_<?=$row_count?>"

               <?php if(isset($diagnosis)):?>
                    data-saved-diagnoses='<?php echo json_encode(array(
                            'id' => $values['id'],
                            'name' => $values['disorder_display'],
                            'disorder_id' => $values['disorder_id'])); ?>'

               <?php endif; ?>
        >
        <input type="hidden" name="<?= $field_prefix ?>[disorder_id][]" value="">
    </td>

    <td>
        <div class="sides-radio-group">
            <label class="inline">
                <input type="radio" name="<?="{$model_name}_diagnosis_side_{$row_count}" ?>" value="" checked="checked" /> None
            </label>

            <?php foreach (Eye::model()->findAll(array('order' => 'display_order')) as $eye) {?>
                <label class="inline">
                    <input type="radio"
                           name="<?="{$model_name}_diagnosis_side_{$row_count}" ?>"
                           value="<?php echo $eye->id?>"
                            <?php if($eye->id == $values['side_id']){ echo "checked"; }?>
                    />
                    <?php echo $eye->name ?>
                </label>
            <?php }?></td>
        </div>

        <input type="hidden" name="<?= $model_name ?>[side_id][]" class="diagnosis-side-value" value="<?=$values['side_id']?>">
    <td>
        <fieldset class="row field-row fuzzy-date">
            <input type="hidden" name="<?= $model_name ?>[date][]" value="<?= $values['date'] ?>" />
            <div class="large-12 column end">
                <span class="start-date-wrapper" <?php if (!$values['date']) {?>style="display: none;"<?php } ?>">
                    <?php $this->render('application.views.patient._fuzzy_date_fields', array(
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
