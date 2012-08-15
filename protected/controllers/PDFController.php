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

class PDFController extends BaseController {

	public function actionPDF($operation_id) {

		$operation = ElementOperation::model()->findByPk($operation_id);
		$patient = $operation->event->episode->patient;

		// Get letter template
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

			// Don't print GP letter if GP is not defined
			if($letter_status != ElementOperation::LETTER_GP || $patient->gp) {
				Yii::log("Printing letter: ".$letter_template, 'trace');
				$this->printLetters($operation, $letter_template);
			} else {
				Yii::log("Patient has no GP, printing letter supressed: ".$patient->id, 'trace');
			}

		} else if($letter_status === null) {
			Yii::log("No letter is due: ".$patient->id, 'trace');
		} else {
			throw new CException('Undefined letter status');
		}
	}

	protected function printLetters($operation, $letter_template) {

		// Get data
		$patient = $operation->event->episode->patient;
		$site = $operation->site;
		$firm = $operation->event->episode->firm;

		// Consultant
		if($consultant = $firm->getConsultant()) {
			$consultant_name = $consultant->contact->title . ' ' . $consultant->contact->first_name . ' ' . $consultant->contact->last_name;
		} else {
			$consultant_name = 'CONSULTANT';
		}

		// From Address
		$from_address = $site->getLetterAddress();
		$from_address .= "\nTel: " . $site->telephone;
		if($site->fax) {
			$from_address .= "\nFax: " . $site->fax;
		}

		// Prepare PDF
		$pdf = new OETCPDF();
		$pdf->SetAuthor("Jamie Neil");
		$pdf->SetTitle("PDF Print Test - GP Letter");
		$pdf->SetSubject("PDF Print Test");

		call_user_func(array($this, 'print_'.$letter_template), $pdf, $from_address, $operation, $consultant_name);
		$this->print_admission_form($pdf, $from_address, $operation, $consultant_name);

		// Render PDF
		$pdf->Output("gp_letter.pdf", "I");
	}

	protected function print_admission_form($pdf, $from_address, $operation, $consultant_name) {
		$patient = $operation->event->episode->patient;
		$to_address = $patient->addressname . "\n" . implode("\n", $patient->correspondAddress->getLetterArray(false));
		$letter = new OELetter($to_address, $patient->salutationname, 'Admissions Officer');
		$letter->setFromAddress($from_address);
		$site = $operation->site;
		$firm = $operation->event->episode->firm;
		$html = $this->renderPartial('/letters/pdf/admission_form', array(
				'operation' => $operation,
				'site' => $site,
				'patient' => $patient,
				'firm' => $firm,
				'emergencyList' => false,
		), true);
		$letter->addBody($html);
		$letter->render($pdf);
	}

	protected function print_invitation_letter($pdf, $from_address, $operation, $consultant_name) {
		$patient = $operation->event->episode->patient;
		$to_address = $patient->addressname . "\n" . implode("\n", $patient->correspondAddress->getLetterArray(false));
		$letter = new OELetter($to_address, $patient->salutationname, 'Admissions Officer');
		$letter->setFromAddress($from_address);
		$re = "Hospital number: " . $patient->hos_num;
		if (!empty($patient->nhs_num)) {
			$re .= ", NHS number: " . $patient->nhs_num;
		}
		$letter->setRe($re);
		$waitingListContact = $operation->waitingListContact;
		$html = $this->renderPartial('/letters/pdf/invitation_letter', array(
				'consultantName' => $consultant_name,
				'operation' => $operation,
				'patient' => $patient,
				'changeContact' => $waitingListContact,
		), true);
		$letter->addBody($html);
		$letter->render($pdf);
	}

	protected function print_reminder_letter($pdf, $from_address, $operation, $consultant_name) {
		$patient = $operation->event->episode->patient;
		$to_address = $patient->addressname . "\n" . implode("\n", $patient->correspondAddress->getLetterArray(false));
		$letter = new OELetter($to_address, $patient->salutationname, 'Admissions Officer');
		$letter->setFromAddress($from_address);
		$re = "Hospital number: " . $patient->hos_num;
		if (!empty($patient->nhs_num)) {
			$re .= ", NHS number: " . $patient->nhs_num;
		}
		$letter->setRe($re);
		$waitingListContact = $operation->waitingListContact;
		$html = $this->renderPartial('/letters/pdf/reminder_letter', array(
				'consultantName' => $consultant_name,
				'operation' => $operation,
				'patient' => $patient,
				'changeContact' => $waitingListContact,
		), true);
		$letter->addBody($html);
		$letter->render($pdf);
	}

	protected function print_gp_letter($pdf, $from_address, $operation, $consultant_name) {

		// GP Letter
		$patient = $operation->event->episode->patient;
		$gp = $patient->gp;
		$to_address = $gp->contact->fullname . "\n" . implode("\n",$gp->contact->correspondAddress->getLetterArray(false));
		$letter = new OELetter($to_address, $gp->contact->salutationname, "Admissions Officer");
		$letter->setFromAddress($from_address);
		$re = $patient->fullname ." (DOB: " . $patient->NHSDate('dob') . ", ";
		$re .= ($patient->gender == 'M') ? 'Male' : 'Female';
		$re .= ', ' . $patient->correspondAddress->letterline;
		$re .= ", Hospital number: " . $patient->hos_num;
		if (!empty($patient->nhs_num)) {
			$re .= ", NHS number: " . $patient->nhs_num . ")";
		}
		$letter->setRe($re);
		$html = $this->renderPartial('/letters/pdf/gp_letter', array(
				'consultantName' => $consultant_name,
		), true);
		$letter->addBody($html);
		$letter->render($pdf);

		// Patient letter
		$to_address = $patient->addressname . "\n" . implode("\n", $patient->correspondAddress->getLetterArray(false));
		$letter = new OELetter($to_address, $patient->salutationname, 'Admissions Officer');
		$letter->setFromAddress($from_address);
		$re = "Hospital number: " . $patient->hos_num;
		if (!empty($patient->nhs_num)) {
			$re .= ", NHS number: " . $patient->nhs_num;
		}
		$letter->setRe($re);
		$html = $this->renderPartial('/letters/pdf/removal_letter', array(
				'consultantName' => $consultant_name,
		), true);
		$letter->addBody($html);
		$letter->render($pdf);

	}

}
