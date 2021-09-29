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

<div class="cols-full">
    <form id="admin_iopinstruments">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>
        <table class="standard generic-admin sortable">
            <thead>
            <tr>
                <th>Display Order</th>
                <th>Name</th>
                <th>Short Name</th>
                <th>Institution</th>
                <th>Active</th>
                <th>Visible</th>
            </tr>
            </thead>
            <colgroup>
                <col class="cols-1">
                <col class="cols-3">
                <col class="cols-2">
                <col class="cols-4">
                <col class="cols-1">
            </colgroup>
            <tbody>
            <?php foreach ($model_list as $i => $model) { ?>
                <tr class="clickable" data-id="<?= $model->id ?>"
                    data-uri="OphCiExamination/admin/editIOPInstrument/<?= $model->id ?>">
                    <td class="reorder">
                        <span>↑↓</span>
                        <?= CHtml::activeHiddenField($model, "[$i]display_order"); ?>
                        <?= CHtml::activeHiddenField($model, "[$i]id"); ?>
                    </td>
                    <td><?= $model->name ?></td>
                    <td><?= $model->short_name ?></td>
                    <td>
                        <?php $institutions = CHtml::listData($model->institutions, 'id', 'name');
                        echo $institutions ? CHtml::encode(implode(', ', $institutions)) : 'N/A'; ?>
                    </td>
                    <td><i class="oe-i <?= ($model->active ? 'tick' : 'remove'); ?> small"></i></td>
                    <td><i class="oe-i <?= ($model->visible ? 'tick' : 'remove'); ?> small"></i></td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="5">
                    <?= CHtml::button(
                        'Add',
                        [
                            'class' => 'button large',
                            'id' => 'et_add',
                            'data-uri' => '/OphCiExamination/admin/addIOPInstrument',
                        ]
                    ) ?>
                    <?= CHtml::submitButton(
                        'Save',
                        [
                            'class' => 'button large',
                            'id' => 'et_admin-save',
                            'formmethod' => 'post',
                        ]
                    ) ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>