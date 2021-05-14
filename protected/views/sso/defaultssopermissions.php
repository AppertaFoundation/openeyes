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

<div class="cols-7">

    <div class="row divider">
        <h2>Default SSO permissions</h2>
        <div class="alert-box info">Default permissions are only assigned to a user when they are first created via Single Sign-On (SSO).</div>
    </div>

    <?php echo $this->renderPartial('/admin/_form_errors', array('errors' => $errors)) ?>
    <?php
    $form = $this->beginWidget(
        'BaseEventTypeCActiveForm',
        [
            'id' => 'adminform',
            'enableAjaxValidation' => false,
            'layoutColumns' => array(
                'label' => 2,
                'field' => 4,
            ),
        ]
    ) ?>

    <table class="standard cols-full">
        <colgroup>
            <col class="cols-5">
            <col class="cols-8">
        </colgroup>
        <tbody>
        <tr>
            <td>Global firm rights</td>
            <td>
                <?= \CHtml::activeRadioButtonList(
                    $rights,
                    'global_firm_rights',
                    [1 => 'Yes', 0 => 'No'],
                    ['separator' => ' ', 'selected' => '1']
                ); ?>
            </td>
        </tr>
        <tr>
            <td><?=Firm::contextLabel()?></td>
            <td>
                <?php
                $firm_label = [];
                foreach (Firm::model()->findAll() as $firm) {
                    $firm_label[$firm->id] = "{$firm->name} ". ($firm->serviceSubspecialtyAssignment ? "({$firm->serviceSubspecialtyAssignment->subspecialty->name})" : "");
                }
                echo $form->multiSelectList(
                    $rights,
                    'SsoDefaultRights[sso_default_firms]',
                    'sso_default_firms',
                    'firm_id',
                    $firm_label,
                    null,
                    [
                        'class' => 'cols-full',
                        'label' => Firm::contextLabel(),
                        'empty' => '-- Add --',
                        'nowrapper' => true
                    ]
                );
                ?>
            </td>
        </tr>
        <tr>
            <td>Consultant</td>
            <td>
                <?= \CHtml::activeRadioButtonList(
                    $rights,
                    'is_consultant',
                    [1 => 'Yes', 0 => 'No'],
                    ['separator' => ' ']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Surgeon</td>
            <td>
                <?= \CHtml::activeRadioButtonList(
                    $rights,
                    'is_surgeon',
                    [1 => 'Yes', 0 => 'No'],
                    ['separator' => ' ']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Assign default SSO roles to the users?</td>
            <td>
                <?= \CHtml::activeRadioButtonList(
                    $rights,
                    'default_enabled',
                    [1 => 'Yes', 0 => 'No'],
                    ['separator' => ' ', 'selected' => '1']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Roles</td>
            <td>
                <?php echo $form->multiSelectList(
                    $rights,
                    'SsoDefaultRights[sso_default_roles]',
                    'sso_default_roles',
                    'roles',
                    CHtml::listData(
                        Yii::app()->authManager->getRoles(),
                        'name',
                        'name'
                    ),
                    null,
                    ['class' => 'cols-full', 'label' => 'Roles',
                        'empty' => '-- Add --', 'nowrapper' => true]
                ); ?>
            </td>
        </tr>
        </tbody>

        <tfoot>
        <tr>
            <td colspan="5">
                <?= \CHtml::submitButton(
                    'Save',
                    [
                        'class' => 'button large',
                        'name' => 'save',
                        'id' => 'et_save'
                    ]
                ); ?>
            </td>
        </tr>
        </tfoot>
    </table>
</div>

<?php $this->endWidget() ?>