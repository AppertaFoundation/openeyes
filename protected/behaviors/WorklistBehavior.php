<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
use \OEModule\PASAPI\models\PasApiAssignment;
use \OEModule\PASAPI\resources\PatientAppointment;

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
            $worklist_patient_id = null;

            // if patientticketing is active (use session parameter),
            // set $worklist_patient_id to be the same as the event that created the patietnticket
            if (isset(\Yii::app()->session['patientticket_ticket_ids']) && \Yii::app()->session['patientticket_ticket_ids']) {
                $patientticket_ticket_id = Yii::app()->session['patientticket_ticket_ids'];
                $ticket = \OEModule\PatientTicketing\models\Ticket::model()->findByPk($patientticket_ticket_id);
                if ($ticket) {
                    $patientticket_event = Event::model()->findByPk($ticket->event_id);
                    if ($patientticket_event) {
                        $worklist_patient_id = $patientticket_event->worklist_patient_id;
                    }
                }
            }
            // worklist_patient_id was not set previously
            if ($worklist_patient_id === null) {
                $worklist_patient_id = $this->worklist_manager->getWorklistPatientId();
            }
            $worklist_patient = $worklist_patient_id ? WorklistPatient::model()->findByPk($worklist_patient_id) : null;

            if ($worklist_patient && $worklist_patient->patient->id === $patient_id) {
                $assignment = $this->getPasApiAssignment($worklist_patient->id);

                //set pas_visit_id
                if ($assignment) {
                    $this->owner->event->worklist_patient_id = $worklist_patient->id;
                }

            } else {
                $this->owner->event->worklist_patient_id = null;
                $worklist_patient = null;

                $search_days = (string)SettingMetadata::model()->getSetting('worklist_search_appt_within');
                //if the setting is invalid we fall back to 30days
                if (!ctype_digit($search_days)) {
                    $search_days = 30;
                }

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
                        ':start_date' => date('Y-m-d H:i:s', strtotime("-{$search_days} days"))
                    ];
                    $worklist_patient = WorklistPatient::model()->find($criteria);
                }

                if ($worklist_patient) {
                    $this->owner->event->worklist_patient_id = $worklist_patient->id;
                }
            }
        }
    }

    public function addToUnbookedWorklist($site_id, $firm_id) {
        if($this->owner->event && !$this->owner->event->worklist_patient_id) {
            $unbooked_worklist_manager = new \UnbookedWorklist();
            $firm = \Firm::model()->findByPk($firm_id);
            $subspecialty_id = isset($firm->subspecialty->id) ? $firm->subspecialty->id : null;
            $unbooked_worklist = $unbooked_worklist_manager->createWorklist(new \DateTime(), $site_id, $subspecialty_id);
            if ($unbooked_worklist) {
                $worklist_patient = $this->worklist_manager->addPatientToWorklist($this->owner->patient, $unbooked_worklist, new \DateTime());
                $this->owner->event->worklist_patient_id = $worklist_patient->id;
                return true;
            } else {
                \OELog::log("Unbooked worklist cannot be found for patient_id: {$this->owner->patient->id}");
            }
        }
        return false;
    }

    public function getPasApiAssignment($worklist_patient_id)
    {
        //Should this come from the PatientAppointment resource instead of directly from PasApiAssignment ???
        return PasApiAssignment::model()->findByAttributes([
            'resource_type' => PatientAppointment::$resource_type,
            'internal_id' => $worklist_patient_id,
            'internal_type' => '\WorklistPatient']);
    }
}

