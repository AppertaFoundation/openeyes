<?php
/**
 * (C) Copyright Apperta Foundation 2020
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

<div class="row divider">
    <h2><?= $title ?></h2>
</div>

<?= $this->renderPartial('//admin/_form_errors', array('errors' => $model->getErrors())) ?>

<div class="cols-5">
    <form method="POST">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>
        <table class="standard cols-full">
            <colgroup>
                <col class="cols-4">
                <col class="cols-5">
            </colgroup>
            <tbody>
            <?php $htmlOptions = ['class' => 'cols-full', 'autocomplete' => Yii::app()->params['html_autocomplete']]; ?>
            <tr>
                <td><?= $model->getAttributeLabel("request_type"); ?></td>
                <td><?= \CHtml::activeTextField($model, "request_type", array_merge([
                        'readonly' => isset($model->request_type) && (bool)Request::model()->count('request_type = ?', [$model->request_type]),
                    ], $htmlOptions)); ?></td>
            </tr>
            <?php foreach (['title_full', 'title_short'] as $field) : ?>
                <tr>
                    <td><?= $model->getAttributeLabel($field); ?></td>
                    <td><?= \CHtml::activeTextField($model, $field, $htmlOptions); ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td><?= $model->getAttributeLabel("default_routine_name"); ?></td>
                <td><?= \CHtml::activeDropDownList(
                    $model,
                    'default_routine_name',
                    \CHtml::listData(RoutineLibrary::model()->findAll(), 'routine_name', 'routine_name'),
                    ['empty' => "-- select --"]
                ); ?></td>
            </tr>
            <tr>
                <td><?= $model->getAttributeLabel("default_request_queue"); ?></td>
                <td><?= \CHtml::activeDropDownList(
                    $model,
                    'default_request_queue',
                    \CHtml::listData(RequestQueue::model()->findAll(), 'request_queue', 'request_queue'),
                    ['empty' => "-- select --"]
                ); ?></td>
            </tr>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="5">
                    <?= \OEHtml::submitButton('Save'); ?>
                    <?= \OEHtml::cancelButton('Cancel', ['data-uri' => '/Api/Request/admin/' . $this->id . '/index',]); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>
