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
        <h2>SSO Roles Mappings</h2>
    </div>
    <?php if (Yii::app()->user->hasFlash('Success')) { ?>
    <div class="flash-success alert-box success">
        <?= Yii::app()->user->getFlash('Success'); ?>
    </div>
    <?php } ?>
    <form id="admin_sso_roles">
        <table class="standard">
            <colgroup>
                <col span="3">
                <col span="4">
            </colgroup>
            <thead>
            <tr>
                <th>SSO Role</th>
                <th>OE Roles</th>
            </tr>
            </thead>
            <tbody>
                <?php foreach ($ssoRoles as $role) { ?>
                <tr class="clickable js-clickable" data-id="<?php echo $role->id ?>"
                    data-uri="sso/editSSORoles/<?php echo $role->id ?>">
                    <td><?= $role->name ?></td>
                    <td>
                        <?php
                            $roles = CHtml::listData($role->sso_roles_assignment, 'id', 'authitem_role');
                            echo $roles ? CHtml::encode(implode(', ', $roles)) : 'N/A';
                        ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot class="pagination-container">
                <tr>
                    <td colspan="5">
                        <?=\CHtml::button(
                            'Add SSO Role',
                            [
                                'data-uri' => '/sso/addSSORoles',
                                'class' => 'button large',
                                'id' => 'et_add'
                            ]
                        ); ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </form>

</div>
