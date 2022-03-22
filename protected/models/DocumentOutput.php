<?php

/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class DocumentOutput extends BaseActiveRecord
{
    public const TYPE_PRINT = 'Print';
    public const TYPE_EMAIL = 'Email';
    public const TYPE_EMAIL_DELAYED = 'Email (Delayed)';
    public const TYPE_INTERNAL_REFERRAL = 'Internalreferral';
    public const TYPE_DOCMAN = 'Docman';

    public const STATUS_DRAFT = 'DRAFT';
    public const STATUS_SENDING = 'SENDING';
    public const STATUS_PENDING = 'PENDING';
    public const STATUS_PENDING_RETRY = 'PENDING_RETRY';
    public const STATUS_FAILED = 'FAILED';
    public const STATUS_COMPLETE = 'COMPLETE';

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Site the static model class
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
        return 'document_output';
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'created_user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'document_target' => array(self::BELONGS_TO, 'DocumentTarget', 'document_target_id'),
            'document_instance_data' => array(self::BELONGS_TO, 'DocumentInstanceData', 'document_instance_data_id'),

        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('document_target_id, ToCc, output_type, output_status, document_instance_version_id, requestor_id, request_datetime, success_datetime', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('document_target_id, ToCc, output_type, output_status, document_instance_version_id, requestor_id, request_datetime, success_datetime', 'safe', 'on' => 'search'),
        );
    }

    public function createSet($eventId)
    {
        $ds = new DocumentSet;
        $ds->event_id = $eventId;
        $ds->save();

        return ($ds->id);
    }

    /**
     * Returns the Correspondence Event
     * @return CActiveRecord|null
     */
    public function getEvent()
    {
        $event_id = isset($this->document_target->document_instance) ? $this->document_target->document_instance->correspondence_event_id : null;
        return $event_id ? Event::model()->disableDefaultScope()->findByPk($event_id) : null;
    }

    public function updateStatus($new_status = null, $is_draft = false, $override = false)
    {
        if (!$override) {
            $is_new = $this->isNewRecord;
            $curr = self::findByPk($this->id);

            if ($curr) {
                // Set the output_status to DRAFT for print type on saving
                if ($curr->output_type === self::TYPE_PRINT && $curr->output_status === self::STATUS_COMPLETE) {
                    $new_status = self::STATUS_DRAFT;
                }

                // Set the output_status of the email output_type to PENDING_RETRY, if it is in COMPLETE status.
                if ($curr->output_type === self::TYPE_EMAIL && $curr->output_status === self::STATUS_COMPLETE) {
                    $new_status = self::STATUS_PENDING_RETRY;
                }

                if (!$is_new) {
                    // If the type of the document output gets changed from Internal Referral to Email,
                    // this can when user updates the event and selects the Subspecialty or the firm with the set email.
                    if ( ($curr->output_type === self::TYPE_INTERNAL_REFERRAL && $curr->output_status === self::STATUS_PENDING) && $this->output_type === self::TYPE_EMAIL) {
                        $new_status = self::STATUS_SENDING;
                    }
                }
            }

            if ($is_new) {
                $draft_inhibited = $is_draft && SettingMetadata::checkSetting('disable_draft_correspondence_email', 'on');

                // If we are saving a new record and the output type is Email, then set the status to be SENDING.
                if ($this->output_type === self::TYPE_EMAIL) {
                    $new_status = $draft_inhibited ? self::STATUS_DRAFT : self::STATUS_SENDING;
                } elseif ($this->output_type === self::TYPE_EMAIL_DELAYED && !$is_draft) {
                    $new_status = $draft_inhibited ? self::STATUS_DRAFT : self::STATUS_PENDING_RETRY;
                }
            } elseif (!$is_draft) {
                // If we are moving a record from draft to final and the output type is Email, then set the status to be SENDING.
                if ($this->output_type === self::TYPE_EMAIL) {
                    $new_status = self::STATUS_SENDING;
                } elseif ($this->output_type === self::TYPE_EMAIL_DELAYED && !$is_draft) {
                    $new_status = self::STATUS_PENDING_RETRY;
                }
            }
        }

        if ($new_status) {
            $this->output_status = $new_status;
        }
    }

    /**
     * @param $outputStatus String updates the output_status column with the given string.
     */
    public function updateOutputStatus($outputStatus)
    {
        // Using saveAttributes so that it does not call beforesave method.
        $this->saveAttributes(array('output_status' => $outputStatus));
    }
}
