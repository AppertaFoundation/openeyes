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

use OEModule\PASAPI\models\PasApiAssignment;
use OEModule\PASAPI\resources\PatientAppointment;
use OEModule\PatientTicketing\models\Ticket;

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
                $ticket = Ticket::model()->findByPk($patientticket_ticket_id);
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
                $this->owner->event->worklist_patient_id = $worklist_patient->id;
            } else {
                $this->owner->event->worklist_patient_id = null;

                $search_past_days = (string)SettingMetadata::model()->getSetting('worklist_future_search_days');
                $search_future_days = (string)SettingMetadata::model()->getSetting('worklist_past_search_days');
                //if the setting is invalid we fall back to 30days
                if (!ctype_digit($search_past_days)) {
                    $search_past_days = 30;
                }

                if (!ctype_digit($search_future_days)) {
                    $search_future_days = 30;
                }

                // The nearest booked appointment from/to -30days(default)
                $criteria = new \CDbCriteria();
                $criteria->addCondition('patient_id = :patient_id');
                $criteria->addCondition('t.when >= :start_date');
                $criteria->addCondition('t.when <= :end_date');
                $criteria->order = 'TIMESTAMPDIFF(MINUTE, t.when, NOW())';
                $criteria->params = [
                    ':patient_id' => $patient_id,
                    ':start_date' => date('Y-m-d 00:00:00', strtotime("-{$search_past_days} days")),
                    ':end_date' => date('Y-m-d 23:59:59', strtotime("+{$search_future_days} days"))
                ];
                $worklist_patient = WorklistPatient::model()->find($criteria);

                if ($worklist_patient) {
                    $this->owner->event->worklist_patient_id = $worklist_patient->id;
                }
            }
        }
    }

    public function addToUnbookedWorklist($site_id, $firm_id)
    {
        $firm = \Firm::model()->findByPk($firm_id);
        $subspecialty = $firm->subspecialty ? $firm->subspecialty : null;
        $patient_id = isset($this->owner->patient->id) ? $this->owner->patient->id : null;

        /**
         * Excluded VC items from generating a new unbooked item where the original event had no worklist entry
         */

        if (isset(\Yii::app()->session['patientticket_ticket_ids']) && \Yii::app()->session['patientticket_ticket_ids']) {
            $patientticket_ticket_id = Yii::app()->session['patientticket_ticket_ids'];
            $ticket = Ticket::model()->findByPk($patientticket_ticket_id);
            $ticket_patient_id = null;

            if ($ticket) {
                // disableDefaultScope() to find deleted events as well, we just want to get the patient, nothing else
                $patientticket_event = Event::model()->disableDefaultScope()->findByPk($ticket->event_id);
                $patientticket_episode = Episode::model()->disableDefaultScope()->findByPK($patientticket_event->episode_id);
                $ticket_patient_id = $patientticket_episode->patient_id;
            }

            // creating event for the patient who has ticket set in session['patientticket_ticket_ids']
            // plus ticket's worklist_patient_id is empty, we do not create unbooked entry
            if ($ticket_patient_id === $patient_id && !$patientticket_event->worklist_patient_id) {
                return false;
            }
        }

        // if there is an event->worklist_patient_id we check if the worklist is EyeCasualty and is for today
        $worklist_patient_today_AE = null;
        if ($this->owner->event->worklist_patient_id) {
            $criteria = new \CDbCriteria();

            $criteria->with = ['worklist.worklist_definition.display_contexts.subspecialty'];
            $criteria->together = true;
            $criteria->addCondition('t.when >= :start_date');
            $criteria->addCondition('t.when <= :end_date');
            $criteria->addCondition('subspecialty.ref_spec = :subspecialty_ref_spec');
            $criteria->addCondition('patient_id = :patient_id');

            $criteria->params = [
                ':patient_id' => $patient_id,
                ':subspecialty_ref_spec' => "AE",
                ':start_date' => date('Y-m-d 00:00:00'),
                ':end_date' => date('Y-m-d 23:59:59')
            ];

            $worklist_patient_today_AE = \WorklistPatient::model()->find($criteria);
        }

        // for Eye Casualty events, the only time the patient wont be added to todays Eye Casualty unbooked worklist is if they already have a "booked" eye casualty appointment for today
        if ($this->owner->event && !$this->owner->event->worklist_patient_id || ($subspecialty && $subspecialty->ref_spec === 'AE' && !($worklist_patient_today_AE))) {

            $site = Site::model()->findByPk($site_id);

            $patient_identifier_type = PatientIdentifierHelper::getPatientIdentifierType('LOCAL', $site->institution_id, $site->id) ??
                PatientIdentifierHelper::getPatientIdentifierType('LOCAL', $site->institution_id);
            if ($patient_identifier_type) {
                $unbooked_worklist_manager = new \UnbookedWorklist();
                $unbooked_worklist = $unbooked_worklist_manager->createWorklist(new \DateTime(), $site_id, $subspecialty->id);


                if ($unbooked_worklist) {
                    $worklist_patient = $this->worklist_manager->addPatientToWorklist($this->owner->patient, $unbooked_worklist, new \DateTime());
                    if ($worklist_patient) {
                        //event already saved here we need to set this indicidually
                        $this->owner->event->saveAttributes(['worklist_patient_id' => $worklist_patient->id]);
                    } else {
                        \OELog::log("Patient patient_id: {$this->owner->patient->id} cannot be added to " .
                            "unbooked worklist {$unbooked_worklist->id} " .
                            "Errors: " . implode(", ", $this->worklist_manager->getErrors()));
                    }
                    return true;
                } else {
                    \OELog::log("Unbooked worklist cannot be found for patient_id: {$this->owner->patient->id}");
                }

            } else {
                \OELog::log("Unbooked worklist cannot be found for patient_id: {$this->owner->patient->id} for 
                institutions/sites that do not have a patient identifier type");
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
