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

class WKHtmlToPDF
{
	protected $wkhtmltopdf;
	protected $documents = 1;
	protected $docrefs = array();
	protected $barcodes = array();
	protected $patients = array();

	public function __construct()
	{
		if (Yii::app()->params['wkhtmltopdf_path']) {
			if (!file_exists(Yii::app()->params['wkhtmltopdf_path'])) {
				throw new Exception(Yii::app()->params['wkhtmltopdf_path'].' is missing.');
			}

			$this->wkhtmltopdf = Yii::app()->params['wkhtmltopdf_path'];
		} else {
			if (!$this->wkhtmltopdf = trim(`which wkhtmltopdf`)) {
				throw new Exception("wkhtmltopdf not found in the current path.");
			}
		}

		$banner = $this->execute($this->wkhtmltopdf." 2>&1");

		if (preg_match('/reduced functionality/i',$banner)) {
			throw new Exception("wkhtmltopdf has not been compiled with patched QT and so cannot be used.");
		}
	}

	protected function execute($command)
	{
		return shell_exec($command);
	}

	public function getAssetManager()
	{
		return Yii::app()->assetManager;
	}

	public function remapAssetPaths($html)
	{
		$html = str_replace('href="/assets/','href="'.$this->getAssetManager()->basePath.'/',$html);
		$html = str_replace('src="/assets/','src="'.$this->getAssetManager()->basePath.'/',$html);

		return $html;
	}

	public function findOrCreateDirectory($path)
	{
		if (!file_exists($path)) {
			if (!@mkdir($path,0755,true)) {
				throw new Exception("Unable to create directory: $path: check permissions.");
			}
		}
	}

	public function readFile($path)
	{
		if (!$data = @file_get_contents($path)) {
			throw new Exception("File not found: $path");
		}

		return $data;
	}

	public function writeFile($path, $data)
	{
		if (!@file_put_contents($path,$data)) {
			throw new Exception("Unable to write to $path: check permissions.");
		}
	}

	public function deleteFile($path)
	{
		if (!@unlink($path)) {
			throw new Exception("Unable to delete $path: check permissions.");
		}
	}

	public function fileExists($path)
	{
		return @file_exists($path);
	}

	public function fileSize($path)
	{
		return @filesize($path);
	}

	public function formatFooter($footer, $left, $middle, $right)
	{
		$patient_names = array();
		$patient_hosnums = array();
		$patient_nhsnums = array();

		foreach ($this->patients as $patient) {
			$patient_names[] = $patient->getHSCICName(true);
			$patient_hosnums[] = $patient->hos_num;
			$patient_nhsnums[] = $patient->nhsnum;
		}

		while (count($patient_names) < $this->documents) {
			$patient_names[] = $patient_names[count($patient_names)-1];
			$patient_hosnums[] = $patient_hosnums[count($patient_hosnums)-1];
			$patient_nhsnums[] = $patient_nhsnums[count($patient_nhsnums)-1];
		}

		while (count($this->barcodes) < $this->documents) {
			$this->barcodes[] = $this->barcodes[count($this->barcodes)-1];
		}

		while (count($this->docrefs) < $this->documents) {
			$this->docrefs[] = $this->docrefs[count($this->docrefs)-1];
		}

		$footer = str_replace('{{FOOTER_LEFT}}',$left,$footer);
		$footer = str_replace('{{FOOTER_MIDDLE}}',$middle,$footer);
		$footer = str_replace('{{FOOTER_RIGHT}}',$right,$footer);
		$footer = str_replace('{{PATIENT_NAMES}}',CJavaScript::encode($patient_names),$footer);
		$footer = str_replace('{{PATIENT_HOSNUMS}}',CJavaScript::encode($patient_hosnums),$footer);
		$footer = str_replace('{{PATIENT_NHSNUMS}}',CJavaScript::encode($patient_nhsnums),$footer);
		$footer = str_replace('{{PATIENT_NAME}}','<span class="patient_name"></span>',$footer);
		$footer = str_replace('{{PATIENT_HOSNUM}}','<span class="patient_hosnum"></span>',$footer);
		$footer = str_replace('{{PATIENT_NHSNUM}}','<span class="patient_nhsnum"></span>',$footer);
		$footer = str_replace('{{BARCODES}}',CJavaScript::encode($this->barcodes),$footer);
		$footer = str_replace('{{BARCODE}}','<span class="barcode"></span>',$footer);
		$footer = str_replace('{{DOCREF}}','<span class="docref"></span>',$footer);
		$footer = str_replace('{{DOCREFS}}',CJavaScript::encode($this->docrefs),$footer);
		$footer = str_replace('{{DOCUMENTS}}',$this->documents,$footer);
		$footer = str_replace('{{PAGE}}','<span class="page"></span>',$footer);
		$footer = str_replace('{{PAGES}}','<span class="topage"></span>',$footer);

		return $footer;
	}

	public function getPDFInject($path)
	{
		return new OEPDFInject($path);
	}

	public function setDocuments($count)
	{
		$this->documents = $count;
	}

	public function setDocref($docref)
	{
		$this->docrefs = array($docref);
	}

	public function setDocrefs($docrefs)
	{
		$this->docrefs = $docrefs;
	}

	public function setBarcode($barcode_html)
	{
		$this->barcodes = array($barcode_html);
	}

	public function setBarcodes($barcodes)
	{
		$this->barcodes = $barcodes;
	}

	public function setPatient($patient)
	{
		$this->patients = array($patient);
	}

	public function setPatients($patients)
	{
		$this->patients = $patients;
	}

	public function generatePDF($imageDirectory, $prefix, $suffix, $html, $output_html=false, $inject_autoprint_js=true)
	{
		!$output_html && $html = $this->remapAssetPaths($html);

		$this->findOrCreateDirectory($imageDirectory);

		$html_file = $suffix ? "$imageDirectory/{$prefix}_$suffix.html" : "$imageDirectory/$prefix.html";
		$pdf_file = $suffix ? "$imageDirectory/{$prefix}_$suffix.pdf" : "$imageDirectory/$prefix.pdf";
		$footer_file = $suffix ? "$imageDirectory/footer_$suffix.html" : "$imageDirectory/footer.html";

		$this->writeFile($html_file, $html);

		$footer = $this->formatFooter(
			$this->readFile(Yii::app()->basePath."/views/print/pdf_footer.php"),
			Yii::app()->params['wkhtmltopdf_footer_left'],
			Yii::app()->params['wkhtmltopdf_footer_middle'],
			Yii::app()->params['wkhtmltopdf_footer_right'],
			$this->patients,
			$this->barcodes,
			$this->docrefs
		);

		$this->writeFile($footer_file, $footer);

		if ($output_html) {
			echo $html.$footer;
			return true;
		}

		$top_margin = Yii::app()->params['wkhtmltopdf_top_margin'] ? "-T ".Yii::app()->params['wkhtmltopdf_top_margin'] : '';
		$bottom_margin = Yii::app()->params['wkhtmltopdf_bottom_margin'] ? "-B ".Yii::app()->params['wkhtmltopdf_bottom_margin'] : '';
		$left_margin = Yii::app()->params['wkhtmltopdf_left_margin'] ? "-L ".Yii::app()->params['wkhtmltopdf_left_margin'] : '';
		$right_margin = Yii::app()->params['wkhtmltopdf_right_margin'] ? "-R ".Yii::app()->params['wkhtmltopdf_right_margin'] : '';

		$res = $this->execute("{$this->wkhtmltopdf} --footer-html '$footer_file' --print-media-type $top_margin $bottom_margin $left_margin $right_margin '$html_file' '$pdf_file' 2>&1");

		if (!$this->fileExists($pdf_file) || $this->fileSize($pdf_file) == 0) {
			if ($this->fileSize($pdf_file) == 0) {
				$this->deleteFile($pdf_file);
			}

			throw new Exception("Unable to generate $pdf_file: $res");
		}

		$this->deleteFile($html_file);
		$this->deleteFile($footer_file);

		if ($inject_autoprint_js) {
			$pdf = $this->getPDFInject($pdf_file);
			$pdf->inject('print(true);');
		}

		return true;
	}
}
