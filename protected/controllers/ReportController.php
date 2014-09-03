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

class ReportController extends BaseReportController
{
	public $layout = 'reports';

	public function accessRules()
	{
		return array(
			array('allow',
				'actions' => array('index', 'validateDiagnoses', 'diagnoses', 'downloadDiagnoses'),
				'roles' => array('admin','OprnGenerateReport'),
			)
		);
	}

	protected function array2Csv(array $data)
	{
		if (count($data) == 0) {
			return null;
		}
		ob_start();
		$df = fopen("php://output", 'w');
		fputcsv($df, array_keys(reset($data)));
		foreach ($data as $row) {
			fputcsv($df, $row);
		}
		fclose($df);
		return ob_get_clean();
	}

	protected function sendCsvHeaders($filename)
	{
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=$filename");
		header("Pragma: no-cache");
		header("Expires: 0");
	}

	public function actionIndex()
	{
		$this->redirect(array('diagnoses'));
	}

	public function actionValidateDiagnoses()
	{
		$errors = array();

		if (!strtotime(@$_POST['start-date'])) {
			$errors[] = 'Start date is required';
		}
		if (!strtotime(@$_POST['end-date'])) {
			$errors[] = 'End date is required';
		}

		if (empty($errors)) {
			if (strtotime($_POST['end-date']) < strtotime($_POST['start-date'])) {
				$errors[] = 'Start date cannot precede end date';
			}
		}

		if (empty($_POST['principal']) && empty($_POST['secondary'])) {
			$errors[] = 'Please enter at least one diagnosis';
		}

		echo json_encode($errors);
	}

	public function getDiagnoses()
	{
		$diagnoses = array();

		$eyes = CHtml::listData(Eye::model()->findAll(),'id','name');

		$select = "p.hos_num, c.first_name, c.last_name, p.dob";

		$query = Yii::app()->db->createCommand()
			->from("patient p")
			->join("contact c","p.contact_id = c.id");

		$condition = '';
		$conditions = array();
		$whereParams = array();

		if (!empty($_POST['principal'])) {
			$i = 0;
			foreach ($_POST['principal'] as $disorder_id) {
				$select .= ", e$i.created_date as pd{$i}_date, pd{$i}.fully_specified_name as pd{$i}_fully_specified_name, e{$i}.eye_id as pd{$i}_eye";

				$whereParams[":pd$i"] = $disorder_id;

				$join_method = $_POST['condition'] == 'and' ? 'join' : 'leftJoin';

				$query->$join_method("episode e$i","e$i.patient_id = p.id and e$i.disorder_id = :pd$i");
				$query->$join_method("disorder pd$i","pd$i.id = e$i.disorder_id");

				if ($_POST['condition'] == 'or') {
					$conditions[] = "pd$i.id is not null";
				} 

				$i++;
			}
		}

		if (!empty($_POST['secondary'])) {
			$i = 0;
			foreach ($_POST['secondary'] as $disorder_id) {
				$select .= ", sd$i.created_date as sd{$i}_date, sdis{$i}.fully_specified_name as sd{$i}_fully_specified_name, sd{$i}.eye_id as sd{$i}_eye";
				$join_clause = array("secondary_diagnosis sd$i","sd$i.patient_id = p.id and sd$i.disorder_id = :sd$i");

				$whereParams[":sd$i"] = $disorder_id;

				$join_method = $_POST['condition'] == 'and' ? 'join' : 'leftJoin';

				$query->$join_method("secondary_diagnosis sd$i","sd$i.patient_id = p.id and sd$i.disorder_id = :sd$i");
				$query->$join_method("disorder sdis$i","sdis$i.id = sd$i.disorder_id");

				if ($_POST['condition'] == 'or') {
					$conditions[] = "sdis$i.id is not null";
				}

				$i++;
			}
		}

		$query->select($select);

		if (@$_POST['condition'] == 'or') {
			if ($condition) {
				$condition .= " and ";
			}
			$condition .= "( ".implode(' or ',$conditions)." )";
		}

		$query->where($condition,$whereParams);

		foreach ($query->queryAll() as $item) {
			$_diagnoses = array();

			if (!empty($_POST['principal'])) {
				for ($i=0; $i<count($_POST['principal']); $i++) {
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

			if (!empty($_POST['secondary'])) {
				for ($i=0; $i<count($_POST['secondary']); $i++) {
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

			while (isset($diagnoses[$ts])) {
				$ts++;
			}

			$diagnoses[$ts] = array(
				'hos_num' => $item['hos_num'],
				'dob' => $item['dob'],
				'first_name' => $item['first_name'],
				'last_name' => $item['last_name'],
				'diagnoses' => $_diagnoses,
			);
		}

		return $diagnoses;
	}

	public function actionDiagnoses()
	{
		if (!empty($_POST)) {
			$this->renderPartial('_diagnoses',array('diagnoses' => $this->getDiagnoses()));
		} else {
			$this->render('diagnoses');
		}
	}

	public function actionDownloadDiagnoses()
	{
		header("Content-type: application/csv");
		header("Content-Disposition: attachment; filename=diagnoses.csv");
		header("Pragma: no-cache");
		header("Expires: 0");

		echo Patient::model()->getAttributeLabel('hos_num').",".Patient::model()->getAttributeLabel('dob').",".Patient::model()->getAttributeLabel('first_name').",".Patient::model()->getAttributeLabel('last_name').",Date,Diagnoses\n";

		foreach ($this->getDiagnoses() as $ts => $diagnosis) {
			echo "\"{$diagnosis['hos_num']}\",\"".($diagnosis['dob'] ? date('j M Y',strtotime($diagnosis['dob'])) : 'Unknown')."\",\"{$diagnosis['first_name']}\",\"{$diagnosis['last_name']}\",\"".date('j M Y',$ts)."\",\"";
			$_diagnosis = array_shift($diagnosis['diagnoses']);
			echo $_diagnosis['eye'].' '.$_diagnosis['disorder'].' ('.$_diagnosis['type'].")\"\n";

			foreach ($diagnosis['diagnoses'] as $_diagnosis) {
				echo "\"\",\"\",\"\",\"\",\"\",\"" . $_diagnosis['eye'].' '.$_diagnosis['disorder'].' ('.$_diagnosis['type'].")\"\n";
			}
		}
	}
}
