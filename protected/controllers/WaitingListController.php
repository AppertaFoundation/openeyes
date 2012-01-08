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

class WaitingListController extends BaseController
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

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		if (empty($_POST)) {
			// look for values from the session
			if (Yii::app()->session['waitinglist_searchoptions']) {
				foreach (Yii::app()->session['waitinglist_searchoptions'] as $key => $value) {
					$_POST[$key] = $value;
				}
			} else {
				$_POST = array(
					'firm-id' => Yii::app()->session['selected_firm_id'],
					'specialty-id' => Firm::Model()->findByPk(Yii::app()->session['selected_firm_id'])->serviceSpecialtyAssignment->specialty_id
				);
			}
		}

		$this->render('index');
	}

	public function actionSearch()
	{
		if (empty($_POST)) {
			$operations = array();
		} else {
			$specialtyId = !empty($_POST['specialty-id']) ? $_POST['specialty-id'] : null;
			$firmId = !empty($_POST['firm-id']) ? $_POST['firm-id'] : null;
			$status = !empty($_POST['status']) ? $_POST['status'] : null;
			$hos_num = !empty($_POST['hos_num']) && ctype_digit($_POST['hos_num']) ? $_POST['hos_num'] : false;
			$site_id = !empty($_POST['site_id']) ? $_POST['site_id'] : false;

			Yii::app()->session['waitinglist_searchoptions'] = array(
				'specialty-id' => $specialtyId,
				'firm-id' => $firmId,
				'status' => $status,
				'hos_num' => $hos_num,
				'site_id' => $site_id
			);

			$service = new WaitingListService;
			$operations = $service->getWaitingList($firmId, $specialtyId, $status, $hos_num, $site_id);
		}

		$this->renderPartial('_list', array('operations' => $operations), false, true);
	}

	/**
	 * Generates a firm list based on a specialty id provided via POST
	 * echoes form option tags for display
	 */
	public function actionFilterFirms()
	{
		$so = Yii::app()->session['waitinglist_searchoptions'];
		$so['specialty-id'] = $_POST['specialty_id'];
		Yii::app()->session['waitinglist_searchoptions'] = $so;

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

	public function actionFilterSetFirm() {
		$so = Yii::app()->session['waitinglist_searchoptions'];
		$so['firm-id'] = $_POST['firm_id'];
		Yii::app()->session['waitinglist_searchoptions'] = $so;
	}

	public function actionFilterSetStatus() {
		$so = Yii::app()->session['waitinglist_searchoptions'];
		$so['status'] = $_POST['status'];
		Yii::app()->session['waitinglist_searchoptions'] = $so;
	}

	public function actionFilterSetSiteId() {
		$so = Yii::app()->session['waitinglist_searchoptions'];
		$so['site_id'] = $_POST['site_id'];
		Yii::app()->session['waitinglist_searchoptions'] = $so;
	}

	public function actionFilterSetHosNum() {
		$so = Yii::app()->session['waitinglist_searchoptions'];
		$so['hos_num'] = $_POST['hos_num'];
		Yii::app()->session['waitinglist_searchoptions'] = $so;
	}
	/**
	 * Helper method to fetch firms by specialty ID
	 *
	 * @param integer $specialtyId
	 * @return array
	 */
	protected function getFilteredFirms($specialtyId)
	{
		$data = Yii::app()->db->createCommand()
			->select('f.id, f.name')
			->from('firm f')
			->join('service_specialty_assignment ssa', 'f.service_specialty_assignment_id = ssa.id')
			->join('specialty s', 'ssa.specialty_id = s.id')
			->where('ssa.specialty_id=:id',
				array(':id'=>$specialtyId))
			->queryAll();

		$firms = array();
		foreach ($data as $values) {
			$firms[$values['id']] = $values['name'];
		}

		return $firms;
	}
	
	/**
	 * Prints next pending letter type for requested operations
	 * Operation IDs are passed as an array (operations[]) via GET or POST
	 * Invalid operation IDs are ignored
	 * @throws CHttpException
	 */
	public function actionPrintLetters() {
		$operation_ids = (isset($_REQUEST['operations'])) ? $_REQUEST['operations'] : null;
		if(!is_array($operation_ids)) {
			throw new CHttpException('400', 'Invalid operation list');
		}
		$operations = ElementOperation::model()->findAllByPk($operation_ids);
		
		// Print a letter for each operation, separated by a page break
		$break = false;
		foreach($operations as $operation) {
			if($break) {
				$this->printBreak();
			} else {
				$break = true;
			}
			$this->printLetter($operation);
		}
	}
	
	/**
	 * Print a page break
	 */
	protected function printBreak() {
		$this->renderPartial("/letters/break");
	}
	
	/**
	 * Print the next letter for an operation
	 * @param ElementOperation $operation
	 */
	protected function printLetter($operation) {
		$letter_status = $operation->getLetterStatus();
		$letter_templates = array(
			ElementOperation::LETTER_INVITE => 'invitation_letter',
			ElementOperation::LETTER_REMINDER_1 => 'reminder_letter',
			ElementOperation::LETTER_REMINDER_2 => 'reminder_letter',
			ElementOperation::LETTER_GP => 'gp_letter',
			ElementOperation::LETTER_REMOVAL => false,
		);
		$letter_template = (isset($letter_templates[$letter_status])) ? $letter_templates[$letter_status] : false;
		if($letter_template) {
			$consultant = $operation->event->episode->firm->getConsultant();
			if (empty($consultant)) {
				$consultantName = 'CONSULTANT';
			} else {
				$contact = $consultant->contact;
				$consultantName = CHtml::encode($contact->title . ' ' . $contact->first_name . ' ' . $contact->last_name);
			}
			// FIXME: The site associated with the operation might not be the one we're looking for
			// if that is the case then both the methods below need attention 
			$site = $operation->site;
			$waitingListContact = $operation->waitingListContact;
			
			$patient = $operation->event->episode->patient;
			$this->renderPartial('/letters/'.$letter_template, array(
				'operation' => $operation,
				'site' => $site,
				'patient' => $patient,
				'consultantName' => $consultantName,
				'changeContact' => $waitingListContact,
			));
			$this->printBreak();
			$this->renderPartial("/letters/form", array(
				'operation' => $operation, 
				'site' => $site,
				'patient' => $patient,
				'consultantName' => $consultantName,
			));
		} else {
			throw CException('Undefined operation letter template: '.$letter_status);
		}
	}

	public function actionConfirmPrinted() {
		foreach ($_POST['operations'] as $operation_id) {
			if ($operation = ElementOperation::Model()->findByPk($operation_id)) {
				$operation->confirmLetterPrinted();
			}
		}
	}
}
