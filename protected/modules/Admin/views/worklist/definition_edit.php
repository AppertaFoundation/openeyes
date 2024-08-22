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

<div class="admin box">
    <h2><?= $definition->isNewRecord ? 'Create' : 'Edit' ?> Worklist Definition</h2>

    <?php echo $this->renderPartial('//admin/_form_errors', array('errors' => $errors)) ?>

    <div class="alert-box with-icon info" id="edit-definition-warning">
        Any changes to settings will only apply to new appointments. Existing appointments will keep the existing settings. Contact the support desk if you need to update existing appointments.
    </div>
    <?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'worklist-form',
        'enableAjaxValidation' => false,
        'focus' => '#Worklist_name',
        'layoutColumns' => array(
            'label' => 2,
            'field' => 5,
        ),
    )) ?>

    <div class="cols-8">
        <table class="standard cols-full">
            <colgroup>
                <col class="cols-2">
                <col class="cols-8">
            </colgroup>
            <tbody>
            <tr>
                <td>Name</td>
                <td>
                    <?= CHtml::activeTextField(
                        $definition,
                        'name',
                        [
                            'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                            'class' => 'cols-full',
                            'field' => 2
                        ]
                    ) ?>
                </td>
            </tr>
            <tr>
                <td>Description</td>
                <td>
                    <?= CHtml::activeTextArea(
                        $definition,
                        'description',
                        [
                            'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                            'class' => 'cols-full',
                        ]
                    ) ?>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <?php $this->widget('application.widgets.RRuleField', array(
                        'element' => $definition,
                        'field' => 'rrule',
                        'name' => CHtml::modelName($definition) . '[rrule]',
                        'layoutColumns' => [
                            'label' => 2,
                            'field' => 12,
                        ]
                    )) ?>
                </td>
            </tr>
            <tr>
                <td>Start time</td>
                <td>
                    <?= CHtml::activeTextField(
                        $definition,
                        'start_time',
                        [
                            'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                            'class' => 'cols-1',
                            'field' => 1
                        ]
                    ) ?>
                </td>
            </tr>
            <tr>
                <td>End time</td>
                <td>
                    <?= CHtml::activeTextField(
                        $definition,
                        'end_time',
                        [
                            'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                            'class' => 'cols-1',
                            'field' => 1,
                            'append-text' => 6,
                        ]
                    ) ?> &nbsp; Appointments will match on any time <strong>before</strong> the end time specified
                    here.
                </td>
            </tr>
            <tr>
                <td>Patient Identifier Type</td>
                <td>
                    <?= CHtml::activeDropDownList(
                        $definition,
                        'patient_identifier_type_id',
                        CHtml::listData(PatientIdentifierType::model()->findAll(), 'id', 'titleWithInstitution'),
                        [
                            'empty' => 'Select',
                            'class' => 'cols-6',
                        ]
                    ) ?>
                </td>
            </tr>
            <tr>
                <td>Default Clinical Pathway</td>
                <td>
                    <?= CHtml::activeDropDownList(
                        $definition,
                        'pathway_type_id',
                        CHtml::listData(PathwayType::model()->findAll(), 'id', 'name'),
                        [
                            'class' => 'cols-6',
                        ]
                    ) ?>
                </td>
            </tr>
            </tbody>

            <tfoot>
            <tr>
                <td colspan="5">
                    <?= CHtml::submitButton(
                        'Save',
                        [
                            'class' => 'button large',
                            'name' => 'save',
                            'id' => 'et_save'
                        ]
                    ) ?>
                    <?= CHtml::submitButton(
                        'Cancel',
                        [
                            'data-uri' => '/Admin/worklist/definitions',
                            'class' => 'button large',
                            'name' => 'cancel',
                            'id' => 'et_cancel'
                        ]
                    ) ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
    <?php $this->endWidget() ?>
</div>
