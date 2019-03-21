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

class WorklistBehavior extends CBehavior
{
    protected $worklist_manager;

    public function __construct()
    {
        $this->worklist_manager = new WorklistManager();
    }

    public function events()
    {
        return array_merge(parent::events(), [
            'onBeforeAction' => 'beforeAction',
        ]);
    }

    public function beforeAction(\CEvent $event)
    {
        $action = isset($event->params['action']) ? $event->params['action'] : null;
        $patient_id = isset($this->owner->patient->id) ? $this->owner->patient->id : null;
        $worklist_patient_id = $this->worklist_manager->getWorklistPatientId();
        $worklist_patient = $worklist_patient_id ? WorklistPatient::model()->findByPk($worklist_patient_id) : null;

        if ($action && ($action->id === 'create') && $this->owner->event) {

            if ($worklist_patient && $worklist_patient->patient->id === $patient_id) {
                $assignment = $this->getPasApiAssignment($worklist_patient->id);

                //set pas_visit_id
                if ($assignment) {
                    $this->owner->event->worklist_patient_id = $assignment->resource_id;
                }

            } else {
                $this->owner->event->worklist_patient_id = null;

                // Using the closest worklist_patient.id matching the current patient and current date
                $worklist_patients = WorklistPatient::model()->findAllByAttributes(['patient_id' => $patient_id]);

                $interval = [];
                foreach ($worklist_patients as $worklist_patient) {
                    $interval[$worklist_patient->id] = abs(strtotime($worklist_patient->when) - time());
                }
                asort($interval);
                $worklist_patient_id = key($interval);

                if ($worklist_patient_id && ($assignment = $this->getPasApiAssignment($worklist_patient_id))) {
                    $this->owner->event->worklist_patient_id = $assignment->resource_id;
                }
            }
        }
    }

    public function getPasApiAssignment($worklist_patient_id)
    {
        //Should this come from the PatientAppointment resource instead of directly from PasApiAssignment ???
        return \OEModule\PASAPI\models\PasApiAssignment::model()->findByAttributes([
            'resource_type' => \OEModule\PASAPI\resources\PatientAppointment::$resource_type,
            'internal_id' => $worklist_patient_id,
            'internal_type' => '\WorklistPatient']);
    }
}

