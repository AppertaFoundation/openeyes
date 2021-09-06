<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class OphTrConsent_API extends BaseAPI
{
    /**
     * checks if there is a consent form for the given episode and the given procedure and eye.
     *
     * @param Episode   $episode
     * @param Procedure $procedure
     * @param string    $side      - left, right or both
     *
     * @throws Exception
     *
     * @return bool
     */
    public function hasConsentForProcedure($episode, $procedure, $side)
    {
        if ($episode) {
            $required_eye = Eye::BOTH;

            if (!in_array($side, array('left', 'right', 'both'))) {
                throw new Exception('unrecognised side value '.$side);
            }

            $event_type = $this->getEventType();

            $criteria = new CDbCriteria();
            $criteria->addCondition('event.event_type_id = :eventtype_id');
            $criteria->addCondition('event.episode_id = :episode_id');
            $criteria->addCondition('event.deleted = 0');
            $criteria->addCondition('procedures.id = :proc_id OR additional_procedures.id = :proc_id');
            $criteria->params = array(
                ':eventtype_id' => $event_type->id,
                ':episode_id' => $episode->id,
                ':proc_id' => $procedure->id,
            );

            $criteria->order = 't.created_date desc';

            $eye_ids = array('eye_id' => Eye::BOTH);

            if ($side == 'left') {
                $eye_ids[] = Eye::LEFT;
                $required_eye = Eye::LEFT;
            } elseif ($side == 'right') {
                $eye_ids[] = Eye::RIGHT;
                $required_eye = Eye::RIGHT;
            }

            $criteria->addInCondition('t.eye_id', $eye_ids);

            foreach (Element_OphTrConsent_Procedure::model()->with('event', 'procedures', 'additional_procedures')->findAll($criteria) as $consent_proc) {
                if ($consent_proc->eye_id == Eye::BOTH || $consent_proc->eye_id == $required_eye) {
                    return true;
                }
            }
        }

        return false;
    }

    public function canUpdate($event_id)
    {
        $type = Element_OphTrConsent_Type::model()->find('event_id=?', array($event_id));

        return $type->isEditable();
    }

    public function getFooterProcedures($event_id)
    {
        if (!$event = Event::model()->findByPk($event_id)) {
            throw new Exception("Event not found: $event_id");
        }

        if (!$element = Element_OphTrConsent_Procedure::model()->find('event_id=?', array($event->id))) {
            throw new Exception("Procedure element not found, possibly not a consent event: $event_id");
        }

        $return = 'Procedure(s): ';

        foreach ($element->procedures as $i => $proc) {
            if ($i >= 2) {
                $return .= '...';
                break;
            } elseif ($i) {
                $return .= ', ';
            }
            $return .= $proc->term;
        }

        return $return;
    }
}
