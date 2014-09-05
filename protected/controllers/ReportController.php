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
	public function accessRules()
	{
		return array(
			array('allow',
				'actions' => array('index', 'diagnoses', 'downloadDiagnoses', 'letters', 'downloadLetters'),
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

	public function actionDiagnoses()
	{
		if (!empty($_POST)) {
			$report = new ReportDiagnoses;
			$report->attributes = $_POST;

			if (!$report->validate()) {
				echo json_encode($report->errors);
				return;
			}

			$report->run();

			echo json_encode(array(
				'_report' => $this->renderPartial('_diagnoses',array('report' => $report),true)
			));
		} else {
			$this->render('diagnoses');
		}
	}

	public function actionDownloadDiagnoses()
	{
		$this->sendCsvHeaders('diagnoses.csv');

		$report = new ReportDiagnoses;
		$report->attributes = $_POST;

		if (!$report->validate()) {
			throw new Exception("Invalid parameters");
		}

		echo $report->toCSV();
	}

	public function actionLetters()
	{
		if (!empty($_POST)) {
			$report = new ReportLetters;
			$report->attributes = $_POST;

			if (!$report->validate()) {
				echo json_encode($report->errors);
				return;
			}

			$report->run();

			echo json_encode(array(
				'_report' => $this->renderPartial('_letters',array('report' => $report),true)
			));
		} else {
			$this->render('letters');
		}
	}

	public function actionDownloadLetters()
	{
		$this->sendCsvHeaders('letters.csv');

		$report = new ReportLetters;
		$report->attributes = $_POST;

		if (!$report->validate()) {
			throw new Exception("Invalid parameters");
		}

		echo $report->toCSV();
	}
}
