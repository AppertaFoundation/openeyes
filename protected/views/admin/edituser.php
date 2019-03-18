<?php
/**
 * (C) OpenEyes Foundation, 2018
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

<div class="cols-7">

    <div class="row divider">
        <h2><?php echo $user->id ? 'Edit' : 'Add' ?> user</h2>
    </div>

    <?php echo $this->renderPartial('_form_errors', array('errors' => $errors)) ?>
    <?php
    $form = $this->beginWidget(
        'BaseEventTypeCActiveForm',
        [
            'id' => 'adminform',
            'enableAjaxValidation' => false,
            'focus' => '#username',
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

        <?php
        $personal_fields = ['username', 'title', 'first_name', 'last_name', 'email', 'qualifications', 'role'];
        foreach ($personal_fields as $field) : ?>
            <tr>
                <td><?php echo $user->getAttributeLabel($field); ?></td>
                <td>
                    <?= \CHtml::activeTextField(
                        $user,
                        $field,
                        [
                            'autocomplete' => Yii::app()->params['html_autocomplete'],
                            'class' => 'cols-full'
                        ]
                    ); ?>
                </td>
            </tr>
        <?php endforeach; ?>

        <tr>
            <td>Grade</td>
            <td>
                <?= \CHtml::activeDropDownList(
                    $user,
                    'doctor_grade_id',
                    CHtml::listData(
                        DoctorGrade::model()->findAll(
                            array('order' => 'display_order')
                        ),
                        'id',
                        'grade'
                    ),
                    ['class' => 'cols-full', 'empty' => '- Select Grade -']
                ); ?></td>
        </tr>
        <tr>
            <td>Registration Code</td>
            <td><?= \CHtml::activeTextField(
                    $user,
                    'registration_code',
                    [
                        'class' => 'cols-full',
                        'autocomplete' => Yii::app()->params['html_autocomplete']
                    ]
                ); ?>
            <td>
        </tr>
        <tr>
            <td>Active</td>
            <td><?= \CHtml::activeRadioButtonList(
                    $user,
                    'active',
                    [1 => 'Yes', 0 => 'No'],
                    ['separator' => ' ']
                ); ?></td>
        </tr>
        <tr>
            <td>Global firm rights</td>
            <td>
                <?= \CHtml::activeRadioButtonList(
                    $user,
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
                echo $form->multiSelectList(
                    $user,
                    'User[firms]',
                    'firms',
                    'id',
                    CHtml::listData(Firm::model()->findAll(), 'id', 'name'),
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
            <td><?= \CHtml::activeRadioButtonList(
                    $user,
                    'is_consultant',
                    [1 => 'Yes', 0 => 'No'],
                    ['separator' => ' ']
                ); ?></td>
        </tr>
        <tr>
            <td>Surgeon</td>
            <td><?= \CHtml::activeRadioButtonList(
                    $user,
                    'is_surgeon',
                    [1 => 'Yes', 0 => 'No'],
                    ['separator' => ' ']
                ); ?></td>
        </tr>
        <tr>
            <td>Password</td>
            <td>
                <?php if (!$is_ldap || $user->is_local) : ?>
                    <?= \CHtml::activePasswordField(
                        $user,
                        'password',
                        [
                            'class' => 'cols-full',
                            'autocomplete' => Yii::app()->params['html_autocomplete']
                        ]
                    ); ?>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td>Confirm password</td>
            <td>
                <?php if (!$is_ldap || $user->is_local) : ?>
                    <?= \CHtml::activePasswordField(
                        $user,
                        'password_repeat',
                        [
                            'class' => 'cols-full',
                            'autocomplete' => Yii::app()->params['html_autocomplete']
                        ]
                    ); ?>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td>Roles</td>
            <td>
                <?php echo $form->multiSelectList(
                    $user,
                    'User[roles]',
                    'roles',
                    'name',
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
                <?= \CHtml::submitButton(
                    'Cancel',
                    [
                        'data-uri' => '/admin/users',
                        'class' => 'button large',
                        'name' => 'cancel',
                        'id' => 'et_cancel'
                    ]
                ); ?>
            </td>
        </tr>
        </tfoot>
    </table>
</div>

<?php $this->endWidget() ?>

