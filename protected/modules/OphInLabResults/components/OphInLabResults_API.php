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

class OphInLabResults_API extends BaseAPI
{

    public function getEventName($event)
    {
        $element = Element_OphInLabResults_Details::model()->findByAttributes(array('event_id' => $event->id));

        return isset($element->type) ? "Lab Result:<br/>" . $element->type->type : null;
    }

    /**
     * @return array|array[]|null[]|OphInLabResults_Type[]
     * @throws Exception
     */
    public function getLabResultTypesForCurrentInstitution()
    {
        $institution = Institution::model()->getCurrent();

        return array_map(
            static function ($entry) {
                return $entry->type;
            },
            OphInLabResults_Type_Institution::model()->with('OphInLabResults_Type')->findAll(
                'institution_id IS NULL OR institution_id = :institution_id',
                array(':institution_id' => $institution->id)
            )
        );
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getLabResultTypeResult($patientId, $eventId, $type)
    {
        // check if type exists for the Lab Results event.
        $labResultsType = OphInLabResults_Type::model()->find('type=?', array($type));

        if (isset($labResultsType)) {
            $criteria = new CDbCriteria();
            $criteria->join = ' LEFT JOIN event on t.event_id = event.id ';
            $criteria->join .= 'LEFT JOIN episode on event.episode_id = episode.id ';
            $criteria->addCondition('t.type = :type');
            $criteria->addCondition('episode.patient_id = :patientId');
            $criteria->addCondition('event.deleted = 0');
            $criteria->order = 'event.event_date DESC, t.time DESC, event.created_date DESC';
            $criteria->limit = 1;
            $criteria->params = array(
                'type' => $labResultsType->id,
                'patientId' => $patientId,
            );
            if (isset($eventId)) {
                $eventDraft = OphTrOperationchecklists_Event::model()->find('event_id = :event_id', array(':event_id' => $eventId))->draft;
                if (!$eventDraft) {
                    $eventLastModifiedDate = Element_OphTrOperationchecklists_Admission::model()->find('event_id = :event_id', array(':event_id' => $eventId))->last_modified_date;
                    $criteria->addCondition('t.created_date <= :eventLastModifiedDate');
                    $criteria->params['eventLastModifiedDate'] = $eventLastModifiedDate;
                }
            }

            $labResult = Element_OphInLabResults_Entry::model()->find($criteria);
            if ($labResult) {
                return array(
                    'result' => $labResult->result,
                    'comment' => $labResult->comment,
                );
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
}
