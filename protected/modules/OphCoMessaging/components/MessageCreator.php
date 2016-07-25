<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

namespace OEModule\OphCoMessaging\components;


use OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message;
use OEModule\OphCoMessaging\models\OphCoMessaging_Message_MessageType;

class MessageCreator
{
    /**
     * @param $episodeId
     * @param \User $sender
     * @param \User $recipient
     * @param $message
     * @param OphCoMessaging_Message_MessageType $type
     * @param string $source
     * @throws \CDbException
     * @throws \Exception
     */
    public function save($episodeId, \User $sender, \User $recipient, $message, OphCoMessaging_Message_MessageType $type, $source = '')
    {
        $messageEvent = new \Event();
        $messageEvent->episode_id = $episodeId;
        $messageEvent->created_user_id = $messageEvent->last_modified_user_id = $sender->id;
        $messageEvent->event_date = date('Y-m-d');
        $messageEvent->event_type_id = $this->getEventType()->id;
        $messageEvent->is_automated = 1;
        $messageEvent->automated_source = $source;

        if ($messageEvent->save(true, null, true)) {
            $messageEvent->refresh();

            $messageElement = new Element_OphCoMessaging_Message();
            $messageElement->event_id = $messageEvent->id;
            $messageElement->created_user_id = $messageElement->last_modified_user_id = $sender->id;
            $messageElement->for_the_attention_of = $recipient->id;
            $messageElement->message_type_id = $type->id;
            $messageElement->message_text = $message;

            if(!$messageElement->save()){
                throw new \CDbException('Element save failed: ' . print_r($messageElement->getErrors(), true));
            }
        } else {
            throw new \CDbException('Event save failed: ' . print_r($messageEvent->getErrors(), true));
        }
    }
    
    /**
     * @return \CActiveRecord
     * @throws \CDbException
     */
    protected function getEventType()
    {
        $eventType = \EventType::model()->findByAttributes('class_name = ?', array('OphCoMessaging'));

        if(!$eventType){
            throw new \CDbException('Event Type for messaging not found');
        }

        return $eventType;
    }
}