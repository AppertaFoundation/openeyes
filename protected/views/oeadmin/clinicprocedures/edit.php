<?php
/**
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="cols-5">
    <h2>Edit Procedure</h2>

    <?= $this->renderPartial('//admin/_form_errors', ['errors' => $errors]) ?>

    <form method="POST">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>
        <table class="standard cols-full">
            <colgroup>
                <col class="cols-3">
                <col class="cols-4">
            </colgroup>
            <tbody>
            <tr>
                <td>Procedure</td>
                <td>
                    <?= $procedure->term ?>
                </td>
            </tr>
            <tr>
                <td>Institution</td>
                <td>
                    <?= CHtml::activeDropDownList(
                        $clinic_procedure,
                        'institution_id',
                        Institution::model()->getList(true),
                        ['class' => 'cols-full', 'empty' => '- All -']
                    ) ?>
                </td>
            </tr>
            <tr>
                <td>Context</td>
                <td class="cols-full">
                    <?= \CHtml::activeDropDownList(
                        $clinic_procedure,
                        'firm_id',
                        CHtml::listData(Firm::model()->activeOrPk($clinic_procedure->firm_id)->findAll(), 'id', 'nameAndSubspecialty'),
                        ['class' => 'cols-full', 'empty' => '- All -']
                    ) ?>
                </td>
            </tr>
            <tr>
                <td>Subspecialty</td>
                <td class="cols-full">
                    <?= \CHtml::activeDropDownList(
                        $clinic_procedure,
                        'subspecialty_id',
                        CHtml::listData(Subspecialty::model()->findAll(), 'id', 'name'),
                        ['class' => 'cols-full', 'empty' => '- All -']
                    ) ?>
                </td>
            </tr>
            </tbody>
            <tr>
                <td colspan="8">
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
                            'class' => 'button large',
                            'data-uri' => '/oeadmin/clinicprocedure/list',
                            'name' => 'cancel',
                            'id' => 'et_cancel'
                        ]
                    ) ?>
                </td>
            </tr>
        </table>
    </form>
</div>