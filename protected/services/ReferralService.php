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
http://www.openeyes.org.uk	 info@openeyes.org.uk
--
*/

class ReferralService
{

	/**
	 * Perform a search based on the patient pas key
	 *
	 * @param int $hosNum
	 */
	public function getNewReferrals()
	{
		if (!Yii::app()->params['use_pas']) {
			return "The use_pas parameter in config/params.php must be set to true for this to work or the User model will ask for passwords.\n";
		}

		$mid = Yii::app()->db->createCommand()
				->select('MAX(refno) AS mrn')
				->from('referral')
				->queryRow();

		if (empty($mid['mrn'])) {
			echo "There are no referrals in the DB. This would cause every referral from PAS to be fetched.\n";
			exit;
		}
		
		$results = PAS_Referral::model()->findAll('REFNO > ? AND REF_SPEC <> \'OP\'', array($mid['mrn']));

		$errors = '';

		// Put all new PAS referrals in OE
		foreach ($results as $pasReferral) {
			$specialty = Specialty::model()->find('ref_spec = ?', array($pasReferral->REF_SPEC));

			if (empty($specialty)) {
				//echo 'No specialty for ref_spec ' . $pasReferral->REF_SPEC . "\n";
			} else {
				$ssa = ServiceSpecialtyAssignment::model()->find('specialty_id = ?', array($specialty->id));
				$referral = new Referral;

				$referral->service_specialty_assignment_id = $ssa->id;
				$referral->patient_id = $pasReferral->X_CN;
				$referral->refno = $pasReferral->REFNO;
	
				if (!empty($pasReferral->DT_CLOSE)) {
					$referral->closed = 1;
				}
	
				if ($referral->save()) {
					//echo 'Added referral refo ' . $referral->refno . "\n";
				} else {
					$errors .= "Unable to save referral refno $referral->refno: ".print_r($referral->getErrors(),true)."\n";
				}
			}
		}

		//echo "\nREFERRAL CREATION COMPLETE.\n\n";

		// Find all the open episodes with no referral
		$command = Yii::app()->db->createCommand()
			->select('p.id AS pid, ep.id AS epid, rea.id AS reaid, ssa.id AS ssaid')
			->from('patient p')
			->join('episode ep', 'ep.patient_id = p.id')
			->join('firm f', 'f.id = ep.firm_id')
			->join('service_specialty_assignment ssa', 'ssa.id = f.service_specialty_assignment_id')
			->leftJoin('referral_episode_assignment rea', 'rea.episode_id = ep.id')
			->where('ep.end_date IS NULL');

		foreach ($command->queryAll() as $result) {
			if (empty($result['reaid'])) {
				$referralId = $this->getReferral($result['pid'], $result['ssaid']);

				if ($referralId) {
					$rea = new ReferralEpisodeAssignment;
					$rea->episode_id = $result['epid'];
					$rea->referral_id = $referralId;
					if (!$rea->save()) {
						$errors .= 'Unable to save referral for epid ' . $result['epid'] . ' and referral ' . $referralId . "\n";
					} else {
						//echo 'Assignment rea id ' . $rea->id . ' to patient ' . $result['epid'] . ' and referral ' . $referralId . "\n";
					}
				}
			}
		}

		if ($errors) {
			$hostname = trim(`/bin/hostname`);
			mail(Yii::app()->params['alerts_email'],"[$hostname] Referrals crontab failed",$errors);
		}

		//echo "\nREFERRAL ASSIGNMENT COMPLETE.\n\n";
	}

	/**
	 * Return the list of referrals to choose from for an episode, if any.
	 *
	 * @param $firm object
	 * @param $patientId id
	 *
	 * @return array
	 */
	public function getReferral($patientId, $ssaId)
	{
		if (!Yii::app()->params['use_pas']) {
			return false;
		}

		// Look for open referrals of this service
		$referrals = Referral::model()->findAll(
			array(
				'order' => 'refno DESC',
				'condition' => 'patient_id = :p AND service_specialty_assignment_id = :s AND closed = 0',
				'params' => array(
					':p' => $patientId,
					':s' => $ssaId
				),
				'limit' => 1
			)
		);

		if (count($referrals)) {
			// There is at least one open referral for this service, so return that.
			return $referrals[0]->id;
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
			return $referrals[0]->id;
		}

		// There are no open referrals so no referral can be associated.
		return false;
	}
}
