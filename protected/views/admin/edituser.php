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
);
?>

<div>
    <div class="cols-7">

        <div class="row divider">
            <h2><?php echo $user->id ? 'Edit' : 'Add' ?> user</h2>
        </div>

        <?php echo $this->renderPartial('_form_errors', array('errors' => $errors)) ?>

        <table class="standard cols-full">
            <colgroup>
                <col class="cols-5">
                <col class="cols-8">
            </colgroup>
            <tbody>

            <?php
            $personal_fields = ['title', 'first_name', 'last_name', 'email', 'role'];
            foreach ($personal_fields as $field) : ?>
                <tr>
                    <td><?php echo $user->getAttributeLabel($field); ?></td>
                    <td>
                        <?= \CHtml::activeTextField(
                            $user,
                            $field,
                            [
                                'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                                'class' => 'cols-full'
                            ]
                        ); ?>
                    </td>
                </tr>
            <?php endforeach; ?>

            <tr>
                <td><?php echo $contact->getAttributeLabel("qualifications"); ?></td>
                <td>
                    <?= \CHtml::activeTextField(
                        $contact,
                        "qualifications",
                        [
                            'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                            'class' => 'cols-full'
                        ]
                    ); ?>
                </td>
            </tr>
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
                            'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete')
                        ]
                ); ?>
                <td>
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
                <td><?=Firm::contextLabel()?>(s)</td>
                <td>
                    <?php
                    $firm_label = [];
                    $available_firms = count($user_auths) > 0 ? $user->getAllAvailableFirms() : (new User())->getAllAvailableFirms();

                    foreach ($available_firms as $firm) {
                        $firm_label[$firm->id] = "{$firm->name} " . ($firm->serviceSubspecialtyAssignment ? "({$firm->serviceSubspecialtyAssignment->subspecialty->name})" : "") . ($firm->institution ? " ({$firm->institution->name})" : " (All institutions)");
                    }

                    echo $form->multiSelectList(
                        $user,
                        'User[firms]',
                        'firms',
                        'id',
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
        </table>
    </div>
    <div class="cols-full">
        <br>

        <h2>Login Authentications</h2>
        <hr class="divider">
        <?php $user_authentication_fields = ['id', 'institution_authentication', 'username', 'lookup_user', 'password', 'password_repeat', 'password_status', 'active'];
        if (!Yii::app()->hasModule('mehstaffdb')) {
            $user_authentication_fields = array_diff($user_authentication_fields, array('lookup_user'));
        }
        ?>
        <table class="standard" id="user-authentications">
            <thead>
            <tr>
                <?php foreach ($user_authentication_fields as $field) { ?>
                    <th><?= UserAuthentication::model()->getAttributeLabel($field) ?></th>
                <?php } ?>
            </tr>
            </thead>
            <tbody id="user-auth-rows">
                <?php foreach ($user_auths as $key => $user_auth) {
                    $this->renderPartial('/admin/_user_authentication_row', [
                        'key' => $key,
                        'user' => $user,
                        'user_authentication' => $user_auth,
                    ]);
                } ?>
            </tbody>
        </table>
        <div class="flex-layout flex-left">
            <?=\CHtml::button(
                'Add User Authentication',
                [
                    'class' => 'button large',
                    'id' => 'add-user-authentication-btn'
                ]
            ); ?>
        </div>
        <br>
    </div>
<?php $this->endWidget() ?>
    <div>
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
    </div>

<script type="x-tmpl-mustache" id="add-user-authentication-template">
  <?php
    $this->renderPartial('/admin/_user_authentication_row', [
        'key' => '{{key}}',
        'user' => $user,
        'user_authentication' => new UserAuthentication(),
    ]);
    ?>
</script>
<script>
    let defaultPasswordStatus = "current";
    $(document).ready(function () {
        $('#add-user-authentication-btn').on('click', function () {
            let $table = $('#user-authentications tbody');
            $table.parent().show();
            let nextDataKey = OpenEyes.Util.getNextDataKey($table.find('tr'), 'key');
            let tr = Mustache.render($('#add-user-authentication-template').text(), {key : nextDataKey});
            $table.append(tr);
        });

        $('#user-auth-rows').on('change', '.js-change-inst-auth', function (e) {
            let $row = $(this).closest('tr');

            if ($row.find('.js-id').val() !== '') {
                new OpenEyes.UI.Dialog.Alert({
                    content: "Warning Institution Authentication changed - password status and expiry has been reset to system defaults."
                }).open();
            }

            $row.find('.js-password-status').val(defaultPasswordStatus);
            $row.find('.js-remove-row').hide();
            $row.find('.js-row-spinner').show();
            $.ajax({
                'type': 'GET',
                'url': baseUrl + '/admin/checkInstAuthType?id=' + $(this).val(),
                'success': res => {
                    if (res === 'LOCAL') {
                        $row.find('.js-password,.js-password-repeat,.js-password-status').prop('disabled', false);
                    } else {
                        $row.find('.js-password,.js-password-repeat,.js-password-status').prop('disabled', true);
                    }
                    $row.find('.js-remove-row').show();
                    $row.find('.js-row-spinner').hide();
                }
            });
        });

        $('#user-auth-rows').on('click', '.js-remove-row', function (e) {
            $(this).closest('tr').remove();
        });
    });

    function lookupUser(key) {
        if($('#UserAuthentication_'+key+'_username').val()) {
            $.ajax({
                'type': 'GET',
                'url': baseUrl + '/admin/lookupUser?username=' + $('#UserAuthentication_'+key+'_username').val() + '&institution_authentication_id=' + $('#UserAuthentication_'+key+'_institution_authentication_id').val(),
                'success': function (resp) {
                    var m = resp.match(/[0-9]+/);
                    if (m) {
                        window.location.href = baseUrl + '/admin/editUser/' + m[0];
                    } else {
                        enableButtons();
                        new OpenEyes.UI.Dialog.Alert({
                            content: "User not found"
                        }).open();
                    }
                }
            });
        }
    };


</script>
