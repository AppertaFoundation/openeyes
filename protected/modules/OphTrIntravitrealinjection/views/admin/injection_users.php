<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="cols-5">
    <table class="standard">
        <colgroup>
            <col class="cols-2">
            <col class="cols-1">
        </colgroup>
        <tr>
            <td>
                <?=\CHtml::dropDownList('user_id', '', CHtml::listData($user_list, 'id', 'fullName'), array('empty' => '- Select user -')) ?>
            </td>
            <td>
                <?=\CHtml::htmlButton('Add user', array('class' => 'button small addUser')) ?>
            </td>
        </tr>
    </table>

    <form id="admin_injection_users">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
        <table class="standard">
            <thead>
            <tr>
                <?php if (count($injection_users) > 0) { ?>
                    <th><input type="checkbox" name="selectall" id="selectall"/></th>
                <?php } ?>
                <th>User</th>
            </tr>
            </thead>
            <tbody>
            <?php $this->renderPartial('_injection_users', array('injection_users' => $injection_users)) ?>
            </tbody>
        </table>
    </form>
    <div class="cols-4 column end">
        <?=\CHtml::submitButton(
            'Delete user(s)',
            [
                'class' => 'button large',
                'name' => 'delete',
                'data-object' => 'injection_users',
                'data-uri' => '/OphTrIntravitrealinjection/admin/deleteInjectionUsers',
                'id' => 'et_delete'
            ]
        ) ?>
    </div>
</div>
