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
}
