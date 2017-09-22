<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class UnlinkLegacyVFCommand extends CConsoleCommand
{
    public function run($args)
    {
        $event_type_id = EventType::model()->find('class_name = :classname', array(':classname' => 'OphInVisualfields'))->id;
        $patient_ids = Yii::app()->db->createCommand()
            ->selectDistinct('patient_id')
            ->from('measurement_reference mr')
            ->join('patient_measurement pm', 'pm.id = mr.patient_measurement_id')
            ->queryColumn();
        foreach ($patient_ids as $patient_id) {
            $criteria = new CDbCriteria();
            $criteria->condition = 'event_type_id = :event_type_id AND patient_id = :patient_id';
            $criteria->join = 'join episode ep on ep.id = t.episode_id';
            $criteria->order = 'event_date desc';
            $criteria->limit = '3';
            $criteria->params = array(':patient_id' => $patient_id, ':event_type_id' => $event_type_id);
            $events = Event::model()->findAll($criteria);
            foreach ($events as $event) {
                echo ' - '.$event->id."\n";
                MeasurementReference::model()->deleteAll('event_id = ?', array($event->id));
                $event->deleted = 1;
                $event->save();
            }
            echo "$patient_id\n";
        }
    }
}
