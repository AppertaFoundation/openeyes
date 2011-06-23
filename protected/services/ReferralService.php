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
	 * Attempts to assign a referral to an episode.
	 *
	 * If the episode already has a referral then no action is required. The function returns true.
	 *
	 * If there are one or more open referral with the same specialty, the most recently created one is
	 * associated with the episode and the function returns true.
	 *
	 * If there are one or more open episodes of the incorrect specialty the most recent is chosen by default and the
	 * function returns false so that the user can choose one manually.
	 *
	 * If there are no open referrals none is associated with the episode and the function returns true.
	 * One will have to be chosen later, e.g. when the next event for the episode is chosen (if any).
	 *
	 * @param int $eventId
	 * @return boolean
	 */
	public function manualReferralNeeded($eventId)
	{
		$event = Event::model()->findByPk($eventId);

		if (!isset($event)) {
			// @todo - is this the correct exception type? This should never happen...
			throw new Exception('No event of that id.');
		}

		$referralEpisode = ReferralEpisodeAssignment::model()->find('episode_id = ?', array($event->episode_id));

		if (isset($referralEpisode)) {
			// There is already at least one referral for this episode, return true
			return false;
		}

		// Look for open referrals of this specialty
		// @todo - change this to just get the top one, ordered by refno DESC
		// @todo - is refno DESC the correct way of determining the most recent referral?
		$referrals = Referral::model()->findAll(			
			array(
				'order' => 'refno DESC',
				'condition' => 'patient_id = :p AND service_id = :s AND closed = 0',
				'params' => array(
					':p' => $event->episode->patient_id,
					':s' => $event->episode->firm->serviceSpecialtyAssignment->service_id
				)
			)
		);

		if (count($referrals)) {
			// There are referrals, use the newest one
			$referralEpisode = new ReferralEpisodeAssignment;
			$referralEpisode->episode_id = $event->episode_id;
			$referralEpisode->referral_id = $referrals[0]->id;
			$referralEpisode->save();
		
			return false;
		}

		// There are no open referrals for this specialty, try and find open referrals for a different
		// specialty
		$referrals = Referral::model()->findAll(
			array(
				'order' => 'refno DESC',
				'condition' => 'patient_id = :p AND closed = 0',
				'params' => array(':p' => $event->episode->patient_id)
			)
		);

		if (count($referrals)) {
			// There are referrals, use the newest one
			$referralEpisode = new ReferralEpisodeAssignment;
			$referralEpisode->episode_id = $event->episode_id;
			$referralEpisode->referral_id = $referrals[0]->id;
			$referralEpisode->save();

			if (count($referrals) > 1) {
				// There's more than one referral so return the chosen default id
				return $referralEpisode->id;
			}
		}

		// Either there are no open referrals of any specialty or there is only one open
		// referral of a specialty other than the one required. therefore the user is not
		// required to choose a specialty
		return false;
	}

}
