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
        <h2><?php echo $ssoRoles->id ? 'Edit' : 'Add' ?> SSO Role</h2>
    </div>

    <?php echo $this->renderPartial('/admin/_form_errors', array('errors' => $errors)) ?>
    <?php $form = $this->beginWidget(
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

    <table class="standard">
        <colgroup>
            <col class="cols-8">
            <col class="cols-12">
        </colgroup>
        <tbody>
            <tr>
                <td>SSO Role</td>
                <td>
                    <?= \CHtml::activeTextField(
                        $ssoRoles,
                        'name',
                        [
                            'autocomplete' => Yii::app()->params['html_autocomplete'],
                            'class' => 'cols-full'
                        ]
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>Associated OE Roles</td>
                <td>
                    <?php echo $form->multiSelectList(
                        $ssoRoles,
                        'SsoRoles[sso_roles_assignment]',
                        'sso_roles_assignment',
                        'authitem_role',
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
                    <?php if ($ssoRoles->id) {
                        echo \CHtml::button(
                            'Delete SSO Role',
                            [
                                'class' => 'button large',
                                'name' => 'delete',
                                'data-object' => 'ssoRoles',
                                'id' => 'et_delete_role',
                            ]
                        );
                    } ?>
                    <?= \CHtml::submitButton(
                        'Cancel',
                        [
                            'data-uri' => '/sso/ssorolesauthassignment',
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

<script type="text/javascript">
    $('#et_delete_role').on('click', function (e) {
        e.preventDefault();
        window.location.href = '/sso/deleteSSORoles/<?= $ssoRoles->id ?>'
    });
</script>

<?php $this->endWidget() ?>