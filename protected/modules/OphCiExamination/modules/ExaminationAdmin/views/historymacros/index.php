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

<h2>Examination History Macros</h2>
<div class="cols-8">
    <form id="admin_historymacros">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>">
        <table class="standard generic-admin sortable" id="et_sort" data-uri = "/OphCiExamination/admin/HistoryMacro/sortHistoryMacros">
            <thead>
            <tr>
                <th><input type="checkbox" name="selectall" id="selectall"/></th>
                <th>Display Order</th>
                <th>Name</th>
                <th>Subspecialties</th>
                <th>Active</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model_list as $i => $model) {
                $subspecialties = "All Subspecialties";
                if (count($model->subspecialties)) {
                    $subspecialties = implode(", ", CHtml::listData($model->subspecialties, 'id', 'name'));
                }
                ?>
                <tr class="clickable" data-id="<?= $model->id ?>"
                    data-uri="OphCiExamination/admin/HistoryMacro/edit/<?= $model->id ?>">
                    <td><input type="checkbox" name="select[]" value="<?= $model->id ?>"/></td>
                    <td class="reorder">
                        <span>&uarr;&darr;</span>
                        <input type="hidden" name="HistoryMacro[display_order][]" value="<?= $model->id ?>">
                    </td>
                    <td>
                        <?= $model->name ?>
                    </td>
                    <td>
                        <?= $subspecialties ?>
                    </td>
                    <td>
                        <i class="oe-i small <?= $model->active ? 'tick' : 'remove' ?>"></i>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5">
                        <?= CHtml::button(
                            'Add',
                            [
                                'data-uri' => '/OphCiExamination/admin/HistoryMacro/create',
                                'class' => 'button large',
                                'id' => 'et_add',
                            ]
                        ); ?>
                        <?= \CHtml::button(
                            'Delete',
                            [
                                'class' => 'button large',
                                'name' => 'delete',
                                'data-object' => 'historymacros',
                                'data-uri' => '/OphCiExamination/admin/HistoryMacro/delete',
                                'id' => 'et_delete'
                            ]
                        ); ?>
                        <?= CHtml::submitButton(
                            'Save',
                            [
                                'class' => 'button large primary event-action',
                                'name' => 'save',
                                'id' => 'et_admin-save',
                                'formmethod' => 'post',
                            ]
                        ); ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </form>
</div>

<script>
    $('.generic-admin.sortable tbody').sortable({
        stop: OpenEyes.Admin.saveSorted
    });
</script>