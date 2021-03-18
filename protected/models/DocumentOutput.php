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

    /**
     * @param $outputStatus String updates the output_status column with the given string.
     */
    public function updateOutputStatus($outputStatus)
    {
        // Using saveAttributes so that it does not call beforesave method.
        $this->saveAttributes(array('output_status' => $outputStatus));
    }

    public function beforeSave()
    {
        $curr = self::findByPk($this->id);

        if ($curr) {
            // Set the output_status to DRAFT for print type on saving
            if ($curr->output_type === 'Print' && $curr->output_status === 'COMPLETE') {
                $this->output_status = 'DRAFT';
            }
            // Set the output_status of the email output_type to PENDING_RETRY, if it is in COMPLETE status.
            if ($curr->output_type === 'Email' && $curr->output_status === 'COMPLETE') {
                $this->output_status = 'PENDING_RETRY';
            }
        }

        // If we are saving a new record and the output type is Email, then set the status to be SENDING.
        if ($this->isNewRecord && $this->output_type === "Email") {
            $this->output_status = "SENDING";
        }

        if ($this->isNewRecord && $this->output_type === "Email (Delayed)") {
            $this->output_status = "PENDING";
        }

        if ($curr && !$this->isNewRecord) {
            // If the type of the document output gets changed from Internal Referral to Email,
            // this can when user updates the event and selects the Subspecialty or the firm with the set email.
            if ( ($curr->output_type === "Internalreferral" && $curr->output_status = "PENDING") && $this->output_type === "Email") {
                $this->output_status = "SENDING";
            }
        }

        return parent::beforeSave();
    }
}
