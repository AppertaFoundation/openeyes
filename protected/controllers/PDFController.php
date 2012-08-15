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

		// Get data
		$operation = ElementOperation::model()->findByPk($operation_id);
		$patient = $operation->event->episode->patient;
		$site = $operation->site;
		$firm = $operation->event->episode->firm;
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

		// GP Letter
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

		// Render PDF
		$pdf->Output("gp_letter.pdf", "I");
	}

}
