<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCoMessaging\components;

use OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message;

class OphCoMessaging_API extends \BaseAPI
{
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
     * Get received messages.
     * 
     * @param CWebUser $user
     *
     * @return array title and content of for the widget
     */
    public function getInboxMessages($user = null)
    {
        $read_check = (\Yii::app()->request->getQuery('OphCoMessaging_read', '0') === '1');

        if (is_null($user)) {
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
            'dob' => array('asc' => 'patient.dob asc',
                            'desc' => 'patient.dob desc', ),
            'user' => array('asc' => 'lower(for_the_attention_of_user.last_name) asc, lower(for_the_attention_of_user.first_name) asc',
                            'desc' => 'lower(for_the_attention_of_user.last_name) desc, lower(for_the_attention_of_user.first_name) desc', ),
        );

        $sort->defaultOrder = 'event_date desc';

        $from = \Yii::app()->request->getQuery('OphCoMessaging_from', '');
        $to = \Yii::app()->request->getQuery('OphCoMessaging_to', '');
        $params = array(':uid' => $user->id, ':read' => $read_check);

        $criteria = new \CDbCriteria();
        $criteria->select = array(
            '*',
            new \CDbExpression('IF(comment.created_user_id = :uid, t.message_text, IF(comment.marked_as_read = 0, comment.comment_text, t.message_text))  as message_text'),
            new \CDbExpression('IF(comment.created_user_id = :uid, t.created_date, IF(comment.marked_as_read = 0, comment.created_date, t.created_date))  as created_date'),
            new \CDbExpression('IF(comment.created_user_id = :uid, t.created_user_id, IF(comment.marked_as_read = 0, comment.created_user_id, t.created_user_id)) as created_user_id'),
        );

        $criteria->addCondition('t.for_the_attention_of_user_id = :uid AND t.marked_as_read = :read');
        $criteria->addCondition('t.created_user_id = :uid AND comment.marked_as_read = 0', 'OR');
        $criteria->join = 'LEFT JOIN ophcomessaging_message_comment AS comment ON t.id = comment.element_id';
        $criteria->with = array('event', 'for_the_attention_of_user', 'message_type', 'event.episode', 'event.episode.patient', 'event.episode.patient.contact');
        $criteria->together = true;
        if ($from) {
            $criteria->addCondition('DATE(t.created_date) >= :from');
            $params[':from'] = \Helper::convertNHS2MySQL($from);
        }
        if ($to) {
            $criteria->addCondition('DATE(t.created_date) <= :to');
            $params[':to'] = \Helper::convertNHS2MySQL($to);
        }

        $criteria->addCondition('event.deleted = 0');
        $criteria->addCondition('episode.deleted = 0');

        $criteria->params = $params;

        $dp = new \CActiveDataProvider('OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message',
            array(
                'sort' => $sort,
                'criteria' => $criteria,
                'pagination' => array(
                    'pageSize' => 10,
                ),
            ));

        $messages = Element_OphCoMessaging_Message::model()->with(array('event'))->findAll($criteria);

        \Yii::app()->getAssetManager()->registerCssFile('module.css', 'application.modules.OphCoMessaging.assets.css');

        $inbox_view = \Yii::app()->controller->renderPartial('OphCoMessaging.views.inbox.grid', array(
                            'module_class' => $this->getModuleClass(),
                            'messages' => $messages,
                            'dp' => $dp,
                            'read_check' => $read_check,
                        ), true);

        $is_open = ($read_check && $dp->totalItemCount > 0) ? true : false;

        $cookie_name = \Yii::app()->user->id.'-inbox-container-state';

        if (\Yii::app()->request->cookies->contains($cookie_name)) {

            //unread messages
            if (!$read_check) {
                //always open the widget if there are unread messages
                $is_open = $dp->totalItemCount > 0 ? true : (bool) \Yii::app()->request->cookies[$cookie_name]->value;
            } else {
                // read messages
                $is_open = (bool) \Yii::app()->request->cookies[$cookie_name]->value;
            }
        }
        \Yii::app()->request->cookies[$cookie_name] = new \CHttpCookie($cookie_name, (int) $is_open);

        return array(
            'title' => 'Messages'.(!$read_check && $dp->totalItemCount ? " [{$dp->totalItemCount}]" : ''),
            'content' => $inbox_view,
            'options' => array(
                'container-id' => \Yii::app()->user->id.'-inbox-container',
                'js-toggle-open' => $is_open,
            ),
        );
    }

    /**
     * Get sent messages.
     * 
     * @param CWebUser $user
     *
     * @return array title and content for the widget
     */
    public function getSentMessages($user = null)
    {
        if (is_null($user)) {
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
            'dob' => array('asc' => 'patient.dob asc',
                            'desc' => 'patient.dob desc', ),
            'user' => array('asc' => 'lower(for_the_attention_of_user.last_name) asc, lower(for_the_attention_of_user.first_name) asc',
                            'desc' => 'lower(for_the_attention_of_user.last_name) desc, lower(for_the_attention_of_user.first_name) desc', ),
        );

        $sort->defaultOrder = 'event_date desc';

        $from = \Yii::app()->request->getQuery('OphCoMessaging_sent_from', '');
        $to = \Yii::app()->request->getQuery('OphCoMessaging_sent_to', '');
        $params = array(':uid' => $user->id);

        $criteria = new \CDbCriteria();
        $criteria->select = array('*');

        $criteria->addCondition('t.created_user_id = :uid');

        $criteria->with = array('event', 'for_the_attention_of_user', 'message_type', 'event.episode', 'event.episode.patient', 'event.episode.patient.contact');
        $criteria->together = true;
        if ($from) {
            $criteria->addCondition('DATE(t.created_date) >= :from');
            $params[':from'] = \Helper::convertNHS2MySQL($from);
        }
        if ($to) {
            $criteria->addCondition('DATE(t.created_date) <= :to');
            $params[':to'] = \Helper::convertNHS2MySQL($to);
        }

        $criteria->addCondition('event.deleted = 0');
        $criteria->addCondition('episode.deleted = 0');

        $criteria->params = $params;

        $dataProvider = new \CActiveDataProvider('OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message',
            array(
                'sort' => $sort,
                'criteria' => $criteria,
                'pagination' => array(
                    'pageSize' => 10,
                ),
            ));

        $messages = Element_OphCoMessaging_Message::model()->findAll($criteria);

        \Yii::app()->getAssetManager()->registerCssFile('module.css', 'application.modules.OphCoMessaging.assets.css');

        $inbox_view = \Yii::app()->controller->renderPartial('OphCoMessaging.views.sent.grid', array(
            'module_class' => $this->getModuleClass(),
            'messages' => $messages,
            'dataProvider' => $dataProvider,
        ), true);

        return array(
            'title' => 'Sent Messages',
            'content' => $inbox_view,
            'options' => array(
                'container-id' => \Yii::app()->user->id.'-sent-container',
                'js-toggle-open' => \Yii::app()->request->cookies->contains(\Yii::app()->user->id.'-sent-container-state') ? (bool) \Yii::app()->request->cookies[\Yii::app()->user->id.'-sent-container-state']->value : false,
            ),
        );
    }
}
