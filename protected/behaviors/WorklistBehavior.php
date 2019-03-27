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

        if ($action && ($action->id === 'create') && $this->owner->event) {

            $patient_id = isset($this->owner->patient->id) ? $this->owner->patient->id : null;
            $worklist_patient_id = $this->worklist_manager->getWorklistPatientId();
            $worklist_patient = $worklist_patient_id ? WorklistPatient::model()->findByPk($worklist_patient_id) : null;

            if ($worklist_patient && $worklist_patient->patient->id === $patient_id) {
                $assignment = $this->getPasApiAssignment($worklist_patient->id);

                //set pas_visit_id
                if ($assignment) {
                    $this->owner->event->worklist_patient_id = $assignment->resource_id;
                }

            } else {
                $this->owner->event->worklist_patient_id = null;
                $worklist_patient = null;

/** @TODO CONFIG DAYS */

                //The nearest booked appointment within 1 month(default) (past or future)
/*                $criteria = new \CDbCriteria();
                $criteria->addCondition('patient_id = :patient_id');
                $criteria->addCondition('t.when >= :start_date AND t.when <= :end_date');
                $criteria->order = 'TIMESTAMPDIFF(MINUTE, t.when, NOW())';
                $criteria->params = [
                    ':patient_id' => $patient_id,
                    ':start_date' => date('Y-m-d H:i:s', strtotime("-30 days")),
                    ':end_date' => date('Y-m-d H:i:s', strtotime("+30 days"))
                ];

                $worklist_patient = WorklistPatient::model()->find($criteria);
*/

                /*
                 * The relevant worklist is determined as (in order of precedence):
                 *  - The nearest booked appointment within 1 month(default) (past or future) OR
                 *  - The nearest booked future appointment OR
                 *  - The 'Unbooked' worklist for the current date, site and subspecialty
                 *
                 *  The first two point can be solved to get the nearest appointment
                 *  from -30day to the future without restriction
                 */

                // The nearest booked appointment from -30days(default) to the infinity and beyond
                if (!$worklist_patient) {
                    $criteria = new \CDbCriteria();
                    $criteria->addCondition('patient_id = :patient_id');
                    $criteria->addCondition('t.when >= :start_date');
                    $criteria->order = 'TIMESTAMPDIFF(MINUTE, t.when, NOW())';
                    $criteria->params = [
                        ':patient_id' => $patient_id,
                        ':start_date' => date('Y-m-d H:i:s', strtotime("-30 days"))
                    ];
                    $worklist_patient = WorklistPatient::model()->find($criteria);
                }

                if (!$worklist_patient) {

                    // UNBOOKED
                    $unbooked_worklist_manager = new \UnbookedWorklist();
                    $site_id = \Yii::app()->session->get('selected_site_id', null);
                    $firm_id = \Yii::app()->session->get('selected_firm_id', null);
                    $firm = \Firm::model()->findByPk($firm_id);
                    $subspecialty_id = isset($firm->subspecialty->id) ? $firm->subspecialty->id : null;

                    $unbooked_worklist = $unbooked_worklist_manager->getWorklist(new \DateTime(), $site_id, $subspecialty_id);

                    $worklist_patient = $this->worklist_manager->addPatientToWorklist($this->owner->patient, $unbooked_worklist, new \DateTime());
                }

                if ($worklist_patient) {
                    $this->owner->event->worklist_patient_id = $worklist_patient->id;
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

