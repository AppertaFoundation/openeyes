<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
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

namespace OEModule\OphCoMessaging\components;

use OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message;
use OEModule\OphCoMessaging\models\Mailbox;
use OEModule\OphCoMessaging\components\MailboxSearch;

class OphCoMessaging_API extends \BaseAPI
{
    private const DEFAULT_MESSAGES_FOLDER = MailboxSearch::FOLDER_UNREAD_ALL;

    public function getMenuItems()
    {
        $user = \Yii::app()->user;
        $mailbox = Mailbox::model()->forPersonalMailbox($user->id)->find();
        $criteria = new \CDbCriteria();
        $criteria->addCondition('recipients.mailbox_id = :mailbox_id');
        $criteria->addCondition('t.marked_as_read = :read');
        $criteria->params = [':mailbox_id' => $mailbox->id, ':read' => false];
        $criteria->order = 't.created_date asc';

        $messages = Element_OphCoMessaging_Message::model()->with('recipients')->findAll($criteria);
        $containsUrgentMessage = false;
        foreach ($messages as $message) {
            if ($message['urgent']) {
                $containsUrgentMessage = true;
            }
        }

        return [
            [
                'title' => 'Messages',
                'uri' => '/OphCoMessaging/Inbox',
                'messageCount' => count($messages),
                'containsUrgentMessage' => $containsUrgentMessage,
            ]
        ];
    }

    /**
     * @return array Dashboard widget content and options (No need for title).
     */
    public function getMessages($user = null, $mailbox = null)
    {
        $message_type = array_key_exists('messages', $_GET) && $_GET['messages'] ? $_GET['messages'] : self::DEFAULT_MESSAGES_FOLDER;
        $mailbox_id = array_key_exists('mailbox', $_GET) && $_GET['mailbox'] ? $_GET['mailbox'] : null;

        $search_parameters = [];

        if ($user == null) {
            $user = \Yii::app()->user;
        }

        if (array_key_exists('OphCoMessaging_Search_Mailbox', $_GET)) {
            $search_mailbox_id = \Yii::app()->request->getQuery('OphCoMessaging_Search_Mailbox', null);
            $date_from = \Yii::app()->request->getQuery('OphCoMessaging_from', '');
            $date_to = \Yii::app()->request->getQuery('OphCoMessaging_to', '');

            $search_parameters = [
                'date_from' => $date_from ? date("Y-m-d", strtotime($date_from)) : null,
                'date_to' => $date_to ? date("Y-m-d", strtotime($date_to)) : null,
                'sender' => \Yii::app()->request->getQuery('OphCoMessaging_Search_Sender', ''),
                'message_type' => \Yii::app()->request->getQuery('OphCoMessaging_Search_MessageType', ''),
                'message_content' => \Yii::app()->request->getQuery('OphCoMessaging_Search', ''),
                'retrieve_all_comments' => \Yii::app()->request->getQuery('OphCoMessaging_All_Comments', 0),
                'retrieve_original_element' => \Yii::app()->request->getQuery('OphCoMessaging_Original_Element', 0),
                'allow_unreplied_started_threads' => \Yii::app()->request->getQuery('OphCoMessaging_Unreplied_Started_Threads', 0),
            ];

            $mailbox_id = $search_mailbox_id;
        }

        $searcher = new MailboxSearch(\Yii::app()->user, $message_type, $search_parameters);

        if ($mailbox_id !== null) {
            $mailbox = Mailbox::model()->findByPk($mailbox_id);
        }

        $recipient_messages = $searcher->retrieveMailboxContentsUsingSQL($user->id, isset($mailbox) ? [$mailbox->id] : null);

        list($mailboxes_with_counts, $count_unread_total) = $this->getMessageCounts($user);

        // Generate the dashboard widget HTML.
        $dashboard_view = \Yii::app()->controller->renderPartial('OphCoMessaging.views.dashboard.message_dashboard', [
                'mailboxes_with_counts' => $mailboxes_with_counts,
                'count_unread_total' => $count_unread_total,
                'selected_mailbox' => $mailbox,
                'message_type' => $message_type,
                'recipient_messages' => $recipient_messages,
                'is_a_sent_folder' => $searcher->isSentFolder(),
                'module_class' => $this->getModuleClass(),
            ]);

        return [
            'content' => $dashboard_view,
            'options' => [
                'container-id' => \Yii::app()->user->id . '-dashboard-container',
            ],
        ];
    }

    /**
     * @param User|OEWebUser|null $user
     * @return array - list with counts of all unread messages for each folder
     */
    public function updateMessagesCount($user = null)
    {
        if ($user === null) {
            $user = \Yii::app()->user;
        }

        list($mailboxes_with_counts, $count_unread_total) = $this->getMessageCounts($user);

        return [
            'mailboxes_with_counts' => $mailboxes_with_counts,
            'count_unread_total' => $count_unread_total
        ];
    }

    /**
     * Get details of mailboxes in an associated array, mapped by their ids.
     *
     * For example, this is used by getMessagesCounts which augments
     * the data returned with counts of the mailbox folders.
     *
     * @param User|OEWebUser $user
     * @return array - [mailbox id => [mailbox id, mailbox name, is personal mailbox]]
     */
    public function getAllUserMailboxesById($user)
    {
        $mailboxes = MailboxSearch::getAllMailboxesForUser($user->id);

        $by_id = [];

        foreach ($mailboxes as $mailbox) {
            $by_id[$mailbox['id']] = $mailbox;
        }

        return $by_id;
    }

    /**
     * Get counts of messages
     *
     * Note: the unread replies count is gather in the sent query
     * because it counts unread replies in the comments table to a message sent
     * by the user and thus the table relation between the message and the comments
     * is more easily expressed in that query.
     */
    public function getMessageCounts($user)
    {
        $mailboxes = MailboxSearch::getAllMailboxesForUser($user->id);

        $counts_by_id = [];
        $total_unread_messages = 0;

        foreach ($mailboxes as $mailbox) {
            $counts_for_mailbox = MailboxSearch::getMailboxFolderCounts($user->id, [$mailbox['id']]);
            $counts_by_id[$mailbox['id']] = array_merge($mailbox, $counts_for_mailbox);
            $total_unread_messages += $counts_for_mailbox['unread_all'];
        }

        return [$counts_by_id, $total_unread_messages];
    }

    /**
     * @param User|OEWebUser $user
     * @return void
     */
    public function createPersonalMailboxIfDoesNotExist($user)
    {
        if (!Mailbox::model()->forPersonalMailbox($user->id)->exists()) {
            $transaction = \Yii::app()->db->beginInternalTransaction();

            try {
                $personal_mailbox = new Mailbox();

                $personal_mailbox->name = trim($user->getFullNameAndTitle());
                $personal_mailbox->is_personal = true;
                $personal_mailbox->users = [$user];

                if (!$personal_mailbox->save()) {
                    throw new \Exception('Failed to save new personal mailbox: ' . print_r($personal_mailbox->getErrors(), true));
                }
            } catch (\Exception $e) {
                $transaction->rollback();

                throw $e;
            }

            $transaction->commit();
        }
    }
}
