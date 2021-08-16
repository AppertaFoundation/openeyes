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
<tbody id="ldap-details">
    <tr>
        <td>LDAP Method</td>
        <td>
            <?= \CHtml::activeTextField(
                $ldap_config,
                'ldap_method',
                [ 'class' => 'cols-full' ]
            ); ?>
        </td>
    </tr>
    <tr>
        <td>LDAP Server(s)</td>
        <td>
            <?= \CHtml::activeTextField(
                $ldap_config,
                'ldap_server',
                [ 'class' => 'cols-full' ]
            ); ?>
        </td>
    </tr>
    <tr>
        <td>LDAP Port</td>
        <td>
            <?= \CHtml::activeTextField(
                $ldap_config,
                'ldap_port',
                [ 'class' => 'cols-full' ]
            ); ?>
        </td>
    </tr>
    <tr>
        <td>LDAP Admin Distinguished Name</td>
        <td>
            <?= \CHtml::activeTextField(
                $ldap_config,
                'ldap_admin_dn',
                [ 'class' => 'cols-full' ]
            ); ?>
        </td>
    <tr>
    </tr>
        <td>LDAP Admin Password</td>
        <td>
            <?= \CHtml::activePasswordField(
                $ldap_config,
                'ldap_admin_password',
                [ 'class' => 'cols-full' ]
            ); ?>
        </td>
    <tr>
    </tr>
        <td>LDAP Base Distinguished Name</td>
        <td>
            <?= \CHtml::activeTextField(
                $ldap_config,
                'ldap_dn',
                [ 'class' => 'cols-full' ]
            ); ?>
        </td>
    </tr>
    <tbody id="ldap-additional-params-table">
        <tr>
            <td>Additional LDAP Params</td>
        </tr>
    <?php foreach ($ldap_config->ldap_additional_params as $key => $param) {
        $this->renderPartial('/admin/ldap_config/_ldap_additional_param_row', [
            'key' => $key,
            'ldap_config' => $ldap_config,
        ]);
    } ?>
    </tbody>
    <tr>
        <td colspan="2">
            <?= \CHtml::button(
                'Add Additional LDAP Param',
                [
                    'class' => 'button large',
                    'id' => 'add-ldap-additional-param-btn'
                ]
            ); ?>
        </td>
    </tr>
</tbody>
