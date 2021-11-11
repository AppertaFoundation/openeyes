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
<tr data-key="<?= $key ?>">
    <?php
    $not_local = $user_authentication->institution_authentication_id ?
        $user_authentication->institutionAuthentication->user_authentication_method == 'LDAP' :
        false;

    $institution_authentication_id = $user_authentication->institution_authentication_id ?? 0;
    ?>
    <?= \CHtml::activeHiddenField($user_authentication, "[{$key}]id", ['class' => 'js-id']); ?>
    <?= \CHtml::activeHiddenField($user_authentication, "[{$key}]user_id", [
        'value' => $user->id
    ]); ?>
    <td><?= $user_authentication->id ?></td>
    <td>
        <?php
        $criteria = new CDbCriteria();
        $criteria->condition = 'active = 1 OR id = :institution_authentication_id';
        $criteria->params = [':institution_authentication_id' => $institution_authentication_id];
        ?>
        <?= \CHtml::activeDropDownList(
            $user_authentication,
            "[{$key}]institution_authentication_id",
            CHtml::listData(
                InstitutionAuthentication::model()->findAll($criteria),
                'id',
                'fullyQualifiedDescription'
            ),
            [
                'class' => 'cols-full js-change-inst-auth',
                'prompt' => 'Select an Institution Authentication'
            ]
        ); ?>
    </td>
    <td>
        <?= \CHtml::activeTextField(
            $user_authentication,
            "[{$key}]username",
            [
                'class' => 'cols-full',
                'autocomplete' => Yii::app()->params['html_autocomplete'],
            ]
        ); ?>
    </td>
    <?php if (Yii::app()->hasModule('mehstaffdb')) {
        echo("<td>");
        echo(\CHtml::button(
            'Lookup User',
            [
                'class' => 'button large',
                'id' => $key . 'lookup_user',
                'href' => '#',
                'onclick' => 'lookupUser("' . $key . '")',
            ]
        ));
        echo("</td>");
    } ?>
    <td>
        <?= \CHtml::activePasswordField(
            $user_authentication,
            "[{$key}]password",
            [
                'class' => 'cols-full js-password',
                'autocomplete' => Yii::app()->params['html_autocomplete'],
                'disabled' => $not_local
            ]
        ); ?>
    </td>
    <td>
        <?= \CHtml::activePasswordField(
            $user_authentication,
            "[{$key}]password_repeat",
            [
                'class' => 'cols-full js-password-repeat',
                'autocomplete' => Yii::app()->params['html_autocomplete'],
                'disabled' => $not_local
            ]
        ); ?>
    </td>
    <td>
        <?= \CHtml::activeDropDownList(
            $user_authentication,
            "[{$key}]password_status",
            [ 'current' => "Current Password",'stale' => "Stale Password",'expired' => "Expire Password",'locked' => "Lock Password" ],
            [
                'class' => 'cols-full js-password-status',
                'disabled' => $not_local
            ]
        ); ?>
    </td>
    <td>
        <?= \CHtml::activeCheckBox(
            $user_authentication,
            "[{$key}]active",
            [ 'class' => 'cols-full' ]
        ); ?>
    </td>
    <td>
        <i class="oe-i info small js-has-tooltip" data-tooltip-content="<?= $user_authentication->last_successful_login_date ? "Last successful login: $user_authentication->last_successful_login_date" : "Never logged in" ?>"></i>
    </td>
    <td>
        <i class="spinner as-icon js-row-spinner" style="display: none;"></i>
        <i class="oe-i trash js-remove-row"></i>
    </td>
</tr>
