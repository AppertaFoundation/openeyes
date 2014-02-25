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

require_once(dirname(__FILE__).'/tcpdf/tcpdf.php');

class OETCPDF extends TCPDF
{
	/**
	 * @var string Reference printed in bottom left corner of footer
	 */
	protected $docref;

	protected $watermark;

	protected $rollover;

	/**
	 * @var $body_start Default Y position for body of letter to start on the page
	 */
	const BODY_START = 40;
	protected $body_start = self::BODY_START;

	/**
	 * @param string $orientation Orientaion of page (Default: P)
	 */
	public function __construct($orientation = 'P', $print = false)
	{
		parent::__construct($orientation, $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false);
		$this->setMargins(15, 15);
		$this->SetAutoPageBreak(true, 25);
		$this->setHtmlVSpace(array(
				'h1' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 5, 'n' => 1)),
				'h2' => array(0 => array('h' => 6, 'n' => 1), 1 => array('h' => 2, 'n' => 1)),
		));
		$preferences = array(
				'PrintScaling' => 'None',
		);
		$this->setViewerPreferences($preferences);
		if ($print) {
			$this->IncludeJS('print(true);');
		}

	}

	/**
	 * checkPageBreak() is protected, but it's useful for adding a page break before a block if required
	 * @param integer $h
	 */
	public function pageBreakIfRequired($h)
	{
		$this->checkPageBreak($h);
	}

	/**
	 * @param string $docref Override default docref string
	 */
	public function setDocref($docref)
	{
		$this->docref = $docref;
	}

	/**
	 * @return string
	 */
	public function getDocref()
	{
		if (!$this->docref) {
			$this->docref = strtoupper(base_convert(time().sprintf('%04d', Yii::app()->user->getId()), 10, 32));
		}
		return $this->docref;
	}

	public function setWatermark($watermark)
	{
		$this->watermark = $watermark;
	}

	public function setRollover($text)
	{
		$this->rollover = trim($text);
	}

	public function Image($file) {
		$args = func_get_args();
		// Strip cache busting strings from paths.
		$args[0] = preg_replace('/\?.*$/', '', $file);
		call_user_func_array(array($this, 'parent::Image'), $args);
	}

	/**
	 * @see TCPDF::Footer()
	 */
	public function Footer()
	{
		// Page number
		if (empty($this->pagegroups)) {
			$pagenumtxt = $this->getAliasNumPage().' / '.$this->getAliasNbPages();
		} else {
			$pagenumtxt = $this->getPageNumGroupAlias().' / '.$this->getPageGroupAlias();
		}
		$this->SetY(-18);
		$this->SetFont('helvetica', '', 7);
		$this->Cell(0, 10, 'Page ' . $pagenumtxt, 0, false, 'C', 0);

		// Patrons
		$this->SetY(-20.7);
		$this->MultiCell(0, 20, "Patron: Her Majesty The Queen\nChairman: Rudy Markham\nChief	Executive: John Pelly", 0, 'R');

		// Barcode
		$docref = $this->getDocref() . '/' . $this->getAliasNumPage();
		$this->SetY(-18);
		if ($barcode = $this->getBarcode()) {
			$this->SetY(-14);
			$style = array(
					'position' => 'L',
					'align' => 'L',
			);
			$this->write1DBarcode($barcode, 'C128', '', '', 60, 2, 0.3, $style, '');
			$docref = $barcode . '/' . $docref;
			$this->SetY(-21);
		}
		$this->Cell(0, 10, $docref, 0, false, 'L');

	}

	/**
	 * @see TCPDF::Header()
	 */
	public function Header()
	{
		if ($this->getGroupPageNo() == 1) {
			$this->Image(Yii::app()->assetManager->getPublishedPath('img/_print/letterhead_seal.jpg'), 15, 10, 25);
			$this->Image(Yii::app()->assetManager->getPublishedPath('img/_print/letterhead_Moorfields_NHS.jpg'), 95, 12, 100);
		} else {
			if ($this->rollover) {
				$this->setMargins(15, 18);
				$this->writeHTMLCell(0, 0, 16, 12, $this->rollover, 0, 'L');
			}
		}
		if ($this->watermark) {
			$this->StartTransform();
			$this->SetFont('helvetica', '', 96);
			$this->SetTextColor(224,224,224);
			$this->Rotate(60,0,280);
			$this->SetXY(20, 280);
			$this->Cell(300, 40, $this->watermark, $border=0, $ln=0, $align='C', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
			$this->StopTransform();
		}
	}

	/**
	 * Render recipient address, aligned to envelope window
	 * @param string $address Lines delimited with \n
	 */
	public function ToAddress($address)
	{
		$this->setY(45);
		$this->Cell(20, 10, "To:", 0 , 1, 'L');
		$this->setX(20);
		$this->MultiCell(100, 20, $address, 0 ,'L');
		if ($this->body_start < $this->getY()) {
			$this->body_start = $this->getY();
		}
	}

	/**
	 * Render sender address
	 * @param string $address Lines delimited with \n
	 */
	public function FromAddress($address, $hide_date = false)
	{
		$this->setY(35);
		$this->MultiCell(0, 20, $address, 0 ,'R');
		if (!$hide_date) {
			$this->Cell(0, 10, Helper::convertDate2NHS(date('Y-m-d')), 0, 2, 'R');
		}
		if ($this->body_start < $this->getY()) {
			$this->body_start = $this->getY();
		}
	}

	/**
	 * Render reply-to address
	 * @param string $address Lines delimited with \n
	 */
	public function ReplyToAddress($address)
	{
		$this->setY(90);
		$this->MultiCell(0, 0, $address, 0, 'L');
		if ($this->body_start < $this->getY()) {
			$this->body_start = $this->getY();
		}
	}

	/**
	 * Move Y position to start of body, avoiding addresses (if used)
	 * @param boolean $reset Reset body_start to default after move
	 */
	public function moveToBodyStart($reset = true)
	{
		$this->setY($this->body_start);
		if ($reset) {
			$this->body_start = self::BODY_START;
		}
	}
}
