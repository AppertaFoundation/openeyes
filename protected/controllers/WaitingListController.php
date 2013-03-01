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

	public function printActions() {
		return array(
				'printletters',
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
						'subspecialty-id' => Firm::Model()->findByPk(Yii::app()->session['selected_firm_id'])->serviceSubspecialtyAssignment->subspecialty_id
				);
			}

			$audit = new Audit;
			$audit->action = "view";
			$audit->target_type = "waiting list";
			$audit->user_id = (Yii::app()->session['user'] ? Yii::app()->session['user']->id : null);
			$audit->save();
		} else {
			$audit = new Audit;
			$audit->action = "search";
			$audit->target_type = "waiting list";
			$audit->user_id = (Yii::app()->session['user'] ? Yii::app()->session['user']->id : null);
			$audit->data = serialize($_POST);
			$audit->save();
		}

		$this->render('index');
	}

	public function actionSearch()
	{
		$audit = new Audit;
		$audit->action = "search";
		$audit->target_type = "waiting list";
		$audit->user_id = (Yii::app()->session['user'] ? Yii::app()->session['user']->id : null);
		$audit->data = serialize($_POST);
		$audit->save();

		if (empty($_POST)) {
			$operations = array();
		} else {
			$subspecialtyId = !empty($_POST['subspecialty-id']) ? $_POST['subspecialty-id'] : null;
			$firmId = !empty($_POST['firm-id']) ? $_POST['firm-id'] : null;
			$status = !empty($_POST['status']) ? $_POST['status'] : null;
			$hos_num = !empty($_POST['hos_num']) && ctype_digit($_POST['hos_num']) ? $_POST['hos_num'] : false;
			$site_id = !empty($_POST['site_id']) ? $_POST['site_id'] : false;

			Yii::app()->session['waitinglist_searchoptions'] = array(
					'subspecialty-id' => $subspecialtyId,
					'firm-id' => $firmId,
					'status' => $status,
					'hos_num' => $hos_num,
					'site_id' => $site_id
			);

			$service = new WaitingListService;
			$operations = $service->getWaitingList($firmId, $subspecialtyId, $status, $hos_num, $site_id);
		}

		$this->renderPartial('_list', array('operations' => $operations), false, true);
	}

	/**
		* Generates a firm list based on a subspecialty id provided via POST
		* echoes form option tags for display
		*/
	public function actionFilterFirms()
	{
		$so = Yii::app()->session['waitinglist_searchoptions'];
		$so['subspecialty-id'] = $_POST['subspecialty_id'];
		Yii::app()->session['waitinglist_searchoptions'] = $so;

		echo CHtml::tag('option', array('value'=>''),
				CHtml::encode('All firms'), true);
		if (!empty($_POST['subspecialty_id'])) {
			$firms = $this->getFilteredFirms($_POST['subspecialty_id']);

			foreach ($firms as $id => $name) {
				echo CHtml::tag('option', array('value'=>$id),
						CHtml::encode($name), true);
			}
		}
	}

	public function setFilter($field, $value) {
		$so = Yii::app()->session['waitinglist_searchoptions'];
		$so[$field] = $value;
		Yii::app()->session['waitinglist_searchoptions'] = $so;
	}

	public function actionFilterSetFirm() {
		$this->setFilter('firm-id', $_POST['firm_id']);
	}

	public function actionFilterSetStatus() {
		$this->setFilter('status', $_POST['status']);
	}

	public function actionFilterSetSiteId() {
		$this->setFilter('site_id', $_POST['site_id']);
	}

	public function actionFilterSetHosNum() {
		$this->setFilter('hos_num', $_POST['hos_num']);
	}
	/**
		* Helper method to fetch firms by subspecialty ID
		*
		* @param integer $subspecialtyId
		* @return array
		*/
	protected function getFilteredFirms($subspecialtyId)
	{
		$data = Yii::app()->db->createCommand()
		->select('f.id, f.name')
		->from('firm f')
		->join('service_subspecialty_assignment ssa', 'f.service_subspecialty_assignment_id = ssa.id')
		->join('subspecialty s', 'ssa.subspecialty_id = s.id')
		->order('f.name asc')
		->where('ssa.subspecialty_id=:id',
				array(':id'=>$subspecialtyId))
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
		$audit = new Audit;
		if (@$_REQUEST['all'] == 'true') {
			$audit->action = "print all";
		} else {
			$audit->action = "print selected";
		}
		$audit->target_type = "waiting list";
		$audit->user_id = (Yii::app()->session['user'] ? Yii::app()->session['user']->id : null);
		$audit->data = serialize($_POST);
		$audit->save();

		$operation_ids = (isset($_REQUEST['operations'])) ? $_REQUEST['operations'] : null;
		$auto_confirm = (isset($_REQUEST['confirm']) && $_REQUEST['confirm'] == 1);
		if(!is_array($operation_ids)) {
			throw new CHttpException('400', 'Invalid operation list');
		}
		$operations = ElementOperation::model()->findAllByPk($operation_ids);


		// Print letter(s) for each operation
		$this->layout = '//layouts/pdf';
		$pdf_print = new OEPDFPrint('Openeyes', 'Waiting list letters', 'Waiting list letters');
		foreach($operations as $operation) {
			$this->printLetter($pdf_print, $operation, $auto_confirm);
		}
		$pdf_print->output();
	}

	/**
		* Print the next letter for an operation
		* @param OEPDFPrint $pdf_print
		* @param ElementOperation $operation
		* @param Boolean $auto_confirm
		*/
	protected function printLetter($pdf_print, $operation, $auto_confirm = false) {
		$patient = $operation->event->episode->patient;
		$letter_status = $operation->getDueLetter();
		if ($letter_status === null && $operation->getLastLetter() == ElementOperation::LETTER_GP) {
			$letter_status = ElementOperation::LETTER_GP;
		}
		$letter_templates = array(
				ElementOperation::LETTER_INVITE => 'invitation_letter',
				ElementOperation::LETTER_REMINDER_1 => 'reminder_letter',
				ElementOperation::LETTER_REMINDER_2 => 'reminder_letter',
				ElementOperation::LETTER_GP => 'gp_letter',
				ElementOperation::LETTER_REMOVAL => false,
		);
		$letter_template = (isset($letter_templates[$letter_status])) ? $letter_templates[$letter_status] : false;

		if($letter_template) {
			$firm = $operation->event->episode->firm;
			$site = $operation->site;
			$waitingListContact = $operation->waitingListContact;
			
			// Don't print GP letter if practice address is not defined
			if($letter_status != ElementOperation::LETTER_GP || ($patient->practice && $patient->practice->address)) {
				Yii::log("Printing letter: ".$letter_template, 'trace');

				call_user_func(array($this, 'print_'.$letter_template), $pdf_print, $operation);
				$this->print_admission_form($pdf_print, $operation);

				if($auto_confirm) {
					$operation->confirmLetterPrinted();
				}
			} else {
				Yii::log("Patient has no practice address, printing letter supressed: ".$patient->id, 'trace');
			}
		} else if($letter_status === null) {
			Yii::log("No letter is due: ".$patient->id, 'trace');
		} else {
			throw new CException('Undefined letter status');
		}
	}

	/**
	 * Get consultant name for letter
	 * @param ElementOperation $operation
	 * @return string
	 */
	protected function getConsultantName($operation) {
		if($consultant = $operation->event->episode->firm->getConsultant()) {
			return $consultant->contact->title . ' ' . $consultant->contact->first_name . ' ' . $consultant->contact->last_name;
		} else {
			return 'CONSULTANT';
		}
	}

	/**
	 * Get letter from address for letter
	 * @param ElementOperation $operation
	 * @return string
	 */
	protected function getFromAddress($operation) {
		$from_address = implode("\n",$operation->site->getLetterArray(false,false));
		$from_address .= "\nTel: " . $operation->site->telephone;
		if($operation->site->fax) {
			$from_address .= "\nFax: " . $operation->site->fax;
		}
		return $from_address;
	}

	/**
	 * @param OEPDFPrint $pdf
	 * @param ElementOperation $operation
	 */
	protected function print_admission_form($pdf, $operation) {
		$patient = $operation->event->episode->patient;
		$to_address = $patient->addressname . "\n" . implode("\n", $patient->correspondAddress->getLetterArray(false));
		$site = $operation->site;
		$firm = $operation->event->episode->firm;
		$body = $this->render('/letters/admission_form', array(
				'operation' => $operation,
				'site' => $site,
				'patient' => $patient,
				'firm' => $firm,
				'emergencyList' => false,
		), true);
		$letter = new OELetter();
		$letter->setBarcode('E:'.$operation->event_id);
		$letter->setFont('helvetica','10');
		$letter->addBody($body);
		$pdf->addLetter($letter);
	}

	/**
	 * @param OEPDFPrint $pdf
	 * @param ElementOperation $operation
	 */
	protected function print_invitation_letter($pdf, $operation) {
		$patient = $operation->event->episode->patient;
		$to_address = $patient->addressname . "\n" . implode("\n", $patient->correspondAddress->getLetterArray(false));
		$body = $this->render('/letters/invitation_letter', array(
				'to' => $patient->salutationname,
				'consultantName' => $this->getConsultantName($operation),
				'overnightStay' => $operation->overnight_stay,
				'patient' => $patient,
				'changeContact' => $operation->waitingListContact,
		), true);
		$letter = new OELetter($to_address, $this->getFromAddress($operation), $body);
		$letter->setBarcode('E:'.$operation->event_id);
		$pdf->addLetter($letter);
	}

	/**
	 * @param OEPDFPrint $pdf
	 * @param ElementOperation $operation
	 */
	protected function print_reminder_letter($pdf, $operation) {
		$patient = $operation->event->episode->patient;
		$to_address = $patient->addressname . "\n" . implode("\n", $patient->correspondAddress->getLetterArray(false));
		$body = $this->render('/letters/reminder_letter', array(
				'to' => $patient->salutationname,
				'consultantName' => $this->getConsultantName($operation),
				'overnightStay' => $operation->overnight_stay,
				'patient' => $patient,
				'changeContact' => $operation->waitingListContact,
		), true);
		$letter = new OELetter($to_address, $this->getFromAddress($operation), $body);
		$letter->setBarcode('E:'.$operation->event_id);
		$pdf->addLetter($letter);
	}

	/**
	 * @param OEPDFPrint $pdf
	 * @param ElementOperation $operation
	 */
	protected function print_gp_letter($pdf, $operation) {

		// GP Letter
		$patient = $operation->event->episode->patient;
		if($gp = $patient->gp) {
			$to_name = $gp->contact->fullname;
			$salutation = $gp->contact->salutationname;
		} else {
			$to_name = Gp::UNKNOWN_NAME;
			$salutation = Gp::UNKNOWN_SALUTATION;
		}
		if($patient->practice && $practice_address = $patient->practice->address) {
			$to_address = $to_name . "\n" . implode("\n",$practice_address->getLetterArray(false));
		} else {
			throw new CException('Patient has no practice address');
		}
		$body = $this->render('/letters/gp_letter', array(
				'to' => $salutation,
				'patient' => $patient,
				'consultantName' => $this->getConsultantName($operation),
		), true);
		$letter = new OELetter($to_address, $this->getFromAddress($operation), $body);
		$letter->setBarcode('E:'.$operation->event_id);
		$pdf->addLetter($letter);

		// Patient letter
		$to_address = $patient->addressname . "\n" . implode("\n", $patient->correspondAddress->getLetterArray(false));
		$body = $this->render('/letters/gp_letter_patient', array(
				'to' => $patient->salutationname,
				'patient' => $patient,
				'consultantName' => $this->getConsultantName($operation),
		), true);
		$letter = new OELetter($to_address, $this->getFromAddress($operation), $body);
		$letter->setBarcode('E:'.$operation->event_id);
		$pdf->addLetter($letter);

	}

	public function actionConfirmPrinted() {
		$audit = new Audit;
		$audit->action = "confirm";
		$audit->target_type = "waiting list";
		$audit->user_id = (Yii::app()->session['user'] ? Yii::app()->session['user']->id : null);
		$audit->data = serialize($_POST);
		$audit->save();

		foreach ($_POST['operations'] as $operation_id) {
			if ($operation = ElementOperation::Model()->findByPk($operation_id)) {
				if (Yii::app()->user->checkAccess('admin') and (isset($_POST['adminconfirmto'])) and ($_POST['adminconfirmto'] != 'OFF') and ($_POST['adminconfirmto'] != '')) {
					$operation->confirmLetterPrinted($_POST['adminconfirmto'], $_POST['adminconfirmdate']);
				} else {
					$operation->confirmLetterPrinted();
				}
			}
		}
	}
	
}
