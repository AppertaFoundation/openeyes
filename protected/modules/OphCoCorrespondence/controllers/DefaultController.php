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

class DefaultController extends BaseEventTypeController
{
	static protected $action_types = array(
		'getAddress' => self::ACTION_TYPE_FORM,
		'getMacroData' => self::ACTION_TYPE_FORM,
		'getString' => self::ACTION_TYPE_FORM,
		'getCc' => self::ACTION_TYPE_FORM,
		'expandStrings' => self::ACTION_TYPE_FORM,
		'users' => self::ACTION_TYPE_FORM,
		'doPrint' => self::ACTION_TYPE_PRINT,
		'markPrinted' => self::ACTION_TYPE_PRINT,
		'doPrintAndView' => self::ACTION_TYPE_PRINT
	);

	/**
	 * Adds direct line phone numbers to jsvars to be used in dropdown select
	 */
	public function loadDirectLines()
	{
		$sfs = FirmSiteSecretary::model()->findAll('firm_id=?',array(Yii::app()->session['selected_firm_id']));
		$vars[]=null;
		foreach($sfs as $sf){
			$vars[$sf->site_id]=$sf->direct_line;
		}

		$this->jsVars['correspondence_directlines']=$vars;
	}

	/**
	 * Set up some key js vars
	 *
	 * @param string $action
	 */
	protected function initAction($action)
	{
		parent::initAction($action);

		if (in_array($action, array("create", "update"))) {
			$this->jsVars['OE_gp_id'] = $this->patient->gp_id;
			$this->jsVars['OE_practice_id'] = $this->patient->practice_id;

			$this->loadDirectLines();
		}
	}

	/**
	 * Set up some js vars
	 *
	 */
	public function initActionView()
	{
		parent::initActionView();
		$this->jsVars['correspondence_markprinted_url'] = Yii::app()->createUrl('OphCoCorrespondence/Default/markPrinted/'.$this->event->id);
		$this->jsVars['correspondence_print_url'] = Yii::app()->createUrl('OphCoCorrespondence/Default/print/'.$this->event->id);
	}

	/**
	 * Ajax action to get the address for a contact
	 *
	 * @throws Exception
	 */
	public function actionGetAddress()
	{
		if (!$patient = Patient::model()->findByPk(@$_GET['patient_id'])) {
			throw new Exception("Unknown patient: ".@$_GET['patient_id']);
		}

		if (!preg_match('/^([a-zA-Z]+)([0-9]+)$/',@$_GET['contact'],$m)) {
			throw new Exception("Invalid contact format: ".@$_GET['contact']);
		}

		if ($m[1] == 'Contact') {
			// NOTE we are assuming that Contact must be a Person model here
			$contact = Person::model()->find('contact_id=?',array($m[2]));
		} else {
			if (!$contact = $m[1]::model()->findByPk($m[2])) {
				throw new Exception("{$m[1]} not found: {$m[2]}");
			}
		}

		if (method_exists($contact, 'isDeceased') && $contact->isDeceased()) {
			echo json_encode(array('errors'=>'DECEASED'));
			return;
		}

		$address = $contact->getLetterAddress(array(
				'patient' => $patient,
				'include_name' => true,
				'include_label' => true,
				'delimiter' => "\n",
			));

		if(!$address){
			$address = '';
		}

		$data = array(
			'text_ElementLetter_address' => $address,
			'text_ElementLetter_introduction' => $contact->getLetterIntroduction(array(
				'nickname' => (boolean) @$_GET['nickname'],
			)),
		);

		echo json_encode($data);
	}

	/**
	 * Ajax action to get macro data for populating the letter elements
	 *
	 * @throws Exception
	 */
	public function actionGetMacroData()
	{
		if (!$patient = Patient::model()->findByPk(@$_GET['patient_id'])) {
			throw new Exception('Patient not found: '.@$_GET['patient_id']);
		}

		if (!$macro = LetterMacro::model()->findByPk(@$_GET['macro_id'])) {
			throw new Exception('Macro not found: '.@$_GET['macro_id']);
		}

		$data = array();

		$macro->substitute($patient);

		if ($macro->recipient && $macro->recipient->name == 'Patient') {
			$data['sel_address_target'] = 'Patient'.$patient->id;
			$contact = $patient;
			if ($patient->date_of_death) {
				echo json_encode(array('error'=>'DECEASED'));
				return;
			}
		}

		if ($macro->recipient && $macro->recipient->name == 'GP' && $contact = ($patient->gp) ? $patient->gp : $patient->practice) {
			$data['sel_address_target'] = get_class($contact).$contact->id;
		}

		if (isset($contact)) {
			$address = $contact->getLetterAddress(array(
				'patient' => $patient,
				'include_name' => true,
				'include_label' => true,
				'delimiter' => "\n",
			));

			if($address){
				$data['text_ElementLetter_address'] = $address;
			}
			else {
				$data['alert'] = "The contact does not have a valid address.";
				$data['text_ElementLetter_address'] = '';
			}

			$data['text_ElementLetter_introduction'] = $contact->getLetterIntroduction(array(
				'nickname' => $macro->use_nickname,
			));
		}

		$data['check_ElementLetter_use_nickname'] = $macro->use_nickname;

		if ($macro->body) {
			$data['text_ElementLetter_body'] = $macro->body;
		}

		$cc = array(
			'text' => array(),
			'targets' => array()
		);
		if ($macro->cc_patient) {
			if ($patient->date_of_death) {
				$data['alert'] = "Warning: the patient cannot be cc'd because they are deceased.";
			} elseif ($patient->contact->address) {
				$cc['text'][] = $patient->getLetterAddress(array(
					'include_name' => true,
					'include_label' => true,
					'delimiter' => ", ",
					'include_prefix' => true,
				));
				$cc['targets'][] = '<input type="hidden" name="CC_Targets[]" value="Patient'.$patient->id.'" />';
			} else {
				$data['alert'] = "Letters to the GP should be cc'd to the patient, but this patient does not have a valid address.";
			}
		}

		if ($macro->cc_doctor && $cc_contact = ($patient->gp) ? $patient->gp : $patient->practice) {
			$cc['text'][] = $cc_contact->getLetterAddress(array(
					'patient' => $patient,
					'include_name' => true,
					'include_label' => true,
					'delimiter' => ", ",
					'include_prefix' => true,
				));
			$cc['targets'][] = '<input type="hidden" name="CC_Targets[]" value="'.get_class($cc_contact).$cc_contact->id.'" />';
		}

		if ($macro->cc_drss) {
			$commissioningbodytype = CommissioningBodyType::model()->find('shortname = ?', array('CCG'));
			if($commissioningbodytype && $commissioningbody = $patient->getCommissioningBodyOfType($commissioningbodytype)) {
				$drss = null;
				foreach($commissioningbody->services as $service) {
					if($service->type->shortname == 'DRSS') {
						$cc['text'][] = $service->getLetterAddress(array(
								'include_name' => true,
								'include_label' => true,
								'delimiter' => ", ",
								'include_prefix' => true,
							));
						$cc['targets'][] = '<input type="hidden" name="CC_Targets[]" value="CommissioningBodyService'.$service->id.'" />';
						break;
					}
				}
			}
		}

		$data['textappend_ElementLetter_cc'] = implode("\n",$cc['text']);
		$data['elementappend_cc_targets'] = implode("\n",$cc['targets']);
		echo json_encode($data);
	}

	/**
	 * Ajax action to process a selected string request
	 *
	 * @throws Exception
	 */
	public function actionGetString()
	{
		if (!$patient = Patient::model()->findByPk(@$_GET['patient_id'])) {
			throw new Exception('Patient not found: '.@$_GET['patient_id']);
		}

		switch (@$_GET['string_type']) {
			case 'site':
				if (!$string = LetterString::model()->findByPk(@$_GET['string_id'])) {
					throw new Exception('Site letter string not found: '.@$_GET['string_id']);
				}
				break;
			case 'subspecialty':
				if (!$string = SubspecialtyLetterString::model()->findByPk(@$_GET['string_id'])) {
					throw new Exception('Subspecialty letter string not found: '.@$_GET['string_id']);
				}
				break;
			case 'firm':
				if (!$firm = FirmLetterString::model()->findByPk(@$_GET['string_id'])) {
					throw new Exception('Firm letter string not found: '.@$_GET['string_id']);
				}
				break;
			case 'examination':
				echo $this->process_examination_findings($_GET['patient_id'],$_GET['string_id']);
				return;
			default:
				throw new Exception('Unknown letter string type: '.@$_GET['string_type']);
		}

		$string->substitute($patient);

		echo $string->body;
	}

	/**
	 * Ajax action to get cc contact details
	 *
	 * @throws Exception
	 */
	public function actionGetCc()
	{
		if (!$patient = Patient::model()->findByPk(@$_GET['patient_id'])) {
			throw new Exception("Unknown patient: ".@$_GET['patient_id']);
		}

		if (!preg_match('/^([a-zA-Z]+)([0-9]+)$/',@$_GET['contact'],$m)) {
			throw new Exception("Invalid contact format: ".@$_GET['contact']);
		}

		if ($m[1] == 'Contact') {
			$contact = Person::model()->find('contact_id=?',array($m[2]));
		} else {
			if (!$contact = $m[1]::model()->findByPk($m[2])) {
				throw new Exception("{$m[1]} not found: {$m[2]}");
			}
		}

		if ($contact->isDeceased()) {
			echo json_encode(array('errors'=>'DECEASED'));
			return;
		}

		$address = $contact->getLetterAddress(array(
			'patient' => $patient,
			'include_name' => true,
			'include_label' => true,
			'delimiter' => "| ",
			'include_prefix' => true,
		));

		$address=str_replace(',',';',$address);
		$address=str_replace('|',',',$address);

		echo $address ? $address : 'NO ADDRESS';
	}

	/**
	 * Ajax action to expand shortcodes in letter string for a patient
	 *
	 * @throws Exception
	 */
	public function actionExpandStrings()
	{
		if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
			throw new Exception('Patient not found: '.@$_POST['patient_id']);
		}

		$text = @$_POST['text'];
		$textNew = OphCoCorrespondence_Substitution::replace($text,$patient);

		if ($text != $textNew) {
			echo $textNew;
		}
	}

	/**
	 * Ajax action to mark a letter as printed
	 *
	 * @param $id
	 * @throws Exception
	 */
	public function actionMarkPrinted($id)
	{
		if ($letter = ElementLetter::model()->find('event_id=?',array($id))) {
			$letter->print = 0;
			$letter->draft = 0;
			if (!$letter->save()) {
				throw new Exception('Unable to mark letter printed: '.print_r($letter->getErrors(),true));
			}
		}
	}

	public function actionPrint($id)
	{
		$letter = ElementLetter::model()->find('event_id=?',array($id));

		$this->printInit($id);
		$this->layout = '//layouts/print';

		$this->render('print',array('element' => $letter));

		if ($this->pdf_print_suffix == 'all' || @$_GET['all']) {
			$this->render('print',array('element' => $letter));

			foreach ($letter->getCcTargets() as $cc) {
				$letter->address = implode("\n",preg_replace('/^[a-zA-Z]+: /','',str_replace(';',',',$cc)));
				$this->render('print',array('element' => $letter));
			}
		}
	}

	public function actionPDFPrint($id)
	{
		if (@$_GET['all']) {
			$this->pdf_print_suffix = 'all';

			$letter = ElementLetter::model()->find('event_id=?',array($id));

			$this->pdf_print_documents = 2 + count($letter->getCcTargets());
		}

		return parent::actionPDFPrint($id);
	}

	/**
	 * Ajax action to get user data list
	 */
	public function actionUsers()
	{
		$users = array();

		$criteria = new CDbCriteria;

		$criteria->addCondition(array("active = :active"));
		$criteria->addCondition(array("LOWER(concat_ws(' ',first_name,last_name)) LIKE :term"));

		$params[':active'] = 1;
		$params[':term'] = '%' . strtolower(strtr($_GET['term'], array('%' => '\%'))) . '%';

		$criteria->params = $params;
		$criteria->order = 'first_name, last_name';

		$firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
		$consultant = null;
		// only want a consultant for medical firms
		if ($specialty = $firm->getSpecialty()) {
			if ($specialty->medical) {
				$consultant = $firm->consultant;
			}
		}

		foreach (User::model()->findAll($criteria) as $user) {
			if ($contact = $user->contact) {

				$consultant_name = false;

				// if we have a consultant for the firm, and its not the matched user, attach the consultant name to the entry
				if ($consultant && $user->id != $consultant->id) {
					$consultant_name = trim($consultant->contact->title.' '.$consultant->contact->first_name.' '.$consultant->contact->last_name);
				}

				$users[] = array(
					'id' => $user->id,
					'value' => trim($contact->title.' '.$contact->first_name.' '.$contact->last_name.' '.$contact->qualifications).' ('.$user->role.')',
					'fullname' => trim($contact->title.' '.$contact->first_name.' '.$contact->last_name.' '.$contact->qualifications),
					'role' => $user->role,
					'consultant' => $consultant_name,
				);
			}
		}

		echo json_encode($users);
	}

	/**
	 * Use the examination API to retrieve findings for the patient and element type
	 *
	 * @param $patient_id
	 * @param $element_type_id
	 * @return mixed
	 * @throws Exception
	 */
	public function process_examination_findings($patient_id, $element_type_id)
	{
		if ($api = Yii::app()->moduleAPI->get('OphCiExamination')) {
			if (!$patient = Patient::model()->findByPk($patient_id)) {
				throw new Exception('Unable to find patient: '.$patient_id);
			}

			if (!$element_type = ElementType::model()->findByPk($element_type_id)) {
				throw new Exception("Unknown element type: $element_type_id");
			}

			if (!$episode = $patient->getEpisodeForCurrentSubspecialty()) {
				throw new Exception('No Episode available for patient: ' . $patient_id);
			}

			return $api->getLetterStringForModel($patient, $episode, $element_type_id);
		}
	}

	/**
	 * Sets a letter element to print when it's next viewed.
	 *
	 * @param $id
	 * @return bool
	 * @throws Exception
	 */
	protected function setPrintForEvent($id)
	{
		if (!$letter = ElementLetter::model()->find('event_id=?',array($id))) {
			throw new Exception("Letter not found for event id: $id");
		}

		$letter->print = 1;
		$letter->draft = 0;

		if (@$_GET['all']) {
			$letter->print_all = 1;
		}

		if (!$letter->save()) {
			throw new Exception("Unable to save letter: ".print_r($letter->getErrors(),true));
		}

		if (!$event = Event::model()->findByPk($id)) {
			throw new Exception("Event not found: $id");
		}

		$event->info = '';

		if (!$event->save()) {
			throw new Exception("Unable to save event: ".print_r($event->getErrors(),true));
		}

		return true;
	}

	/**
	 * Wrapper action to mark letter for printing and then view the letter to trigger
	 * printing behaviour client side
	 *
	 * @param $id
	 */
	public function actionDoPrintAndView($id)
	{
		if ($this->setPrintForEvent($id)) {
			$this->redirect(array('default/view/'.$id));
		}
	}

	/**
	 * Ajax action to mark letter for printing
	 *
	 * @param $id
	 * @throws Exception
	 */
	public function actionDoPrint($id)
	{
		if ($this->setPrintForEvent($id)) {
			echo "1";
		}
	}
}
