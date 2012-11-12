<?php

class OEPDFPrint {

	protected $letters = array();
	protected $pdf;

	public function __construct($author, $title, $subject) {
		$this->pdf = new OETCPDF();
		$this->pdf->SetAuthor($author);
		$this->pdf->SetTitle($title);
		$this->pdf->SetSubject($subject);
	}

	public function addLetter($letter) {
		$this->letters[] = $letter;
	}

	public function output() {
		foreach($this->letters as $letter) {
			$letter->render($this->pdf);
		}
		$this->pdf->Output($this->pdf->getDocref().".pdf", "I");
	}

}
