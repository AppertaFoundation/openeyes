<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCoMessaging\models;

use OE\factories\models\traits\HasFactory;

/**
 * This is the model class for table "ophcomessaging_message_recipient"
 *
 * When a message element is created, the recipients associated with that message should not change.
 *
 * The following are the available columns in table:
 *
 * @property int $id

 * @property int $element_id
 * @property int $mailbox_id
 * @property bool $primary_recipient
 */

class OphCoMessaging_Message_Recipient extends \BaseActiveRecordVersioned
{
    use HasFactory;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophcomessaging_message_recipient';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ['element_id, mailbox_id, primary_recipient', 'safe'],
            ['element_id, mailbox_id', 'required'],
            ['mailbox_id', 'validateOnlyOnePrimaryRecipient'],
            ['mailbox_id, primary_recipient', 'validateMailboxAndPrimaryRecipientHaveNotChangedOnUpdate', 'except' => 'insert'],
            ['id, element_id, mailbox_id, primary_recipient', 'safe', 'on' => 'search'],
        ];
    }

    /**
     * @return array relational rules
     */
    public function relations()
    {
        return [
            'element' => [self::BELONGS_TO, Element_OphCoMessaging_Message::class, 'element_id'],
            'mailbox' => [self::BELONGS_TO, Mailbox::class, 'mailbox_id'],
            'createdUser' => [self::BELONGS_TO, \User::class, 'created_user_id'],
            'lastModifiedUser' => [self::BELONGS_TO, \User::class, 'last_modified_user_id'],
        ];
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return \CActiveDataProvider
     */
    public function search()
    {
        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('element_id', $this->element_id, true);
        $criteria->compare('mailbox_id', $this->mailbox_id, true);
        $criteria->compare('primary_recipient', $this->primary_recipient);

        return new \CActiveDataProvider(get_class($this), [
            'criteria' => $criteria,
        ]);
    }

    public function validateOnlyOnePrimaryRecipient($attribute, $params)
    {
        if ($this->primary_recipient === '1' &&
            !empty($this->element->for_the_attention_of) &&
            $this->element->for_the_attention_of->mailbox_id !== $this->mailbox_id
        ) {
            $this->addError('mailbox_id', 'Only one primary recipient is permitted');
        }
    }

    /*
     * When a message is created, its recipients should not change.
     * In practical terms this means the mailbox and primary recipient status
     * need to stay fixed.
     */
    public function validateMailboxAndPrimaryRecipientHaveNotChangedOnUpdate()
    {
        if ($this->isAttributeDirty('mailbox_id')) {
            $this->addError('mailbox_id', 'cannot change the mailbox for a recipient after a message has been created');
        }

        if ($this->isAttributeDirty('primary_recipient')) {
            $this->addError('primary_recipient', 'cannot change the primary recipient status for a recipient after a message has been created');
        }
    }

    public function formattedName($include_user_name_for_shared = false)
    {
        if ($this->mailbox->is_personal || !$include_user_name_for_shared) {
            return $this->mailbox->name;
        } elseif ($include_user_name_for_shared) {
            return $this->lastModifiedUser->getFullName() . ' (' . $this->mailbox->name . ')';
        }
    }

    public function forElement($element_id): self
    {
        $this->getDbCriteria()
             ->mergeWith([
                 'condition' => 'element_id = :element_id',
                 'params' => [':element_id' => $element_id]
             ]);

        return $this;
    }

    public function forReceivedByUser($user_id): self
    {
        $this->getDbCriteria()
            ->mergeWith([
                'with' => [
                    'mailbox.users',
                    'mailbox.teams.users' => ['alias' => 'team']
                ],
                'condition' => '(users_users.user_id = :user_id OR users_team.user_id = :user_id)',
                'params' => [':user_id' => $user_id]
            ]);

        return $this;
    }
}
