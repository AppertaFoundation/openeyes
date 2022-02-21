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

class OphCoMessaging_API extends \BaseAPI
{
    const DEFAULT_MESSAGES_FOLDER = 'unread_all';

    public function getMenuItem()
    {
        $user = \Yii::app()->user;
        $criteria = new \CDbCriteria();
        $criteria->addCondition('for_the_attention_of_user_id = :uid');
        $criteria->addCondition('marked_as_read = :read');
        $criteria->params = array(':uid' => $user->id, ':read' => false);
        $criteria->order = 'created_date asc';

        $messages = Element_OphCoMessaging_Message::model()->findAll($criteria);
        $containsUrgentMessage = false;
        foreach ($messages as $message) {
            if ($message['urgent']) {
                $containsUrgentMessage = true;
            }
        }

        return array(
            'title' => 'Messages',
            'uri' => '/OphCoMessaging/Inbox',
            'messageCount' => count($messages),
            'containsUrgentMessage' => $containsUrgentMessage,
        );
    }

    /**
     * @return array Dashboard widget content and options (No need for title).
     */
    public function getMessages($user = null)
    {
        $unread_all_messages = $this->getInboxMessages($user, false);
        $unread_urgent_messages = $this->getInboxMessages($user, false, true);
        $unread_query_messages = $this->getInboxMessages($user, false, false, true);
        $unread_received_messages = $this->getInboxMessages($user, false, false, false, false);
        $unread_copied_messages = $this->getInboxMessages($user, false, false, false, true);
        $unread_replied_messages = $this->getUnreadQueryReplies($user);
        $read_all_messages = $this->getInboxMessages($user, true);
        $read_urgent_messages = $this->getInboxMessages($user, true, true);
        $read_received_messages = $this->getInboxMessages($user, true, false, false, false);
        $read_copied_messages = $this->getInboxMessages($user, true, false, false, true);
        $sent_all_messages = $this->getSentMessages($user);
        $sent_unreplied_messages = $this->getSentMessages($user, false, true);
        $sent_unread_messages = $this->getSentMessages($user, true);

        // Generate the dashboard widget HTML.
        $dashboard_view = \Yii::app()->controller->renderPartial('OphCoMessaging.views.dashboard.message_dashboard', array(
                'unread_all' => $unread_all_messages['list'],
                'unread_urgent' => $unread_urgent_messages['list'],
                'unread_query' => $unread_query_messages['list'],
                'unread_received' => $unread_received_messages['list'],
                'unread_copied' => $unread_copied_messages['list'],
                'unread_replies' => $unread_replied_messages['list'],
                'read_all' => $read_all_messages['list'],
                'read_urgent' => $read_urgent_messages['list'],
                'read_received' => $read_received_messages['list'],
                'read_copied' => $read_copied_messages['list'],
                'sent_all' => $sent_all_messages['list'],
                'sent_unreplied' => $sent_unreplied_messages['list'],
                'sent_unread' => $sent_unread_messages['list'],
                'number_unread_all' => $unread_all_messages['message_count'],
                'number_unread_urgent' => $unread_urgent_messages['message_count'],
                'number_unread_query' => $unread_query_messages['message_count'],
                'number_unread_received' => $unread_received_messages['message_count'],
                'number_unread_copied' => $unread_copied_messages['message_count'],
                'number_unread_replies' => $unread_replied_messages['message_count'],
                'number_read_all' => $read_all_messages['message_count'],
                'number_read_urgent' => $read_urgent_messages['message_count'],
                'number_read_received' => $read_received_messages['message_count'],
                'number_read_copied' => $read_copied_messages['message_count'],
                'number_sent_all' => $sent_all_messages['message_count'],
                'number_sent_unreplied' => $sent_unreplied_messages['message_count'],
                'number_sent_unread' => $sent_unread_messages['message_count'],
                'default_folder' => $this::DEFAULT_MESSAGES_FOLDER,
                'module_class' => $this->getModuleClass(),
            ));

        return array(
            'content' => $dashboard_view,
            'options' => array(
                'container-id' => \Yii::app()->user->id.'-dashboard-container',
            ),
        );
    }

    /**
     * @param null $user
     * @return array - list with counts of all unread messages for each folder
     */
    public function updateMessagesCount($user = null)
    {
        return [
            'number_unread_all' => $this->getInboxMessages($user, false)['message_count'],
            'number_unread_urgent' => $this->getInboxMessages($user, false, true)['message_count'],
            'number_unread_query' => $this->getInboxMessages($user, false, false, true)['message_count'],
            'number_unread_received' => $this->getInboxMessages($user, false, false, false, false)['message_count'],
            'number_unread_copied' => $this->getInboxMessages($user, false, false, false, true)['message_count'],
            'number_unread_replies' => $this->getUnreadQueryReplies($user)['message_count'],
            'number_read_all' => $this->getInboxMessages($user, true)['message_count'],
            'number_read_urgent' => $this->getInboxMessages($user, true, true)['message_count'],
            'number_read_received' => $this->getInboxMessages($user, true, false, false, false)['message_count'],
            'number_read_copied' => $this->getInboxMessages($user, true, false, false, true)['message_count'],
            'number_sent_all' => $this->getSentMessages($user)['message_count'],
            'number_sent_unreplied' => $this->getSentMessages($user, false, true)['message_count'],
            'number_sent_unread' => $this->getSentMessages($user, true)['message_count'],
        ];
    }

    /**
     * Get received messages
     *
     * @param \CWebUser $user
     * @param bool $marked_as_read
     * @param bool $urgent_only
     * @param bool $query_only
     * @param null $copied_only
     * @return array
     * @throws \CException
     */
    private function getInboxMessages($user = null, $marked_as_read = false, $urgent_only = false, $query_only = false, $copied_only = null)
    {
        if ($user === null) {
            $user = \Yii::app()->user;
        }

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
            'patient_name' => array('asc' => 'lower(contact.last_name) asc, lower(contact.first_name) asc',
                'desc' => 'lower(contact.last_name) desc, lower(contact.first_name) desc', ),
            'age' => array('asc' => 'patient.dob desc',
                'desc' => 'patient.dob asc', ),
            'user' => array('asc' => 'lower(for_the_attention_of_user.last_name) asc, lower(for_the_attention_of_user.first_name) asc',
                'desc' => 'lower(for_the_attention_of_user.last_name) desc, lower(for_the_attention_of_user.first_name) desc', ),
            'gender' => array('asc' => 'patient.gender asc',
                'desc' => 'patient.gender desc', ),
        );

        $sort->defaultOrder = 'event_date desc';

        $from = \Yii::app()->request->getQuery('OphCoMessaging_from', '');
        $to = \Yii::app()->request->getQuery('OphCoMessaging_to', '');
        $sender = \Yii::app()->request->getQuery('OphCoMessaging_Search_Sender', '');
        $messageType = \Yii::app()->request->getQuery('OphCoMessaging_Search_MessageType', '');
        $messageContent = \Yii::app()->request->getQuery('OphCoMessaging_Search', '');
        $params = array(':uid' => $user->id);

        if ($marked_as_read === true) {
            $params[':marked_as_read'] = '1';
        } else {
            $params[':marked_as_read'] = '0';
        }

        $criteria = new \CDbCriteria();
        $criteria->select = array(
            '*',
            new \CDbExpression('if (last_comment.created_user_id = :uid, t.created_date, if (last_comment.marked_as_read = 0, last_comment.created_date, t.created_date))  as created_date'),
            new \CDbExpression('if (last_comment.created_user_id = :uid, t.created_user_id, if (last_comment.marked_as_read = 0, last_comment.created_user_id, t.created_user_id)) as created_user_id'),
        );

        $criteria->addCondition('t.for_the_attention_of_user_id = :uid OR copyto_users.user_id = :uid OR t.created_user_id = :uid');
        $criteria->with = array('event', 'for_the_attention_of_user', 'user', 'message_type', 'event.episode', 'event.episode.patient', 'event.episode.patient.contact' , 'last_comment', 'copyto_users');
        $criteria->together = true;

        if ($from) {
            $criteria->addCondition('DATE(t.created_date) >= :from');
            $params[':from'] = date("Y-m-d", strtotime($from));
        }
        if ($to) {
            $criteria->addCondition('DATE(t.created_date) <= :to');
            $params[':to'] = date("Y-m-d", strtotime($to));
        }
        if ($sender) {
            $criteria->addCondition('t.created_user_id = :sender');
            $params[':sender'] = $sender;
        }
        if ($messageType) {
            $criteria->addCondition('t.message_type_id = :messageType');
            $params[':messageType'] = $messageType;
        }
        if ($messageContent) {
            $criteria->addCondition('LOWER(t.message_text) LIKE :messageContent');
            $params[':messageContent'] = '%'.strtolower(strtr($messageContent, array('%' => '\%'))).'%';
        }

        $criteria->addCondition('event.deleted = 0');
        $criteria->addCondition('episode.deleted = 0');

        if ($urgent_only) {
            $criteria->addCondition('t.urgent != 0');
        }
        if ($query_only) {
            $message_type_query_id = \Yii::app()->db->createCommand()
                ->select('id')
                ->from('ophcomessaging_message_message_type')
                ->where('name = :name', array(':name' => 'Query'))->queryScalar();

            $criteria->addCondition("t.message_type_id = :message_type_id");
            $params[':message_type_id'] = $message_type_query_id;
        }
        if (isset($copied_only)) {
            if ($copied_only) {
                $criteria->addCondition('copyto_users.marked_as_read = :marked_as_read AND copyto_users.user_id = :uid');
            } else {
                $criteria->addCondition('(t.marked_as_read = :marked_as_read AND t.for_the_attention_of_user_id = :uid) OR
                (last_comment.marked_as_read = :marked_as_read AND last_comment.created_user_id != :uid AND t.for_the_attention_of_user_id = :uid)');
            }
        } else {
            $criteria->addCondition('(t.marked_as_read = :marked_as_read AND t.for_the_attention_of_user_id = :uid) OR
            (last_comment.marked_as_read = :marked_as_read AND last_comment.created_user_id != :uid AND (t.for_the_attention_of_user_id = :uid OR t.created_user_id = :uid))
            OR (copyto_users.marked_as_read = :marked_as_read AND copyto_users.user_id = :uid)');
        }

        $criteria->params = $params;

        $total_messages = Element_OphCoMessaging_Message::model()->with(array('event'))->count($criteria);

        $dp = new \CActiveDataProvider(
            'OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message',
            array(
                'sort' => $sort,
                'criteria' => $criteria,
                'pagination' => array(
                    'pageSize' => 30,
                    'itemCount' => $total_messages
                ),
            )
        );

        return array(
            'list' => $dp,
            'message_count' => $total_messages,
        );
    }

    /**
     * Get sent messages
     *
     * @param \CWebUser $user
     * @param bool $unread_only
     * @param bool $unreplied_only
     * @return array
     * @throws \CException
     */
    private function getSentMessages($user = null, $unread_only = false, $unreplied_only = false)
    {
        if ($user === null) {
            $user = \Yii::app()->user;
        }

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
            'patient_name' => array('asc' => 'lower(contact.last_name) asc, lower(contact.first_name) asc',
                'desc' => 'lower(contact.last_name) desc, lower(contact.first_name) desc', ),
            'hos_num' => array('asc' => 'patient.localIdentifiers asc',
                'desc' => 'patient.localIdentifiers desc', ),
            'age' => array('asc' => 'patient.dob desc',
                'desc' => 'patient.dob asc', ),
            'user' => array('asc' => 'lower(for_the_attention_of_user.last_name) asc, lower(for_the_attention_of_user.first_name) asc',
                'desc' => 'lower(for_the_attention_of_user.last_name) desc, lower(for_the_attention_of_user.first_name) desc', ),
        );

        $sort->defaultOrder = 'event_date desc';

        $from = \Yii::app()->request->getQuery('OphCoMessaging_from', '');
        $to = \Yii::app()->request->getQuery('OphCoMessaging_to', '');
        $messageType = \Yii::app()->request->getQuery('OphCoMessaging_Search_MessageType', '');
        $messageContent = \Yii::app()->request->getQuery('OphCoMessaging_Search', '');
        $params = array(':uid' => $user->id);

        $criteria = new \CDbCriteria();
        $criteria->select = array('*');

        $criteria->addCondition('t.created_user_id = :uid');

        $criteria->with = array('event', 'for_the_attention_of_user', 'message_type', 'event.episode', 'event.episode.patient', 'event.episode.patient.contact');
        $criteria->together = true;
        if ($from) {
            $criteria->addCondition('DATE(t.created_date) >= :from');
            $params[':from'] = date("Y-m-d", strtotime($from));
        }
        if ($to) {
            $criteria->addCondition('DATE(t.created_date) <= :to');
            $params[':to'] = date("Y-m-d", strtotime($to));
        }
        if ($messageType) {
            $criteria->addCondition('t.message_type_id = :messageType');
            $params[':messageType'] = $messageType;
        }
        if ($messageContent) {
            $criteria->addCondition('LOWER(t.message_text) LIKE :messageContent');
            $params[':messageContent'] = '%'.strtolower(strtr($messageContent, array('%' => '\%'))).'%';
        }

        $criteria->addCondition('event.deleted = 0');
        $criteria->addCondition('episode.deleted = 0');

        if ($unread_only) {
            $criteria->addCondition('t.marked_as_read = 0');
        }
        if ($unreplied_only) {
            $message_type_query_id = \Yii::app()->db->createCommand()
                ->select('id')
                ->from('ophcomessaging_message_message_type')
                ->where('name = :name', array(':name' => 'Query'))->queryScalar();

            $criteria->addCondition("t.message_type_id = :message_type_id");
            $params[':message_type_id'] = $message_type_query_id;
            $criteria->addCondition('t.id NOT IN (SELECT DISTINCT element_id FROM ophcomessaging_message_comment WHERE element_id = t.id)');
        }

        $criteria->params = $params;

        $total_messages = Element_OphCoMessaging_Message::model()->with(array('event'))->count($criteria);

        $dp = new \CActiveDataProvider(
            'OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message',
            array(
                'sort' => $sort,
                'criteria' => $criteria,
                'pagination' => array(
                    'pageSize' => 30,
                    'itemCount' => $total_messages
                ),
            )
        );

        return array(
            'list' => $dp,
            'message_count' => $total_messages,
        );
    }

    /**
     * Get replies of the queries
     *
     * @param \CWebUser $user
     * @return array
     * @throws \CException
     */
    private function getUnreadQueryReplies($user = null)
    {
        if ($user === null) {
            $user = \Yii::app()->user;
        }

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

        $from = \Yii::app()->request->getQuery('OphCoMessaging_from', '');
        $to = \Yii::app()->request->getQuery('OphCoMessaging_to', '');
        $messageType = \Yii::app()->request->getQuery('OphCoMessaging_Search_MessageType', '');
        $messageContent = \Yii::app()->request->getQuery('OphCoMessaging_Search', '');
        $params = array(':uid' => $user->id);

        $criteria = new \CDbCriteria();
        $criteria->select = array(
            '*',
            new \CDbExpression('if (last_comment.created_user_id = :uid, t.created_date, if (last_comment.marked_as_read = 0, last_comment.created_date, t.created_date))  as created_date'),
            new \CDbExpression('if (last_comment.created_user_id = :uid, t.created_user_id, if (last_comment.marked_as_read = 0, last_comment.created_user_id, t.created_user_id)) as created_user_id'),
        );

        $criteria->addCondition('t.created_user_id = :uid OR t.for_the_attention_of_user_id = :uid');

        $criteria->with = array('event', 'for_the_attention_of_user', 'message_type', 'event.episode', 'event.episode.patient', 'event.episode.patient.contact', 'comments', 'last_comment');
        $criteria->together = true;
        if ($from) {
            $criteria->addCondition('DATE(t.created_date) >= :from');
            $params[':from'] = date("Y-m-d", strtotime($from));
        }
        if ($to) {
            $criteria->addCondition('DATE(t.created_date) <= :to');
            $params[':to'] = date("Y-m-d", strtotime($to));
        }
        if ($messageType) {
            $criteria->addCondition('t.message_type_id = :messageType');
            $params[':messageType'] = $messageType;
        }
        if ($messageContent) {
            $criteria->addCondition('LOWER(t.message_text) LIKE :messageContent');
            $params[':messageContent'] = '%'.strtolower(strtr($messageContent, array('%' => '\%'))).'%';
        }

        $criteria->addCondition('event.deleted = 0');
        $criteria->addCondition('episode.deleted = 0');

        $message_type_query_id = \Yii::app()->db->createCommand()
            ->select('id')
            ->from('ophcomessaging_message_message_type')
            ->where('name = :name', array(':name' => 'Query'))->queryScalar();

        $criteria->addCondition("t.message_type_id = :message_type_id");
        $params[':message_type_id'] = $message_type_query_id;

        $criteria->addCondition('last_comment.marked_as_read = 0 AND last_comment.created_user_id != :uid');

        $criteria->params = $params;

        $total_messages = Element_OphCoMessaging_Message::model()->with(array('event'))->count($criteria);

        $dp = new \CActiveDataProvider(
            'OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message',
            array(
                'sort' => $sort,
                'criteria' => $criteria,
                'pagination' => array(
                    'pageSize' => 30,
                    'itemCount' => $total_messages
                ),
            )
        );

        return array(
            'list' => $dp,
            'message_count' => $total_messages,
        );
    }
}
