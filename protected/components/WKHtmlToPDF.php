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

		$banner = shell_exec($this->wkhtmltopdf." 2>&1");

		if (preg_match('/reduced functionality/i',$banner)) {
			throw new Exception("wkhtmltopdf has not been compiled with patched QT and so cannot be used.");
		}
	}

	public function generateEventPDF($event, $html, $output_html=false)
	{
		$html = str_replace('href="/assets/','href="'.Yii::app()->assetManager->basePath.'/',$html);
		$html = str_replace('src="/assets/','src="'.Yii::app()->assetManager->basePath.'/',$html);

		$docref = "E:$event->id/".strtoupper(base_convert(time().sprintf('%04d', Yii::app()->user->getId()), 10, 32)).'/{{PAGE}}';

		if (!file_exists($event->imageDirectory)) {
			if (!@mkdir($event->imageDirectory,0755,true)) {
				throw new Exception("Unable to create directory: $event->imageDirectory: check permissions.");
			}
		}

		if (!@file_put_contents("$event->imageDirectory/event.html",$html)) {
			throw new Exception("Unable to write to $event->imageDirectory/event.html: check permissions.");
		}

		if (!$footer = @file_get_contents(Yii::app()->basePath."/views/print/event_footer.php")) {
			throw new Exception("Footer partial not found");
		}

		$footer = str_replace('{{FOOTER_LEFT}}',Yii::app()->params['wkhtmltopdf_footer_left'],$footer);
		$footer = str_replace('{{FOOTER_MIDDLE}}',Yii::app()->params['wkhtmltopdf_footer_middle'],$footer);
		$footer = str_replace('{{FOOTER_RIGHT}}',Yii::app()->params['wkhtmltopdf_footer_right'],$footer);

		$footer = str_replace('{{PATIENT_NAME}}',$event->episode->patient->getHSCICName(),$footer);
		$footer = str_replace('{{PATIENT_HOSNUM}}',$event->episode->patient->hos_num,$footer);
		$footer = str_replace('{{PATIENT_NHSNUM}}',$event->episode->patient->nhs_num,$footer);
		$footer = str_replace('{{BARCODE}}',$event->barCodeHTML,$footer);
		$footer = str_replace('{{DOCREF}}',$docref,$footer);
		$footer = str_replace('{{PAGE}}','<span class="page"></span>',$footer);
		$footer = str_replace('{{PAGES}}','<span class="topage"></span>',$footer);

		if (!@file_put_contents("$event->imageDirectory/footer.html",$footer)) {
			throw new Exception("Unable to write to $event->imageDirectory/footer.html: check permissions.");
		}

		if ($output_html) {
			echo $html.$footer;
			Yii::app()->end();
		}

		$res = shell_exec("{$this->wkhtmltopdf} --footer-html '{$event->imageDirectory}/footer.html' --print-media-type '{$event->imageDirectory}/event.html' '{$event->imageDirectory}/event.pdf' 2>&1");

		if (!file_exists("$event->imageDirectory/event.pdf") || filesize("$event->imageDirectory/event.pdf") == 0) {
			return false;
		}

		@unlink("$event->imageDirectory/event.html");
		@unlink("$event->imageDirectory/footer.html");

		return true;
	}
}
