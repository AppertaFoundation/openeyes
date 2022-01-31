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
class OphCoTherapyapplication_API extends BaseAPI
{
    /**
     * Therapy applications have no locking at the moment.
     *
     * @param int $event_id
     *
     * @return bool
     */
    public function canUpdate($event_id)
    {
        return true;
    }

    /**
     * Get the right side therapy application treatment date if there is one.
     *
     * @param $patient
     * @param $episode
     * @return string date of the treatment or "nil"
     * @throws Exception
     */
    public function getLetterApplicationTreatmentDateRight($patient)
    {
        if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
            return $this->getLetterApplicationTreatmentDateSide($patient, $episode, 'right');
        }

        return "nil";
    }

    /**
     * Get the left side therapy application treatment date if there is one.
     *
     * @param $patient
     * @param $episode
     * @return string date of the treatment or "nil"
     * @throws Exception
     */
    public function getLetterApplicationTreatmentDateLeft($patient)
    {
        if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
            return $this->getLetterApplicationTreatmentDateSide($patient, $episode, 'left');
        }

        return "nil";
    }

    /**
     * Get the side therapy application treatment date if there is one.
     *
     * @param $patient
     * @param $episode
     * @param $side
     * @return string date of the treatment or "nil"
     * @throws Exception
     */
    public function getLetterApplicationTreatmentDateSide($patient, $episode, $side)
    {
        if ($el = $this->getElementFromLatestEvent('Element_OphCoTherapyapplication_PatientSuitability', $patient)) {
            if ($el->{$side.'_treatment'}) {
                $event = $el->event;
                $date_time = new \DateTime($event->event_date);
                $date_time->format(\Helper::NHS_DATE_FORMAT);
                return $date_time->format(\Helper::NHS_DATE_FORMAT);
            }
        }
        return "nil";
    }

    /**
     * Gets the last drug that was applied for for the given patient, episode and side.
     *
     * @param Patient $patient
     * @param Episode $episode
     * @param string  $side
     *
     * @throws Exception
     *
     * @return OphTrIntravitrealinjection_Treatment_Drug
     */
    public function getLatestApplicationDrug($patient, $episode, $side)
    {
        if ($episode) {
            $event_type = $this->getEventType();

            $criteria = new CDbCriteria();
            $criteria->compare('event.event_type_id', $event_type->id);
            $criteria->compare('event.episode_id', $episode->id);
            $criteria->compare('event.deleted', 0);
            $criteria->order = 't.created_date desc';
            $criteria->limit = 1;

            $eye_ids = array('eye_id' => SplitEventTypeElement::BOTH);

            if ($side == 'left') {
                $eye_ids[] = SplitEventTypeElement::LEFT;
            } elseif ($side == 'right') {
                $eye_ids[] = SplitEventTypeElement::RIGHT;
            } else {
                throw new Exception('unrecognised side value '.$side);
            }

            $criteria->addInCondition('eye_id', $eye_ids);

            if ($suit = Element_OphCoTherapyapplication_PatientSuitability::model()->with('event', $side.'_treatment')->find($criteria)) {
                return $suit->{$side.'_treatment'}->drug;
            }
        }
    }

    /**
     * returns the side of the most recent application (see Eye for definition of constants that indicate side or both).
     *
     * @param unknown $patient
     * @param unknown $episode
     *
     * @return int $side
     */
    public function getLatestApplicationSide($patient, $use_context = false)
    {
        if ($el = $this->getLatestElement('Element_OphCoTherapyapplication_Therapydiagnosis', $patient, $use_context)) {
            return $el->eye_id;
        }
    }

    /**
     * return all the disorders for level 1.
     *
     * @return Disorder[]
     */
    public function getLevel1Disorders()
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'parent_id IS NULL';
        $criteria->order = 'display_order asc';

        $therapy_disorders = OphCoTherapyapplication_TherapyDisorder::model()->with('disorder')->findAll($criteria);
        $disorders = array();
        foreach ($therapy_disorders as $td) {
            $disorders[] = $td->disorder;
        }

        return $disorders;
    }

    /**
     * return all the disorders that are level 2 for the given $disorder_id.
     *
     * @param int $disorder_id
     *
     * @return Disorder[]
     */
    public function getLevel2Disorders($disorder_id)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'parent_id IS NULL AND disorder_id = :did';
        $criteria->params = array(':did' => $disorder_id);
        $disorders = array();

        if ($td = OphCoTherapyapplication_TherapyDisorder::model()->find($criteria)) {
            $disorders = $td->getLevel2Disorders();
        }

        return $disorders;
    }

    /**
     * return the diagnosis string for the patient on the given side.
     *
     * @param $patient
     * @param $side
     * @param $use_context
     */
    public function getLetterApplicationDiagnosisForSide($patient, $side, $use_context = false)
    {
        if ($el = $this->getElementFromLatestEvent(
            'Element_OphCoTherapyapplication_Therapydiagnosis',
            $patient,
            $use_context
        )
        ) {
            return $el->getDiagnosisStringForSide($side);
        }
    }

    /**
     * get the therapy application diagnosis description for the left.
     *
     * @param Patient $patient
     * @param $use_context
     * @return mixed
     */
    public function getLetterApplicationDiagnosisLeft($patient, $use_context = false)
    {
        return $this->getLetterApplicationDiagnosisForSide($patient, 'left', $use_context);
    }

    /**
     * get the therapy application diagnosis description for the right if there is one.
     *
     * @param Patient $patient
     * @param $use_context
     * @return mixed
     */
    public function getLetterApplicationDiagnosisRight($patient, $use_context = false)
    {
        return $this->getLetterApplicationDiagnosisForSide($patient, 'right', $use_context);
    }

    /**
     * get the therapy application diagnosis description for all sides that have one for this patient.
     *
     * @param Patient $patient
     * @param $use_context
     * @return string
     */
    public function getLetterApplicationDiagnosisBoth($patient, $use_context = false)
    {
        $res = '';
        if ($right = $this->getLetterApplicationDiagnosisRight($patient, $use_context)) {
            $res .= 'Right eye: '.$right;
        }
        if ($left = $this->getLetterApplicationDiagnosisLeft($patient, $use_context)) {
            if ($right) {
                $res .= "\n";
            }
            $res .= 'Left eye: '.$left;
        }
        if (strlen($res)) {
            return $res;
        }
    }

    /**
     * Get the therapy application treatment for the given side if there is one.
     *
     * @param $patient
     * @param $side
     * @param $use_context
     *
     * @return mixed
     */
    public function getLetterApplicationTreatmentForSide($patient, $side, $use_context = false)
    {
        if ($el = $this->getElementFromLatestEvent(
            'Element_OphCoTherapyapplication_PatientSuitability',
            $patient,
            $use_context
        )
        ) {
            if ($drug = $el->{$side.'_treatment'}) {
                return $drug->name;
            }
        }
    }

    /**
     * get the left side therapy application treatment if there is one.
     *
     * @param $patient
     * @param $use_context
     * @return mixed
     */
    public function getLetterApplicationTreatmentLeft($patient, $use_context = false)
    {
        return $this->getLetterApplicationTreatmentForSide($patient, 'left', $use_context);
    }

    /**
     * get the right side therapy application treatment if there is one.
     *
     * @param $patient
     * @param $use_context
     * @return mixed
     */
    public function getLetterApplicationTreatmentRight($patient, $use_context = false)
    {
        return $this->getLetterApplicationTreatmentForSide($patient, 'right', $use_context);
    }

    /**
     * get the therapy application treatment for all sides that have one for this patient.
     *
     * @param Patient $patient
     * @param $use_context
     * @return string
     */
    public function getLetterApplicationTreatmentBoth($patient, $use_context = false)
    {
        $res = '';
        if ($right = $this->getLetterApplicationTreatmentRight($patient, $use_context)) {
            $res .= $right.' to the right eye';
        }
        if ($left = $this->getLetterApplicationTreatmentLeft($patient, $use_context)) {
            if ($right) {
                $res .= ' and ';
            }
            $res .= $left.' to the left eye';
        }
        if (strlen($res)) {
            return $res;
        }
    }
}
