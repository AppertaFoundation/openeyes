<?php
/**
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

<div class="cols-5">
    <div class="row divider">
        <h2><?= $institution_authentication->isNewRecord ? 'Add' : 'Edit' ?> Institution Authentication Method</h2>
    </div>

    <?php echo $this->renderPartial('_form_errors', ['errors' => $errors]) ?>
    <?php
    $form = $this->beginWidget(
        'BaseEventTypeCActiveForm',
        [
            'id' => 'adminform',
            'enableAjaxValidation' => false,
            'focus' => '#username',
            'layoutColumns' => array(
                'label' => 2,
                'field' => 5,
            ),
        ]
    ) ?>

    <table class="standard cols-full" id="inst-auth-main-table">
        <colgroup>
            <col class="cols-3">
            <col class="cols-8">
        </colgroup>
        <?= \CHtml::activeHiddenField(
            $institution_authentication,
            'id',
        ); ?>

        <tbody>
            <tr>
                <td><?= $institution_authentication->getAttributeLabel('institution'); ?></td>
                <td>
                    <?= $institution_authentication->institution->name ?>
                    <?= \CHtml::activeHiddenField(
                        $institution_authentication,
                        'institution_id',
                    ); ?>
                </td>
            </tr>
            <tr>
                <td><?= $institution_authentication->getAttributeLabel('site'); ?></td>
                <td>
                    <?= \CHtml::activeDropDownList(
                        $institution_authentication,
                        'site_id',
                        CHtml::listData(Site::model()->findAllByAttributes(['institution_id' => $institution_authentication->institution_id]), 'id', 'name'),
                        ['style' => 'width:200px', 'empty' => 'None specified']
                    ); ?>
                </td>
            </tr>
            <tr>
                <td><?= $institution_authentication->getAttributeLabel('description'); ?></td>
                <td>
                    <?= \CHtml::activeTextField(
                        $institution_authentication,
                        'description',
                        [ 'class' => 'cols-full' ]
                    ); ?>
                </td>
            </tr>
            <tr>
                <td><?= $institution_authentication->getAttributeLabel('user_authentication_method'); ?></td>
                <td>
                    <?= \CHtml::activeDropDownList(
                        $institution_authentication,
                        'user_authentication_method',
                        CHtml::listData(UserAuthenticationMethod::model()->findAll(), 'code', 'code'),
                        ['class' => 'cols-full']
                    ); ?>
                </td>
            </tr>
            <tr id="ldap-field" style="display:none;">
                <td><?= $institution_authentication->getAttributeLabel('ldap_config_id'); ?></td>
                <td>
                    <?= \CHtml::activeDropDownList(
                        $institution_authentication,
                        'ldap_config_id',
                        CHtml::listData(LDAPConfig::model()->findAll(), 'id', 'description'),
                        ['class' => 'cols-full']
                    ); ?>
                </td>
            </tr>
            <tr>
                <td><?= $institution_authentication->getAttributeLabel('active'); ?></td>
                <td>
                    <?= \CHtml::activeCheckBox(
                        $institution_authentication,
                        'active',
                        [ 'class' => 'cols-full' ]
                    ); ?>
                </td>
            </tr>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="2">
                <?= \CHtml::submitButton(
                    'Save',
                    [
                        'class' => 'button large',
                        'name' => 'save',
                        'id' => 'et_save'
                    ]
                ); ?>
                <?= \CHtml::submitButton(
                    'Back',
                    [
                        'class' => 'button large',
                        'data-uri' => "/admin/editinstitution?institution_id={$institution_authentication->institution->id}",
                        'name' => 'cancel',
                        'id' => 'et_cancel'
                    ]
                ); ?>
            </td>
        </tr>
        </tfoot>
    </table>
    <?php $this->endWidget() ?>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#InstitutionAuthentication_user_authentication_method').on('change', function (e) {
            if (e.target.value === 'LDAP') {
                $('#ldap-field').show().removeAttr('style');
            } else {
                $('#ldap-field').hide();
            }
        });
        $('#InstitutionAuthentication_user_authentication_method').change();
    });
</script>
