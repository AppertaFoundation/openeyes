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
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php
if (!isset($values)) {
    $values = array(
        'id' => $entry->id,
        'relative_id' => $entry->relative_id,
        'relative_display' => $entry->displayrelative,
        'other_relative' => $entry->other_relative,
        'side_id' => $entry->side_id,
        'side_display' => $entry->side,
        'condition_id' => $entry->condition_id,
        'condition_display' => $entry->displaycondition,
        'other_condition' => $entry->other_condition,
        'comments' => $entry->comments,
    );
}

?>
<tr class="row-<?=$row_count;?><?php if($editable){ echo " read-only"; } ?>" data-key="<?=$row_count;?>">
    <td>
        <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?=$values['id'] ?>" />

        <?php if(!$editable): ?>
            <?= $values['relative_display'] ?>
        <?php else: ?>
            <?php
                $relatives_opts = array(
                    'options' => array(),
                    'empty' => '- select -',
                    'class' => 'relatives'
                );
                $is_other_selected = false;
                foreach ($relative_options as $rel) {
                    $relatives_opts['options'][$rel->id] = array('data-other' => $rel->is_other ? '1' : '0');
                    if ($rel->id == $values['relative_id'] && $rel->is_other){
                        $is_other_selected = true;
                    }
                }
                echo CHtml::dropDownList($field_prefix . '[relative_id]', $values['relative_id'], CHtml::listData($relative_options, 'id', 'name'), $relatives_opts);
            ?>
            <div class="<?php echo $is_other_selected ? '' : 'hidden';?> other_relative_wrapper">
                <?php echo CHtml::textField($field_prefix . '[other_relative]', ( $is_other_selected ? $values['other_relative'] : ''), array('class' => 'other_relative_text other-type-input', 'autocomplete' => Yii::app()->params['html_autocomplete']))?>
            </div>
        <?php endif;?>

    </td>
    <td>
        <?php if(!$editable): ?>
            <?= $values['side_display'] ?>
        <?php else: ?>
            <?php echo CHtml::dropDownList($field_prefix . '[side_id]', $values['side_id'], CHtml::listData($side_options, 'id', 'name'))?>
        <?php endif;?>

    </td>
    <td>
        <?php if(!$editable): ?>
            <?= $values['condition_display'] ?>
        <?php else: ?>
            <?php
            $conditions_opts = array(
                'options' => array(),
                'empty' => '- select -',
                'class' => 'conditions',
            );
            $is_other_selected = false;
            foreach ($condition_options as $con) {
                $conditions_opts['options'][$con->id] = array('data-other' => $con->is_other ? '1' : '0');
                if ($con->id == $values['condition_id'] && $con->is_other){
                    $is_other_selected = true;
                }
            }
            echo CHtml::dropDownList($field_prefix . '[condition_id]', $values['condition_id'], CHtml::listData($condition_options, 'id', 'name'), $conditions_opts);
            ?>

            <div class="<?php echo $is_other_selected ? '' : 'hidden';?> other_condition_wrapper">
                <br>
                <?php echo CHtml::textField($field_prefix . '[other_condition]', $values['other_condition'], array('class'=>'other_condition_text', 'autocomplete' => Yii::app()->params['html_autocomplete']))?>
            </div>
        <?php endif;?>
    </td>
    <td>
        <?php if(!$editable): ?>
            <?= $values['comments'] ?>
        <?php else: ?>
            <?php echo CHtml::textField($field_prefix . '[comments]', $values['comments'], array('autocomplete' => Yii::app()->params['html_autocomplete']))?>
        <?php endif;?>
    </td>
    <td class="edit-column" <?php if (!$editable) {?>style="display: none;"<?php } ?>>
        <button class="button small warning remove">remove</button>
    </td>
</tr>
