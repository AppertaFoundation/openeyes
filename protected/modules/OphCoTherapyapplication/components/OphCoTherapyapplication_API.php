<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class OphCoTherapyapplication_API extends BaseAPI
{

	/**
	 * Therapy applications have no locking at the moment
	 * @param integer $event_id
	 * @return boolean
	 */
	public function canUpdate($event_id)
	{
		return true;
	}

	/**
	 * Gets the last drug that was applied for for the given patient, episode and side
	 *
	 * @param Patient $patient
	 * @param Episode $episode
	 * @param string $side
	 * @throws Exception
	 *
	 * @return OphTrIntravitrealinjection_Treatment_Drug
	 */
	public function getLatestApplicationDrug($patient, $episode, $side)
	{
		if ($episode) {
			$event_type = $this->getEventType();

			$criteria = new CDbCriteria;
			$criteria->compare('event.event_type_id',$event_type->id);
			$criteria->compare('event.episode_id',$episode->id);
			$criteria->order = 't.created_date desc';
			$criteria->limit = 1;

			$eye_ids = array('eye_id' => SplitEventTypeElement::BOTH);

			if ($side == 'left') {
				$eye_ids[] = SplitEventTypeElement::LEFT;
			} elseif ($side == 'right') {
				$eye_ids[] = SplitEventTypeElement::RIGHT;
			} else {
				throw new Exception('unrecognised side value ' . $side);
			}

			$criteria->addInCondition('eye_id', $eye_ids);

			if ($suit = Element_OphCoTherapyapplication_PatientSuitability::model()->with('event', $side . '_treatment')->find($criteria)) {
				return $suit->{$side . '_treatment'}->drug;
			}

		}
	}

	/**
	 * returns the side of the most recent application (see Eye for definition of constants that indicate side or both)
	 *
	 * @param unknown $patient
	 * @param unknown $episode
	 *
	 * @return int $side
	 */
	public function getLatestApplicationSide($patient, $episode)
	{
		if ($el = $this->getMostRecentElementInEpisode($episode->id, $this->getEventType()->id, 'Element_OphCoTherapyapplication_Therapydiagnosis')) {
			return $el->eye_id;
		}
	}

	/**
	 * return all the disorders for level 1
	 *
	 * @return Disorder[]
	 */
	public function getLevel1Disorders()
	{
		$criteria = new CDbCriteria;
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
	 * return all the disorders that are level 2 for the given $disorder_id
	 *
	 * @param integer $disorder_id
	 * @return Disorder[]
	 */
	public function getLevel2Disorders($disorder_id)
	{
		$criteria = new CDbCriteria;
		$criteria->condition = 'parent_id IS NULL AND disorder_id = :did';
		$criteria->params = array(':did' => $disorder_id);
		$disorders = array();

		if ($td = OphCoTherapyapplication_TherapyDisorder::model()->find($criteria)) {
			$disorders = $td->getLevel2Disorders();
		}

		return $disorders;
	}

	/**
	 *
	 * return the diagnosis string for the patient on the given side
	 * @param $patient
	 * @param $episode
	 * @param $side
	 */
	public function getLetterApplicationDiagnosisForSide($patient, $episode, $side)
	{
		if ($el = $this->getElementForLatestEventInEpisode($episode, 'Element_OphCoTherapyapplication_Therapydiagnosis')) {
			return $el->getDiagnosisStringForSide($side);
		}
	}

	/**
	 * get the therapy application diagnosis description for the left
	 *
	 * @param Patient $patient
	 * @return mixed
	 */
	public function getLetterApplicationDiagnosisLeft($patient)
	{
		if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
			return $this->getLetterApplicationDiagnosisForSide($patient, $episode, 'left');
		}
	}

	/**
	 * get the therapy application diagnosis description for the right if there is one
	 *
	 * @param Patient $patient
	 * @return mixed
	 */
	public function getLetterApplicationDiagnosisRight($patient)
	{
		if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
			return $this->getLetterApplicationDiagnosisForSide($patient, $episode, 'right');
		}
	}

	/**
	 * get the therapy application diagnosis description for all sides that have one for this patient.
	 *
	 * @param Patient $patient
	 * @return string
	 */
	public function getLetterApplicationDiagnosisBoth($patient)
	{
		$res = "";
		if ($right = $this->getLetterApplicationDiagnosisRight($patient)) {
			$res .= "Right eye: " . $right;
		}
		if ($left = $this->getLetterApplicationDiagnosisLeft($patient)) {
			if ($right) {
				$res .= "\n";
			}
			$res .= "Left eye: " . $left;
		}
		if (strlen($res)) {
			return $res;
		}
	}

	/**
	 * Get the therapy application treatment for the given side if there is one.
	 *
	 * @param $patient
	 * @param $episode
	 * @param $side
	 * @return mixed
	 */
	public function getLetterApplicationTreatmentForSide($patient, $episode, $side)
	{
		if ($el = $this->getElementForLatestEventInEpisode($episode, 'Element_OphCoTherapyapplication_PatientSuitability')) {
			if ($drug = $el->{$side . '_treatment'}) {
				return $drug->name;
			}
		}
	}

	/**
	 * get the left side therapy application treatment if there is one
	 *
	 * @param $patient
	 * @return mixed
	 */
	public function getLetterApplicationTreatmentLeft($patient)
	{
		if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
			return $this->getLetterApplicationTreatmentForSide($patient, $episode, 'left');
		}
	}

	/**
	 * get the right side therapy application treatment if there is one
	 *
	 * @param $patient
	 * @return mixed
	 */
	public function getLetterApplicationTreatmentRight($patient)
	{
		if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
			return $this->getLetterApplicationTreatmentForSide($patient, $episode, 'right');
		}
	}

	/**
	 * get the therapy application treatment for all sides that have one for this patient.
	 *
	 * @param Patient $patient
	 * @return string
	 */
	public function getLetterApplicationTreatmentBoth($patient)
	{
		$res = "";
		if ($right = $this->getLetterApplicationTreatmentRight($patient)) {
			$res .= $right . " to the right eye";
		}
		if ($left = $this->getLetterApplicationTreatmentLeft($patient)) {
			if ($right) {
				$res .= " and ";
			}
			$res .= $left . " to the left eye";
		}
		if (strlen($res)) {
			return $res;
		}
	}
}
