<?php
/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php foreach (Yii::app()->user->getFlashes() as $message) { ?>
<p class="alert-box info" style="margin-bottom: 0px;"><?= $message ?></p>
<?php } ?>
<h2>Manage Subspecialty Subsections</h2>
<div class="cols-5" id="generic-admin-list">
    <form id="admin_subspecialty_sections">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>" />
        <input type="hidden" name="page" value="1" />
        <table>
            <colgroup>
                <col class="cols-6">
                <col class="cols-6">
            </colgroup>
            <tbody>
                <tr>
                    <td><h3>Subspecialty: </h3></td>
                    <td>
                    <?= \CHtml::dropDownList(
                        'subspecialty',
                        $subspecialty_id,
                        CHtml::listData(
                            Subspecialty::model()->findAll(),
                            'id',
                            'name',
                            'subspecialty.name'
                        ),
                        [
                            'id' => 'subspecialty-subsection-select',
                            'empty' => 'Select a subspecialty'
                        ]
                    )?>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php if ($subspecialty_id && !empty($subspecialty_id)) { ?>
        <table class="standard" id="et_sort" data-uri="/oeadmin/SubspecialtySubsections/sortConditions">
            <thead>
                <tr>
                    <th>Select</th>
                    <th>Subspecialty Subsection</th>
                    <th>Display Order</th>
                </tr>
            </thead>
            <tbody class="sortable">
            <?php foreach ($model_list as $key => $model) { ?>
                <tr class="clickable" data-id="<?= $model->id ?>"
                    data-uri="oeadmin/subspecialtySubsections/edit?id=<?= $model->id ?>&subspecialty_id=<?= $subspecialty_id ?>" >
                    <td class="reorder">
                        <span>↑↓</span>
                        <?=\CHtml::hiddenField(CHtml::modelName($model)."[display_order][]", $model->id);?>
                        <?=\CHtml::hiddenField(CHtml::modelName($model)."[id][]", $model->id);?>
                    </td>
                    <td><?= $model->name ?></td>
                    <td><?= $model->display_order ?></td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5">
                        <?=\CHtml::button(
                            'Create',
                            [
                                'class' => 'button large',
                                'type' => 'button',
                                'name' => 'create',
                                'data-uri' => 'create?subspecialty_id='.$subspecialty_id,
                                'id' => 'et_add'
                            ]
                        ); ?>
                    </td>
                </tr>
            </tfoot>
        </table>
        <?php } ?>
    </form>
</div>
<script>
    $('#subspecialty-subsection-select').change( e => {
        window.location.href = 'list?subspecialty_id=' + e.target.value;
    });
</script>
