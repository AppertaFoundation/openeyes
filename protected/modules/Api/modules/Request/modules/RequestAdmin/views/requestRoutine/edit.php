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

<?= $this->renderPartial('//admin/_form_errors', array('errors' => isset($errors) ? $errors : null)); ?>
<?php
$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
    'id' => 'adminform',
    'enableAjaxValidation' => false,
    'htmlOptions' => array(
        'enctype' => 'multipart/form-data',
    ),
    'layoutColumns' => array(
        'label' => 2,
        'field' => 5,
    ),
)) ?>

<?php echo $form->errorSummary($model) ?>


    <div class="cols-full">
        <div class="row divider">
            <h2><?php echo $title ?></h2>
        </div>

        <table class="standard cols-full">
            <colgroup>
                <col class="cols-3">
                <col class="cols-full">
            </colgroup>
            <tbody>
            <tr>
                <td>Id</td>
                <td class="cols-full">
                    <?= \CHtml::activeTextArea(
                        $model,
                        'id',
                        ['class' => 'cols-full']
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>Execute Request Queue</td>
                <td class="cols-full">
                    <?= \CHtml::activeTextArea(
                        $model,
                        'execute_request_queue',
                        ['class' => 'cols-full']
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>Status</td>
                <td>
                    <?= \CHtml::activeDropDownList(
                        $model,
                        'status',
                        ['COMPLETE' => 'COMPLETE', 'NEW' => 'NEW', 'VOID' => 'VOID', 'RETRY' => 'RETRY', 'FAILED' => 'FAILED'],
                        ['class' => 'cols-full']
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>Routine name</td>
                <td>
                    <?= \CHtml::activeDropDownList(
                        $model,
                        'routine_name',
                        CHtml::listData(RoutineLibrary::model()->findAll(), 'routine_name', 'routine_name'),
                        ['class' => 'cols-full autosize',
                            'style' => 'overflow: hidden; ']
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>Try Count</td>
                <td>
                    <?= \CHtml::activeNumberField(
                        $model,
                        'try_count',
                        ['class' => 'cols-full']
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>Next Try Date Time</td>
                <td>
                    <?= \CHtml::activeDateTimeField(
                        $model,
                        'next_try_date_time',
                        ['class' => 'cols-full']
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>Execute Sequence</td>
                <td>
                    <?= \CHtml::activeNumberField(
                        $model,
                        'execute_sequence',
                        ['class' => 'cols-full']
                    ); ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

<?php echo $form->formActions(array('cancel-uri' => isset($cancel_uri) ? $cancel_uri : "")) ?>

<?php $this->endWidget() ?>
