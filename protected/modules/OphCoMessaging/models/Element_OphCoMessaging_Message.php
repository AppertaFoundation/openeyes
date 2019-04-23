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

namespace OEModule\OphCoMessaging\models;

/**
 * This is the model class for table "et_ophcomessaging_message".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $for_the_attention_of_user_id
 * @property int $message_type_id
 * @property int $urgent
 * @property string $message_text
 * @property int $marked_as_read
 *
 * The followings are the available model relations:
 * @property \ElementType $element_type
 * @property \EventType $eventType
 * @property \Event $event
 * @property \User $user
 * @property \User $usermodified
 * @property OphCoMessaging_Message_MessageType $message_type
 */
class Element_OphCoMessaging_Message extends \BaseEventTypeElement
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return Element_OphCoMessaging_Message static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophcomessaging_message';
    }

    protected $errorExceptions = array(
        'OEModule_OphCoMessaging_models_Element_OphCoMessaging_Message_for_the_attention_of_user_id' => 'fao-field',
    );

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('event_id, for_the_attention_of_user_id, message_type_id, urgent, message_text, marked_as_read', 'safe'),
            array('for_the_attention_of_user_id, message_type_id, message_text, ', 'required'),
            array('id, event_id, for_the_attention_of_user_id, message_type_id, urgent, message_text, marked_as_read', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'element_type' => array(self::HAS_ONE, 'ElementType', 'id', 'on' => "element_type.class_name='".get_class($this)."'"),
            'comments' => array(self::HAS_MANY, 'OEModule\\OphCoMessaging\\models\\OphCoMessaging_Message_Comment', 'element_id'),
            'last_comment' => array(self::HAS_ONE,'OEModule\\OphCoMessaging\\models\\OphCoMessaging_Message_Comment', 'element_id',  'order' => 'created_date DESC'),
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'for_the_attention_of_user' => array(self::BELONGS_TO, 'User', 'for_the_attention_of_user_id'),
            'message_type' => array(self::BELONGS_TO, 'OEModule\\OphCoMessaging\\models\\OphCoMessaging_Message_MessageType', 'message_type_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'event_id' => 'Event',
            'for_the_attention_of_user_id' => 'For the attention of',
            'message_type_id' => 'Type',
            'urgent' => 'Urgent',
            'marked_as_read' => 'Mark as read',
            'message_text' => 'Text',
            'comment_text' => 'Comment',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return \CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('for_the_attention_of_user_id', $this->for_the_attention_of_user_id);
        $criteria->compare('message_type_id', $this->message_type_id);
        $criteria->compare('urgent', $this->urgent);
        $criteria->compare('marked_as_read', $this->marked_as_read);
        $criteria->compare('message_text', $this->message_text);
        $criteria->order = 'created_date desc';

        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function getMessageDate()
    {
        return $this->event->event_date;
    }

    /**
     * Info Text set based on the current attributes.
     */
    public function getInfotext()
    {
        return $this->marked_as_read ? 'read' : 'unread';
    }

    public function getPrint_view() {
        return 'print_'.$this->getDefaultView();
    }
}