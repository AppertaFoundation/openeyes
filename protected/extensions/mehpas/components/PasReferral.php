<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/** 
 * Temporary container for PAS referral integration code
 * @author jamie
 *
 * FIXME: This code is unfinished and probably broken in many ways. It need reviewing and if necessary completely rebuilding
 */
class PasReferral {
	
	/**
	 * Match referrals to episodes that don't currently have one
	 */
	public function matchReferrals() {
		// Find all the open episodes with no referral
		$episodes = Yii::app()->db->createCommand()
		->select('ep.id AS episode_id, ep.patient_id AS patient_id, rea.id AS rea_id, ssa.id AS ssa_id')
		->from('episode ep')
		->join('firm f', 'f.id = ep.firm_id')
		->join('service_specialty_assignment ssa', 'ssa.id = f.service_specialty_assignment_id')
		->leftJoin('referral_episode_assignment rea', 'rea.episode_id = ep.id')
		->where('ep.end_date IS NULL AND rea_id IS NULL')
		->queryAll();

		foreach($episodes as $episode) {
			if ($referral = $this->getReferral($episode['patient_id'], $episode['ssa_id'])) {
				Yii::log("Found referral_id $referral->id for episode_id ".$episode['episode_id'].", creating assignment", 'trace');
				$rea = new ReferralEpisodeAssignment();
				$rea->episode_id = $episode['episode_id'];
				$rea->referral_id = $referral->id;
				$rea->save();
			} else {
				Yii::log("Cannot find a referral for episode_id ".$episode['episode_id'], 'trace');
			}
		}
	}

	protected function getReferral($patient_id, $ssa_id) {

		// Look for open referrals of this service
		$referral = Referral::model()->find(array(
				'condition' => 'patient_id = :p AND service_specialty_assignment_id = :s AND closed = 0',
				'order' => 'refno DESC',
				'params' => array(':p' => $patientId, ':s' => $ssaId),
		)
		);
		if ($referral) {
			return $referral;
		}

		// There are no open referrals for this specialty, try and find open referrals for a different specialty
		$referral = Referral::model()->find(array(
				'order' => 'refno DESC',
				'condition' => 'patient_id = :p AND closed = 0',
				'params' => array(':p' => $patientId),
		)
		);

		return $referral;
	}

	/**
	 * Fetch new referrals from PAS and link them to patients
	 */
	public function fetchNewReferrals() {
		Yii::log('Fetching new referrals from PAS', 'trace');

		// Find the last referral that is not linked to an episode. This is required because referrals fetched
		// on demand may otherwise leave holes (and these will always be linked to an episode).
		$last_refno = Yii::app()->db->createCommand()
		->select('MAX(refno) AS mrn')
		->from('referral')
		->leftJoin('referral_episode_assignment', 'referral_episode_assignment.referral_id = referral.id')
		->where('episode_id IS NULL')
		->queryScalar();

		// Get new referrals
		$pas_referrals = PAS_Referral::model()->findAll("REFNO > :last_refno AND REF_SPEC <> 'OP'", array(
				':last_refno' => $last_refno,
		));
		if(count($pas_referrals) > 1000) {
			echo "There are more than 1000 new referrals to fetch, aborting.\n";
			Yii::app()->end();
		}

		$errors = '';
		foreach ($pas_referrals as $pas_referral) {

			// First check that we haven't already imported this referral (on demand)
			if($referral = Referral::model()->findByAttributes(array('refno' => $pas_referral->REFNO))) {
				Yii::log("Imported referral already exists for REFNO $pas_referral->REFNO, skipping", 'trace');
				continue;
			}

			// Find matching specialty
			$specialty = Specialty::model()->find('ref_spec = :ref_spec', array(
					':ref_spec' => $pas_referral->REF_SPEC,
			));
			if(!$specialty) {
				Yii::log("Cannot find specialty for REF_SPEC $pas_referral->REF_SPEC, REFNO $pas_referral->REFNO, skipping", 'trace');
				continue;
			}
			$ssa = ServiceSpecialtyAssignment::model()->find('specialty_id = :specialty_id', array(
					':specialty_id' => $specialty->id,
			));

			// Match referral to a Patient
			if ($pas_referral->X_CN) {
				$external_id = $pas_referral->X_CN;
			} else {
				/**
				 * Due to a dodgy migration by some third party company we don't always have the X_CN field containing
				 * the patient number, so this is an alternative way to obtain it.
				 * TODO: We should probably do something similar for the referral event handler
				 */
				$external_id = Yii::app()->db_pas->createCommand()
				->select('distinct b.X_CN')
				->from('SILVER.OUT040_REFDETS a')
				->leftJoin('SILVER.OUT031_OUTAPPT b', 'a.REFNO = b.REFNO and a.REF_SPEC = b.CLI_SPEC')
				->where("a.REFNO = '$pas_referral->REFNO'")
				->queryScalar();
				if(!$external_id) {
					Yii::log("Cannot find patient (external_id) for REFNO $referral->refno, skipping", 'trace');
					continue;
				}
			}

			// Find patient
			Yii::log("Looking for patient with external_id $external_id", 'trace');
			$pas_patient_num = PAS_PatientNumber::model()->find("RM_PATIENT_NO = :rm_patient_no AND REGEXP_LIKE(NUM_ID_TYPE, '[[:digit:]]')", array(
					':rm_patient_no' => $external_id,
			));
			$patient_assignment = $this->findPatientAssignment($external_id, $pas_patient_num->NUM_ID_TYPE . $pas_patient_num->NUMBER_ID);

			// Create new referral
			Yii::log("Creating new referral REFNO $referral->refno", 'trace');
			$referral = new Referral();
			$referral->service_specialty_assignment_id = $ssa->id;
			$referral->refno = $pas_referral->REFNO;
			$referral->patient_id = $patient_assignment->internal_id;
			if(!empty($pas_referral->DT_CLOSE)) {
				$referral->closed = 1;
			}
			$referral->save();

		}

		$this->matchReferrals();

	}

	/**
	 * Find and associate a referral to an episode if it doesn't already have one
	 * @param Episode $episode
	 */
	public function matchReferralToEpisode($episode) {
		Yii::log("Trying to match referral to episode_id $episode->id", 'trace');
		$patient_id = $episode->patient_id;
		$ssa_id = $episode->firm->service_specialty_assignment_id;

		// First try to match patient _and_ service specialty
		$referrals = Referral::model()->findAll(array(
				'condition' => 'patient_id = :patient_id AND service_specialty_assignment_id = :ssa_id AND closed = 0',
				'order' => 'refno DESC',
				'params' => array(
						':patient_id' => $patient_id,
						':ssa_id' => $ssa_id
				),
		));
		if(count($referrals) == 1) {
			// Found one matching referral, so we assume this is the right one
			$referral = $referrals[0];
		} else if(count($referrals) > 1) {
			// Found more than one candidate, cannot continue
			return false;
		} else {
			// No referrals found
			$referral = false;
		}

		if(!$referral) {
			// Fall back to trying a looser match on just the patient
			$referrals = Referral::model()->findAll(array(
					'condition' => 'patient_id = :patient_id AND closed = 0',
					'order' => 'refno DESC',
					'params' => array(
							':patient_id' => $patient_id,
							':ssa_id' => $ssa_id
					),
			));
			if(count($referrals) == 1) {
				// Found one matching referral, so we assume this is the right one
				Yii::log('One referral found on loose match');
				$referral = $referrals[0];
			} else if(count($referrals) > 1) {
				// Found more than one candidate, cannot continue
				Yii::log('More than one referral found on loose match');
				return false;
			} else {
				// No referrals found
				Yii::log('No referrals found on loose match');
				return false;
			}

		}

		$assignment = PasAssignment::model()->findByInternal('Patient', $episode->patient_id);
		if(!$assignment) {
			throw new CException("Patient has no PAS assignment, cannot fetch referral");
		}
		$rm_patient_no = $assignment->external_id;
		$ref_spec = $episode->firm->serviceSpecialtyAssignment->specialty->ref_spec;
		$pas_referrals = PAS_Referral::model()->findAll(array(
				'condition' => 'x_cn = :rm_patient_no',
				'params' => array(
						':rm_patient_no' => $rm_patient_no,
						':ref_spec' => $ref_spec,
				),
		));

		// Only create referral if there is a single matching referral in PAS as otherwise we cannot determine which on matches
		if($pas_referrals && count($pas_referrals) == 1) {
			Yii::log("Found referral for patient id $episode->patient_id (rm_patient_no $rm_patient_no)", 'trace');
			$pas_referral = $pas_referrals[0];
			$referral = new Referral();
			$referral->refno = $pas_referral->REFNO;
			$referral->patient_id = $episode->patient_id;
			$referral->service_specialty_assignment_id = $episode->firm->serviceSpecialtyAssignment->id;
			$referral->firm_id = $episode->firm_id;
			if (!$referral->save()) {
				throw new CException("Failed to save referral for patient id $episode->patient_id (rm_patient_no $rm_patient_no), episode $episode->id: " . print_r($referral->getErrors(), true));
			}

			$rea = new ReferralEpisodeAssignment();
			$rea->referral_id = $referral->id;
			$rea->episode_id = $episode->id;
			if (!$rea->save()) {
				throw new CException("Failed to associate referral $referral->id with episode $episode->id: ".print_r($rea->getErrors(), true));
			}
		} else if(count($pas_referrals) > 1) {
			Yii::log('There were '.count($pas_referrals)." referrals found in PAS for patient id $episode->patient_id (rm_patient_no $rm_patient_no), so none were imported",'trace');
		} else {
			Yii::log("No referrals found in PAS for patient id $episode->patient_id (rm_patient_no $rm_patient_no)",'trace');
		}

	}

}