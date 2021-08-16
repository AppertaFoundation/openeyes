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

<div>
    <h2><?= $title ?></h2>
</div>

<?php
$form = $this->beginWidget('BaseEventTypeCActiveForm', [
    'id' => 'adminform',
    'enableAjaxValidation' => false,
    'layoutColumns' => [
        'label' => 6,
        'field' => 6,
    ],
]) ?>

<table class="standard">
    <tbody>
    <tr>
        <td>Name</td>
        <td>
            <?= CHtml::activeTextField(
                $model,
                'name',
                [
                    'class' => 'cols-full',
                    'autocomplete' => Yii::app()->params['html_autocomplete'],
                    'disabled' => !Yii::app()->user->checkAccess('admin')
                ]
            ) ?>
        </td>
    </tr>
    <tr>
        <td>Short Name</td>
        <td>
            <?= CHtml::activeTextField(
                $model,
                'short_name',
                [
                    'class' => 'cols-full',
                    'autocomplete' => Yii::app()->params['html_autocomplete'],
                    'disabled' => !Yii::app()->user->checkAccess('admin')
                ]
            ) ?>
        </td>
    </tr>
    <tr>
        <td>Institutions</td>
        <td>
            <?php if ($model->id) {
                echo $form->multiSelectList(
                    $model,
                    \CHtml::modelName($model).'[institutions]',
                    'institutions',
                    'id',
                    Institution::model()->getList(!\Yii::app()->user->checkAccess('admin')),
                    null,
                    ['class' => 'cols-full', 'empty' => '-- Add --', 'nowrapper' => true]);
            } else {
                echo Institution::model()->getCurrent()->name;
            } ?>
        </td>
        </td>
    </tr>
    <tr>
        <td>Active</td>
        <td>
            <?= CHtml::activeCheckBox($model, 'active', ['disabled' => !Yii::app()->user->checkAccess('admin')]) ?>
        </td>
    </tr>
    </tbody>
</table>

<?php $this->endWidget() ?>