<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */


use OEModule\OphCiExamination\models\SystemicSurgery_Operation;

?>
<script type="text/javascript" src="<?= $this->getJsPublishedPath('SystemicSurgery.js') ?>"></script>
<script type="text/javascript" src="<?= $this->getPublishedPath('../widgets/js', 'EyeSelector.js', true) ?>"></script>
<?php $model_name = CHtml::modelName($element); ?>
<div class="element-fields flex-layout full-width" id="<?= $model_name ?>_form">
    <div class="data-group cols-10">
        <input type="hidden" name="<?= $model_name ?>[present]" value="1" />
        <div class="cols-5 align-left <?= $model_name ?>_no_systemicsurgery_wrapper">
            <label class="inline highlight" for="<?= $model_name ?>_no_systemicsurgery">
                <?= \CHtml::checkBox(
                    $model_name . '[no_systemicsurgery]',
                    $element->no_systemicsurgery_date ? true : false,
                    array('class' => $model_name . '_no_systemicsurgery')
                ); ?>
                No previous systemic surgery
            </label>
        </div>
        <table id="<?= $model_name ?>_operation_table" class="cols-full <?= $model_name ?>_Operation">
            <colgroup>
                <col class="cols-3">
                <col class="cols-3">
                <col class="cols-1">
                <col class="cols-1">
                <col class="cols-1">
            </colgroup>
            <tbody>
                <?php
                $row_count = 0;
                // these are the missing but required to collect operations
                foreach ($this->getMissingRequiredOperations() as $operation) {
                    $this->render(
                        'SystemicSurgery_OperationEntry_event_edit',
                        [
                            'operation' => $operation,
                            'form' => $form,
                            'row_count' => ($row_count),
                            'field_prefix' => $model_name . '[operation][' . ($row_count) . ']',
                            'model_name' => CHtml::modelName($element),
                            //hack here: removable set to true as we need to edit the fields,
                            // 'required' introduced as we need to hide the remove btn.
                            'removable' => true,
                            'required' => true,
                            'posted_not_checked' => $element->widget->postedNotChecked($row_count)
                        ]
                    );
                    $row_count++;
                }
                //$operations : operations that have been recorded as entries in this element
                foreach ($this->getCurrentAndRequiredOperations() as $operation) {
                    $this->render(
                        'SystemicSurgery_OperationEntry_event_edit',
                        [
                            'operation' => $operation['op'],
                            'form' => $form,
                            'row_count' => ($row_count),
                            'field_prefix' => $model_name . '[operation][' . ($row_count) . ']',
                            'model_name' => CHtml::modelName($element),
                            'removable' => true,
                            'read_only' => true,
                            //hack here: removable set to true as we need to edit the fields,
                            // 'required' introduced as we need to hide the remove btn.
                            'required' => $operation['required'],
                            'posted_not_checked' => $element->widget->postedNotChecked($row_count)
                        ]
                    );
                    $row_count++;
                }
                ?>
            </tbody>
        </table>
        <div id="<?= $model_name ?>-comments" class="field-row-pad-top comment-group js-comment-container flex-layout flex-left" style="<?= $element->comments ? '' : 'display: none;' ?>" data-comment-button="#<?= $model_name ?>-comment-button">
            <br />
            <?php echo $form->textArea(
                $element,
                'comments',
                ['nowrapper' => true],
                false,
                [
                    'class' => 'autosize js-comment-field',
                    'placeholder' => $element->getAttributeLabel('comments'),
                ]
            )
                    ?>
            <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
        </div>
    </div>

    <div class="add-data-actions flex-item-bottom" id="add-to-systemic-past-surgery" style="display: <?php echo $element->no_systemicsurgery_date ? 'none' : ''; ?>">
        <button id="<?= $model_name ?>-comment-button" class="button js-add-comments" data-comment-container="#<?= $model_name ?>-comments" style="<?php if ($element->comments) :
            ?>visibility: hidden;<?php
                    endif; ?>" type="button">
            <i class="oe-i comments small-icon"></i>
        </button>
        <button id="show-add-popup-systemic" class="button hint green js-add-select-search" type="button">
            <i class="oe-i plus pro-theme"></i>
        </button>
    </div>
    <script type="text/template" id="<?= CHtml::modelName($element) . '_operation_template' ?>" class="hidden">
        <?php
        $empty_operation = new SystemicSurgery_Operation();
        $this->render(
            'SystemicSurgery_OperationEntry_event_edit',
            [
                'operation' => $empty_operation,
                'form' => $form,
                'model_name' => CHtml::modelName($element),
                'row_count' => '{{row_count}}',
                'field_prefix' => $model_name . '[operation][{{row_count}}]',
                'removable' => true,
                'values' => [
                    'id' => '',
                    'previous_operation_id' => '',
                    'operation' => '{{operation}}',
                    'side_id' => (string) EyeSelector::$NOT_CHECKED,
                    'side_display' => '{{side_display}}',
                    'date' => '{{date}}',
                    'date_display' => '{{date_display}}',
                    'had_operation' => (string)SystemicSurgery_Operation::$PRESENT,
                ],
                'posted_not_checked' => false,
            ]
        );
        ?>
    </script>
    <script type="text/javascript">
        $(function() {
            let controller;
            $(document).ready(function() {
                controller = new OpenEyes.OphCiExamination.SystemicPreviousSurgeryController();

                <?php $op_list = $this->getPreviousOperationOptions(); ?>

                var item_list = <?= CJSON::encode(
                                    array_map(function ($op_item) {
                                        return ['label' => $op_item->name, 'id' => $op_item->id];
                                    }, $op_list)
                                ) ?>;
                item_list.push({
                    'label': 'Other',
                    'id': ''
                });
                new OpenEyes.UI.AdderDialog({
                    openButton: $('#show-add-popup-systemic'),
                    itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(item_list, {
                        'multiSelect': true
                    })],
                    onReturn: function(adderDialog, selectedItems) {
                        controller.addEntry(selectedItems);
                        return true;
                    }
                });
            });
        });
    </script>