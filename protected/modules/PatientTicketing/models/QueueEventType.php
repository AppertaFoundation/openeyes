<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\PatientTicketing\models;

/**
 * This is the model class for table "patientticketing_queue".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property string $queue_id
 * @property string $event_type_id
 *
 * The followings are the available model relations:
 * @property \OEModule\PatientTicketing\models\Queue $queue
 * @property EventType event_type
 */
class QueueEventType extends \BaseActiveRecordVersioned
{
    public function tableName()
    {
        return 'patientticketing_queue_event_type';
    }

    public function rules()
    {
        return array(
            array('event_type_id', 'safe'),
            array('event_type_id', 'required'),
        );
    }

    public function relations()
    {
        return array(
            'queue' => array(self::BELONGS_TO, 'Queue', 'queue_id'),
            'event_type' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
        );
    }
}
