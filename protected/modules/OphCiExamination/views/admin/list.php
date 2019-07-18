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

<div class="cols-4">

    <table class="cols-full standard">
        <title class="box-title"><?php echo $title ? $title : 'Examination Admin' ?></title>
        <colgroup>
            <col class="cols-1">
            <col class="cols-1">
        </colgroup>
        <tbody>
        <tr>
            <td><h2>No Treatment Reasons</h2></td>
            <td>
                <?= CHtml::submitButton(
                    'Add New',
                    [
                        'class' => 'button large',
                        'name' => 'cancel',
                        'data-uri' => Yii::app()->createUrl('OphCiExamination/admin/create'.$model_class),
                        'id' => 'et_cancel',
                    ]
                ); ?>
            </td>
        </tr>
        </tbody>
    </table>

    <table class="standard">
        <thead>
            <tr>
                <th>Name</th>
                <th>Enabled</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($model_list as $i => $model) {
                ?>
                <tr data-attr-id="<?php echo $model->id?>" data-attr-name="No Treatment Reason">
                    <td>
                        <a href="<?php echo Yii::app()->createUrl($this->module->getName().'/admin/update'.Helper::getNSShortname($model), array('id' => $model->id)) ?>"><?php echo $model->name?></a>
                    </td>
                    <td>
                        <input type="checkbox" class="model_enabled" <?php if ($model->active) {
    echo 'checked';
}
                ?> />
                    </td>
                </tr>
            <?php 
            }?>
        </tbody>
    </table>
</div>
