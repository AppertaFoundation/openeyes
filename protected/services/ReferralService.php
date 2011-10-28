<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

class ReferralService
{

	/**
	 * Perform a search based on the patient pas key
	 *
	 * @param int $hosNum
	 */
	public function search($hosNum)
	{
		$results = PAS_Referral::model()->findAll('X_CN = ?', array($hosNum));

		if (!empty($results)) {
			foreach ($results as $pasReferral) {
				$patient = Patient::model()->find('pas_key = ?', array($pasReferral->X_CN));

				$specialty = Specialty::model()->find('ref_spec = ?', array($pasReferral->REFSPEC));

				$referral = Referral::model()->find('refno = ?', array($pasReferral->REFNO));

				if (!isset($referral)) {
					$referral = new Referral;
				}

				$referral->service_id = $specialty->id;
				$referral->patient_id = $patient->id;
				$referral->refno = $pasReferral->REFNO;

				if (!empty($pasReferral->DT_CLOSE)) {
					$referral->closed = 1;
				}

				$referral->save();
			}
		}
	}

	/**
	 * Return the list of referrals to choose from for an episode, if any.
	 *
	 * @param $firm object
	 * @param $patientId id
	 *
	 * @return array
	 */
	public function getReferral($firm, $patientId)
	{
		if (!Yii::app()->params['use_pas']) {
			return false;
		}

		// Check for an open episode for this patient and firm's service with a referral
		$episode = Yii::app()->db->createCommand()
			->select('referral_id AS rid')
			->from('referral_episode_assignment r_e_a')
			->join('episode e', 'e.id = r_e_a.episode_id')
			->join('firm f', 'e.firm_id = f.id')
			->join('service_specialty_assignment s_s_a', 'f.service_specialty_assignment_id = s_s_a.id')
			->where('e.end_date IS NULL AND e.patient_id = :patient_id AND s_s_a.specialty_id = :specialty_id', array(
				':patient_id' => $patientId, ':specialty_id' => $firm->serviceSpecialtyAssignment->specialty_id
			))
			->queryRow();

		if (isset($episode['rid'])) {
			// There is an open episode and it has a referral, no action required
			return false;
		}

		// Look for open referrals of this service
		$referrals = Referral::model()->findAll(
			array(
				'order' => 'refno DESC',
				'condition' => 'patient_id = :p AND service_id = :s AND closed = 0',
				'params' => array(
					':p' => $patientId,
					':s' => $firm->serviceSpecialtyAssignment->specialty_id
				),
				'limit' => 1
			)
		);

		if (count($referrals)) {
			// There is at least one open referral for this service, so return that.
			return $referrals[0];
		}

		// There are no open referrals for this specialty, try and find open referrals for a different
		// specialty
		$referrals = Referral::model()->findAll(
			array(
				'order' => 'refno DESC',
				'condition' => 'patient_id = :p AND closed = 0',
				'params' => array(':p' => $patientId),
				'limit' => 1
			)
		);

		if (count($referrals)) {
			// There are referrals, use the newest one
			return $referrals[0];
		}

		// There are no open referrals so no referral can be associated.
		return false;
	}

	public function assignReferral($eventId, $firm, $patientId)
	{
		$referral = $this->getReferral($firm, $patientId);

		if (empty($referral)) {
			// Either there is already a referral for the episode or there are no open referrals
			// for this patient, so do nothing
			return;
		}

		$event = Event::model()->findByPk($eventId);

		if (!isset($event)) {
			return;
		}

		$rea = new ReferralEpisodeAssignment;

		$rea->episode_id = $event->episode_id;
		$rea->referral_id = $referral->id;
		$rea->save();
	}
}
