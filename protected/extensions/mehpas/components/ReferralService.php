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

class ReferralService
{


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
