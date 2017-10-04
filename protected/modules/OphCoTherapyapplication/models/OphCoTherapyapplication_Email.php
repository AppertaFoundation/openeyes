<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class OphCoTherapyapplication_Email extends BaseActiveRecordVersioned
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ophcotherapya_email';
    }

    public function rules()
    {
        return array(
            array('eye_id', 'in', 'range' => array(1, 2)),
        );
    }

    public function defaultScope()
    {
        return array(
            'order' => $this->getTableAlias(false, false) . '.created_date desc',
        );
    }

    public function scopes()
    {
        return array(
            'leftEye' => array('condition' => $this->getTableAlias(false, false) . '.eye_id = '.Eye::LEFT),
            'rightEye' => array('condition' => $this->getTableAlias(false, false) . '.eye_id = '.Eye::RIGHT),
            'unarchived' => array('condition' => $this->getTableAlias(false, false) . '.archived = 0'),
        );
    }

    public function relations()
    {
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'attachments' => array(self::MANY_MANY, 'ProtectedFile', 'ophcotherapya_email_attachment(email_id, file_id)'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id')
        );
    }

    /**
     * Scope to limit to emails for a specific event.
     *
     * @param Event $event
     *
     * @return Ophcotherapya_Email_Attachment
     */
    public function forEvent(Event $event)
    {
        $this->getDbCriteria()->mergeWith(
            array('condition' => 'event_id = :event_id', 'params' => array('event_id' => $event->id))
        );

        return $this;
    }

    /**
     * Get the application status based on the emails for the given event.
     *
     * @param Event $event
     *
     * @return string One of the OphCoTherapyapplication_Processor STATUS_ constants
     */
    public function getStatusForEvent(Event $event)
    {
        if (!$this->forEvent($event)->exists()) {
            return OphCoTherapyapplication_Processor::STATUS_PENDING;
        }

        if (!$this->forEvent($event)->unarchived()->exists()) {
            return OphCoTherapyapplication_Processor::STATUS_REOPENED;
        }

        return OphCoTherapyapplication_Processor::STATUS_SENT;
    }

    /**
     * @param ProtectedFile[] $attachments
     */
    public function addAttachments(array $attachments)
    {
        foreach ($attachments as $attachment) {
            $this->getDbConnection()->createCommand()
                 ->insert(
                     'ophcotherapya_email_attachment',
                     array('email_id' => $this->id, 'file_id' => $attachment->id)
                 );
        }
    }

    /**
     * Mark all emails for the specified event as archived.
     *
     * @parm Event $event
     */
    public function archiveForEvent(Event $event)
    {
        $this->updateAll(array('archived' => 1), 'event_id = ?', array($event->id));
    }
}
