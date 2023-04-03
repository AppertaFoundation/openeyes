<?php

/**
 * (C) OpenEyes Foundation, 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2022, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

    <?php if (!$mailboxes) :?>
    <div class="row divider">
        <div class="alert-box issue"><b>No mailboxes found</b></div>
    </div>
    <?php endif; ?>

    <form id="admin_shared_mailboxes">
        <input type="hidden"
               name="YII_CSRF_TOKEN"
               value="<?php echo Yii::app()->request->csrfToken ?>"/>
        <table class="standard">
            <colgroup>
                <col class="cols-3">
                <col class="cols-3">
                <col class="cols-3">
                <col class="cols-3">
            </colgroup>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Users</th>
                    <th>Teams</th>
                    <th>Active</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($mailboxes as $i => $mailbox) {
                $user_names = array_map(
                    static function ($user) {
                        return $user->getFullName();
                    },
                    $mailbox->users
                );

                $team_names = array_map(
                    static function ($team) {
                        return $team->name;
                    },
                    $mailbox->teams
                ); ?>
                <tr class="clickable js-clickable" data-id="<?= $mailbox->id ?>"
                    data-uri="OphCoMessaging/SharedMailboxSettings/edit/<?= $mailbox->id ?>">
                    <td data-test="list-shared-mailbox-name"><?= $mailbox->name ?></td>
                    <td><?= implode(', ', $user_names) ?></td>
                    <td><?= implode(', ', $team_names) ?></td>
                    <td><i class="oe-i <?= $mailbox->active ? 'tick' : 'remove' ?> small"></i></td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot class="pagination-container">
            <tr>
                <td colspan="5">
                    <?=\CHtml::button(
                        'Add Shared Mailbox',
                        [
                            'class' => 'button large',
                            'id' => 'et_add',
                            'data-uri' => '/OphCoMessaging/SharedMailboxSettings/create'
                        ]
                    ); ?>
                </td>
                <td colspan="<?=(Yii::app()->params['auth_source'] === 'BASIC') ? '5' : '4' ?>">
                <?php $this->widget('LinkPager', [ 'pages' => $pagination ]); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
