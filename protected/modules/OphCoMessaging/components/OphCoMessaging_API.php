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
    const DEFAULT_MESSAGES_FOLDER = 'unread';

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
        $inbox_messages = $this->getInboxMessages($user);
        $sent_messages = $this->getSentMessages($user);
        $urgent_messages = $this->getInboxMessages($user, true);
        $query_messages = $this->getInboxMessages($user, false, true);
        $unread_messages = $this->getInboxMessages($user, false, false, true);

        // Generate the dashboard widget HTML.
        $dashboard_view = \Yii::app()->controller->renderPartial('OphCoMessaging.views.dashboard.message_dashboard', array(
                'inbox' => $inbox_messages['list'],
                'sent' => $sent_messages['list'],
                'urgent' => $urgent_messages['list'],
                'query' => $query_messages['list'],
                'number_inbox_unread' => $inbox_messages['number_unread'],
                'unread' => $unread_messages['list'],
                'number_sent_unread' => $sent_messages['number_unread'],
                'number_urgent_unread' => $urgent_messages['number_unread'],
                'number_query_unread' => $query_messages['number_unread'],
                'default_folder' => $this::DEFAULT_MESSAGES_FOLDER,
                'module_class' => $this->getModuleClass(),
            )
        );

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
    public function updateMessagesCount($user = null) {
        return [
            'number_inbox_unread' => $this->getInboxMessages($user)['number_unread'],
            'number_urgent_unread' => $this->getInboxMessages($user, true)['number_unread'],
            'number_query_unread' => $this->getInboxMessages($user, false, true)['number_unread'],
            'number_sent_unread' => $this->getSentMessages($user)['number_unread']
        ];
    }

    /**
     * Get received messages.
     *
     * @param \CWebUser $user
     * @param bool $urgent_only
     * @param bool $query_only
     * @param bool $unread_only
     * @param bool $read_only
     *
     * @return array data provider and total unread messages
     */
    private function getInboxMessages($user = null, $urgent_only = false, $query_only = false, $unread_only = false)
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
            'event_date' => array('asc' => 't.created_date asc',
                'desc' => 't.created_date desc', ),
            'patient_name' => array('asc' => 'lower(contact.last_name) asc, lower(contact.first_name) asc',
                'desc' => 'lower(contact.last_name) desc, lower(contact.first_name) desc', ),
            'hos_num' => array('asc' => 'patient.hos_num asc',
                'desc' => 'patient.hos_num desc', ),
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
        $params = array(':uid' => $user->id);

        $criteria = new \CDbCriteria();
        $criteria->select = array(
            '*',
            new \CDbExpression('if (last_comment.created_user_id = :uid, t.message_text, if (last_comment.marked_as_read = 0, last_comment.comment_text, t.message_text))  as message_text'),
            new \CDbExpression('if (last_comment.created_user_id = :uid, t.created_date, if (last_comment.marked_as_read = 0, last_comment.created_date, t.created_date))  as created_date'),
            new \CDbExpression('if (last_comment.created_user_id = :uid, t.created_user_id, if (last_comment.marked_as_read = 0, last_comment.created_user_id, t.created_user_id)) as created_user_id'),
        );

        $criteria->addCondition('t.for_the_attention_of_user_id = :uid OR t.created_user_id = :uid');
        $criteria->with = array('event', 'for_the_attention_of_user', 'message_type', 'event.episode', 'event.episode.patient', 'event.episode.patient.contact' , 'last_comment');
        $criteria->together = true;
        if ($from) {
            $criteria->addCondition('DATE(t.created_date) >= :from');
            $params[':from'] = $from;
        }
        if ($to) {
            $criteria->addCondition('DATE(t.created_date) <= :to');
            $params[':to'] = $to;
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
        if ($unread_only) {
            $criteria->addCondition('(t.marked_as_read != "1" AND t.for_the_attention_of_user_id = :uid) OR (last_comment.marked_as_read != "1"
            AND last_comment.created_user_id != :uid)');
        }
        $criteria->params = $params;

        $total_messages = Element_OphCoMessaging_Message::model()->with(array('event'))->count($criteria);

        $dp = new \CActiveDataProvider('OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message',
            array(
                'sort' => $sort,
                'criteria' => $criteria,
                'pagination' => array(
                    'pageSize' => 10,
                    'itemCount' => $total_messages
                ),
            ));

        $unread_criteria = new \CDbCriteria();
        $unread_criteria->addCondition('(t.marked_as_read != 1  AND t.for_the_attention_of_user_id = :uid)
         OR (last_comment.marked_as_read != 1  AND last_comment.created_user_id != :uid)');
        $unread_criteria->mergeWith($criteria);
        $number_unread_messages = Element_OphCoMessaging_Message::model()->with(array('event'))->count($unread_criteria);

        return array(
            'list' => $dp,
            'number_unread' => $number_unread_messages,
        );
    }

    /**
     * Get sent messages.
     *
     * @param \CWebUser $user
     *
     * @return array data provider and total unread messages
     */
    private function getSentMessages($user = null)
    {
        if ($user === null) {
            $user = \Yii::app()->user;
        }

        $sort = new \CSort();

        $sort->attributes = array(
            'priority' => array('asc' => 'urgent asc',
                'desc' => 'urgent desc', ),
            'event_date' => array('asc' => 't.created_date asc',
                'desc' => 't.created_date desc', ),
            'patient_name' => array('asc' => 'lower(contact.last_name) asc, lower(contact.first_name) asc',
                'desc' => 'lower(contact.last_name) desc, lower(contact.first_name) desc', ),
            'hos_num' => array('asc' => 'patient.hos_num asc',
                'desc' => 'patient.hos_num desc', ),
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
        $params = array(':uid' => $user->id);

        $criteria = new \CDbCriteria();
        $criteria->select = array('*');

        $criteria->addCondition('t.created_user_id = :uid');

        $criteria->with = array('event', 'for_the_attention_of_user', 'message_type', 'event.episode', 'event.episode.patient', 'event.episode.patient.contact');
        $criteria->together = true;
        if ($from) {
            $criteria->addCondition('DATE(t.created_date) >= :from');
            $params[':from'] = $from;
        }
        if ($to) {
            $criteria->addCondition('DATE(t.created_date) <= :to');
            $params[':to'] = $to;
        }

        $criteria->addCondition('event.deleted = 0');
        $criteria->addCondition('episode.deleted = 0');

        $criteria->params = $params;

        $total_messages = Element_OphCoMessaging_Message::model()->with(array('event'))->count($criteria);

        $dataProvider = new \CActiveDataProvider('OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message',
            array(
                'sort' => $sort,
                'criteria' => $criteria,
                'pagination' => array(
                    'pageSize' => 10,
                    'itemCount' => $total_messages
                ),
            ));


        $unread_criteria = new \CDbCriteria();
        $unread_criteria->addCondition('t.marked_as_read != 1');

        $unread_criteria->mergeWith($criteria);
        $number_unread_messages = Element_OphCoMessaging_Message::model()->with(array('event'))->count($unread_criteria);

        return array(
            'list' => $dataProvider,
            'number_unread' => $number_unread_messages
        );
    }
}