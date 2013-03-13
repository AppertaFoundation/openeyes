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

class TheatrePDF extends FPDF {
	public $columns = array(
		'P' => array(
			0 => array(
				'title' => 'HOSPT NO',
				'width' => 18,
				'align' => 'C'
			),
			1 => array(
				'title' => 'PATIENT',
				'width' => 25,
				'align' => 'L',
				'wrap' => true
			),
			2 => array(
				'title' => 'AGE',
				'width' => 8,
				'align' => 'C'
			),
			3 => array(
				'title' => 'WARD',
				'width' => 15,
				'align' => 'C',
				'wrap' => true
			),
			4 => array(
				'title' => 'GA or LA',
				'width' => 15,
				'align' => 'C'
			),
			5 => array(
				'title' => 'PROCEDURES AND COMMENTS',
				'width' => 92,
				'align' => 'L',
				'wrap' => true
			),
			6 => array(
				'title' => 'ADMISSION',
				'width' => 0, // 0 used on final column to wrap to the edge of the right margin
				'align' => 'C'
			)
		),
		'L' => array(
			0 => array(
				'title' => 'HOSPT NO',
				'width' => 24,
				'align' => 'C'
			),
			1 => array(
				'title' => 'PATIENT',
				'width' => 45,
				'align' => 'L',
				'wrap' => true
			),
			2 => array(
				'title' => 'AGE',
				'width' => 10,
				'align' => 'C'
			),
			3 => array(
				'title' => 'WARD',
				'width' => 20,
				'align' => 'C',
				'wrap' => true
			),
			4 => array(
				'title' => 'GA or LA',
				'width' => 20,
				'align' => 'C'
			),
			5 => array(
				'title' => 'PROCEDURES AND COMMENTS',
				'width' => 137,
				'align' => 'L',
				'wrap' => true
			),
			6 => array(
				'title' => 'ADMISSION',
				'width' => 0, // 0 used on final column to wrap to the edge of the right margin
				'align' => 'C'
			)
		)
	);
	private $font = 'Helvetica';
	private $row_heights = array();
	private $field_heights = array();
	private $row_count = 0;
	private $row_padding_top = 2;
	private $row_padding_bottom = 2;
	private $row_height = 4;
	private $bold = false;
	private $fontsize = 12;
	private $row_data = array();
	private $n_pages = 0;
	private $orientation = 'L';
	private $page_size = 'A4';

	function __construct() {
		parent::__construct();
		$this->columns = $this->columns[$this->orientation];
	}

	function fontsize($pt) {
		$this->fontsize = $pt;
		$this->SetFont($this->font,($this->bold ? 'B' : ''),$this->fontsize);
	}

	function bold($sw=true) {
		$this->bold = $sw;
		$this->SetFont($this->font,($this->bold ? 'B' : ''),$this->fontsize);
	}

	function addPage() {
		parent::addPage($this->orientation, $this->page_size);
		$this->n_pages++;
	}

	function add_page($params, $has_procedures=true) {
		$this->addPage();
		$this->fontsize(14);

		$this->setY(5);

		$this->Cell(0,4,'OPERATION LIST FORM',0,1,'C');

		$this->SetLineWidth(0.5);

		if ($this->orientation == 'P') {
			$this->Line(50,11,150,11);
		} else {
			$this->Line(120,11,177,11);
		}

		$this->setY(15);

		$this->fontsize(12);

		$this->SetLineWidth(0.1);

		$this->page_params[$this->n_pages-1] = $params;

		if ($this->orientation == 'P') {
			$this->Cell(80,8,'THEATRE NO:','B',0);
			$this->Cell(110,8,$params['theatre_no'],'B',1);

			$this->Cell(80,8,'SESSION:','B',0);
			$this->Cell(67,8,$params['session'],'B',0);
			$this->Cell(43,8,'NHS','B',1);

			$this->Cell(80,8,'SURGICAL FIRM: '.$params['surgical_firm'],'B',0);
			$this->Cell(67,8,'ANAESTHETIST: '.$params['anaesthetist'],'B',0);
			$this->Cell(15,8,'DATE:','B',0);
			$this->Cell(28,8,$params['date'],'B',1);
		} else {
			$this->Cell(100,8,'THEATRE NO:','B',0);
			$this->Cell(177,8,$params['theatre_no'],'B',1);

			$this->Cell(100,8,'SESSION:','B',0);
			$this->Cell(97,8,$params['session'],'B',0);
			$this->Cell(80,8,'NHS','B',1);

			$this->Cell(100,8,'SURGICAL FIRM: '.$params['surgical_firm'],'B',0);
			$this->Cell(97,8,'ANAESTHETIST: '.$params['anaesthetist'],'B',0);
			$this->Cell(15,8,'DATE:','B',0);
			$this->Cell(65,8,$params['date'],'B',1);
		}

		$this->setY(42);

		$this->bold();

		$this->fontsize(10);

		if ($has_procedures) {
			foreach ($this->columns as $i => $column) {
				$this->Cell($column['width'],8,$column['title'],0,($i+1 == count($this->columns)) ? 1 : 0);
			}
		} else {
			$this->Cell(80,8,"No operations are booked in this session.",0,1);
		}

		$this->bold(false);
	}

	function add_row() {
		$this->row_data[$this->n_pages-1][] = func_get_args();
		$this->calc_row_height($this->row_data[$this->n_pages-1][count($this->row_data[$this->n_pages-1])-1]);
	}

	function calc_row_height($params) {
		$this->y_offset = $y_original = $this->GetY();

		foreach ($this->columns as $i => $column) {
			$this->columnwrap($column['width'],4,$params[$i],1,$column['align']);
			$this->field_heights[$this->n_pages-1][count($this->row_heights[$this->n_pages-1])][$i] = $this->field_height;
		}

		$this->row_heights[$this->n_pages-1][] = $this->y_offset-$y_original;

		$this->SetY($this->y_offset);
	}

	function build() {
		$this->pdf = new TheatrePDF;

		for ($n=0; $n<$this->n_pages; $n++) {
			$this->pdf->add_page($this->page_params[$n], !empty($this->row_data[$n]));

			if (!empty($this->row_data[$n])) {
				$this->x = $this->pdf->GetX();
				$this->y = $this->pdf->GetY();

				foreach ($this->row_heights[$n] as $i => $height) {
					foreach ($this->columns as $j => $column) {
						$this->pdf->Cell($column['width'],$height + $this->row_padding_top + $this->row_padding_bottom,"",1,($j+1 == count($this->columns)) ? 1 : 0,$column['align']);
					}
				}

				$this->pdf->SetXY($this->x,$this->y+$this->row_padding_top);

				$this->row = 0;

				foreach ($this->row_data[$n] as $row) {
					$this->operation($n, $row);
				}
			}
		}

		$this->write();
	}

	function operation($pagen, $params) {
		$x = $this->x;

		foreach ($this->columns as $i => $column) {
			if ($this->field_heights[$pagen][$this->row][$i] == $this->row_height) {
				$this->pdf->MultiCell($column['width'],$this->row_heights[$pagen][$this->row],$params[$i],0,$column['align']);
				$x += $column['width'];
				$this->pdf->SetXY($x,$this->y+$this->row_padding_top);
			} else {
				$this->pdf->MultiCell($column['width'],$this->row_height,$params[$i],0,$column['align']);
				$x += $column['width'];
				$this->pdf->SetXY($x,$this->y+$this->row_padding_top);
			}
		}

		$this->y += $this->row_heights[$pagen][$this->row] + $this->row_padding_top + $this->row_padding_bottom;
		$this->pdf->SetXY($this->x,$this->y+$this->row_padding_top);
		$this->row++;
	}

	function write() {
		$this->pdf->Output('Theatre List '.date('d.m.Y').'.pdf','I');
	}

	function columnwrap($width, $height, $data, $border=0, $align='J', $fill=false) {
		$y = $this->GetY();
		$x = $this->GetX();

		$this->MultiCell($width,$height,$data,$border,$align,$fill);

		$this->field_height = $this->GetY() - $y;

		if ($this->GetY() > $this->y_offset) {
			$this->y_offset = $this->GetY();
		}

		$this->setY($y);
		$this->setX($x+$width);
	}
}
?>
