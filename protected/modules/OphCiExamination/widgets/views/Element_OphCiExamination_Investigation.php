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
<script type="text/javascript" src="<?= $this->getJsPublishedPath('Investigations.js') ?>"></script>

<?php
$model_name = CHtml::modelName($element);
?>

<div class="element-fields full-width ">
    <div class="cols-10">
        <table id="<?= $model_name ?>_entry_table" class="cols-full ">
            <colgroup>
                <col class="cols-3">
                <col class="cols-3">
                <col><!-- auto -->
                <col><!-- auto -->
                <col class="cols-4">
            </colgroup>
            <tbody>
                <?php
                    $row_count = 0;
                foreach ($element->entries as $i => $entry) {
                    $this->render(
                        'application.modules.OphCiExamination.widgets.views.Examination_Investigation_Entry',
                        array(
                            'entry' => $entry,
                            'form' => $form,
                            'id' => $entry->id,
                            'investigation_code' => $entry->investigation_code,
                            'investigation_code_name' => \OEModule\OphCiExamination\models\OphCiExamination_Investigation_Codes::model()->findByPk($entry->investigation_code)->name,
                            'last_modified_user_id' => $entry->last_modified_user_id,
                            'last_modified_user_name' => User::model()->findByPk($entry->last_modified_user_id)->getFullNameAndTitle(),
                            'date' => $entry->date,
                            'time' => $entry->time,
                            'comments' => $entry->comments,
                            'row_count' => $row_count,
                            'field_prefix' => $model_name . '[entries][' . ($row_count) . ']',
                            'model_name' => $model_name
                        )
                    );
                    $row_count++;
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="cols-full comment-group flex-layout flex-left"   id = 'investigation-other-comments' ">
        <?php echo $form->textArea(
            $element,
            'description',
            array('nowrapper' => true),
            false,
            array('class' => 'js-input-comments js-allow-qtags cols-10', 'rows' => 2, 'placeholder' => 'Comments', 'style' => 'overflow: hidden; overflow-wrap: break-word; height: 24px;')
        )
?>
        <i class="cols-2 oe-i remove-circle small-icon pad-left js-remove-add-comments" id="remove-general-investigation-comments"></i>
    </div>
    <div class="add-data-actions flex-item-bottom">
        <button id="investigation-comment-button"
                class="button js-add-comments" data-comment-container="#investigation-comments"
                type="button" style=" display: none;" >
            <i class="oe-i comments small-icon"></i>
        </button>
        <button class="button hint green js-add-select-search"
                id="add-investigation-btn" type="button">
            <i class="oe-i plus pro-theme"></i>
        </button>
    </div>
</div>

<script type="text/template" id="<?= CHtml::modelName($element) . '_entry_template' ?>" style="display:none">
    <?php
    $empty_entry = new \OEModule\OphCiExamination\models\OphCiExamination_Investigation_Entry();
    $this->render(
        'application.modules.OphCiExamination.widgets.views.Examination_Investigation_Entry',
        array(
            'entry' => $empty_entry,
            'form' => $form,
            'row_count' => '{{row_count}}',
            'field_prefix' => $model_name . '[entries][{{row_count}}]',
            'model_name' => $model_name,
            'values' => array(
                'id' => '',
                'investigation_code' => '{{investigation_code}}',
                'investigation_code_name' => '{{investigation_code_name}}',
                'last_modified_user_id' => Yii::app()->user->id,
                'last_modified_user_name' => User::model()->findByPk(Yii::app()->user->id)->getFullNameAndTitle(),
                'date' =>  date("Y-m-d"),
                'time' =>  (new DateTime())->format('H:i'),
                'comments' => null,
            ),

        )
    );
    ?>
</script>

<script type="text/javascript">
    $(function () {
        autosize($('.autosize'));
        $(document).ready(function () {
            investigationController = new OpenEyes.OphCiExamination.InvestigationsController({
                element: $('#<?=$model_name?>_element')
            });

        });
        <?php $investigations = \OEModule\OphCiExamination\models\OphCiExamination_Investigation_Codes::model()->findAll(
            [
                    'select' => 't.name,t.id',
                ]
        );
                                ?>

        new OpenEyes.UI.AdderDialog({
            openButton: $('#add-investigation-btn'),
            itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                array_map(function ($investigation) {
                    return ['label' => $investigation->name, 'id' => $investigation->id];
                }, $investigations)
            )?>, {'multiSelect': true, 'header': "Investigations"})],
            onReturn: function (adderDialog, selectedItems) {
                investigationController.addEntry(selectedItems);
                investigationController.showTable();
                return true;
            }
        });


    });

    // Show General comments
    $('#investigation-comment-button').on('click', function () {
        $('#investigation-comment-button').hide();
        $('#investigation-other-comments').show();
        $('#remove-general-investigation-comments').show();
    });
    // Hide general comments
    $('#remove-general-investigation-comments').on('click', function () {
        $('#investigation-comment-button').show();
        $( '#investigation-other-comments').hide();
        $( '#<?=$model_name?>_description').val('');
        $('#remove-general-investigation-comments').hide();
    });
</script>
