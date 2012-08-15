<?php

require_once(dirname(__FILE__).'/tcpdf/tcpdf.php');

class OETCPDF extends TCPDF {

	protected $docref;

	protected $body_start = 95;

	public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false) {
		parent::__construct();
		$this->setImageScale(1.5);
		$this->setMargins(15, 15);
		$this->SetFont("times", "", 12);
	}

	public function Header() {
	}

	public function setDocref($docref) {
		$this->docref = $docref;
	}

	public function getDocref() {
		if($this->docref) {
			return $this->docref;
		} else {
			return strtoupper(base_convert(time().sprintf('%04d', Yii::app()->user->getId()), 10, 32));
		}
	}

	public function Footer() {
		// Page number
		$this->SetY(-20);
		$this->SetFont('helvetica', '', 8);
		$this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0);

		// Patrons
		$this->SetY(-24);
		$this->MultiCell(0, 20, "Patron: Her Majesty The Queen\nChairman: Rudy Markham\nChief	Executive: John Pelly", 0, 'R');

		// Document reference
		$this->SetY(-20);
		$this->Cell(0, 10, $this->getDocref() . '/' . $this->getAliasNumPage(), 0, false, 'L');

	}

	public function Banner() {
		$image_path = Yii::app()->getBasePath() . '/../img';
		$this->Image($image_path.'/_print/letterhead_seal.jpg', 15, 10, 25);
		$this->Image($image_path.'/_print/letterhead_Moorfields_NHS.jpg', 95, 12, 100);
	}

	public function ToAddress($address) {
		$this->setY(45);
		$this->Cell(20, 10, "To:", 0 , 1, 'L');
		$this->setX(20);
		$this->MultiCell(100, 20, $address, 0 ,'L');
		if($this->body_start < $this->getY()) {
			$this->body_start = $this->getY();
		}
	}

	public function FromAddress($address) {
		$this->setY(35);
		$this->MultiCell(0, 20, $address, 0 ,'R');
		$this->Cell(0, 10, Helper::convertDate2NHS(date('Y-m-d')), 0, 2, 'R');
		if($this->body_start < $this->getY()) {
			$this->body_start = $this->getY();
		}
	}

	public function BodyStart() {
		$this->setY($this->body_start);
	}
	
}