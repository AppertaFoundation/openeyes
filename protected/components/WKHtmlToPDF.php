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

	public function generateDocRef($event_id)
	{
		return "E:$event_id/".strtoupper(base_convert(time().sprintf('%04d', Yii::app()->user->getId()), 10, 32)).'/{{PAGE}}';
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

	public function formatFooter($footer, $left, $middle, $right, $patient, $barcode_html, $docref)
	{
		$footer = str_replace('{{FOOTER_LEFT}}',$left,$footer);
		$footer = str_replace('{{FOOTER_MIDDLE}}',$middle,$footer);
		$footer = str_replace('{{FOOTER_RIGHT}}',$right,$footer);
		$footer = str_replace('{{PATIENT_NAME}}',$patient->getHSCICName(),$footer);
		$footer = str_replace('{{PATIENT_HOSNUM}}',$patient->hos_num,$footer);
		$footer = str_replace('{{PATIENT_NHSNUM}}',$patient->nhs_num,$footer);
		$footer = str_replace('{{BARCODE}}',$barcode_html,$footer);
		$footer = str_replace('{{DOCREF}}',$docref,$footer);
		$footer = str_replace('{{PAGE}}','<span class="page"></span>',$footer);
		$footer = str_replace('{{PAGES}}','<span class="topage"></span>',$footer);

		return $footer;
	}

	public function getPDFInject($path)
	{
		return new OEPDFInject($path);
	}

	public function generateEventPDF($event, $html, $output_html=false, $inject_autoprint_js=true)
	{
		$html = $this->remapAssetPaths($html);
		$docref = $this->generateDocRef($event->id);

		$this->findOrCreateDirectory($event->imageDirectory);
		$this->writeFile("$event->imageDirectory/event.html",$html);

		$footer = $this->formatFooter(
			$this->readFile(Yii::app()->basePath."/views/print/event_footer.php"),
			Yii::app()->params['wkhtmltopdf_footer_left'],
			Yii::app()->params['wkhtmltopdf_footer_middle'],
			Yii::app()->params['wkhtmltopdf_footer_right'],
			$event->episode->patient,
			$event->barCodeHTML,
			$docref
		);

		$this->writeFile("$event->imageDirectory/footer.html",$footer);

		if ($output_html) {
			echo $html.$footer;
			return true;
		}

		$res = $this->execute("{$this->wkhtmltopdf} --footer-html '{$event->imageDirectory}/footer.html' --print-media-type '{$event->imageDirectory}/event.html' '{$event->imageDirectory}/event.pdf' 2>&1");

		if (!$this->fileExists("$event->imageDirectory/event.pdf") || $this->fileSize("$event->imageDirectory/event.pdf") == 0) {
			return false;
		}

		$this->deleteFile("$event->imageDirectory/event.html");
		$this->deleteFile("$event->imageDirectory/footer.html");

		if ($inject_autoprint_js) {
			$pdf = $this->getPDFInject("$event->imageDirectory/event.pdf");
			$pdf->inject('print(true);');
		}

		return true;
	}
}
