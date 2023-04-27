<?php

namespace OEModule\OphCoMessaging\components;

use OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message;
use OEModule\OphCoMessaging\models\Mailbox;
use CDbCriteria;

class MailboxSearch
{
    public const FOLDER_ALL = 'all';

    public const FOLDER_UNREAD_ALL = 'unread_all';
    public const FOLDER_UNREAD_URGENT = 'unread_urgent';
    public const FOLDER_UNREAD_QUERY = 'unread_query';
    public const FOLDER_UNREAD_RECEIVED = 'unread_received';
    public const FOLDER_UNREAD_COPIED = 'unread_copied';
    public const FOLDER_UNREAD_REPLIES = 'unread_replies';

    public const FOLDER_READ_ALL = 'read_all';
    public const FOLDER_READ_URGENT = 'read_urgent';
    public const FOLDER_READ_RECEIVED = 'read_received';
    public const FOLDER_READ_COPIED = 'read_copied';

    public const FOLDER_SENT_ALL = 'sent_all';
    public const FOLDER_SENT_UNREPLIED = 'sent_unreplied';
    public const FOLDER_SENT_UNREAD = 'sent_unread';

    private const RETRIEVE_RECEIVED = 'received';
    private const RETRIEVE_SENT = 'sent';
    private const RETRIEVE_RECEIVED_AND_REPLIES = 'received_and_replies';

    private $criteria;
    private $retrieve_from = self::RETRIEVE_RECEIVED;

    public function __construct($user, $folder, $search_parameters = [])
    {
        $this->criteria = new CDbCriteria();

        // The following are common for all 4 kinds of retrieval (received/sent and specific mailbox/all mailboxes for a user)
        $this->criteria->with = [
            'event',
            'message_type',
            'recipients',
            'recipients.mailbox' => ['condition' => 'mailbox.active <> 0'],
            'sender' => ['condition' => 'sender.active <> 0'],
            'event.episode',
            'event.episode.patient',
            'event.episode.patient.contact'
        ];

        $this->criteria->addCondition('event.deleted = 0');

        switch ($folder) {
            case self::FOLDER_ALL:
                $this->retrieve_from = self::RETRIEVE_RECEIVED_AND_REPLIES;
                $this->addLastCommentCriteriaTo($this->criteria, $user);
                break;

            case self::FOLDER_UNREAD_ALL:
                $this->retrieve_from = self::RETRIEVE_RECEIVED_AND_REPLIES;
                $this->addLastCommentCriteriaTo($this->criteria, $user);
                $this->addMarkedReadCondition($this->criteria, false, true);
                break;

            case self::FOLDER_UNREAD_URGENT:
                $this->retrieve_from = self::RETRIEVE_RECEIVED;
                $this->addLastCommentCriteriaTo($this->criteria, $user);
                $this->addMarkedReadCondition($this->criteria, false, true);
                $this->addIsUrgentCondition($this->criteria);
                break;

            case self::FOLDER_UNREAD_QUERY:
                $this->retrieve_from = self::RETRIEVE_RECEIVED;
                $this->addLastCommentCriteriaTo($this->criteria, $user);
                $this->addMarkedReadCondition($this->criteria, false, true);
                $this->addIsQueryCondition($this->criteria);
                break;

            case self::FOLDER_UNREAD_RECEIVED:
                $this->retrieve_from = self::RETRIEVE_RECEIVED;
                $this->addLastCommentCriteriaTo($this->criteria, $user);
                $this->addMarkedReadCondition($this->criteria, false, true);
                $this->addPrimaryRecipientCondition($this->criteria);
                break;

            case self::FOLDER_UNREAD_COPIED:
                $this->retrieve_from = self::RETRIEVE_RECEIVED;
                $this->addLastCommentCriteriaTo($this->criteria, $user);
                $this->addMarkedReadCondition($this->criteria, false, true);
                $this->addCopiedCondition($this->criteria);
                break;

            case self::FOLDER_UNREAD_REPLIES:
                $this->retrieve_from = self::RETRIEVE_RECEIVED_AND_REPLIES;
                $this->addLastCommentCriteriaTo($this->criteria, $user, true);
                break;

            case self::FOLDER_READ_ALL:
                $this->retrieve_from = self::RETRIEVE_RECEIVED;
                $this->addMarkedReadCondition($this->criteria, true, false);
                break;

            case self::FOLDER_READ_URGENT:
                $this->retrieve_from = self::RETRIEVE_RECEIVED;
                $this->addMarkedReadCondition($this->criteria, true, false);
                $this->addIsUrgentCondition($this->criteria);
                break;

            case self::FOLDER_READ_RECEIVED:
                $this->retrieve_from = self::RETRIEVE_RECEIVED;
                $this->addMarkedReadCondition($this->criteria, true, false);
                $this->addPrimaryRecipientCondition($this->criteria);
                break;

            case self::FOLDER_READ_COPIED:
                $this->retrieve_from = self::RETRIEVE_RECEIVED;
                $this->addMarkedReadCondition($this->criteria, true, false);
                $this->addCopiedCondition($this->criteria);
                break;

            case self::FOLDER_SENT_ALL:
                $this->retrieve_from = self::RETRIEVE_SENT;
                break;

            case self::FOLDER_SENT_UNREPLIED:
                $this->retrieve_from = self::RETRIEVE_SENT;
                $this->addIsQueryCondition($this->criteria);
                $this->addLastCommentCriteriaTo($this->criteria, $user, false, false);
                break;

            case self::FOLDER_SENT_UNREAD:
                $this->retrieve_from = self::RETRIEVE_SENT;
                $this->addMarkedReadCondition($this->criteria, false, false);
                break;

            default:
                throw new \Exception('Invalid folder name supplied to MailboxSearch: ' . $folder);
        }

        $this->criteria->together = true;

        if (!empty($search_parameters['date_from'])) {
            $this->criteria->addCondition('event_date >= :date_from');
            $this->criteria->params[':date_from'] = $search_parameters['date_from'];
        }

        if (!empty($search_parameters['date_to'])) {
            $this->criteria->addCondition('event_date <= :date_to');
            $this->criteria->params[':date_to'] = $search_parameters['date_to'];
        }

        if (!empty($search_parameters['sender'])) {
            $this->criteria->addCondition('sender_mailbox_id = :sender_id');
            $this->criteria->params[':sender_id'] = $search_parameters['sender'];
        }

        if (!empty($search_parameters['message_type'])) {
            $this->criteria->addCondition('message_type.id = :message_type');
            $this->criteria->params[':message_type'] = $search_parameters['message_type'];
        }

        if (!empty($search_parameters['message_content'])) {
            $this->criteria->addCondition('LOWER(message_text) LIKE CONCAT("%", LOWER(:message_text), "%")');
            $this->criteria->params[':message_text'] = $search_parameters['message_content'];
        }
    }

    public function isReceviedFolder(): bool
    {
        return $this->retrieve_from === self::RETRIEVE_RECEIVED
            || $this->retrieve_from === self::RETRIEVE_RECEIVED_AND_REPLIES;
    }

    public function isSentFolder(): bool
    {
        return $this->retrieve_from === self::RETRIEVE_SENT;
    }

    public function makeCriteriaForMailbox(Mailbox $mailbox): CDbCriteria
    {
        $retrieval_criteria = new CDbCriteria();

        if ($this->retrieve_from === self::RETRIEVE_RECEIVED) {
            $retrieval_criteria->addCondition('(recipients.mailbox_id = :mailbox_id)');
        } elseif ($this->retrieve_from === self::RETRIEVE_SENT) {
            $retrieval_criteria->addCondition('(t.sender_mailbox_id = :mailbox_id)');
        } elseif ($this->retrieve_from === self::RETRIEVE_RECEIVED_AND_REPLIES) {
            $retrieval_criteria->addCondition('(recipients.mailbox_id = :mailbox_id AND (last_comment.created_user_id IS NULL OR t.created_user_id = last_comment.created_user_id))');
            $retrieval_criteria->addCondition('(t.sender_mailbox_id = :mailbox_id AND recipients.marked_as_read = 1 AND t.created_user_id = :uid AND last_comment.marked_as_read = 0 AND last_comment.created_user_id <> :uid)', 'OR');
        }

        $retrieval_criteria->params = [':mailbox_id' => $mailbox->id];

        $retrieval_criteria->mergeWith($this->criteria);
        $retrieval_criteria->together = true;

        return $retrieval_criteria;
    }

    public function makeCriteriaForMailboxesBelongingTo($user): CDbCriteria
    {
        $retrieval_criteria = new CDbCriteria();

        if ($this->retrieve_from === self::RETRIEVE_RECEIVED) {
            $retrieval_criteria->with = [
                'recipients.mailbox.users',
                'recipients.mailbox.teams.users' => ['alias' => 'team'],
            ];

            $retrieval_criteria->addCondition('(users_users.user_id = :uid OR users_team.user_id = :uid)');
        } elseif ($this->retrieve_from === self::RETRIEVE_SENT) {
            $retrieval_criteria->with = [
                'sender.users',
                'sender.teams.users' => ['alias' => 'team'],
            ];

            $retrieval_criteria->addCondition('(users_users.user_id = :uid OR users_team.user_id = :uid)');
        } elseif ($this->retrieve_from === self::RETRIEVE_RECEIVED_AND_REPLIES) {
            $retrieval_criteria->with = [
                'recipients.mailbox.users' => ['alias' => 'receive_user'],
                'recipients.mailbox.teams' => ['alias' => 'receive_team'],
                'recipients.mailbox.teams.users' => ['alias' => 'receive_team_user'],
                'sender.users' => ['alias' => 'send_user'],
                'sender.teams' => ['alias' => 'sender_team'],
                'sender.teams.users' => ['alias' => 'send_team_user'],
            ];

            $retrieval_criteria->addCondition('((users_receive_user.user_id = :uid OR users_receive_team_user.user_id = :uid) AND (last_comment.created_user_id IS NULL OR t.created_user_id = last_comment.created_user_id))');
            $retrieval_criteria->addCondition('((users_send_user.user_id = :uid OR users_send_team_user.user_id = :uid) AND (recipients.marked_as_read = 1 AND t.created_user_id = :uid AND last_comment.marked_as_read = 0 AND last_comment.created_user_id <> :uid))', 'OR');
        }

        $retrieval_criteria->params = [':uid' => $user->id];

        $retrieval_criteria->mergeWith($this->criteria);
        $retrieval_criteria->together = true;

        return $retrieval_criteria;
    }

    public function retrieveContentsForMailbox($mailbox)
    {
        return $this->retrieveContentsUsingCriteria($this->makeCriteriaForMailbox($mailbox));
    }

    public function retrieveContentsForMailboxesBelongingTo($user)
    {
        return $this->retrieveContentsUsingCriteria($this->makeCriteriaForMailboxesBelongingTo($user));
    }

    private function retrieveContentsUsingCriteria($criteria)
    {
        $sort = $this->makeSort();

        $total_messages = Element_OphCoMessaging_Message::model()->with(array('event'))->count($criteria);

        return new \CActiveDataProvider(
            Element_OphCoMessaging_Message::class,
            array(
                'sort' => $sort,
                'criteria' => $criteria,
                'pagination' => array(
                    'pageSize' => 30,
                    'itemCount' => $total_messages
                ),
            )
        );
    }

    private function makeSort()
    {
        $sort = new \CSort();

        $sort->attributes = array(
            'priority' => array('asc' => 'urgent asc',
                'desc' => 'urgent desc', ),
            'is_query' => array('asc' => 'is_query asc',
                'desc' => 'is_query desc', ),
            'message_type' => array('asc' => 'lower(message_type.name) asc',
                'desc' => 'lower(message_type.name) desc'),
            'event_date' => array('asc' => 't.created_date asc',
                'desc' => 't.created_date desc', ),
            'user' => array('asc' => 'lower(for_the_attention_of_user.last_name) asc, lower(for_the_attention_of_user.first_name) asc',
                'desc' => 'lower(for_the_attention_of_user.last_name) desc, lower(for_the_attention_of_user.first_name) desc', ),
        );

        $sort->defaultOrder = 'event_date desc';

        return $sort;
    }

    private function addLastCommentCriteriaTo(CDbCriteria $target_criteria, $user, bool $comment_must_exist = false, bool $comments_are_for_user = true)
    {
        $criteria = new CDbCriteria();

        $criteria->with = ['last_comment'];
        $criteria->together = true;

        $criteria->select = [
            '*',
            new \CDbExpression(
                'if (last_comment.created_user_id = :uid, t.created_date, if (last_comment.marked_as_read = 0, last_comment.created_date, t.created_date)) as created_date'
            ),
            new \CDbExpression(
                'if (last_comment.created_user_id = :uid, t.created_user_id, if (last_comment.marked_as_read = 0, last_comment.created_user_id, t.created_user_id)) as created_user_id'
            ),
        ];

        if ($comments_are_for_user) {
            $criteria->addCondition('last_comment.created_user_id <> :uid');
        } else {
            $criteria->addCondition('last_comment.created_user_id = :uid');
        }

        if (!$comment_must_exist) {
            $criteria->addCondition('last_comment.created_user_id IS NULL', 'OR');
        }

        $criteria->params = [':uid' => $user->id];

        $target_criteria->mergeWith($criteria);
    }

    private function addMarkedReadCondition(CDbCriteria $criteria, bool $marked_read, bool $including_last_comment)
    {
        $criteria->params[':marked_as_read'] = $marked_read ? '1' : '0';

        if ($including_last_comment) {
            $criteria->addCondition('(recipients.marked_as_read = :marked_as_read AND last_comment.marked_as_read IS NULL)');
            $criteria->addCondition('(last_comment.marked_as_read = :marked_as_read AND last_comment.created_user_id <> :uid)', 'OR');
        } else {
            $criteria->addCondition('recipients.marked_as_read = :marked_as_read');
        }
    }

    private function addIsUrgentCondition(CDbCriteria $criteria)
    {
        $criteria->addCondition('urgent <> 0');
    }

    private function addIsQueryCondition(CDbCriteria $criteria)
    {
        $criteria->addCondition('message_type.name = "Query"');
    }

    private function addPrimaryRecipientCondition(CDbCriteria $criteria)
    {
        $criteria->addCondition('recipients.primary_recipient <> 0');
    }

    private function addCopiedCondition(CDbCriteria $criteria)
    {
        $criteria->addCondition('recipients.primary_recipient = 0');
    }
}
