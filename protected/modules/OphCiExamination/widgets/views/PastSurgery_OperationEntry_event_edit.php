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
        'id' => $op->id,
        'operation' => $op->operation,
        'side_id' => $op->side_id,
        'side_display' => $op->side ? $op->side->adjective : 'None',
        'date' => $op->date,
        'date_display' => $op->getDisplayDate()
    );
}

$sel_day = null;
$sel_month = null;
$sel_year = null;

?>
<tr>
    <td>
        <input type="hidden" name="<?= $model_name ?>[id][]" value="<?=$values['id'] ?>" />

        <?php echo CHtml::dropDownList($model_name . '[operation][]', '',
            CHtml::listData(CommonPreviousOperation::model()->findAll(
                array('order' => 'display_order asc')), 'id', 'name'),
            array('empty' => '- Select -'))?>
        <br><br>
        <?php echo CHtml::textField($model_name . '_previous_operation', '', array('autocomplete' => Yii::app()->params['html_autocomplete']))?>
    </td>
    <td style="white-space:nowrap">
        <input type="hidden" name="<?= $model_name ?>[side_id][]" value="<?=$values['side_id'] ?>" />
        <label class="inline"><input type="radio" name="<?= $model_name ?>_previous_operation_side" class="<?= $model_name ?>_previous_operation_side" value="" checked="checked" /> None </label>
        <?php foreach (Eye::model()->findAll(array('order' => 'display_order')) as $i => $eye) {?>
            <label class="inline">
                <input type="radio" name="<?= $model_name ?>_previous_operation_side" class="<?= $model_name ?>_previous_operation_side" value="<?php echo $eye->id?>" />
                <?php echo $eye->name?>
            </label>
        <?php }?>
    </td>
    <td>
        <input type="hidden" name="<?= $model_name ?>[date][]" value="<?=$values['date'] ?>" />

        <fieldset id="<?= $model_name ?>_fuzzy_date" class="row field-row fuzzy_date">
            <div class="large-12 column end">
                <div class="row">
                    <div class="large-3 column">
                        <select class="fuzzy_day">
                            <option value="0">Day</option>
                            <?php for ($i = 1;$i <= 31;++$i) {?>
                                <option value="<?= $i?>"<?= ($i == $sel_day) ? ' selected' : ''?>><?= $i?></option>
                            <?php }?>
                        </select>
                    </div>
                    <div class="large-5 column">
                        <select class="fuzzy_month">
                            <option value="0">Month</option>
                            <?php foreach (array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December') as $i => $month) {?>
                                <option value="<?= $i + 1?>"<?= ($i + 1 == $sel_month) ? ' selected' : ''?>><?= $month?></option>
                            <?php }?>
                        </select>
                    </div>
                    <div class="large-3 column end">
                        <select class="fuzzy_year">
                            <option value="0">Year</option>
                            <?php for ($i = date('Y') - 50;$i <= date('Y');++$i) {?>
                                <option value="<?= $i?>"<?= ($i == $sel_year) ? ' selected' : ''?>><?= $i?></option>
                            <?php }?>
                        </select>
                    </div>
                </div>
            </div>
        </fieldset>


    </td>

    <?php if($removable) : ?>

    <td class="edit-column">
        <button class="button small warning remove">remove</button>
    </td>
    <?php else: ?>
    <td>read only <span class="has-tooltip fa fa-info-circle" data-tooltip-content="This operation is recorded as an Operation Note event in OpenEyes and cannot be edited here"></span></td>
    <?php endif; ?>

</tr>