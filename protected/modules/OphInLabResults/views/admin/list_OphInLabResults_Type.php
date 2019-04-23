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

    </div>
    <form id="admin_list_disorders">
        <table class="standard cols-full">
            <colgroup>
                <col class="cols-1">
                <col class="cols-1">
                <col class="cols-3">
                <col class="cols-3">
                <col class="cols-2">
            </colgroup>
            <thead>
            <tr>
                <th><input type="checkbox" name="selectall" id="selectall"/></th>
                <th>Type</th>
                <th>Element Id</th>
                <th>Field Type</th>
                <th>Default Units</th>
                <th>Custom Warning Message</th>
                <th>Show on Whiteboard</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model_list as $i => $model) { ?>
                <tr class="clickable" data-id="<?php echo $model->id ?>"
                    data-uri="OphInLabResults/oeadmin/resultType/edit/<?php echo $model->id ?>">
                    <td><input type="checkbox" name="resultTypes[]" value="<?php echo $model->id ?>"/></td>
                    <td><?= $model->type; ?></td>
                    <td><?= $model->result_element_type->name ?></td>
                    <td><?= $model->fieldType->name ?></td>
                    <td><?= $model->default_units ?></td>
                    <td><?= $model->custom_warning_message ?></td>
                    <td><?= $model->show_on_whiteboard ?></td>
                </tr>
            <?php } ?>
            </tbody>
            <tr>
                <td colspan="2">
                    <?= \CHtml::button(
                        'Add',
                        [
                            'class' => 'button large',
                            'type' => 'button',
                            'name' => 'add',
                            'data-uri' => 'add',
                            'id' => 'et_add'
                        ]
                    ); ?>
                    <?= \CHtml::submitButton(
                        'Delete',
                        [
                            'class' => 'button large',
                            'name' => 'delete_result_type',
                            'id' => 'et_delete_result_type'
                        ]
                    ); ?>
                </td>
            </tr>
        </table>
    </form>
</div>