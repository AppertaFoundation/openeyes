<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
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

<?php $this->renderPartial('//base/_messages'); ?>

<div class="cols-full">
    <table class="standard">
        <thead>
        <tr>
            <th>Name</th>
            <th>Values</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($model_list as $i => $model) {
            ?>
            <tr data-attr-id="<?php echo $model->id?>">
                <td>
                    <a href="<?php echo Yii::app()->createUrl('PASAPI/admin/default/update' . Helper::getNSShortname($model)) . '/' . $model->id ?>"><?php echo $model->name?></a>
                </td>
                <td>
                    <a href="<?php echo Yii::app()->createUrl('PASAPI/admin/default/viewRemapValues') . '/' . $model->id ?>"><?= count($model->values) ?></a>
                </td>
                <td>
                    <a href="<?php echo Yii::app()->createUrl('PASAPI/admin/default/deleteXpathRemap') . '/' . $model->id; ?>">Delete</a>
                </td>
            </tr>
            <?php
        }?>
        </tbody>
        <tfoot class="pagination-container">
        <tr>
            <td colspan="3">
                <a class="button small" href="<?php echo Yii::app()->createUrl('PASAPI/admin/default/create' . $model_class); ?>">Add New</a>
            </td>
        </tr>
        </tfoot>
    </table>
</div>
