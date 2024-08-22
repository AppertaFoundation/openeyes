<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCoMessaging\components;

use OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message;
use OEModule\OphCoMessaging\models\OphCoMessaging_Message_MessageType;
use OEModule\OphCoMessaging\models\OphCoMessaging_Message_Recipient;

/**
 * Class MessageCreator.
 *
 * Create a Message event
 */
class MessageCreator
{
    /**
     * @var \Episode
     */
    protected $episode;

    /**
     * @var \User
     */
    protected $sender;

    /**
     * @var \User
     */
    protected $recipient;

    /**
     * @var OphCoMessaging_Message_MessageType
     */
    protected $type;

    /**
     * @var string
     */
    protected $messageTemplate = '';

    /**
     * @var array
     */
    protected $messageData = array();

    /**
     * @var int
     */
    protected $institution_id;

    /**
     * @var int
     */
    protected $site_id;

    /**
     * @param $template
     */
    public function setMessageTemplate($template)
    {
        if (\Yii::getPathOfAlias($template) && is_readable(\Yii::getPathOfAlias($template) . '.php')) {
            $this->messageTemplate = $template;
        }
    }

    /**
     * @param array $data
     */
    public function setMessageData(array $data)
    {
        $this->messageData = $data;
    }

    /**
     * MessageCreator constructor.
     *
     * @param \Episode                           $episode
     * @param \User                              $sender
     * @param \User                              $recipient
     * @param OphCoMessaging_Message_MessageType $type
     * @param int                                $institution_id
     * @param ?int                                $site_id
     */
    public function __construct(\Episode $episode, \User $sender, \User $recipient, OphCoMessaging_Message_MessageType $type, int $institution_id, ?int $site_id)
    {
        $this->episode = $episode;
        $this->sender = $sender;
        $this->recipient = $recipient;
        $this->type = $type;
        $this->institution_id = $institution_id;
        $this->site_id = $site_id;
    }

    /**
     * @param $message
     * @param OphCoMessaging_Message_MessageType $type
     * @param string                             $source
     * @param string                             $alertAddress
     *
     * @return Element_OphCoMessaging_Message
     *
     * @throws \CDbException
     * @throws \Exception
     */
    public function save($message = '', $source = '')
    {
        $messageEvent = new \Event("automatic");
        $messageEvent->institution_id = $this->institution_id;
        $messageEvent->site_id = $this->site_id;
        $messageEvent->episode_id = $this->episode->id;
        $messageEvent->created_user_id = $messageEvent->last_modified_user_id = $this->sender->id;
        $messageEvent->event_date = date('Y-m-d');
        $messageEvent->event_type_id = $this->getEventType()->id;
        $messageEvent->is_automated = 1;
        $messageEvent->automated_source = $source;

        if ($messageEvent->save(true, null, true)) {
            $messageEvent->refresh();

            $messageElement = new Element_OphCoMessaging_Message();
            $messageElement->event_id = $messageEvent->id;
            $messageElement->created_user_id = $messageElement->last_modified_user_id = $this->sender->id;
            $messageElement->message_type_id = $this->type->id;
            $messageElement->sender_mailbox_id = $this->sender->personalMailbox->id ?? null;

            $message_recipient = new OphCoMessaging_Message_Recipient();
            $message_recipient->mailbox_id = $this->recipient->personalMailbox->id;
            $message_recipient->primary_recipient = true;
            $messageElement->recipients = [$message_recipient];

            if ($this->messageTemplate) {
                $patient_identifier = \PatientIdentifierHelper::getIdentifierForPatient(
                    \SettingMetadata::model()->getSetting('display_primary_number_usage_code'),
                    $this->episode->patient->id,
                    $messageEvent->institution_id,
                    $messageEvent->site_id
                );
                $this->messageData['patient_identifier'] = $patient_identifier;
                $messageElement->message_text = $this->renderTemplate();
            } else {
                $messageElement->message_text = $message;
            }

            if (!$messageElement->save()) {
                throw new \CDbException('Element save failed: ' . print_r($messageElement->getErrors(), true));
            }
            $messageElement->refresh();
            $message_recipient->element_id = $messageElement->id;
            if (!$message_recipient->save()) {
                throw new \CDbException('Message recipient save failed: ' . print_r($message_recipient->getErrors(), true));
            }
        } else {
            throw new \CDbException('Event save failed: ' . print_r($messageEvent->getErrors(), true));
        }

        return $messageElement;
    }

    /**
     * @return \CActiveRecord
     *
     * @throws \CDbException
     */
    protected function getEventType()
    {
        $eventType = \EventType::model()->findByAttributes(array('class_name' => 'OphCoMessaging'));

        if (!$eventType) {
            throw new \CDbException('Event Type for messaging not found');
        }

        return $eventType;
    }

    /**
     * @return string
     */
    protected function renderTemplate()
    {
        $controller = new \CController('message');

        return $controller->renderInternal(\Yii::getPathOfAlias($this->messageTemplate) . '.php', $this->messageData, true);
    }

    /**
     * Sends an email alert when a message is created
     *
     * @param $recipients
     * @param $subject
     * @param $content
     *
     * @return mixed
     */
    public function emailAlert(array $recipients, $subject, $content)
    {
        $message = \Yii::app()->mailer->newMessage();
        $from = (isset(\Yii::app()->params['from_address'])) ? \Yii::app()->params['from_address'] : 'noreply@openeyes.org.uk';
        $message->setFrom(array($from => 'OpenEyes Alerts'));
        $message->setTo($recipients);
        $message->setSubject($subject);
        $message->setBody($content);

        try {
            return \Yii::app()->mailer->sendMessage($message);
        } catch (\Exception $e) {
            return false;
        }
    }
}
