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

class OELetter {

	protected $to_address;
	protected $from_address;
	protected $re;
	protected $to;
	protected $body_html;
	protected $from;
	protected $accessible = false;

	/**
	 * @param string $to_address Address of recipient, lines separated by \n
	 * @param string $to Name of recipient (used in salutation)
	 * @param string $from Name of sender (used in sign off)
	 * @param string $body_html (optional) Body of the letter in HTML format
	 */
	public function __construct($to_address, $to, $from, $body_html = null) {
		$this->to_address = $to_address;
		$this->to = $to;
		$this->from = $from;
		$this->body_html = $body_html;
	}

	/**
	 * Set sender address
	 * @param string $from_address Address of sender, lines separated by \n,
	 */
	public function setFromAddress($from_address) {
		$this->from_address = $from_address;
	}

	/**
	 * Get sender address
	 * @return string
	 */
	protected function getFromAddress() {
		if($this->from_address) {
			return $this->from_address;
		} else {
			// TODO: Implement alternative ways of getting the sender address, e.g. current site
		}
	}

	/**
	 * Set regarding line
	 * @param string $re
	 */
	public function setRe($re) {
		$this->re = $re;
	}

	/**
	 * Add HTML to body
	 * @param string $body_html
	 */
	public function addBody($body_html) {
		$this->body_html .= $body_html;
	}

	/**
	 * Render the letter into supplied PDF
	 * @param OETCPDF $pdf
	 */
	public function render($pdf) {
		$pdf->startPageGroup();
		$pdf->AddPage();
		$pdf->ToAddress($this->to_address);
		$pdf->FromAddress($this->getFromAddress());
		$pdf->moveToBodyStart();
		if($this->accessible) {
			$pdf->SetFontSize(16);
		}

		// Re
		$pdf->SetFont('times','B');
		$pdf->MultiCell(0, 0, 'Re: '.$this->re, 0, 'L');
		$pdf->setY($pdf->GetY() + 5);

		// Salutation
		$pdf->SetFont('times','');
		$pdf->Cell(0, 0, "Dear " . $this->to . ",", 0, 1, 'L');
		$pdf->setY($pdf->GetY() + 5);

		// Body
		$pdf->writeHTML($this->body_html, true, false, true, true, 'L');
		$pdf->setY($pdf->GetY() + 5);
		
		// Signed
		$sign_off = "Yours sincerely,\n\n\n\n".$this->from;
		$sign_off_height = $pdf->getStringHeight(0, $sign_off, true);
		$pdf->pageBreakIfRequired($sign_off_height);
		$pdf->MultiCell(0, 0, "Yours sincerely,\n\n\n\n".$this->from, 0, 'L');
		
	}

}