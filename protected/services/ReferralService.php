<?php

class ReferralService
{

	/**
	 * Perform a search based on the patient pas key
	 *
	 * @param int $pasKey
	 */
	public function search($pasKey)
	{
		$results = PAS_Referral::model()->findAll('X_CN = ?', array($pasKey));

		if (!empty($results)) {
			foreach ($results as $pasReferral) {
				$patient = Patient::model()->find('pas_key = ?', array($pasReferral->X_CN));

// @todo what's going on here? Specialties are not as they should be in PAS so findByPk(1) has been left here for now.
				//$specialty = Specialty::model()->find('ref_spec = ?', array($pasReferral->REFSPEC));
				$specialty = Specialty::model()->findByPk(1);

				$referral = Referral::model()->find('refno = ?', array($pasReferral->REFNO));

				if (!isset($referral)) {
					$referral = new Referral;
				}

// @todo - does ref_spec refer to a specialty or service?
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
	public function getReferralsList($firm, $patientId)
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
			->where('e.end_date IS NULL AND e.patient_id = :patient_id AND s_s_a.service_id = :service_id', array(
				':patient_id' => $patientId, ':service_id' => $firm->serviceSpecialtyAssignment->service_id
			))
			->queryRow();

		if (isset($episode['rid'])) {
			// There is an open episode and it has a referral, no action required
			return false;
		}

		// Look for open referrals of this specialty
		// @todo - change this to just get the top one
		// @todo - is refno DESC the correct way of determining the most recent referral?
		$referrals = Referral::model()->findAll(
			array(
				'order' => 'refno DESC',
				'condition' => 'patient_id = :p AND service_id = :s AND closed = 0',
				'params' => array(
					':p' => $patientId,
					':s' => $firm->serviceSpecialtyAssignment->service_id
				)
			)
		);

		if (count($referrals)) {
			// There is at least one open referral for this service, so return that.
			return array($referrals[0]);
		}

		// There are no open referrals for this specialty, try and find open referrals for a different
		// specialty
		$referrals = Referral::model()->findAll(
			array(
				'order' => 'refno DESC',
				'condition' => 'patient_id = :p AND closed = 0',
				'params' => array(':p' => $patientId)
			)
		);

		if (count($referrals)) {
			// There are referrals, use the newest one
			return $referrals;
		}

		// There are no open referrals so no referral can be associated.
		return false;
	}

	public function assignReferral($eventId, $firm, $patientId)
	{
		$referrals = $this->getReferralsList($firm, $patientId);

		if (!isset($referrals) || !$referrals) {
			// Either there is already a referral for the episode or there are no open referrals
			// for this patient, so do nothing
			return;
		}

		if (is_array($referrals)) {
			// There is at least one referral - check to see if the referral_id provided by the user is in it.
			// If not, assign the first referral to the episode.
			if (isset($_REQUEST['referral_id'])) {
				foreach ($referrals as $referral) {
					if ($referral->id = $_REQUEST['referral_id']) {
						$this->addReferral($eventId, $referral->id);
						return;
					}
				}
			}

			// No referral_id provided, or doesn't match, so assign the first referral in the list to the episode
			$this->addReferral($eventId, $referrals[0]->id);
		}
	}

	public function addReferral($eventId, $referralId)
	{
		$event = Event::model()->findByPk($eventId);

		if (!isset($event)) {
			// @todo - what to do here?
			return;
		}

		$rea = new ReferralEpisodeAssignment;

		$rea->episode_id = $event->episode_id;
		$rea->referral_id = $referralId;
		$rea->save();
	}
}
