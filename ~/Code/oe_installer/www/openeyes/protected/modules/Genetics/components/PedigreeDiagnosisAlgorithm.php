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

/**
 * Class PedigreeDiagnosisAlgorithm
 */
class PedigreeDiagnosisAlgorithm
{

	public static function updatePedigreeDiagnosisByPatient($patient_id)
	{
		if ($pedigree = self::findPedigreeByPatient($patient_id)) {
			self::updatePedigreeDiagnosisByPedigree($pedigree);
		}
		else {
			throw new Exception('Patient has no pedigree');
		}
	}

	public static function updatePedigreeDiagnosisByPedigreeID($pedigree_id)
	{
		if ($pedigree = Pedigree::model()->find('id=?',array($pedigree_id))) {
			self::updatePedigreeDiagnosisByPedigree($pedigree);
		}
		else {
			throw new Exception('Pedigree not found');
		}
	}

	public static function updatePedigreeDiagnosisByPedigree($pedigree)
	{
			$disorder_id = self::mostCommonDiagnosis($pedigree->members);
			$pedigree->disorder_id = $disorder_id;
			$pedigree->save();
	}

	private static function findPedigreeByPatient($patient_id)
	{
		if($patient_pedigree = PatientPedigree::model()->find('patient_id=?',array($patient_id))) {
			$pedigree = $patient_pedigree->pedigree;
			return $pedigree;
		}
		return false;
	}

	private static function mostCommonDiagnosis($pedigree_members)
	{
		$diagnoses_count = self::countDiagnoses($pedigree_members);
		if(empty($diagnoses_count)) {
			return null;
		}
		else {
			$most_common = array_keys($diagnoses_count, max($diagnoses_count)); //maybe equal first
			return $most_common[0]; //slice off top result if joint first
		}
	}

	private static function countDiagnoses($pedigree_members)
	{
		$table_results = array();
		foreach ($pedigree_members as $member){
			$systemic_diagnoses = $member->patient->systemicDiagnoses;
			$ophthalmic_diagnoses = $member->patient->OphthalmicDiagnoses;
			$member_diagnoses = array_merge($systemic_diagnoses,$ophthalmic_diagnoses);
			if(!empty($member_diagnoses)) {
				foreach($member_diagnoses as $diagnosis){
					$diagnosis_disorder_id = $diagnosis->disorder_id;
					if(array_key_exists($diagnosis_disorder_id, $table_results)) {
						$table_results[$diagnosis_disorder_id]  += 1;
					}
					else {
						$table_results[$diagnosis_disorder_id] = 1;
					}
				}
			}
		}

		return $table_results;
	}
}
