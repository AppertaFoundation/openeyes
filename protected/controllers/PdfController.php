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

class PdfController extends BaseController {

	public function actionPdf($operation_id) {

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

				// Prepare PDF
				$pdf = new OETCPDF();
				$pdf->SetAuthor($operation->usermodified->getFullName());
				$pdf->SetTitle($letter_template);
				$pdf->SetSubject("Booking letter");

				call_user_func(array($this, 'print_'.$letter_template), $pdf, $operation);
				$this->print_admission_form($pdf, $operation);

				// Render PDF
				$pdf->Output($pdf->getDocref().".pdf", "I");
					
			} else {
				Yii::log("Patient has no GP, printing letter supressed: ".$patient->id, 'trace');
			}

		} else if($letter_status === null) {
			Yii::log("No letter is due: ".$patient->id, 'trace');
		} else {
			throw new CException('Undefined letter status');
		}
	}

	protected function getConsultantName($operation) {
		if($consultant = $operation->event->episode->firm->getConsultant()) {
			return $consultant->contact->title . ' ' . $consultant->contact->first_name . ' ' . $consultant->contact->last_name;
		} else {
			return 'CONSULTANT';
		}
	}

	protected function getFromAddress($operation) {
		$from_address = $operation->site->getLetterAddress();
		$from_address .= "\nTel: " . $operation->site->telephone;
		if($operation->site->fax) {
			$from_address .= "\nFax: " . $operation->site->fax;
		}
		return $from_address;
	}

	protected function print_admission_form($pdf, $operation) {
		$patient = $operation->event->episode->patient;
		$to_address = $patient->addressname . "\n" . implode("\n", $patient->correspondAddress->getLetterArray(false));
		$site = $operation->site;
		$firm = $operation->event->episode->firm;
		$body = $this->renderPartial('/letters/pdf/admission_form', array(
				'operation' => $operation,
				'site' => $site,
				'patient' => $patient,
				'firm' => $firm,
				'emergencyList' => false,
		), true);
		$letter = new OELetter();
		$letter->addBody($body);
		$letter->render($pdf);
	}

	protected function print_invitation_letter($pdf, $operation) {
		$patient = $operation->event->episode->patient;
		$to_address = $patient->addressname . "\n" . implode("\n", $patient->correspondAddress->getLetterArray(false));
		$body = $this->renderPartial('/letters/pdf/invitation_letter', array(
				'to' => $patient->salutationname,
				'consultantName' => $this->getConsultantName($operation),
				'overnightStay' => $operation->overnight_stay,
				'isChild' => $patient->isChild(),
				'changeContact' => $operation->waitingListContact,
		), true);
		$letter = new OELetter($to_address, $this->getFromAddress($operation), $body);
		$letter->render($pdf);
	}

	protected function print_reminder_letter($pdf, $operation) {
		$patient = $operation->event->episode->patient;
		$to_address = $patient->addressname . "\n" . implode("\n", $patient->correspondAddress->getLetterArray(false));
		$body = $this->renderPartial('/letters/pdf/reminder_letter', array(
				'to' => $patient->salutationname,
				'consultantName' => $this->getConsultantName($operation),
				'overnightStay' => $operation->overnight_stay,
				'isChild' => $patient->isChild(),
				'changeContact' => $operation->waitingListContact,
		), true);
		$letter = new OELetter($to_address, $this->getFromAddress($operation), $body);
		$letter->render($pdf);
	}

	protected function print_gp_letter($pdf, $operation) {

		// GP Letter
		$patient = $operation->event->episode->patient;
		$gp = $patient->gp;
		$to_address = $gp->contact->fullname . "\n" . implode("\n",$gp->contact->correspondAddress->getLetterArray(false));
		$body = $this->renderPartial('/letters/pdf/gp_letter', array(
				'to' => $gp->contact->salutationname,
				'consultantName' => $this->getConsultantName($operation),
		), true);
		$letter = new OELetter($to_address, $this->getFromAddress($operation), $body);
		$letter->render($pdf);

		// Patient letter
		$to_address = $patient->addressname . "\n" . implode("\n", $patient->correspondAddress->getLetterArray(false));
		$body = $this->renderPartial('/letters/pdf/gp_letter_patient', array(
				'to' => $patient->salutationname,
				'consultantName' => $this->getConsultantName($operation),
		), true);
		$letter = new OELetter($to_address, $this->getFromAddress($operation), $body);
		$letter->render($pdf);

	}

}
