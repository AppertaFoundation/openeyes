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
Yii::app()->clientScript->registerScriptFile($this->getJsPublishedPath('PastSurgery.js'), CClientScript::POS_HEAD);
$model_name = CHtml::modelName($element);
?>

<div class="element-fields">

    <?php /*
    <div class="field-row row">
        <div class="large-2 column"><label for="<?= $model_name ?>_common_previous_operation">Common Operation:</label></div>
        <div class="large-3 column end">
            <?php echo CHtml::dropDownList($model_name . '_common_previous_operation', '',
                CHtml::listData(CommonPreviousOperation::model()->findAll(
                    array('order' => 'display_order asc')), 'id', 'name'),
                array('empty' => '- Select -'))?>
        </div>
    </div>
    <div class="field-row row">
        <div class="large-2 column"><label for="<?= $model_name ?>_previous_operation">Operation:</label></div>
        <div class="large-3 column end"><?php echo CHtml::textField($model_name . '_previous_operation', '', array('autocomplete' => Yii::app()->params['html_autocomplete']))?></div>
    </div>
    <div class="field-row row">
        <div class="large-2 column"><label for="<?= $model_name ?>_previous_operation_side">Side:</label></div>
        <div class="large-3 column end">
            <label class="inline"><input type="radio" name="<?= $model_name ?>_previous_operation_side" class="<?= $model_name ?>_previous_operation_side" value="" checked="checked" /> None </label>
            <?php foreach (Eye::model()->findAll(array('order' => 'display_order')) as $eye) {?>
                <label class="inline"><input type="radio" name="<?= $model_name ?>_previous_operation_side" class="<?= $model_name ?>_previous_operation_side" value="<?php echo $eye->id?>" /> <?php echo $eye->name?>	</label>
            <?php }?>
        </div>
    </div>
    <div class="row">
        <div class="large-8 column">
            <?php $this->render('application.views.patient._fuzzy_date', array('class' => $model_name . '_previousOperation')) ?>
        </div>
        <div class="large-4 column end">
            <button class="button small primary" id="<?= $model_name ?>_add_previous_operation">Add</button>
        </div>
    </div>
 */ ?>

  <input type="hidden" name="<?= $model_name ?>[present]" value="1" />
    <table id="<?= $model_name ?>_operation_table" class="<?= $model_name ?>_Operation">
        <thead>
        <tr>
            <th>Operation</th>
            <th>Side</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($operations as $op) {
            if (!array_key_exists('object', $op)) { ?>
                <tr>
                    <td><?= $op['operation'] ?></td>
                    <td><?= $op['side'] ?></td>
                    <td><?= Helper::formatFuzzyDate($op['date']) ?></td>
                    <td>read only <span class="has-tooltip fa fa-info-circle" data-tooltip-content="This operation is recorded as an Operation Note event in OpenEyes and cannot be edited here"></span></td>
                </tr>
            <?php }
        }
        foreach ($element->operations as $op) {
            $this->render(
                'PastSurgery_Operation_event_edit',
                array(
                    'op' => $op,
                    'form' => $form,
                    'model_name' => CHtml::modelName($element),
                )
            );
        }
        ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="3"></td>
            <td class="text-right"><button class="button small primary" id="<?= $model_name ?>_add_entry">Add New</button></td>
        </tr>
        </tfoot>
    </table>
</div>

<script type="text/template" id="<?= CHtml::modelName($element).'_operation_template' ?>" class="hidden">
    <?php
    $empty_operation = new \OEModule\OphCiExamination\models\PastSurgery_Operation();
    $this->render(
        'PastSurgery_OperationEntry_event_edit',
        array(
            'op' => $empty_operation,
            'form' => $form,
            'model_name' => CHtml::modelName($element),
            'removable' => true,
            'values' => array(
                'id' => '',
                'previous_operation_id' => '',
                'operation' => '{{operation}}',
                'side_id' => '{{side_id}}',
                'side_display' => '{{side_display}}',
                'date' => '{{date}}',
                'date_display' => '{{date_display}}'
            )
        )
    );
    ?>
</script>
