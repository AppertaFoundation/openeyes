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
    <div class="cols-11">
        <table id="<?= $model_name ?>_entry_table" class="cols-full ">
            <colgroup>
                <col class="cols-4">
                <col class="">
                <col class="">
                <col class="">
                <col class="cols-full">
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
        <div id="investigation-other-comments" class="cols-full js-comment-container"
         data-comment-button="#add-investigation-popup .js-add-comments"
         style="display: <?= $element->description ? : 'none'; ?>">
        <!-- comment-group, textarea + icon -->
            <div class="comment-group flex-layout flex-left">
                    <textarea id="<?= $model_name ?>description"
                              name="<?= $model_name ?>[description]"
                              class="js-comment-field cols-10"
                              placeholder="Comments"
                              autocomplete="off" rows="1"
                              style="overflow: hidden; word-wrap: break-word; height: 24px;"><?= CHtml::encode($element->description) ?></textarea>
                <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
            </div>
        </div>
    </div>

    <div class="add-data-actions flex-item-bottom" id="add-investigation-popup">
        <button id="investigation-comment-button" class="button js-add-comments"
            type="button"
            data-comment-container="#investigation-other-comments"
            style="<?= $element->description ? 'display: none;' : '' ?>">
            <i class="oe-i comments small-icon "></i>
        </button>
        <button class="button hint green js-add-select-search" data-adder-trigger="true" id="add-investigation-btn" type="button">
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
                'order' => 't.name'
            ]
        );
                                ?>
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

    let ids = [];

    $(document).ready(function() {
        new OpenEyes.UI.AdderDialog({
            id: 'investigation_popup',
            openButton: $('#add-investigation-btn'),
            showEmptyItemSets: true,
            itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                array_map(function ($investigation) {
                    return ['label' => $investigation->name, 'id' => $investigation->id,];
                }, $investigations)
            )?>, {'header': "Investigations", 'id': "investigation-id", 'multiSelect': true}),
                new OpenEyes.UI.AdderDialog.ItemSet([], {'header': "Comments", 'id': "comment-id", 'multiSelect': true})
            ],
            liClass: 'restrict-width extended',
            popupClass: 'oe-add-select-search',
            onReturn: function(adderDialog, selectedItems) {
                // check if the user has selected a comment or not.
                let selectedInvestigation =
                   selectedItems.filter(selectedItem => {
                       return selectedItem.hasOwnProperty('itemSet');
                   });

                let selectedComments =
                   selectedItems.filter(selectedItem => {
                       return !selectedItem.hasOwnProperty('itemSet');
                   });

                investigationController.addEntry(selectedInvestigation);
                investigationController.showTable();

                if (typeof selectedComments !== 'undefined' && selectedComments.length > 0) {
                    // the selectedComment is defined and has at least one element

                    let comments = "";
                    for (let i = 0; i < selectedComments.length; i++) {
                        comments += selectedComments[i].label;
                        if (i !== selectedComments.length-1) {
                            comments += ', '
                        }
                    }

                    let $rows = $('#OEModule_OphCiExamination_models_Element_OphCiExamination_Investigation_entry_table').find(`input[name*='investigation_code']`).closest('tr');

                    $rows = $rows.slice(-selectedInvestigation.length);

                    $rows.each(function( index ) {
                        const $divCommentContainer = $(this).find('div .js-comment-container');
                        $divCommentContainer.show();
                        const $buttonComment = $(this).find($divCommentContainer.attr('data-comment-button'));
                        $buttonComment.hide();

                        $(this).find('textarea').val(comments);
                    });
                }
                ids = [];
                return true;
            },

        });
        initialiseProcedureAdder();
    });

    function initialiseProcedureAdder() {


        $('.add-options[data-id="investigation-id"]').on('click', 'li', function() {
            let id = $(this).data('id');

            if ($(this).attr('class') === 'selected') {
                ids = ids.filter(function(e) {
                    return e !== id;
                });
            } else {
                ids.push($(this).data('id'));
            }

            updateInvestigationDialog(ids);
        });
    }

    function updateInvestigationDialog(investigation) {
        if (investigation !== '' && investigation !== 'none') {
            $.ajax({
                'url': '<?php echo Yii::app()->createUrl('investigationComment/list') ?>',
                'type': 'POST',
                'data': {
                    'investigation': investigation,
                    'dialog': true,
                    'YII_CSRF_TOKEN': YII_CSRF_TOKEN
                },
                'success': function(data) {
                    $('.add-options[data-id="comment-id"]').each(function() {
                        $(this).html(data).find('li').find('span').removeClass('auto-width').addClass('restrict-width extended');
                        $(this).show();
                    });
                }
            });
        }
    }
</script>


