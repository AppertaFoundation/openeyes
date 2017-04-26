<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

?>

<?php
Yii::app()->clientScript->registerScriptFile($this->getJsPublishedPath('FamilyHistory.js'), CClientScript::POS_HEAD);
$model_name = CHtml::modelName($element);
?>
<input type="hidden" name="<?= $model_name ?>[present]" value="1" />
<div class="element-fields">
    <div class="field-row row<?= count($element->entries) ? ' hidden' : ''?>" id="<?=$model_name?>_no_family_history_wrapper">
        <div class="large-3 column">
            <label for="<?=$model_name?>_no_family_history">Confirm patient has no family history:</label>
        </div>
        <div class="large-2 column end">
            <?php echo CHtml::checkBox($model_name .'[no_family_history]', $element->no_family_history_date ? true : false); ?>
        </div>
    </div>

    <div class="<?= $element->no_family_history_date ? 'hidden' :''?>" id="<?=$model_name?>_form_wrapper">
        <div class="field-row row">
            <div class="large-2 column">
                <label for="<?=$model_name?>_relative_id">Relative:</label>
            </div>
            <div class="large-3 column end">
                <?php
                $relatives = FamilyHistoryRelative::model()->findAll(array('order' => 'display_order'));
                $relatives_opts = array(
                    'options' => array(),
                    'empty' => '- select -',
                );
                foreach ($relatives as $rel) {
                    $relatives_opts['options'][$rel->id] = array('data-other' => $rel->is_other ? '1' : '0');
                }
                echo CHtml::dropDownList($model_name . '_relative_id', '', CHtml::listData($relatives, 'id', 'name'), $relatives_opts)
                ?>
            </div>
        </div>

        <div class="field-row row hidden" id="<?= $model_name ?>_other_relative_wrapper">
            <div class="large-2 column">
                <label for="<?=$model_name?>_other_relative">Other Relative:</label>
            </div>
            <div class="large-3 column end">
                <?php echo CHtml::textField($model_name . '_other_relative', '', array('autocomplete' => Yii::app()->params['html_autocomplete']))?>
            </div>
        </div>

        <div class="field-row row">
            <div class="large-2 column">
                <label for="<?=$model_name?>side_id">Side:</label>
            </div>
            <div class="large-3 column end">
                <?php echo CHtml::dropDownList($model_name . '_side_id', '', CHtml::listData(FamilyHistorySide::model()->findAll(array('order' => 'display_order')), 'id', 'name'))?>
            </div>
        </div>

        <div class="field-row row">
            <div class="large-2 column">
                <label for="<?= $model_name ?>_condition_id">Condition:</label>
            </div>
            <div class="large-3 column end">
                <?php
                $conditions = FamilyHistoryCondition::model()->findAll(array('order' => 'display_order'));
                $conditions_opts = array(
                    'options' => array(),
                    'empty' => '- select -',
                );
                foreach ($conditions as $con) {
                    $conditions_opts['options'][$con->id] = array('data-other' => $con->is_other ? '1' : '0');
                }
                echo CHtml::dropDownList($model_name . '_condition_id', '', CHtml::listData($conditions, 'id', 'name'), $conditions_opts);
                ?>
            </div>
        </div>

        <div class="field-row row hidden" id="<?= $model_name ?>_other_condition_wrapper">
            <div class="large-2 column">
                <label for="<?= $model_name ?>_other_condition">Other Condition:</label>
            </div>
            <div class="large-3 column end">
                <?php echo CHtml::textField($model_name . '_other_condition', '', array('autocomplete' => Yii::app()->params['html_autocomplete']))?>
            </div>
        </div>

        <div class="field-row row">
            <div class="large-2 column">
                <label for="<?= $model_name ?>_comments">Comments:</label>
            </div>
            <div class="large-3 column">
                <?php echo CHtml::textField($model_name . '_comments', '', array('autocomplete' => Yii::app()->params['html_autocomplete']))?>
            </div>
            <div class="large-4 column end">
                <button class="button small primary" id="<?= $model_name ?>_add_entry">Add</button>
            </div>
        </div>


        <table id="<?= $model_name ?>_entry_table">
            <thead>
            <tr>
                <th>Relative</th>
                <th>Side</th>
                <th>Condition</th>
                <th>Comments</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($element->entries as $entry) {
                $this->render(
                    'FamilyHistory_Entry_event_edit',
                    array(
                        'entry' => $entry,
                        'form' => $form,
                        'model_name' => CHtml::modelName($element),
                    )
                );
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<script type="text/template" id="<?= CHtml::modelName($element).'_entry_template' ?>" class="hidden">
    <?php
    $empty_entry = new \OEModule\OphCiExamination\models\FamilyHistory_Entry();
    $this->render(
        'FamilyHistory_Entry_event_edit',
        array(
            'entry' => $empty_entry,
            'form' => $form,
            'model_name' => CHtml::modelName($element),
            'values' => array(
                'id' => '',
                'relative_id' => '{{relative_id}}',
                'relative_display' => '{{relative_display}}',
                'other_relative' => '{{other_relative}}',
                'side_id' => '{{side_id}}',
                'side_display' => '{{side_display}}',
                'condition_id' => '{{condition_id}}',
                'condition_display' => '{{condition_display}}',
                'other_condition' => '{{other_condition}}',
                'comments' => '{{comments}}',
            )
        )
    );
    ?>
</script>
