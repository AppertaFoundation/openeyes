<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2022, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCoMessaging\controllers;

use OEModule\OphCoMessaging\models\Mailbox;

class SharedMailboxSettingsController extends \ModuleAdminController
{
    public $group = 'Message';

    /**
     * Renders the index page
     * @throws \CHttpException
     */
    public function actionIndex()
    {
        $criteria = new \CDbCriteria();

        $criteria->addCondition('is_personal = 0');

        $mailboxes = Mailbox::model()->findAll($criteria);
        $pagination = $this->initPagination(Mailbox::model(), $criteria);

        $this->render(
            '/admin/shared_mailboxes',
            [
                'mailboxes' => $mailboxes,
                'pagination' => $pagination,
            ]
        );
    }

    public function actionCreate()
    {
        $mailbox = new Mailbox();
        $errors = [];

        $users = [];
        $teams = [];

        if (\Yii::app()->request->isPostRequest) {
            $data = $_POST[\CHtml::modelName($mailbox)];
            $errors = $this->saveMailbox($mailbox, $data);

            if (empty($errors)) {
                $this->redirect('/OphCoMessaging/SharedMailboxSettings');
            } else {
                $users = \User::model()->findAllByPk($data['mailboxUsers'] ?? []);
                $teams = \Team::model()->findAllByPk($data['mailboxTeams'] ?? []);
            }
        }

        $this->render(
            '/admin/edit_shared_mailbox',
            [
                'mailbox' => $mailbox,
                'errors' => $errors,
                'selected_users' => $users,
                'selected_teams' => $teams,
            ]
        );
    }

    public function actionEdit($id)
    {
        $mailbox = Mailbox::model()->findByPk($id);
        $errors = [];

        if ($mailbox === null) {
            throw new \Exception('Mailbox not found');
        }

        if (\Yii::app()->request->isPostRequest) {
            $data = $_POST[\CHtml::modelName($mailbox)];
            $errors = $this->saveMailbox($mailbox, $data);

            if (empty($errors)) {
                $this->redirect('/OphCoMessaging/SharedMailboxSettings');
            } else {
                $users = \User::model()->findAllByPk($data['mailboxUsers'] ?? $mailbox->users);
                $teams = \Team::model()->findAllByPk($data['mailboxTeams'] ?? $mailbox->teams);
            }
        } else {
            $users = $mailbox->users;
            $teams = $mailbox->teams;
        }

        $this->render(
            '/admin/edit_shared_mailbox',
            [
                'mailbox' => $mailbox,
                'errors' => $errors,
                'selected_users' => $users,
                'selected_teams' => $teams,
            ]
        );
    }

    private function saveMailbox($mailbox, $data)
    {
        $mailbox->name = $data['name'];
        $mailbox->is_personal = 0;
        $mailbox->active = $data['active'] ?? '0';

        $user_ids = $data['mailboxUsers'] ?? [];
        $team_ids = $data['mailboxTeams'] ?? [];

        $mailbox->users = $user_ids;
        $mailbox->teams = $team_ids;

        $transaction = \Yii::app()->db->beginTransaction();
        $errors = [];

        try {
            if (!$mailbox->save()) {
                $errors = $mailbox->getErrors();

                $transaction->rollback();
            } else {
                $transaction->commit();
            }
        } catch (\Exception $e) {
            $transaction->rollback();

            throw $e;
        }

        return $errors;
    }
}
