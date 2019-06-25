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

<?php $this->renderPartial('//base/_messages') ?>

<div class="cols-12">
    <div class="row divider">
        <form id="context-search-form" action="#" method="post">
            <table class="standard">
                <colgroup>
                    <col class="cols-4">
                    <col class="cols-1">
                    <col class="cols-7">

                </colgroup>
                <tr>
                    <td><?= CHtml::textField('search[query]', $search['query'], [
                            'placeholder' => 'Search Term , Procedure Id - (all are case sensitive)',
                            'class' => 'cols-full',
                        ]); ?>
                    </td>
                    <td>
                        <input type="hidden" name="YII_CSRF_TOKEN"
                               value="<?= Yii::app()->request->csrfToken ?>"/>
                        <button class="blue hint" id="search-button" formmethod="post" type="submit">Search</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <form id="admin_list_generic_operation_data">
        <table class="standard">
            <thead>
            <tr>
                <th><input type="checkbox" name="selectall" id="selectall"/></th>
                <th>Procedure Term</th>
                <th>Default Text</th>
                <th>Edit</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model_list as $i => $model) { ?>
                <tr class="clickable" data-id="<?php echo $model->id ?>"
                    data-uri="OphTrOperationnote/GenericProcedureData/edit/<?php echo $model->id ?>">
                    <td><input type="checkbox" name="genericProcedures[]" value="<?php echo $model->id ?>"/></td>
                    <td><?php echo $model->procedure->term ?></td>
                    <td style="overflow-wrap: break-word;"><?= $model->default_text ?></td>
                    <td>
                        <?= \CHtml::link(
                            'Edit',
                            '/OphTrOperationnote/GenericProcedureData/edit/' . $model->id,
                            ['class' => 'small event-action']
                        ) ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot class="pagination-container">
            <tr>
                <td colspan="5">
                    <?= \CHtml::button(
                        'Add',
                        [
                            'class' => 'button large',
                            'type' => 'button',
                            'name' => 'add',
                            'data-uri' => '/OphTrOperationnote/GenericProcedureData/add',
                            'id' => 'et_add'
                        ]
                    ); ?>
                    <?= \CHtml::submitButton(
                        'Delete',
                        [
                            'class' => 'button large',
                            'name' => 'delete_operator',
                            'data-object' => 'users',
                            'id' => 'et_delete_generic_procedure_data'
                        ]
                    ); ?>
                </td>
                <td colspan="4">
                    <?php $this->widget('LinkPager', ['pages' => $pagination]); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>
