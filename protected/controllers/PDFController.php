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

	public function actionHTML($operation_id) {
		$operation = ElementOperation::model()->findByPk($operation_id);
		$html = $this->getLetter($operation);
		echo $html;
	}

	public function actionPDF($operation_id) {
		$operation = ElementOperation::model()->findByPk($operation_id);
		$pdf = new OETCPDF();
		$pdf->SetAuthor("Jamie Neil");
		$pdf->SetTitle("PDF Print Test - GP Letter");
		$pdf->SetSubject("PDF Print Test");
		$pdf->AddPage();
		
		// Banner
		$pdf->Banner();
		
		// Envelope Address
		$patient = $operation->event->episode->patient;
		$gp = $patient->gp;
		$address = $gp->contact->fullname . "\n" . implode("\n",$gp->contact->correspondAddress->getLetterArray(false));
		$pdf->ToAddress($address);
		
		// From Address
		$site = $operation->site;
		$address = $site->getLetterAddress();
		$address .= "\nTel: " . $site->telephone;
		if($site->fax) {
			$address .= "\nFax: " . $site->fax;
		}
		$pdf->FromAddress($address);
		
		$html = $this->getLetter($operation);
		$pdf->BodyStart();
		$pdf->SetFontSize(16);
		$pdf->writeHTML($html, true, false, true, false, '');
		$pdf->lastPage();
		$pdf->Output("gp_letter.pdf", "I");
	}

	protected function getLetter($operation) {
		$patient = $operation->event->episode->patient;
		$firm = $operation->event->episode->firm;
		$site = $operation->site;
		$waitingListContact = $operation->waitingListContact;
		return $this->renderPartial('/letters/pdf/gp_letter', array(
				'operation' => $operation,
				'site' => $site,
				'patient' => $patient,
				'firm' => $firm,
				'changeContact' => $waitingListContact,
		), true);
	}

}
