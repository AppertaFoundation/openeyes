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
class OphTrOperationnote_API extends BaseAPI
{
    /**
     * Return the list of procedures as a string for use in correspondence for the given patient and episode.
     * if the $snomed_terms is true, return the snomed_term, otherwise the standard text term.
     *
     * @param Patient $patient
     * @param boolean $use_context - defaults to true
     * @return string
     */
    public function getLetterProcedures($patient, $use_context = false)
    {
        $return = '';
        $procedureList =  $this->getElementFromLatestSameDayEvents('Element_OphTrOperationnote_ProcedureList', $patient, $use_context);
        if ($procedureList) {
            foreach ($procedureList as $procedureIndex => $plist) {
                foreach ($plist->procedures as $i => $procedure) {
                    if ($i) {
                        $return .= ', ';
                    }
                    $return .= $plist->eye->adjective . ' ' . $procedure->term;
                }
                if ($procedureIndex !== (count($procedureList)-1)) {
                    $return .= ', ';
                }
            }
        }

        return $return;
    }

    /*
     * @param Patient $patient
     * @param boolean $use_context - defaults to true
     * @return integer
     */
    public function getLetterProceduresBookingEventID($patient, $use_context = false)
    {
        if ($plist =  $this->getElementFromLatestEvent('Element_OphTrOperationnote_ProcedureList', $patient, $use_context)) {
            return $plist->booking_event_id;
        }
    }

    /*
     * @param Patient $patient
     * @param boolean $use_context - defaults to true
     * @return integer
     */
    public function getLastEye($patient, $use_context = false)
    {
        if ($plist =  $this->getElementFromLatestEvent('Element_OphTrOperationnote_ProcedureList', $patient, $use_context)) {
            return $plist->eye_id;
        }
    }

    /*
     * Operations carried out with SNOMED terms
     * @param Patient $patient
     * @param boolean $use_context - defaults to true
     * @return string
     */
    public function getLetterProceduresSNOMED($patient, $use_context = false)
    {
        $return = '';
        if ($plist =  $this->getElementFromLatestEvent('Element_OphTrOperationnote_ProcedureList', $patient, $use_context)) {
            foreach ($plist->procedures as $i => $procedure) {
                if ($i) {
                    $return .= ', ';
                }
                $return .= $plist->eye->adjective . ' ' . $procedure->snomed_term;
            }
        }
        return $return;
    }

    public function getOpnoteWithCataractElementInCurrentEpisode($patient, $use_context = false)
    {
        if ($episode = $this->getLatestEvent($patient, $use_context)) {
            $event_type = EventType::model()->find('class_name=?', array('OphTrOperationnote'));

            $criteria = new CDbCriteria();
            $criteria->compare('episode_id', $episode->episode_id);
            $criteria->compare('event_type_id', $event_type->id);

            return Element_OphTrOperationnote_Cataract::model()
                ->with('event')
                ->find($criteria);
        }
    }

    public function getPatientUniqueCode($patient, $use_context = false)
    {
        $patient_latest_event = $this->getLatestEventUniqueCode($patient, $use_context);
        OELog::log($patient_latest_event);
        $event_unique_code = '';
        if (!empty($patient_latest_event)) {
            $salt = isset(Yii::app()->params['portal']['credentials']['client_id']) ? Yii::app()->params['portal']['credentials']['client_id'] : '';
            $check_digit1 = new CheckDigitGenerator(
                Yii::app()->params['institution_code'] . $patient_latest_event,
                $salt
            );
            $check_digit2 = new CheckDigitGenerator(
                $patient_latest_event . $patient->dob,
                $salt
            );
            $event_unique_code = Yii::app()->params['institution_code'] . $check_digit1->generateCheckDigit()
                . '-' . $patient_latest_event . '-' . $check_digit2->generateCheckDigit();
        }

        return $event_unique_code;
    }

    /**
     * Last operation date
     * @param Patient $patient
     * @param boolean $use_context
     * @return false|string
     */

    public function getLastOperationDate(\Patient $patient, $use_context = false)
    {
        $event = $this->getLatestEvent($patient, $use_context);
        if (isset($event->event_date)) {
            return Helper::convertDate2NHS($event->event_date);
        }
        return '';
    }

    /**
     * Get the last operation date
     * @param Patient $patient
     * @param boolean $use_context - defaults to false
     * @return false|string
     *
     * @deprecated - since v2.0. Not replaced event date is inadequate for comparisons
     */
    public function getLastOperationDateUnformatted(\Patient $patient, $use_context = false)
    {
        $event = $this->getLatestEvent($patient, $use_context);
        if (isset($event->event_date)) {
            return $event->event_date;
        }
        return '';
    }

    /**
     * Get the last operation's surgeon name
     *
     * @param Patient $patient
     * @param boolean $use_context - defaults to false
     * @return string
     */

    public function getLastOperationSurgeonName(\Patient $patient, $use_context = false)
    {
        $surgeon_name = '';
        if ($surgeon_element =  $this->getElementFromLatestEvent('Element_OphTrOperationnote_Surgeon', $patient, $use_context)) {
            $surgeon_name = ($surgeon = User::model()->findByPk($surgeon_element->surgeon_id)) ? $surgeon->getFullNameAndTitle() : '';
        }
        return $surgeon_name;
    }

    /**
     * Get the last operation's location
     *
     * @param Patient $patient
     * @param boolean $use_context - defaults to false
     * @return string
     */
    public function getLastOperationLocation(\Patient $patient, $use_context = false)
    {
        $site = '';
        if ($site_element =  $this->getElementFromLatestEvent('Element_OphTrOperationnote_SiteTheatre', $patient, $use_context)) {
            $site = $site_element->site->name;
        }
        return $site;
    }


    /*
     * Cataract Element from the latest operation note
     * @param Patient $patient
     * @param boolean $use_context - defaults to false
     */
    public function getLatestCataractElementForEpisode(\Patient $patient, $use_context = false)
    {
        if ($cataract_element =  $this->getElementFromLatestEvent('Element_OphTrOperationnote_Cataract', $patient, $use_context)) {
            return $cataract_element;
        }
        return false;
    }

    /**
     * Get the last operation Incision Meridian
     * @param Patient $patient
     * @param bool $use_context
     * @return string
     */
    public function getLastOperationIncisionMeridian(\Patient $patient, $use_context = true)
    {
        $meridian = '';
        $cataract_element = $this->getLatestCataractElementForEpisode($patient, $use_context);
        if ($cataract_element) {
            $meridian = $cataract_element->meridian . ' degrees';
        }

        return $meridian;
    }

    /**
     * Get the last operation Predicted Refraction
     * @param Patient $patient
     * @param bool $use_context
     * @return string
     */
    public function getLastOperationPredictedRefraction(\Patient $patient, $use_context = true)
    {
        $predicted_refraction = '';
        if ($cataract_element = $this->getLatestCataractElementForEpisode($patient, $use_context)) {
            $predicted_refraction = $cataract_element->predicted_refraction ?: '';
        }

        return $predicted_refraction;
    }

    /**
     * Get the last operation Details
     * @param Patient $patient
     * @param bool $use_context
     * @return string
     */
    public function getLastOperationDetails(\Patient $patient, $use_context = true)
    {
        $details = '';
        if ($cataract_element = $this->getLatestCataractElementForEpisode($patient, $use_context)) {
            $details = (empty($cataract_element->report2) ? $cataract_element->report : $cataract_element->report2) ."\n". ($cataract_element->comments ?: '');
        }

        return $details;
    }


    /**
     * Get the last operation peri-operative complications to cataract, trabeculectomy and trabectome
     * @param Patient $patient
     * @param $use_context
     * @return string
     */
    public function getLastOperationPeriOperativeComplications(\Patient $patient, $use_context = false)
    {
        $result = '';
        if ($latestCataract = $this->getElementFromLatestEvent(
            'Element_OphTrOperationnote_Cataract',
            $patient,
            $use_context
        )
        ) {
            $result .= 'Cataract complications: '.$latestCataract->getComplicationsString();
            $result .="\n";
        }

        if ($latestTrabeculectomy = $this->getElementFromLatestEvent(
            'Element_OphTrOperationnote_Trabeculectomy',
            $patient,
            $use_context
        )
        ) {
            $result .= 'Trabeculectomy complications: '.$latestTrabeculectomy->getComplicationsString();
            $result .="\n";
        }

        if ($latestTrabectome = $this->getElementFromLatestEvent(
            'Element_OphTrOperationnote_Trabectome',
            $patient,
            $use_context
        )
        ) {
            $result .= 'Trabectome complications: '.$latestTrabectome->getComplicationsString();
            $result .="\n";
        }

        return $result;
    }

    /**
     * Get the last operation comments
     * @param Patient $patient
     * @param $use_context
     * @return string
     */
    public function getLastOperationComments(\Patient $patient, $use_context = false)
    {
        $comments = '';
        if ($comments = $this->getElementFromLatestEvent(
            'Element_OphTrOperationnote_Comments',
            $patient,
            $use_context
        )
        ) {
            return $comments->comments;
        }
        return $comments;
    }

    /**
     * Get the last operation Post-op instructions
     * @param Patient $patient
     * @param boolean $use_context - defaults to true
     * @return string
     */

    public function getLastOperationPostOpInstructions(\Patient $patient, $use_context = false)
    {
        if ($latest =  $this->getElementFromLatestEvent('Element_OphTrOperationnote_Comments', $patient, $use_context)) {
            return $latest->postop_instructions;
        }
        return '';
    }

    /**
     * @param Patient $patient
     * @return array
     */
    public function getOperationsSummaryData(Patient $patient, $use_context = false)
    {
        $operations = array();
        foreach ($this->getElements('Element_OphTrOperationnote_ProcedureList', $patient, $use_context) as $element) {
            $operations[] = array(
                'date' => $element->event->event_date,
                'side' => $element->eye->adjective,
                'operation' => implode(
                    ', ',
                    array_map(
                        function($proc) {
                            // if there is no short_format for this procedure, fallback to the long term
                            return $proc->short_format ?: $proc->term;
                        },
                        $element->procedures
                    )
                ),
                'link' => '/OphTrOperationnote/Default/view/' . $element->event_id
            );
        }
        return $operations;
    }

    public function getLatestEventUniqueCode(Patient $patient, $use_context = false)
    {
        $event = $this->getLatestEvent($patient, $use_context);
        if (!empty($event)) {
            OELog::log($event->id);
            return UniqueCodes::codeForEventId($event->id);
        } else {
            return '';
        }
    }

    /**
     * This method will be triggered
     * after every softDelete calls
     * on Events of type OphTrOperationnote
     */

    public function afterSoftDeleteEvent(Event $event)
    {
        $proclist = Element_OphTrOperationnote_ProcedureList::model()->find('event_id=?', array($event->id));
        if ($proclist && $proclist->booking_event_id) {
            if ($api = Yii::app()->moduleAPI->get('OphTrOperationbooking')) {
                $last_status_id = $api->getLastNonCompleteStatus($proclist->booking_event_id);
                $status = OphTrOperationbooking_Operation_Status::model()->findByPk($last_status_id);
                $api->setOperationStatus($proclist->booking_event_id, $status->name);
            }
        }
    }

    /**
     * get laterality of event by looking at the procedure list element eye side
     *
     * @param $event_id
     * @return mixed
     * @throws Exception
     */
    public function getLaterality($event_id)
    {
        $operation_note = Element_OphTrOperationnote_ProcedureList::model()->find('event_id=?', array($event_id));
        if (!$operation_note) {
            throw new Exception("Operation note (procedure list) event not found: $event_id");
        }

        return $operation_note->eye;
    }

}
