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
	protected $body_html;

	/**
	 * @param string $to_address Address of recipient, lines separated by \n
	 * @param string $from_address Address of sender, lines separated by \n
	 * @param string $body (optional) Body of the letter in HTML format
	 */
	public function __construct($to_address = null, $from_address = null, $body = null) {
		$this->to_address = $to_address;
		$this->from_address = $from_address;
		$this->body_html = $body;
	}

	/**
	 * Add HTML to body
	 * @param string $body HTML to be added to letter body
	 */
	public function addBody($body) {
		$this->body_html .= $body;
	}

	/**
	 * Render the letter into supplied PDF
	 * @param OETCPDF $pdf
	 */
	public function render($pdf) {
		$pdf->startPageGroup();
		$pdf->AddPage();
		if($this->to_address) {
			$pdf->ToAddress($this->to_address);
		}
		if($this->from_address) {
			$pdf->FromAddress($this->from_address);
		}
		$pdf->moveToBodyStart();
		$pdf->writeHTML($this->body_html, true, false, true, true, 'L');
	}

}