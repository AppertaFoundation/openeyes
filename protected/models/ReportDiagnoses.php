<?php /**
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

class ReportDiagnoses extends BaseReport
{
	public $principal;
	public $secondary;
	public $condition_type;
	public $start_date;
	public $end_date;
	public $diagnoses;

	public function attributeNames()
	{
		return array(
			'principal',
			'secondary',
			'condition_type',
			'start_date',
			'end_date',
		);
	}

	public function attributeLabels()
	{
		return array(
			'start_date' => 'Start date',
			'end_date' => 'End date',
			'condition_type' => 'Condition type',
		);
	}

	public function rules()
	{
		return array(
			array('principal, secondary, condition_type, start_date, end_date', 'safe'),
			array('start_date, end_date, condition_type', 'required'),
		);
	}

	public function afterValidate()
	{
		if (empty($this->principal) && empty($this->secondary)) {
			$this->addError('principal','Please select at least one diagnosis');
		}

		return parent::afterValidate();
	}

	public function run()
	{
		if (!empty($this->secondary)) {
			$secondary = array();
			foreach ($this->secondary as $disorder_id) {
				if (empty($this->principal) || !in_array($disorder_id,$this->principal)) {
					$secondary[] = $disorder_id;
				}
			}

			$this->secondary = $secondary;
		}

		$this->diagnoses = array();

		$eyes = CHtml::listData(Eye::model()->findAll(),'id','name');

		$select = "p.hos_num, c.first_name, c.last_name, p.dob";

		$query = Yii::app()->db->createCommand()
			->from("patient p")
			->join("contact c","p.contact_id = c.id");

		$condition = '';
		$conditions = array();
		$whereParams = array();

		$join_method = $this->condition_type == 'and' ? 'join' : 'leftJoin';

		if (!empty($this->principal)) {
			$i = 0;
			foreach ($this->principal as $disorder_id) {
				$select .= ", e$i.created_date as pd{$i}_date, pd{$i}.fully_specified_name as pd{$i}_fully_specified_name, e{$i}.eye_id as pd{$i}_eye";

				$whereParams[":pd$i"] = $disorder_id;

				$episode_join = "e$i.patient_id = p.id and e$i.disorder_id = :pd$i";

				if ($this->start_date) {
					$episode_join .= " and e$i.created_date >= :start_date";
				}
				if ($this->end_date) {
					$episode_join .= " and e$i.created_date <= :end_date";
				}

				$query->$join_method("episode e$i",$episode_join);
				$query->$join_method("disorder pd$i","pd$i.id = e$i.disorder_id");

				if ($this->condition_type == 'or') {
					$conditions[] = "pd$i.id is not null";
				}

				$i++;
			}
		}

		if (!empty($this->secondary)) {
			$i = 0;
			foreach ($this->secondary as $disorder_id) {
				$select .= ", sd$i.created_date as sd{$i}_date, sdis{$i}.fully_specified_name as sd{$i}_fully_specified_name, sd{$i}.eye_id as sd{$i}_eye";

				$whereParams[":sd$i"] = $disorder_id;

				$sd_join = "sd$i.patient_id = p.id and sd$i.disorder_id = :sd$i";

				if ($this->start_date) {
					$sd_join .= " and sd$i.created_date >= :start_date";
				}
				if ($this->end_date) {
					$sd_join .= " and sd$i.created_date <= :end_date";
				}

				$query->$join_method("secondary_diagnosis sd$i",$sd_join);
				$query->$join_method("disorder sdis$i","sdis$i.id = sd$i.disorder_id");

				if ($this->condition_type == 'or') {
					$conditions[] = "sdis$i.id is not null";
				}

				$i++;
			}
		}

		$query->select($select);

		if ($this->condition_type == 'or') {
			if ($condition) {
				$condition .= " and ";
			}
			$condition .= "( ".implode(' or ',$conditions)." )";
		}

		if ($this->start_date) {
			$whereParams[':start_date'] = date('Y-m-d',strtotime($this->start_date)).' 00:00:00';
		}
		if ($this->end_date) {
			$whereParams[':end_date'] = date('Y-m-d',strtotime($this->end_date)).' 23:59:59';
		}

		$query->where($condition,$whereParams);

		foreach ($query->queryAll() as $item) {
			$_diagnoses = array();

			if (!empty($this->principal)) {
				for ($i=0; $i<count($this->principal); $i++) {
					if ($item["pd{$i}_date"]) {
						$ts = strtotime($item["pd{$i}_date"]);

						while (isset($_diagnoses[$ts])) {
							$ts++;
						}

						$_diagnoses[$ts] = array(
							'type' => 'Principal',
							'disorder' => $item["pd{$i}_fully_specified_name"],
							'date' => $item["pd{$i}_date"],
							'eye' => $eyes[$item["pd{$i}_eye"]],
						);
					}
				}
			}

			if (!empty($this->secondary)) {
				for ($i=0; $i<count($this->secondary); $i++) {
					if ($item["sd{$i}_date"]) {
						$ts = strtotime($item["sd{$i}_date"]);

						while (isset($_diagnoses[$ts])) {
							$ts++;
						}

						$_diagnoses[$ts] = array(
							'type' => 'Secondary',
							'disorder' => $item["sd{$i}_fully_specified_name"],
							'date' => $item["sd{$i}_date"],
							'eye' => $eyes[$item["sd{$i}_eye"]],
						);
					}
				}
			}

			ksort($_diagnoses);
			reset($_diagnoses);
			$ts = key($_diagnoses);

			while (isset($this->diagnoses[$ts])) {
				$ts++;
			}

			$this->diagnoses[$ts] = array(
				'hos_num' => $item['hos_num'],
				'dob' => $item['dob'],
				'first_name' => $item['first_name'],
				'last_name' => $item['last_name'],
				'diagnoses' => $_diagnoses,
			);
		}
	}

	public function description()
	{
		$description = 'Patients with '.($this->condition_type == 'or' ? 'any' : 'all')." of these diagnoses:\n";

		if (!empty($this->principal)) {
			foreach ($this->principal as $disorder_id) {
				$description .= Disorder::model()->findByPk($disorder_id)->term." (Principal)\n";
			}
		}

		if (!empty($this->secondary)) {
			foreach ($this->secondary as $disorder_id) {
				$description .= Disorder::model()->findByPk($disorder_id)->term." (Secondary)\n";
			}
		}

		return $description . "Between ".$this->start_date." and ".$this->end_date;
	}

	/**
	 * Output the report in CSV format
	 *
	 * @return string
	 */
	public function toCSV()
	{
		$output = $this->description()."\n\n";

		$output .= Patient::model()->getAttributeLabel('hos_num').",".Patient::model()->getAttributeLabel('dob').",".Patient::model()->getAttributeLabel('first_name').",".Patient::model()->getAttributeLabel('last_name').",Date,Diagnoses\n";

		foreach ($this->diagnoses as $ts => $diagnosis) {
			$output .= "\"{$diagnosis['hos_num']}\",\"".($diagnosis['dob'] ? date('j M Y',strtotime($diagnosis['dob'])) : 'Unknown')."\",\"{$diagnosis['first_name']}\",\"{$diagnosis['last_name']}\",\"".date('j M Y',$ts)."\",\"";
			$_diagnosis = array_shift($diagnosis['diagnoses']);
			$output .= $_diagnosis['eye'].' '.$_diagnosis['disorder'].' ('.$_diagnosis['type'].")\"\n";

			foreach ($diagnosis['diagnoses'] as $_diagnosis) {
				$output .= "\"\",\"\",\"\",\"\",\"\",\"" . $_diagnosis['eye'].' '.$_diagnosis['disorder'].' ('.$_diagnosis['type'].")\"\n";
			}
		}

		return $output;
	}
}
