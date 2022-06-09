<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<?= CHtml::errorSummary($model, null, null, ['class' => 'alert-box alert with-icon']) ?>
<?php
$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
    'id' => 'adminform',
    'enableAjaxValidation' => false,
    'focus' => '#name',
    'layoutColumns' => array(
        'label' => 2,
        'field' => 4,
    ),
));
?>

<div class="cols-8">
    <table class="standard cols-full">
        <colgroup>
            <col class="cols-3">
            <col class="cols-5">
        </colgroup>
        <tbody>
            <tr>
                <td>Name</td>
                <td class="cols-full">
                    <?= $form->textField(
                        $model,
                        'name',
                        ['class' => 'cols-8', 'nowrapper' => true]
                    ) ?>
                </td>
            </tr>
            <tr>
                <td>Subspecialties</td>
                <td class="cols-4">
                    <?= $form->multiSelectList(
                        $model,
                        CHtml::modelName($model) . '[subspecialties]',
                        'subspecialties',
                        'id',
                        CHtml::listData(Subspecialty::model()->findAll(array('order' => 'name asc')), 'id', 'name'),
                        null,
                        ['empty' => '- Select -', 'class' => 'cols-8', 'nowrapper' => true],
                    ) ?>
                </td>
            </tr>
            <tr>
                <td>Body</td>
                <td colspan="4">
                    <?= \CHtml::activeTextArea($model, 'body', ['class' => 'cols-8 autosize', 'rows' => 21]) ?>
                    <br/>
                    <?= CHtml::button('Insert bullet list', ['class' => 'button large', 'id' => 'et_insert',]) ?>
                </td>
            </tr>
            <tr>
                <td>Active</td>
                <td class="cols-full">
                    <?= $form->checkBox(
                        $model,
                        'active',
                        ['text-align' => 'right', 'nowrapper' => true]
                    ) ?>
                </td>
            </tr>
        </tbody>
    </table>
    <?= OEHtml::submitButton() ?>
    <?= OEHtml::cancelButton("Cancel", [
        'data-uri' => '/OphCiExamination/admin/HistoryMacro/list'
    ]) ?>
</div>
<?php $this->endWidget() ?>

<script type="text/javascript">
    $(document).ready(function () {
        // Insert bullet list in text area
        let insert_btn = document.getElementById("et_insert");
        let text_area = document.getElementById("OEModule_OphCiExamination_models_HistoryMacro_body");
        insert_btn.onclick = function() {
            if (insert_btn.classList.contains('selected')) {
                // Remove the bullet icon
                if (text_area.value.slice(-2) === '● ') {
                    text_area.value = text_area.value.slice(0,-2);
                }
                insert_btn.classList.remove('selected');
            } else {
                // Insert bullets
                insert_btn.classList.add('selected');
                text_area.value += '● ';
            }
            $(text_area).focus();
        }
        // To clicking button for each bullet, keep inserting until disabled
        text_area.addEventListener("keyup", function (e){
            if ($(insert_btn).hasClass('selected')) {
                if (e.key === 'Enter') {
                    text_area.value += '● ';
                }
                // Press backspace on a bullet list to disable it
                if (e.key === 'Backspace' && text_area.value.slice(-1) === '●') {
                    text_area.value = text_area.value.slice(0,-1);
                    insert_btn.classList.remove('selected');
                }
            }
        });
    });
</script>
