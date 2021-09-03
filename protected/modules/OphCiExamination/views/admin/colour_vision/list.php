<?php
/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php $this->renderPartial('//base/_messages')?>

<div class="cols-4">
    <form id="admin_workflows">
        <table class="standard">
            <thead>
            <tr>
                <th><input type="checkbox" name="selectall" id="selectall"/></th>
                <th>Name</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model_list as $i => $model) {
                ?>
                <tr class="clickable" data-id="<?php echo $model->id ?>"
                    data-uri="<?= $edit_link_generator($model); ?>">
                    <td><input type="checkbox" name="methods[]" value="<?php echo $model->id ?>"/></td>
                    <td>
                        <?php echo $model ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot class="pagination-container">
            <tr>
                <td colspan="5">
                    <?=\CHtml::button(
                        'Add',
                        [
                            'class' => 'button large',
                            'id' => 'et_add',
                            'data-uri' => '/OphCiExamination/admin/addColourVisionMethod',
                        ]
                    ); ?>
                    <?=\CHtml::button(
                        'Delete',
                        [
                            'class' => 'button large',
                            'name' => 'delete',
                            'data-object' => 'workflows',
                            'id' => 'et_delete',
                            'data-uri' => '/OphCiExamination/admin/deleteColourVisionMethod',
                        ]
                    ); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>
