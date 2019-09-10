<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="row divider">
    <h2><?= $model->isNewRecord ? 'Create' : 'Edit'; ?> Medication</h2>
</div>

<form id="prescription-admin-medication-form" action="/OphDrPrescription/admin/Medication/edit/<?=$model->id?>" method="post">
    <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>
    <table class="standard">
        <tbody>
            <tr>
                <td>
                    <div class="data-group flex-layout cols-full">
                        <div class="cols-2"><label>Preferred Term</label></div>
                        <div class="cols-5">
                            <?=CHtml::activeTextField($model, 'preferred_term', [
                                'class' => 'cols-full',
                            ])?>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="data-group flex-layout cols-full">
                        <div class="cols-2"><label>Short Term</label></div>
                        <div class="cols-5">
                            <?=CHtml::activeTextField($model, 'short_term', [
                                'class' => 'cols-full',
                            ])?>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="data-group flex-layout cols-full">
                        <div class="cols-2"><label>Preferred Code</label></div>
                        <div class="cols-5">
                            <?=CHtml::activeTextField($model, 'preferred_code', [
                                'class' => 'cols-full',
                            ])?>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="data-group flex-layout cols-full">
                        <div class="cols-2"><label>Source Type</label></div>
                        <div class="cols-5">
                            <?=CHtml::activeDropDownList($model, 'source_type',
                            CHtml::listData(
                                Medication::model()->findAll(),
                                'source_type',
                                'source_type'
                            ),
                            [
                                'class' => 'cols-full',
                                'default' => 'local',
                                'disabled' => 'disabled'
                            ])?>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="data-group flex-layout cols-full">
                        <div class="cols-2"><label>Source Subtype</label></div>
                        <div class="cols-5">
                            <?=CHtml::activeDropDownList($model, 'source_subtype',
                            CHtml::listData(
                                Medication::model()->findAll(),
                                'source_subtype',
                                'source_subtype'
                            ),
                            [
                                'class' => 'cols-full',
                                'empty' => '-- None --',
                                'disabled' => 'disabled'
                            ])?>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="data-group flex-layout cols-full">
                        <div class="cols-2"><label>VTM Term</label></div>
                        <div class="cols-5">
                            <?=CHtml::activeTextField($model, 'vtm_term', [
                                'class' => 'cols-full',
                            ])?>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="data-group flex-layout cols-full">
                        <div class="cols-2"><label>VTM Code</label></div>
                        <div class="cols-5">
                            <?=CHtml::activeTextField($model, 'vtm_code', [
                                'class' => 'cols-full',
                            ])?>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="data-group flex-layout cols-full">
                        <div class="cols-2"><label>VMP Term</label></div>
                        <div class="cols-5">
                            <?=CHtml::activeTextField($model, 'vmp_term', [
                                'class' => 'cols-full',
                            ])?>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="data-group flex-layout cols-full">
                        <div class="cols-2"><label>VMP Code</label></div>
                        <div class="cols-5">
                            <?=CHtml::activeTextField($model, 'vmp_code', [
                                'class' => 'cols-full',
                            ])?>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="data-group flex-layout cols-full">
                        <div class="cols-2"><label>AMP Term</label></div>
                        <div class="cols-5">
                            <?=CHtml::activeTextField($model, 'amp_term', [
                                'class' => 'cols-full',
                            ])?>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="data-group flex-layout cols-full">
                        <div class="cols-2"><label>AMP Code</label></div>
                        <div class="cols-5">
                            <?=CHtml::activeTextField($model, 'amp_code', [
                                'class' => 'cols-full',
                            ])?>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="data-group flex-layout cols-full">
                        <div class="cols-2"><label>Default Form</label></div>
                        <div class="cols-5">
                            <?=CHtml::activeDropDownList($model, 'default_form_id',
                                $data = CHtml::listData(MedicationForm::model()->findAll(), 'id', 'term'),
                            [
                                'class' => 'cols-full',
                                'empty' => '-- None --'
                            ])?>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="data-group flex-layout cols-full">
                        <div class="cols-2"><label>Default Route</label></div>
                        <div class="cols-5">
                            <?=CHtml::activeDropDownList($model, 'default_route_id',
                                $data = CHtml::listData(MedicationRoute::model()->findAll(), 'id', 'term'),
                            [
                                'class' => 'cols-full',
                                'empty' => '-- None --'
                            ])?>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="data-group flex-layout cols-full">
                        <div class="cols-2"><label>Default Dose Unit</label></div>
                        <div class="cols-5">
                            <?=CHtml::activeTextField($model, 'default_dose_unit_term', [
                                'class' => 'cols-full',
                            ])?>
                        </div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <?= $this->renderPartial('edit_attributes', ['medication' => $model]); ?>
    <?= $this->renderPartial('edit_sets', ['medication' => $model]); ?>
    <?= $this->renderPartial('edit_alternative_terms', ['medication' => $model]); ?>
    <?=\CHtml::submitButton(
        'Save',
        [
            'class' => 'button large green hint',
            'name' => 'save',
            'id' => 'et_save'
        ])
?>
    <?=\CHtml::submitButton(
        'Cancel',
        [
            'class' => 'button large green hint',
            'data-uri' => '/OphDrPrescription/admin/Medication/index',
            'name' => 'cancel',
            'id' => 'et_cancel'
        ])
?>
</form>
