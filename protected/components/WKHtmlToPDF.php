<?php

/**
 * OpenEyes.
 *
 *
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class WKHtmlToPDF extends WKHtmlToX
{
    public $left;
    public $middle;
    public $right;

    protected $barcodes = array();
    protected $documents = 1;
    protected $docrefs = array();
    protected $patients = array();
    public $custom_tags = array();

    public $top_margin;
    public $bottom_margin;
    public $left_margin;
    public $right_margin;
    public $page_size;
    public $orientation;

    public function __construct()
    {
        $this->param_key = 'wkhtmltopdf';
        $this->application_name = 'wkhtmltopdf';

        parent::__construct();

        $this->left = Yii::app()->params['wkhtmltopdf_footer_left'];
        $this->middle = Yii::app()->params['wkhtmltopdf_footer_middle'];
        $this->right = Yii::app()->params['wkhtmltopdf_footer_right'];

        $this->top_margin = Yii::app()->params['wkhtmltopdf_top_margin'];
        $this->bottom_margin = Yii::app()->params['wkhtmltopdf_bottom_margin'];
        $this->left_margin = Yii::app()->params['wkhtmltopdf_left_margin'];
        $this->right_margin = Yii::app()->params['wkhtmltopdf_right_margin'];
        $this->page_size = Yii::app()->params['wkhtmltopdf_page_size'];
        $this->orientation = Yii::app()->params['wkhtmltopdf_orientation'];
    }

    public function formatFooter($footer, $left, $middle, $right)
    {
        $patient_names = array();
        $patient_hosnums = array();
        $patient_nhsnums = array();
        $patient_dobs = array();

        foreach ($this->patients as $patient) {
            $patient_names[] = $patient->getHSCICName(true);
            $patient_hosnums[] = $patient->hos_num;
            $patient_nhsnums[] = $patient->nhsnum;
            $patient_dobs[] = date('d-m-Y', strtotime($patient->dob));
        }

        while (count($patient_names) < $this->documents) {
            $patient_names[] = $patient_names[count($patient_names) - 1];
            $patient_hosnums[] = $patient_hosnums[count($patient_hosnums) - 1];
            $patient_nhsnums[] = $patient_nhsnums[count($patient_nhsnums) - 1];
            $patient_dobs[] = $patient_dobs[count($patient_dobs) - 1];
        }

        while (count($this->barcodes) < $this->documents) {
            $this->barcodes[] = $this->barcodes[count($this->barcodes) - 1];
        }

        while (count($this->docrefs) < $this->documents) {
            $this->docrefs[] = $this->docrefs[count($this->docrefs) - 1];
        }

        $footer = str_replace('{{FOOTER_LEFT}}', $left, $footer);
        $footer = str_replace('{{FOOTER_MIDDLE}}', $middle, $footer);
        $footer = str_replace('{{FOOTER_RIGHT}}', $right, $footer);
        $footer = str_replace('{{PATIENT_NAMES}}', CJavaScript::encode($patient_names), $footer);
        $footer = str_replace('{{PATIENT_HOSNUMS}}', CJavaScript::encode($patient_hosnums), $footer);
        $footer = str_replace('{{PATIENT_NHSNUMS}}', CJavaScript::encode($patient_nhsnums), $footer);
        $footer = str_replace('{{PATIENT_DOBS}}', CJavaScript::encode($patient_dobs), $footer);
        $footer = str_replace('{{PATIENT_NAME}}', '<span class="patient_name"></span>', $footer);
        $footer = str_replace('{{PATIENT_HOSNUM}}', '<span class="patient_hosnum"></span>', $footer);
        $footer = str_replace('{{PATIENT_NHSNUM}}', '<span class="patient_nhsnum"></span>', $footer);
        $footer = str_replace('{{PATIENT_DOB}}', '<span class="patient_dob"></span>', $footer);
        $footer = str_replace('{{BARCODES}}', CJavaScript::encode($this->barcodes), $footer);
        $footer = str_replace('{{BARCODE}}', '<span class="barcode"></span>', $footer);
        $footer = str_replace('{{DOCREF}}', '<span class="docref"></span>', $footer);
        $footer = str_replace('{{DOCREFS}}', CJavaScript::encode($this->docrefs), $footer);
        $footer = str_replace('{{DOCUMENTS}}', $this->documents, $footer);
        $footer = str_replace('{{PAGE}}', '<span class="page"></span>', $footer);
        $footer = str_replace('{{PAGES}}', '<span class="topage"></span>', $footer);
        $footer = str_replace('{{CUSTOM_TAGS}}', CJavaScript::encode($this->custom_tags), $footer);
        $footer = str_replace('{{NHS No}}', Yii::app()->params['nhs_num_label'].' Number', $footer);

        return $footer;
    }

    public function getPDFOptions($path)
    {
        return new OEPDFOptions($path);
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

    public function setLeft($left)
    {
        $this->left = $left;
    }

    public function setMiddle($middle)
    {
        $this->middle = $middle;
    }

    public function setRight($right)
    {
        $this->right = $right;
    }

    public function setMarginTop($top_margin)
    {
        $this->top_margin = $top_margin;
    }

    public function setMarginBottom($bottom_margin)
    {
        $this->bottom_margin = $bottom_margin;
    }

    public function setMarginLeft($left_margin)
    {
        $this->left_margin = $left_margin;
    }

    public function setMarginRight($right_margin)
    {
        $this->right_margin = $right_margin;
    }

    public function generatePDF(
        $imageDirectory,
        $prefix,
        $suffix,
        $html,
        $output_html = false,
        $inject_autoprint_js = true,
        $print_footer = true
    ) {
        $footer = null;
        !$output_html && $html = $this->remapAssetPaths($html);
        !$output_html && $html = $this->remapCanvasImagePaths($html);

        $this->findOrCreateDirectory($imageDirectory);

        $html_file = $suffix ? "$imageDirectory" . DIRECTORY_SEPARATOR . "{$prefix}_$suffix.html" : "$imageDirectory" . DIRECTORY_SEPARATOR . "$prefix.html";
        $pdf_file = $suffix ? "$imageDirectory" . DIRECTORY_SEPARATOR . "{$prefix}_$suffix.pdf" : "$imageDirectory" . DIRECTORY_SEPARATOR . "$prefix.pdf";

        $this->writeFile($html_file, $html);

        $footer_file = $suffix ? "$imageDirectory" . DIRECTORY_SEPARATOR . "footer_$suffix.html" : "$imageDirectory" . DIRECTORY_SEPARATOR . 'footer.html';
        $footer = $this->formatFooter(
            $this->readFile(Yii::app()->basePath . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'print' . DIRECTORY_SEPARATOR . 'pdf_footer.php'),
            $this->left,
            $this->middle,
            $this->right,
            $this->patients,
            $this->barcodes,
            $this->docrefs
        );

        $this->writeFile($footer_file, $footer);
        if ($output_html) {
            echo $html . ($print_footer ? $footer : null);

            return true;
        }

        $top_margin = $this->top_margin ? '-T ' . $this->top_margin : '';
        $bottom_margin = $this->bottom_margin ? '-B ' . $this->bottom_margin : '';
        $left_margin = $this->left_margin ? '-L ' . $this->left_margin : '';
        $right_margin = $this->right_margin ? '-R ' . $this->right_margin : '';

        $page_size = $this->page_size;

        $nice = Yii::app()->params['wkhtmltopdf_nice_level'] ? 'nice -n' . Yii::app()->params['wkhtmltopdf_nice_level'] . ' ' : '';
        $res = $this->execute($nice . escapeshellarg($this->application_path) .  ($print_footer === 'true' ? ' --footer-html ' . escapeshellarg($footer_file) : null)  . ($this->orientation ? " --orientation $this->orientation" : null) . " --print-media-type $top_margin $bottom_margin $left_margin $right_margin " . ($page_size ? "--page-size $page_size ": null).  escapeshellarg($html_file) . ' ' . escapeshellarg($pdf_file) . ' 2>&1');

        if (!$this->fileExists($pdf_file) || $this->fileSize($pdf_file) == 0) {
            if ($this->fileSize($pdf_file) == 0) {
                $this->deleteFile($pdf_file);
            }

            throw new Exception("Unable to generate $pdf_file: $res");
        }

        //$this->deleteFile($html_file);
        $this->deleteFile($footer_file);

        if ($pdf = $this->getPDFOptions($pdf_file)) {
            if ($inject_autoprint_js) {
                $pdf->injectJS('print(true);');
            }

            $pdf->disablePrintScaling();
            $pdf->write();
        }

        return true;
    }

    public function setCustomTag($tag_name, $value)
    {
        $this->custom_tags[$tag_name] = $value;
    }
}
