<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
class OphTrOperationbooking_API extends BaseAPI
{
    /**
     * Gets latest booking diagnosis from completed operation booking or defaults to episode diagnosis.
     * @param $patient
     * @return mixed
     */
    public function getLatestCompletedOperationBookingDiagnosis($patient)
    {
        $completed = OphTrOperationbooking_Operation_Status::model()->find('name=?', array('Completed'));

        $criteria = new CDbCriteria();
        $criteria->addCondition('episode.patient_id = :patient_id');
        $criteria->params[':patient_id'] = $patient->id;
        $criteria->order = 'event.created_date desc';
        $criteria->addCondition('t.status_id = :status_id');
        $criteria->params[':status_id'] = $completed->id;

        if ($operation = Element_OphTrOperationbooking_Operation::model()->with(array(
            'event' => array(
                'with' => 'episode',
            ),
        ))
            ->find($criteria)
        ) {
            return Element_OphTrOperationbooking_Diagnosis::model()->find('event_id=?',
                array($operation->event_id))->disorder->term;
        }

        return $patient->epd;
    }

    public function getBookingsForEpisode($episode_id)
    {
        $criteria = new CDbCriteria();
        $criteria->order = 't.created_date asc';
        $criteria->addCondition('episode_id', $episode_id);
        $criteria->addCondition('booking_cancellation_date is null');

        return OphTrOperationbooking_Operation_Booking::model()
            ->with('session')
            ->with(array(
                'operation' => array(
                    'condition' => "episode_id = $episode_id",
                    'with' => 'event',
                ),
            ))
            ->findAll($criteria);
    }

    public function getOperationsForEpisode($episode_id)
    {
        $criteria = new CDbCriteria();
        $criteria->order = 't.created_date asc';
        $criteria->condition = 't.status_id != 5';
        $criteria->compare('episode_id', $episode_id);

        return Element_OphTrOperationbooking_Operation::model()
            ->with(array(
                'booking' => array(
                    'with' => 'session',
                ),
                'event',
            ))
            ->findAll($criteria);
    }

    /**
     * Gets 'open' bookings for the specified episode
     * A booking is deemed open if it has no operation note linked to it.
     * @param $episode_id
     * @return mixed
     */
    public function getOpenBookingsForEpisode($episode_id)
    {
        $criteria = new CDbCriteria();
        $criteria->order = 'event.created_date asc';
        $criteria->compare('episode_id', $episode_id);
        $criteria->addCondition('`t`.booking_cancellation_date is null');

        $status_scheduled = OphTrOperationbooking_Operation_Status::model()->find('name=?', array('Scheduled'));
        $status_rescheduled = OphTrOperationbooking_Operation_Status::model()->find('name=?', array('Rescheduled'));

        return OphTrOperationbooking_Operation_Booking::model()
            ->with('session')
            ->with(array(
                'operation' => array(
                    'condition' => "episode_id = $episode_id and status_id in ($status_scheduled->id,$status_rescheduled->id)",
                    'with' => 'event',
                ),
            ))
            ->findAll($criteria);
    }

    public function getOperationProcedures($operation_id)
    {
        return OphTrOperationbooking_Operation_Procedures::model()->findAll('element_id=?', array($operation_id));
    }

    public function getOperationForEvent($event_id)
    {
        return Element_OphTrOperationbooking_Operation::model()->find('event_id=?', array($event_id));
    }

    public function setOperationStatus($event_id, $status_name)
    {
        if (!$operation = Element_OphTrOperationbooking_Operation::model()->find('event_id=?', array($event_id))) {
            throw new Exception("Operation event not found: $event_id");
        }

        if ($status_name == 'Scheduled or Rescheduled') {
            if (OphTrOperationbooking_Operation_Booking::model()->find('element_id=? and booking_cancellation_date is not null',
                array($operation->id))
            ) {
                $status_name = 'Rescheduled';
            } else {
                $status_name = 'Scheduled';
            }
        }

        if (!$status = OphTrOperationbooking_Operation_Status::model()->find('name=?', array($status_name))) {
            throw new Exception("Unknown operation status: $status_name");
        }

        if ($operation->status_id != $status->id) {
            $operation->status_id = $status->id;

            if (!$operation->save()) {
                throw new Exception('Unable to save operation: ' . print_r($operation->getErrors(), true));
            }
        }
    }

    public function getProceduresForOperation($event_id)
    {
        if (!$operation = Element_OphTrOperationbooking_Operation::model()->find('event_id=?', array($event_id))) {
            throw new Exception("Operation event not found: $event_id");
        }

        return $operation->procedures;
    }

    public function getEyeForOperation($event_id)
    {
        if (!$operation = Element_OphTrOperationbooking_Operation::model()->find('event_id=?', array($event_id))) {
            throw new Exception("Operation event not found: $event_id");
        }

        return $operation->eye;
    }

    /**
     * Get the most recent booking for the patient in the given episode.
     *
     * @param Episode $episode
     *
     * @return OphTrOperationbooking_Operation_Booking
     */
    public function getMostRecentBookingForEpisode($episode)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('episode_id', $episode->id);
        $criteria->order = 'event.created_date desc';

        return OphTrOperationbooking_Operation_Booking::model()
            ->with(array(
                'operation' => array(
                    'with' => 'event',
                ),
            ))
            ->find($criteria);
    }

    /**
     * get the procedures for this patient and episode as a string for use in correspondence.
     *
     * @param Patient $patient
     *
     * @return string
     */
    public function getLetterProcedures($patient)
    {
        if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
            $return = '';

            if ($operation = $this->getElementForLatestEventInEpisode($episode,
                'Element_OphTrOperationbooking_Operation')
            ) {
                foreach ($operation->procedures as $i => $procedure) {
                    if ($i) {
                        $return .= ', ';
                    }
                    $return .= $operation->eye->adjective . ' ' . $procedure->term;
                }
            }

            return strtolower($return);
        }
    }

    public function getAdmissionDate($patient)
    {
        if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
            if ($booking = $this->getMostRecentBookingForEpisode($episode)) {
                return $booking->session->NHSDate('date');
            }
        }
    }

    /* TODO: this should be refactored at some point */

    public function generateSessions($args = array())
    {
        $output = '';

        // Get sequences
        $today = date('Y-m-d');
        $initialEndDate = empty($args) ? strtotime('+13 months') : strtotime($args[0]);

        $sequences = OphTrOperationbooking_Operation_Sequence::model()->findAll(
            'start_date <= :end_date AND (end_date IS NULL or end_date >= :today)',
            array(':end_date' => date('Y-m-d', $initialEndDate), ':today' => $today)
        );

        foreach ($sequences as $sequence) {
            $criteria = new CDbCriteria();
            $criteria->addCondition('sequence_id = :sequence_id');
            $criteria->params[':sequence_id'] = $sequence->id;
            $criteria->order = 'date desc';

            $session = OphTrOperationbooking_Operation_Session::model()->find($criteria);

            // The date of the most recent session for this sequence plus one day, or the sequence start date if no sessions for this sequence yet
            $startDate = empty($session) ? strtotime($sequence->start_date) : strtotime($session->date) + (60 * 60 * 24);

            // Sessions should be generated up to the smaller of initialEndDate (+13 months or command line) and sequence end_date
            if ($sequence->end_date && strtotime($sequence->end_date) < $initialEndDate) {
                $endDate = strtotime($sequence->end_date);
            } else {
                $endDate = $initialEndDate;
            }

            $dateList = array();
            if ($sequence->interval_id == 1) {
                // NO REPEAT (single session)
                // If a session already exists for this one off there's no point creating another
                if (empty($session)) {
                    $dateList[] = $sequence->start_date;
                }
            } elseif ($sequence->interval_id == 6 && $sequence->week_selection) {
                // MONTHLY REPEAT (weeks x,y of month)
                $date = date('Y-m-d', $startDate);
                $time = $startDate;
                // Get the next occurrence of the sequence on/after the start date
                while (date('N', $time) != date('N', strtotime($sequence->start_date))) {
                    $date = date('Y-m-d', mktime(0, 0, 0, date('m', $time), date('d', $time) + 1, date('Y', $time)));
                    $time = strtotime($date);
                }
                $dateList = $sequence->getWeekOccurrences($sequence->weekday, $sequence->week_selection, $time,
                    $endDate, $date, date('Y-m-d', $endDate));
            } else {
                // WEEKLY REPEAT (every x weeks)
                // There is a repeat interval, e.g. once every two weeks. In the instance of two weeks, the
                //	function below returns 60 * 60 * 24 * 14, i.e. two weeks
                $interval = $sequence->interval->getInteger($endDate);

                // The number of days in the interval - 14 in the case of two week interval
                $days = $interval / 24 / 60 / 60;

                // IF there's no session use the sequence start date. If there is use the most recent
                //	session date plus the interval (e.g. two weeks)
                if (empty($session)) {
                    $nextStartDate = $startDate;
                } else {
                    $nextStartDate = $startDate + $interval - 86400;
                }

                // Convert $nextStartDate (a timestamp of the seqence start date or the most recent session date plus the interval to a date.
                $date = date('Y-m-d', $nextStartDate);

                // The timestamp of the start date
                $time = $nextStartDate;

                // get the next occurrence of the sequence on/after the start date

                // Check to see if the day of the week for the time is the same day of the week as the sequence start date
                //	Process loop if it isn't
                while (date('N', $time) != date('N', strtotime($sequence->start_date))) {
                    // Set the date to $time + 1 day
                    $date = date('Y-m-d', mktime(0, 0, 0, date('m', $time), date('d', $time) + 1, date('Y', $time)));

                    // Set the time to the timstamp for the date + 1 day
                    $time = strtotime($date);
                }

                while ($time <= $endDate) {
                    $dateList[] = $date;

                    $date = date('Y-m-d',
                        mktime(0, 0, 0, date('m', $time), date('d', $time) + $days, date('Y', $time)));
                    $time = strtotime($date);
                }
            }

            if (!empty($dateList)) {
                // Process dateList into sessions
                foreach ($dateList as $date) {
                    // TODO: Check for collisions, maybe in Session validation code
                    $new_session = new OphTrOperationbooking_Operation_Session();
                    foreach (array(
                                 'start_time',
                                 'end_time',
                                 'consultant',
                                 'anaesthetist',
                                 'paediatric',
                                 'general_anaesthetic',
                                 'theatre_id',
                                 'default_admission_time',
                             ) as $attribute) {
                        $new_session->$attribute = $sequence->$attribute;
                    }
                    $new_session->date = $date;
                    $new_session->sequence_id = $sequence->id;
                    $new_session->firm_id = $sequence->firm_id;

                    if (Yii::app()->params['sessions_unavailable_past_date'] && $date >= Yii::app()->params['sessions_unavailable_past_date']) {
                        $new_session->available = 0;
                    }
                    $new_session->save();
                }
                $output .= "Sequence ID {$sequence->id}: Created " . count($dateList) . " session(s).\n";
            }
        }

        if (!empty($args[1])) {
            return $output;
        }
    }

    public function findSiteForBookingEvent($event)
    {
        if ($operation = Element_OphTrOperationbooking_Operation::model()->with('booking')->find('event_id=?', array($event->id))) {
            if ($operation->booking) {
                return $operation->booking->theatre->site;
            }
        }
    }

    public function findTheatreForBookingEvent($event)
    {
        if ($operation = Element_OphTrOperationbooking_Operation::model()->with('booking')->find('event_id=?', array($event->id))) {
            if ($operation->booking) {
                return $operation->booking->theatre;
            }
        }
    }

    public function canUpdate($event_id)
    {
        $operation = Element_OphTrOperationbooking_Operation::model()->find('event_id=?', array($event_id));

        return $operation->isEditable();
    }

    public function showDeleteIcon($event_id)
    {
        $operation = Element_OphTrOperationbooking_Operation::model()->find('event_id=?', array($event_id));

        return $operation->isEditable();
    }

    public function findBookingByEventID($event_id)
    {
        if ($operation = Element_OphTrOperationbooking_Operation::model()->with('booking')->find('event_id=?', array($event_id))) {
            return $operation->booking;
        }

        return false;
    }

    /**
     * To get All Booked procedures Without OperationNotes
     *
     * @param Patient $patient
     * @return array|string
     */
    public function getAllBookingsWithoutOperationNotes(\Patient $patient)
    {
        $event_type = $this->getEventType();
        $criteria = new \CDbCriteria;
        $criteria->join .= 'join et_ophtroperationbooking_operation on t.element_id = et_ophtroperationbooking_operation.id ';
        $criteria->join .= 'join event on et_ophtroperationbooking_operation.event_id = event.id ';
        $criteria->join .= 'join episode on event.episode_id = episode.id ';
        $criteria->join .= 'left join et_ophtroperationnote_procedurelist eop on event.id = eop.booking_event_id';
        $criteria->addCondition('event.deleted <> 1');
        $criteria->addCondition('event.event_type_id = :event_type_id and episode.patient_id = :patient_id');
        $criteria->addCondition('episode.patient_id = :patient_id');
        $criteria->addCondition('eop.booking_event_id is null');
        $criteria->params = array(
            ':patient_id' => $patient->id,
            ':event_type_id' => $event_type->id,
        );
        $not_booked_events = array();
        $booking_procs = OphTrOperationbooking_Operation_Procedures::model()->findAll($criteria);
        if ($booking_procs) {
            foreach ($booking_procs as $proc) {
                $not_booked_events[] = $proc->element->eye->getAdjective() . ' ' . $proc->procedure->term;
            }
        }

        return implode(', ', $not_booked_events);
    }
    
    /**
     * Automatically scheduleds all the un-scheduled op bookings in the episode
     * @param \Episode $episode
     * @return type
     */
    public function autoScheduleOperationBookings(\Episode $episode)
    {
        $errors = array();
        
        $criteria = new CDbCriteria();
        $criteria->order = 't.created_date asc';
        $criteria->condition = 't.status_id = 1';
        $criteria->compare('episode_id', $episode->id);

        $operations = Element_OphTrOperationbooking_Operation::model()->with(array(
            'event' => array(
                'condition'=>'event.deleted=0',
            )
        ))->findAll($criteria);
        
        $op_status_scheduled = OphTrOperationbooking_Operation_Status::model()->find('name=?', array('Scheduled'));
        $ep_status_listed = EpisodeStatus::model()->find('name=?', array('Listed/booked'));

        foreach($operations as $operation){
            // get the first bookable session regardless of the firm
            $session = $this->getFirstBookableSession($operation);

            //we need to pass to schedule the op
            $schedule_options = Element_OphTrOperationbooking_ScheduleOperation::model()->find('event_id = ?', array($operation->event->id));
            
            if($session){
                $transaction = Yii::app()->db->beginTransaction();
                
                try {
                    
                    $ward = OphTrOperationbooking_Operation_Ward::model()->find('site_id = ?', array($operation->site->id));
                    if(!$ward){
                        //as this feature is used when the client/hospital doesn't use the 
                        //scheduling, most likely it will have a dummy ward set up for only one site 
                        $ward = OphTrOperationbooking_Operation_Ward::model()->find();
                    }
                    $booking = new OphTrOperationbooking_Operation_Booking('insert');
                    $booking->ward_id = $ward->id;
                    $booking->element_id = $operation->id;
                    $booking->session_id = $session->id;
                    $booking->session_theatre_id = 1;
                    $booking->session_date = date("Y-m-d H:i:s");
                    $booking->session_start_time = $session->start_time;
                    $booking->admission_time = $session->start_time;
                    $booking->session_end_time = $session->end_time;
                    $booking->cancellation_comment = '';
                    //$booking will be saved in $operation->schedule()
                    
                    $result = $operation->schedule($booking, '', '', '', false, null, $schedule_options);

                    if ($result !== true) {
                        $errors[$operation->id] = $result;
                    } else {
                        $operation->status_id = $op_status_scheduled->id;
                        $operation->save();

                        $episode->episode_status_id = $ep_status_listed->id;
                        $episode->save();

                        $operation->event->deleteIssues();

                        $transaction->commit();
                    }

                } catch (RaceConditionException $e) {
                    $errors[$operation->id] = $e->getMessage();
                    $transaction->rollback();
                } catch (Exception $e) {
                    $errors[$operation->id] = $e->getMessage();
                    $transaction->rollback();
                }

            } else {
                $errors[$operation->id] = 'Operation notes cannot be created for un-scheduled Operations. Please add free sessions.';
            }

            if( isset($errors[$operation->id]) ){
                $evevnt_date = new DateTime($operation->event->event_date);
                $errors[$operation->id] .= ' (' . $evevnt_date->format("d M Y") .': '. $operation->getProceduresCommaSeparated() . ')';
            }
        }

        return $errors ? $errors : true;

    }
    
    public function getFirstBookableSession(\Element_OphTrOperationbooking_Operation $operation)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('available', 1);
        $criteria->addCondition("date >= '" . date("Y-m-d") . "'"  );
        $criteria->order = 'date asc';
        
        $dataProvider = new CActiveDataProvider('OphTrOperationbooking_Operation_Session',
                array(
                    'criteria' => $criteria
                )
        );
        
        $session_iterator = new CDataProviderIterator($dataProvider);

        foreach ($session_iterator as $session){
            $is_bookable = $session->operationBookable($operation);

            if ($is_bookable && ($session->availableMinutes >= $operation->total_duration)) {
                return $session;
            }
        }
        
        return null;
    }

}
