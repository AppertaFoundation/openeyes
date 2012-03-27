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

class TheatreController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/main';
 
	public function filters()
	{
		return array('accessControl');
	}

	public function accessRules()
	{
		return array(
			array('allow',
				'users'=>array('@')
			),
			// non-logged in can't view anything
			array('deny',
				'users'=>array('?')
			),
		);
	}

	public function actionIndex()
	{
		$firm = Firm::model()->findByPk($this->selectedFirmId);

		if (empty($firm)) {
			// No firm selected, reject
			throw new CHttpException(403, 'You are not authorised to view this page without selecting a firm.');
		}

		$theatres = array();
		$wards = array();

		if (empty($_POST)) {
			// look for values from the session
			$theatre_searchoptions = Yii::app()->session['theatre_searchoptions'];

			if (!empty($theatre_searchoptions)) {
				foreach (Yii::app()->session['theatre_searchoptions'] as $key => $value) {
					$_POST[$key] = $value;
				}

				if (isset($_POST['site-id'])) {
					$wards = $this->getFilteredWards($_POST['site-id']);
					$theatres = $this->getFilteredTheatres($_POST['site-id']);
				}

				if (!isset($_POST['firm-id'])) {
					$_POST['firm-id'] = $theatre_searchoptions['firm-id'] = Yii::app()->session['selected_firm_id'];
					$_POST['specialty-id'] = $theatre_searchoptions['specialty-id'] = $firm->serviceSpecialtyAssignment->specialty_id;
				}

				Yii::app()->session['theatre_searchoptions'] = $theatre_searchoptions;

			} else {
				$_POST = Yii::app()->session['theatre_searchoptions'] = array(
					'firm-id' => Yii::app()->session['selected_firm_id'],
					'specialty-id' => $firm->serviceSpecialtyAssignment->specialty_id
				);

				Yii::app()->session['theatre_searchoptions'] = $_POST;
			}
		}

		$this->render('index', array('wards'=>$wards, 'theatres'=>$theatres));
	}

	public function actionPrintDiary()
	{
		$this->renderPartial('_print_diary', array('theatres'=>$this->getTheatres()), false, true);
		/*
		$pdf = new TheatrePDF;

		$_POST = $_GET;

		$previousSequenceId = false;

		foreach ($this->getTheatres() as $name => $dates) {
			foreach ($dates as $date => $sessions) {
				foreach ($sessions as $session) {
					if ($session['sequenceId'] != $previousSequenceId) {
						$pdf->add_page(array(
							'theatre_no' => $name,
							'session' => substr($session['startTime'], 0, 5).' - '.substr($session['endTime'], 0, 5),
							'surgical_firm' => empty($session['firm_name']) ? 'Emergency list' : $session['firm_name'],
							'anaesthetist' => '', // todo: wtf
							'date' => Helper::convertDate2NHS($date)
						));
					}

					if (!empty($session['patientId'])) {
						$procedures = !empty($session['procedures']) ? '['.$session['eye'].'] '.$session['procedures'] : 'No procedures';

						if ($session['operationComments']) {
							$procedures .= "\n".$session['operationComments'];
						}

						$pdf->add_row($session['patientHosNum'], $session['patientName'], $session['patientAge'], $session['ward'], $session['anaesthetic'], $procedures, $session['admissionTime']);
					}

					$previousSequenceId = $session['sequenceId'];
				}
			}
		}

		$pdf->build();
		*/
	}

	public function actionPrintList() {
		$this->renderPartial('_print_list', array('bookings'=>$this->getBookingList()), false, true);
	}

	public function actionSearch()
	{
		$this->renderPartial('_list', array('theatres' => $this->getTheatres()), false, true);
	}

	public function getTheatres()
	{
		$operation = new ElementOperation;
		$theatres = array();
		if (!empty($_POST)) {
			$siteId = !empty($_POST['site-id']) ? $_POST['site-id'] : null;
			$specialtyId = !empty($_POST['specialty-id']) ? $_POST['specialty-id'] : null;
			$firmId = !empty($_POST['firm-id']) ? $_POST['firm-id'] : null;
			$theatreId = !empty($_POST['theatre-id']) ? $_POST['theatre-id'] : null;
			$wardId = !empty($_POST['ward-id']) ? $_POST['ward-id'] : null;
			$emergencyList = !empty($_POST['emergency_list']) ? $_POST['emergency_list'] : null;

			if (!empty($_POST['date-start']) && !empty($_POST['date-end'])) {
				$_POST['date-filter'] = 'custom';
			}
			$filter = !empty($_POST['date-filter']) ? $_POST['date-filter'] : null;

			$service = new BookingService;

			if (
				empty($siteId) &&
				empty($specialtyId) &&
				empty($firmId) &&
				empty($theatreId) &&
				empty($wardId) &&
				empty($filter) &&
				empty($emergencyList)
			) {
				// No search options selected, e.g. the page has just loaded, so set to the session firm
				$firmId = Yii::app()->session['selected_firm_id'];
			}

			$_POST['date-start'] = Helper::convertNHS2MySQL($_POST['date-start']);
			$_POST['date-end'] = Helper::convertNHS2MySQL($_POST['date-end']);

			if (empty($_POST['date-start']) || empty($_POST['date-end'])) {
				$startDate = $service->getNextSessionDate($firmId);
				$endDate = $startDate;
			} else {
				$startDate = $_POST['date-start'];
				$endDate = $_POST['date-end'];
			}

			$data = $service->findTheatresAndSessions(
				$startDate,
				$endDate,
				$siteId,
				$theatreId,
				$specialtyId,
				$firmId,
				$wardId,
				$emergencyList
			);

			foreach ($data as $values) {
				$sessionTime = explode(':', $values['session_duration']);
				$sessionDuration = ($sessionTime[0] * 60) + $sessionTime[1];

				$operation->eye = $values['eye'];
				$operation->anaesthetic_type = $values['anaesthetic_type'];
				$age = Helper::getAge($values['dob']);

				$procedures = array('List'=>'');

				foreach (Yii::app()->db->createCommand()
					->select("p.short_format")
					->from("proc p")
					->join('operation_procedure_assignment opa', 'opa.proc_id = p.id')
					->where('opa.operation_id = :id',
						array(':id'=>$values['operation_id']))
					->order('opa.display_order ASC')
					->queryAll() as $row) {
					if ($procedures['List']) {
						$procedures['List'] .= ", ";
					}
					$procedures['List'] .= $row['short_format'];
				}
				
				$theatreTitle = $values['name'] . ' ('. $values['site_name'] . ')';
				//$theatreTitle = $values['name'];
				$theatres[$theatreTitle][$values['date']][] = array(
					'operationId' => $values['operation_id'],
					'episodeId' => $values['episodeId'],
					'eventId' => $values['eventId'],
					'firm_name' => @$values['firm_name'],
					'specialty_name' => @$values['specialty_name'],
					'startTime' => $values['start_time'],
					'endTime' => $values['end_time'],
					'sequenceId' => $values['sequence_id'], // TODO: References to sequences need to be removed when possible
					'sessionId' => $values['session_id'],
					'sessionDuration' => $sessionDuration,
					'operationDuration' => $values['operation_duration'],
					'operationComments' => $values['comments'],
					'consultantRequired' => $values['consultant_required'],
					'overnightStay' => $values['overnight_stay'],
					'admissionTime' => $values['admission_time'],
					'timeAvailable' => $sessionDuration,
					'eye' => substr($operation->getEyeText(), 0, 1),
					'anaesthetic' => $operation->getAnaestheticAbbreviation(),
					'procedures' => $procedures['List'],
					'patientHosNum' => $values['hos_num'],
					'patientId' => $values['patientId'],
					'patientName' => $values['last_name'] . ', ' . $values['first_name'],
					'patientAge' => $age,
					'patientGender' => $values['gender'],
					'ward' => $values['ward'],
					'displayOrder' => $values['display_order'],
					'comments' => $values['session_comments'],
					'operationDuration' => $values['operation_duration'],
					'confirmed' => $values['confirmed'],
					'consultant' => $values['session_consultant'],
					'paediatric' => $values['session_paediatric'],
					'anaesthetist' => $values['session_anaesthetist'],
					'general_anaesthetic' => $values['session_general_anaesthetic'],
					'priority' => $values['urgent'] ? 'Urgent' : 'Routine',
					'status' => $values['status'],
					'created_user' => $values['cu_fn'].' '.$values['cu_ln'],
					'last_modified_user' => $values['mu_fn'].' '.$values['mu_ln'],
					'last_modified_date' => preg_replace('/ .*$/','',$values['last_modified_date']),
					'last_modified_time' => preg_replace('/^.* /','',$values['last_modified_date']),
					'session_first_name' => $values['session_first_name'],
					'session_last_name' => $values['session_last_name']
				);

				if (empty($theatreTotals[$values['name']][$values['date']][$values['session_id']])) {
					$theatreTotals[$theatreTitle][$values['date']][$values['session_id']] = $values['operation_duration'];
				} else {
					$theatreTotals[$theatreTitle][$values['date']][$values['session_id']] += $values['operation_duration'];
				}
			}

			foreach ($theatres as $name => &$dates) {
				foreach ($dates as $date => &$sessions) {
					foreach ($sessions as &$session) {
						$totalBookings = $theatreTotals[$name][$date][$session['sessionId']];
						$session['timeAvailable'] = $session['sessionDuration'] - $totalBookings;
					}
				}
			}
		}

		return $theatres;
	}

	public function getBookingList() {
		$from = Helper::convertNHS2MySQL($_POST['date-start']);
		$to = Helper::convertNHS2MySQL($_POST['date-end']);

		$whereSql = 't.site_id = :siteId and sp.id = :specialtyId and eo.status in (1,3) and date >= :dateFrom and date <= :dateTo';
		$whereParams = array(':siteId' => $_POST['site-id'], ':specialtyId' => $_POST['specialty-id'], ':dateFrom' => $from, ':dateTo' => $to);
		$order = 'w.name ASC, p.hos_num ASC';

		if ($_POST['ward-id']) {
			$whereSql .= ' and w.id = :wardId';
			$whereParams[':wardId'] = $_POST['ward-id'];
			$order = 'p.hos_num ASC';
		}

		if ($_POST['firm-id']) {
			$whereSql .= ' and f.id = :firmId';
			$whereParams[':firmId'] = $_POST['firm-id'];
		}

		return Yii::app()->db->createCommand()
			->select('p.hos_num, p.first_name, p.last_name, p.dob, p.gender, s.date, w.name as ward_name, f.pas_code as consultant, sp.ref_spec as specialty')
			->from('booking b')
			->join('session s','b.session_id = s.id')
			->join('theatre t','s.theatre_id = t.id')
			->join('session_firm_assignment sfa','sfa.session_id = s.id')
			->join('firm f','f.id = sfa.firm_id')
			->join('service_specialty_assignment ssa','ssa.id = f.service_specialty_assignment_id')
			->join('specialty sp','sp.id = ssa.specialty_id')
			->join('element_operation eo','b.element_operation_id = eo.id')
			->join('event e','eo.event_id = e.id')
			->join('episode ep','e.episode_id = ep.id')
			->join('patient p','ep.patient_id = p.id')
			->join('ward w','b.ward_id = w.id')
			->where($whereSql, $whereParams)
			->order($order)
			->queryAll();
	}

	public function get_month_num($month) {
		for ($i=1;$i<=12;$i++) {
			if (date('M',mktime(0,0,0,$i,1,date('Y'))) == $month) {
				return $i;
			}
		}
	}

	/**
	 * Generates a firm list based on a specialty id provided via POST
	 * echoes form option tags for display
	 */
	public function actionFilterFirms()
	{
		echo CHtml::tag('option', array('value'=>''),
			CHtml::encode('All firms'), true);
		if (!empty($_POST['specialty_id'])) {
			$firms = $this->getFilteredFirms($_POST['specialty_id']);

			foreach ($firms as $id => $name) {
				echo CHtml::tag('option', array('value'=>$id),
					CHtml::encode($name), true);
			}
		}
	}

	/**
	 * Generates a theatre list based on a site id provided via POST
	 * echoes form option tags for display
	 */
	public function actionFilterTheatres()
	{
		echo CHtml::tag('option', array('value'=>''),
			CHtml::encode('All theatres'), true);
		if (!empty($_POST['site_id'])) {
			$theatres = $this->getFilteredTheatres($_POST['site_id']);

			foreach ($theatres as $id => $name) {
				echo CHtml::tag('option', array('value'=>$id),
					CHtml::encode($name), true);
			}
		}
	}

	/**
	 * Generates a theatre list based on a site id provided via POST
	 * echoes form option tags for display
	 */
	public function actionFilterWards()
	{
		echo CHtml::tag('option', array('value'=>''),
			CHtml::encode('All wards'), true);
		if (!empty($_POST['site_id'])) {
			$wards = $this->getFilteredWards($_POST['site_id']);

			foreach ($wards as $id => $name) {
				echo CHtml::tag('option', array('value'=>$id),
					CHtml::encode($name), true);
			}
		}
	}

	/*public function actionUpdateSessionComments()
	{
		if (Yii::app()->getRequest()->getIsAjaxRequest()) {
			if (!empty($_POST['id']) && !empty($_POST['comments'])) {
				$session = Session::model()->findByPk($_POST['id']);

				if (!empty($session)) {
					$session->comments = $_POST['comments'];
					$session->save();
				}
			}
			return true;
		}
	}*/

	public function actionSaveSessions() {
		if (Yii::app()->getRequest()->getIsAjaxRequest()) {
			$display_order = 1;

			foreach ($_POST as $key => $value) {
				if (preg_match('/^operation_([0-9]+)$/',$key,$m)) {
					$booking = Booking::model()->findByAttributes(array('element_operation_id' => $m[1]));

					if (!empty($booking)) {
						// This is validated in the model and the front-end so doesn't need an if ()
						preg_match('/^([0-9]{1,2}).*?([0-9]{2})$/',$value,$m2);
						$value = $m2[1].":".$m2[2];

						$booking->confirmed = (@$_POST['confirm_'.$m[1]] ? 1 : 0);
						$booking->admission_time = $value;
						$booking->display_order = $display_order++;
						if (!$booking->save()) {
							throw new SystemException('Unable to save booking: '.print_r($booking->getErrors(),true));
						}
					}
				}

				if (preg_match('/^comments_([0-9]+)$/',$key,$m)) {
					$session = Session::model()->findByPk($m[1]);

					if (!empty($session)) {
						$session->comments = $value;
						if (!$session->save()) {
							throw new SystemException('Unable to save session: '.print_r($session->getErrors(),true));
						}
					}
				}

				if (preg_match('/^consultant_([0-9]+)$/',$key,$m)) {
					$session = Session::model()->findByPk($m[1]);

					if (!empty($session)) {
						$session->consultant = ($value == 'true' ? 1 : 0);
						if (!$session->save()) {
							throw new SystemException('Unable to save session: '.print_r($session->getErrors(),true));
						}
					}
				}

				if (preg_match('/^paediatric_([0-9]+)$/',$key,$m)) {
					$session = Session::model()->findByPk($m[1]);

					if (!empty($session)) {
						$session->paediatric = ($value == 'true' ? 1 : 0);
						if (!$session->save()) {
							throw new SystemException('Unable to save session: '.print_r($session->getErrors(),true));
						}
					}
				}

				if (preg_match('/^anaesthetic_([0-9]+)$/',$key,$m)) {
					$session = Session::model()->findByPk($m[1]);

					if (!empty($session)) {
						$session->anaesthetist = ($value == 'true' ? 1 : 0);
						if (!$session->save()) {
							throw new SystemException('Unable to save session: '.print_r($session->getErrors(),true));
						}
					}
				}

				if (preg_match('/^general_anaesthetic_([0-9]+)$/',$key,$m)) {
					$session = Session::model()->findByPk($m[1]);

					if (!empty($session)) {
						$session->general_anaesthetic = ($value == 'true' ? 1 : 0);
						if (!$session->save()) {
							throw new SystemException('Unable to save session: '.print_r($session->getErrors(),true));
						}
					}
				}

				if (preg_match('/^available_([0-9]+)$/',$key,$m)) {
					$session = Session::model()->findByPk($m[1]);

					if (!empty($session)) {
						$session->status= ($value == 'true' ? 0 : 1);
						if (!$session->save()) {
							throw new SystemException('Unable to save session: '.print_r($session->getErrors(),true));
						}
					}
				}
			}

			return true;
		}
	}

	/*public function actionUpdateAdmitTime()
	{
		if (Yii::app()->getRequest()->getIsAjaxRequest()) {
			if (!empty($_POST['id']) && !empty($_POST['admission_time'])) {
				$booking = Booking::model()->findByAttributes(array('element_operation_id' => $_POST['id']));

				if (!empty($booking)) {
					$booking->admission_time = $_POST['admission_time'];
					$booking->save();
				}
			}
			return true;
		}
	}*/

	public function actionMoveOperation()
	{
		if (Yii::app()->getRequest()->getIsAjaxRequest()) {
			if (!empty($_POST['id'])) {
				$operation = ElementOperation::model()->findByPk($_POST['id']);

				if ($operation->move($_POST['up'])) {
					echo CJavaScript::jsonEncode(1);
				} else {
					return CJavaScript::jsonEncode(1);;
				}

				return true;
			}
		}

		return false;
	}

	public function actionConfirmOperation()
	{
		if (Yii::app()->getRequest()->getIsAjaxRequest()) {
			if (!empty($_POST['id'])) {
				$operation = ElementOperation::model()->findByPk($_POST['id']);

				$operation->booking->confirmed = 1;
				if (!$operation->booking->save()) {
					throw new SystemException('Unable to save booking: '.print_r($operation->booking->getErrors(),true));
				}

				echo CJavaScript::jsonEncode(1);

				return true;
			}
		}

		return false;
	}

	/**
	 * Helper method to fetch firms by specialty ID
	 *
	 * @param integer $specialtyId
	 *
	 * @return array
	 */
	protected function getFilteredFirms($specialtyId)
	{
		$data = Yii::app()->db->createCommand()
			->select('f.id, f.name')
			->from('firm f')
			->join('service_specialty_assignment ssa', 'f.service_specialty_assignment_id = ssa.id')
			->join('specialty s', 'ssa.service_id = s.id')
			->where('ssa.specialty_id=:id',
				array(':id'=>$specialtyId))
			->queryAll();

		$firms = array();
		foreach ($data as $values) {
			$firms[$values['id']] = $values['name'];
		}

		natcasesort($firms);

		return $firms;
	}

	/**
	 * Helper method to fetch theatres by site ID
	 *
	 * @param integer $siteId
	 *
	 * @return array
	 */
	protected function getFilteredTheatres($siteId)
	{
		$data = Yii::app()->db->createCommand()
			->select('t.id, t.name')
			->from('theatre t')
			->where('t.site_id = :id',
				array(':id'=>$siteId))
			->queryAll();

		$theatres = array();
		foreach ($data as $values) {
			$theatres[$values['id']] = $values['name'];
		}

		return $theatres;
	}

	/**
	 * Helper method to fetch theatres by site ID
	 *
	 * @param integer $siteId
	 *
	 * @return array
	 */
	protected function getFilteredWards($siteId)
	{
		$data = Yii::app()->db->createCommand()
			->select('w.id, w.name')
			->from('ward w')
			->where('w.site_id = :id',
				array(':id'=>$siteId))
			->order('w.name ASC')
			->queryAll();

		$wards = array();
		foreach ($data as $values) {
			$wards[$values['id']] = $values['name'];
		}

		return $wards;
	}

	public function actionRequiresConsultant() {
		if (isset($_POST['operations']) && is_array($_POST['operations'])) {
			foreach ($_POST['operations'] as $operation_id) {
				if ($operation = ElementOperation::Model()->findByPk($operation_id)) {
					if ($operation->consultant_required) {
						die("1");
					}
				} else {
					throw new SystemException('Operation not found: '.$operation_id);
				}
			}
		}
		die("0");
	}

	public function actionIsChild() {
		if (isset($_POST['patients']) && is_array($_POST['patients'])) {
			foreach ($_POST['patients'] as $hos_num) {
				if ($patient = Patient::Model()->find('hos_num = ?',array($hos_num))) {
					if ($patient->isChild()) {
						die("1");
					}
				} else {
					throw new SystemException('Patient not found: '.$hos_num);
				}
			}
		}
		die("0");
	}

	public function actionRequiresAnaesthetist() {
		if (isset($_POST['operations']) && is_array($_POST['operations'])) {
			foreach ($_POST['operations'] as $operation_id) {
				if ($operation = ElementOperation::Model()->findByPk($operation_id)) {
					if ($operation->anaesthetist_required) {
						die("1");
					}
				} else {
					throw new SystemException('Operation not found: '.$operation_id);
				}
			}
		}
		die("0");
	}

	public function actionRequiresGeneralAnaesthetic() {
		if (isset($_POST['operations']) && is_array($_POST['operations'])) {
			foreach ($_POST['operations'] as $operation_id) {
				if ($operation = ElementOperation::Model()->findByPk($operation_id)) {
					if ($operation->anaesthetic_type == ElementOperation::ANAESTHETIC_GENERAL) {
						die("1");
					}
				} else {
					throw new SystemException('Operation not found: '.$operation_id);
				}
			}
		}
		die("0");
	}

	public function actionSetFilter() {
		$so = Yii::app()->session['theatre_searchoptions'];
		foreach ($_POST as $key => $value) {
			$so[$key] = $value;
		}
		Yii::app()->session['theatre_searchoptions'] = $so;
	}

	public function actionGetSessionTimestamps() {
		if (isset($_POST['session_id'])) {
			if ($session = Session::model()->findByPk($_POST['session_id'])) {
				$ex = explode(' ',$session->last_modified_date);
				$last_modified_date = $ex[0];
				$last_modified_time = $ex[1];
				$user = User::model()->findByPk($session->last_modified_user_id);
				echo "Modified on ".Helper::convertMySQL2NHS($last_modified_date)." at ".$last_modified_time." by ".$user->first_name." ".$user->last_name;
			}
		}
	}
}
