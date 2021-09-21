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

    <?php if (!$users) :?>
    <div class="row divider">
        <div class="alert-box issue"><b>No results found</b></div>
    </div>
    <?php endif; ?>

    <div class="row divider">
        <?php
        $form = $this->beginWidget(
            'BaseEventTypeCActiveForm',
            [
                'id' => 'searchform',
                'enableAjaxValidation' => false,
                'focus' => '#search',
                'action' => Yii::app()->createUrl('/admin/users'),
                'method' => 'get'
            ]
        ) ?>
        <input type="text"
               autocomplete="<?php echo Yii::app()->params['html_autocomplete'] ?>"
               name="search" id="search" placeholder="Search Users..."
               value="<?php echo !empty($search) ? strip_tags($search) : ''; ?>"/>
        <?php $this->endWidget() ?>
    </div>

    <form id="admin_users">
        <input type="hidden"
               name="YII_CSRF_TOKEN"
               value="<?php echo Yii::app()->request->csrfToken ?>"/>
        <table class="standard">
            <colgroup>
                <col span="2">
                <col class="cols-1" span="3">
                <col>
                <col class="cols-4"><!-- roles -->
            </colgroup>
            <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>First name</th>
                <th>Last name</th>
                <th>Doctor</th>
                <th>Roles</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $i => $user) : ?>
                <tr class="clickable js-clickable" data-id="<?php echo $user->id ?>"
                    data-uri="admin/editUser/<?php echo $user->id ?>">
                    <td><?php echo $user->id ?></td>
                    <td><?php echo $user->title ?></td>
                    <td><?php echo $user->first_name ?></td>
                    <td><?php echo $user->last_name ?></td>
                    <td><?php echo $user->is_doctor ? 'Yes' : 'No' ?></td>
                    <td>
                        <?php
                            $roles = CHtml::listData($user->roles, 'name', 'name');
                            echo $roles ? CHtml::encode(implode(', ', $roles)) : '-';
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot class="pagination-container">
            <tr>
                <td colspan="5">
                    <?=\CHtml::button(
                        'Add User',
                        [
                            'class' => 'button large',
                            'id' => 'et_add'
                        ]
                    ); ?>
                </td>
                <td colspan="<?=(Yii::app()->params['auth_source'] === 'BASIC')? '5' : '4' ?>">
                    <?php $this->widget('LinkPager', [ 'pages' => $pagination ]); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
