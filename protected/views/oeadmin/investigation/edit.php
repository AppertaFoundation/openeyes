<?php

/**
 * (C) OpenEyes Foundation, 2018
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

?>

<div class="cols-full">

    <h2>Edit Investigation</h2>

    <?= $this->renderPartial('//admin/_form_errors', array('errors' => $errors)) ?>

    <form method="POST">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>
        <table class="standard cols-full">

            <tbody>
            <?php
            $personal_fields = ['name', 'snomed_code', 'snomed_term', 'ecds_code', 'specialty_id'];
            foreach ($personal_fields as $field) : ?>
                <tr>
                    <td class="cols-3"><?php echo $investigation->getAttributeLabel($field); ?></td>
                    <?php if ($field !== 'specialty_id') { ?>
                        <td class="cols-4">
                            <?= \CHtml::activeTextField(
                                $investigation,
                                $field,
                                [
                                    'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                                    'class' => 'cols-full'
                                ]
                            ); ?>
                        </td>
                    <?php } else { ?>
                        <td>
                            <?= \CHtml::activeDropDownList(
                                $investigation,
                                $field,
                                CHtml::listData(Specialty::model()->findAll(array('order' => 'name asc')), 'id', 'name'),
                                array('name' => 'specialty_id_select', 'nolabel' => true, 'empty' => '- Select -', 'class' => 'cols-full')
                            ); ?>
                            <input
                                name="OEModule_OphCiExamination_models_OphCiExamination_Investigation_Codes[specialty_id]"
                                type='hidden' id='specialty_id' value=<?= $investigation->specialty_id ?>>
                        </td>
                    <?php } ?>

                </tr>
            <?php endforeach; ?>

            </tbody>
        </table>

        <h2>Edit Investigation Comments</h2>

        <table class="standard cols-full investigation_comments sortable" id="et_sort"
               data-uri="/oeadmin/investigation/sortComments">
            <tr>
                <th>Display order</th>
                <th>Comments</th>
                <th>Actions</th>
            </tr>
            <tbody>
            <?php foreach ($investigation->investigationComments as $investigationComment) : ?>
                <tr>
                    <td class="reorder">
                        <span>&uarr;&darr;</span>
                        <input type="hidden"
                               name="OEModule_OphCiExamination_models_InvestigationComments[display_order][]"
                               value="<?= $investigationComment->id ?>">
                    </td>
                    <td class="cols-6">
                        <input autocomplete="on" class="cols-6"
                               name="OEModule_OphCiExamination_models_InvestigationComments[comments][]"
                               id="OEModule_OphCiExamination_models_InvestigationComments_comments" type="text"
                               value="<?= $investigationComment->comments; ?>">
                    </td>
                    <td class="cols-6">
                        <button type='button'><a href="#" class="deleteRow">delete</a></button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>

            <tfoot>
            <tr>
                <td colspan="8">
                    <button class='large' type='button'><a href="#" class="addRow">Add</a></button>
                </td>
            </tr>
            <tr>
                <td colspan="8">
                    <?= \CHtml::submitButton(
                        'Save',
                        [
                            'class' => 'button large',
                            'name' => 'save',
                            'id' => 'et_save'
                        ]
                    ); ?>
                    <?= \CHtml::submitButton(
                        'Cancel',
                        [
                            'class' => 'button large',
                            'data-uri' => '/oeadmin/investigation/list',
                            'name' => 'cancel',
                            'id' => 'et_cancel'
                        ]
                    ); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>
<script>
    $("select[name='specialty_id_select']").change(function () {
        $("input[name='OEModule_OphCiExamination_models_OphCiExamination_Investigation_Codes[specialty_id]']").val($(this).val());
    });

    $(".addRow").click((e) => {
        e.preventDefault();

        const trHtml =
            '<tr> ' +
            '<td class="reorder">' +
            '<span>&uarr;&darr;</span>' +
            `<input type="hidden" name="OEModule_OphCiExamination_models_InvestigationComments[display_order][]" value="">` +
            '</td>' +
            '<td class="cols-6"> ' +
            `<input autocomplete="on" class="cols-6" name="OEModule_OphCiExamination_models_InvestigationComments[comments][]" id="OEModule_OphCiExamination_models_InvestigationComments_comments" type="text" value="">` +
            '</td> ' +
            '<td class="cols-6"> ' +
            '<button type="button"><a href="#" class="deleteRow" onclick="deleteRow(this)">delete</a></button> ' +
            '</td> ' +
            '</tr>';

        $('.investigation_comments tbody tr:last').after(trHtml);
    });

    $(".deleteRow").click((e) => {
        e.preventDefault();
        deleteRow(e.currentTarget);
    });

    $('.sortable tbody').sortable();

    function deleteRow(e) {
        $(e).closest('tr').remove();
    }
</script>
