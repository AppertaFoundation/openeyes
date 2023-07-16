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

use EventSubTypeItem;
use OE\factories\models\traits\HasFactory;

/**
 * This is the model class for table "et_ophcomessaging_message".
 *
 * Note that the recipients for a message should not be changed after the message is created.
 * The validator for this rule is the function validateRecipientsHaveNotChangedOnUpdate below.
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $message_type_id
 * @property int $urgent
 * @property string $message_text
 * @property bool $deleted
 * @property int $cc_enabled
 * @property int $sender_mailbox_id
 *
 * The followings are the available model relations:
 * @property \ElementType $element_type
 * @property \EventType $eventType
 * @property \Event $event
 * @property \User $user
 * @property \User $usermodified
 * @property Mailbox $sender
 * @property OphCoMessaging_Message_MessageType $message_type
 * @property OphCoMessaging_Message_Recipient $for_the_attention_of
 * @property OphCoMessaging_Message_Recipient[] $recipients
 * @property OphCoMessaging_Message_Recipient[] $cc_recipients
 * @property OphCoMessaging_Message_Recipient[] $read_by_recipients
 * @property OphCoMessaging_Message_Comment[] $comments
 * @property OphCoMessaging_Message_Comment $last_comment
 */
class Element_OphCoMessaging_Message extends \BaseEventTypeElement
{
    use HasFactory;

    protected $auto_update_relations = true;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophcomessaging_message';
    }

    protected $errorExceptions = array(
        'OEModule_OphCoMessaging_models_Element_OphCoMessaging_Message_for_the_attention_of_user_id' => 'fao-search',
    );

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ['event_id, message_type_id, urgent, message_text, cc_enabled, sender_mailbox_id', 'safe'],
            ['message_type_id, message_text, sender_mailbox_id', 'required'],
            ['recipients', 'validateHasPrimaryRecipient', 'on' => 'insert'],
            ['recipients', 'validateRecipientsHaveNotChangedOnUpdate', 'except' => 'insert'],
            ['id, event_id, message_type_id, urgent, message_text, cc_enabled, sender_mailbox_id', 'safe', 'on' => 'search'],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return [
            'element_type' => [self::HAS_ONE, \ElementType::class, 'id', 'on' => "element_type.class_name='" . get_class($this) . "'"],
            'comments' => [self::HAS_MANY, OphCoMessaging_Message_Comment::class, 'element_id'],
            'last_comment' => [
                self::HAS_ONE,
                OphCoMessaging_Message_Comment::class,
                'element_id',
                'order' => 'created_date DESC, id DESC'
            ],
            'eventType' => [self::BELONGS_TO, \EventType::class, 'event_type_id'],
            'event' => [self::BELONGS_TO, \Event::class, 'event_id'],
            'user' => [self::BELONGS_TO, \User::class, 'created_user_id'],
            'sender' => [self::BELONGS_TO, Mailbox::class, 'sender_mailbox_id'],
            'usermodified' => [self::BELONGS_TO, \User::class, 'last_modified_user_id'],
            'message_type' => [self::BELONGS_TO, OphCoMessaging_Message_MessageType::class, 'message_type_id'],
            'recipients' => [
                self::HAS_MANY,
                OphCoMessaging_Message_Recipient::class,
                'element_id'
            ],
            'cc_recipients' => [
                self::HAS_MANY,
                OphCoMessaging_Message_Recipient::class,
                'element_id',
                'condition' => 'primary_recipient != 1',
            ],
            // instead of restricting to primary_recipient true, we order by the flag so that
            // it will fall back to a cc value. we also sort by id so that it will be consistent
            // if the fallback is applied.
            'for_the_attention_of' => [
                self::HAS_ONE,
                OphCoMessaging_Message_Recipient::class,
                'element_id',
                'order' => 'primary_recipient desc, id asc'
            ],
            'read_by_recipients' => [
                self::HAS_MANY,
                OphCoMessaging_Message_Recipient::class,
                'element_id',
                'condition' => 'marked_as_read = 1'
            ],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_id' => 'Event',
            'message_type_id' => 'Type',
            'urgent' => 'Urgent',
            'message_text' => 'Text',
            'comment_text' => 'Comment',
            'recipients' => 'Recipients',
        ];
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
        $criteria->compare('message_type_id', $this->message_type_id);
        $criteria->compare('urgent', $this->urgent);
        $criteria->compare('message_text', $this->message_text);
        $criteria->compare('cc_enabled', $this->cc_enabled);
        $criteria->order = 'created_date desc';

        return new \CActiveDataProvider(get_class($this), [
            'criteria' => $criteria,
        ]);
    }

    public function validateHasPrimaryRecipient($attribute, $params)
    {
        $primary_recipients =  array_filter($this->$attribute ?? [], function ($recipient) {
            return $recipient->primary_recipient;
        });
        if (!empty($primary_recipients)) {
            return;
        }

        $this->addError('recipients', 'message must be sent to a recipient');
    }

    /*
     * Once a message has beeen created, the recipients are fixed and should not be changed
     * when the message element is being updated.
     */
    public function validateRecipientsHaveNotChangedOnUpdate($attribute, $params)
    {
        $existing_recipients = $this->getExistingRecipientsWithIds();
        $existing_count = count($existing_recipients);

        if (count($this->recipients) === $existing_count) {
            $intersection = array_uintersect(
                $this->recipients,
                $existing_recipients,
                static function ($new, $old) { return (int)$new->id - (int)$old->id; }
            );

            if (count($intersection) === $existing_count) {
                return;
            }
        }

        $this->addError('recipients', 'message cannot have recipients changed after being saved');
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
        foreach ($this->recipients as $recipient) {
            if ($recipient->marked_as_read) {
                return 'read';
            }
        }
        return 'unread';
    }

    public function getPrint_view()
    {
        return 'print_' . $this->getDefaultView();
    }

    /**
     * Get the name and id of the mailboxes that sent messages to the current user
     *
     * @return array
     */
    public function getSenders()
    {
        $senders = \Yii::app()->db->createCommand()
            ->selectDistinct('sender_mailbox.id, sender_mailbox.name')
            ->from('et_ophcomessaging_message msg')
            ->join('mailbox sender_mailbox', 'sender_mailbox.id = msg.sender_mailbox_id')
            ->join('ophcomessaging_message_recipient msgr', 'msgr.element_id = msg.id')
            ->leftJoin('mailbox_user mu', 'mu.mailbox_id = msgr.mailbox_id')
            ->leftJoin('mailbox_team mt', 'mt.mailbox_id = msgr.mailbox_id')
            ->leftJoin('team_user_assign tua', 'tua.team_id = mt.team_id')
            ->where('mu.user_id = :id OR tua.user_id = :id')
            ->bindValues([':id' => \Yii::app()->user->id])
            ->queryAll();

        $sender_names = [];

        foreach ($senders as $sender) {
            $sender_names[$sender['id']] = $sender['name'];
        }

        return $sender_names;
    }

    /**
     *
     * @param Mailbox $mailbox
     * @param User|OEWebUser $user
     * @return bool
     */
    public function getMarkedRead(Mailbox $mailbox, $user): bool
    {
        if (isset($this->last_comment)) {
            return $this->last_comment->marked_as_read !== '0' || $this->last_comment->created_user_id === $user->id;
        }

        foreach ($this->recipients as $recipient) {
            if ($recipient->mailbox_id === $mailbox->id && $recipient->marked_as_read === '0') {
                return false;
            }
        }

        return true;
    }

    public function getReadByLine()
    {
        return implode(', ', array_map(static function ($recipient) {
            return $recipient->formattedName(true);
        }, $this->read_by_recipients));
    }

    protected function beforeSave()
    {
        if ($this->isAttributeDirty('message_type_id')) {
            // unload relation to ensure we get the correct instance during event subtype update
            unset($this->message_type);
        }

        $this->cc_enabled = false;

        foreach ($this->recipients as $recipient) {
            if ($recipient->primary_recipient === '0') {
                $this->cc_enabled = true;
                break;
            }
        }

        return parent::beforeSave();
    }

    protected function afterSave()
    {
        $this->updateEventSubtype();

        parent::afterSave();
    }

    protected function updateEventSubtype()
    {
        $event_subtype_item = $this->getOrInstantiateEventSubtypeItem();

        if (!$this->message_type->event_subtype) {
            if (!$event_subtype_item->getIsNewRecord()) {
                $event_subtype_item->delete();
            }
            return;
        }
        $event_subtype_item->event_subtype = $this->message_type->event_subtype;
        $event_subtype_item->save();
    }

    private function getOrInstantiateEventSubtypeItem()
    {
        $event_subtype_item = EventSubtypeItem::model()->findByAttributes([
            'event_id' => $this->event_id
        ]) ?? new EventSubTypeItem();

        $event_subtype_item->event_id = $this->event_id;

        return $event_subtype_item;
    }

    private function getExistingRecipientsWithIds()
    {
        $criteria = new \CDbCriteria();

        $criteria->select = 'id';
        $criteria->addCondition('element_id = :element_id');
        $criteria->params = [':element_id' => $this->id];

        return OphCoMessaging_Message_Recipient::model()->findAll($criteria);
    }

    public function getAllInvolvedMailboxIds()
    {
        return array_merge(
            array_map(
                function ($recipient) {
                    return $recipient->mailbox_id;
                },
                $this->recipients
            ),
            [$this->sender_mailbox_id]
        );
    }

    public function setReadStatusForMailbox($mailbox, bool $is_read)
    {
        //We are the original sender
        if ((int) $this->sender_mailbox_id === (int) $mailbox->id) {
            $this->last_comment->marked_as_read = $is_read;
            $this->last_comment->save();
        } else {
            $recipient = OphCoMessaging_Message_Recipient::model()->findByAttributes(['element_id' => $this->id, 'mailbox_id' => $mailbox->id]);
            $recipient->marked_as_read = $is_read;
            $recipient->save();
        }
    }

    public function getReadStatusForMailbox($mailbox)
    {
        //We are the original sender
        if ((int) $this->sender_mailbox_id === (int) $mailbox->id) {
            return $this->last_comment->marked_as_read;
        } else {
            $recipient = OphCoMessaging_Message_Recipient::model()->findByAttributes(['element_id' => $this->id, 'mailbox_id' => $mailbox->id]);
            return $recipient->marked_as_read;
        }
    }
}
