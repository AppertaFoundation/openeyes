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
 * @copyright Copyright (C) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

    <div class="row divider">
    <h2><?php echo $user->id ? 'Edit' : 'Add'?> user</h2>
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
    <div class="cols-7">
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
                        <?php echo CHtml::activeTextField(
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
                <td >
                    <?php echo CHtml::activeDropDownList(
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
                <td><?php echo CHtml::activeTextField(
                    $user,
                    'registration_code',
                    [
                        'class' => 'cols-full',
                        'autocomplete' => Yii::app()->params['html_autocomplete']
                    ]
                ); ?><td>
            </tr>
            <tr>
                <td>Active</td>
                <td><?php echo CHtml::activeRadioButtonList(
                    $user,
                    'active',
                    [1 => 'Yes', 0 => 'No'],
                    ['separator' => ' ']
                ); ?></td>
            </tr>
            <tr>
                <td>Global firm rights</td>
                <td>
                    <?php echo CHtml::activeRadioButtonList(
                        $user,
                        'global_firm_rights',
                        [1 => 'Yes', 0 => 'No'],
                        ['separator' => ' ', 'selected' => '1']
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>Firms</td>
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
                            'label' => 'Firms',
                            'empty' => '-- Add --',
                            'nowrapper' => true
                        ]
                    );
                    ?>
                </td>
            </tr>
            <tr>
                <td>Clinically trained</td>
                <td><?php echo CHtml::activeRadioButtonList(
                    $user,
                    'is_clinical',
                    [1 => 'Yes', 0 => 'No'],
                    ['separator' => ' ']
                ); ?></td>
            </tr>
            <tr>
                <td>Consultant</td>
                <td><?php echo CHtml::activeRadioButtonList(
                    $user,
                    'is_consultant',
                    [1 => 'Yes', 0 => 'No'],
                    ['separator' => ' ']
                ); ?></td>
            </tr>
            <tr>
                <td>Surgeon</td>
                <td><?php echo CHtml::activeRadioButtonList(
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
                        <?php echo CHtml::activePasswordField(
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
                        <?php echo CHtml::activePasswordField(
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
                    <?php echo CHtml::button(
                        'Save',
                        [
                            'class' => 'button large primary event-action',
                            'name' => 'save',
                            'type' => 'submit',
                            'id' => 'et_save'
                        ]
                    ); ?>
                    <?php echo CHtml::button(
                        'Cancel',
                        [
                            'data-uri' => '/admin/users',
                            'class' => 'warning button large primary event-action',
                            'type' => 'submit',
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

