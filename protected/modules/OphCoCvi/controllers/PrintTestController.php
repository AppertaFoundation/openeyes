<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OEModule\OphCoCvi\controllers;

use \OEModule\OphCoCvi\components\ODTTemplateManager;
use mikehaertl\pdftk\XfdfFile;
use mikehaertl\pdftk\FdfFile;
use mikehaertl\pdftk\Pdf;

use FPDF;

require_once str_replace('index.php', 'vendor/setasign/fpdi/pdf_parser.php', \Yii::app()->getRequest()->getScriptFile());
/**
 * Class PrintTestController
 *
 * @package OEModule\OphCoCvi\controllers
 */
class PrintTestController extends \BaseController
{
    public $inputFile = 'example_certificate_4.odt';
    public $xmlDoc;
    public $xpath;
    public $printTestXml;
    public $xml;

    /**
     * @return array
     */
    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('test', 'getPDF'),
                'roles' => array('admin'),
            ),
        );
    }

    /**
     * Test action
     */
    public function actionTest()
    {
        $pdfLink = '';
        if (isset($_POST['test_print'])) {
            $this->printTestXml = new ODTTemplateManager($this->inputFile, realpath(__DIR__ . '/..') . '/files');

            $this->printTestXml->exchangeStringValues($_POST);

            // $this->printTestXml->imgReplace( 'image1.png' , $this->printTestXml->templateDir.'/signature3.png');

            $this->printTestXml->changeImage('image2.png', $this->testGdImage());

            $tablesData = $this->generateTestTablesData('myTable');
            $this->printTestXml->exchangeGeneratedTablesWithTextNodes($tablesData);

            $this->printTestXml->fillTable("Table30", $this->whoIs());
            $this->printTestXml->fillTable("Table45", $this->iConsider());
            $this->printTestXml->fillTable("Table102", $this->copiesInConfidenceTo());
            $this->printTestXml->fillTable("Table348", $this->genTableDatas(), 1);
            $this->printTestXml->fillTable("Table690", $this->yesNoQuestions(), 1);

            $this->printTestXml->saveContentXML($this->printTestXml->contentXml);

            $this->printTestXml->generatePDF();
            $pdfLink = $this->pdfLink();
        }

        $this->render("test", array('pdfLink' => $pdfLink, 'imageSrc' => $this->getImage()));
    }

    /**
     * Get the image
     *
     * @return string
     */
    public function getImage()
    {
        if ($this->printTestXml != null) {
            $data = file_get_contents($this->printTestXml->templateDir . '/signature3.png');

            return '<div style="width:30%;max-height:30%;position:relative;"/><img src="data:image/jpeg;base64,' . base64_encode($data) . '"/></div>';
        }
    }

    /**
     * Get PDF
     */
    public function actionGetPDF()
    {
        $folder = realpath(__DIR__ . '/..') . '/views/odtTemplate/';
        $file = realpath(__DIR__ . '/..') . '/views/odtTemplate/CVI_test.pdf';
        $image = realpath(__DIR__ . '/..') . '/views/odtTemplate/cvi_image.jpg';
        
        $pdf = new Pdf($file);
        //$data = $pdf->getDataFields();
        
        $pdf->fillForm([
                'Address1'  => 'UXBRIDGE, 76  Canterbury Road',
                'Postcode1' => 'UB8 8JX',
                'Title_Surname' => 'Test Patient',
                'Sex' => '0'
            ])
            ->flatten()
            ->saveAs($folder.'CVI_example.pdf');


        $fpdf = new \FPDI();
     
        $pagecount = $fpdf->setSourceFile($folder.'CVI_example.pdf');
        for ($i = 1; $i <= 8; $i++) {
            $fpdf->importPage($i);
            $fpdf->AddPage();
            $fpdf->useTemplate($i);

            if($i == 1){
                $fpdf->Image($image, 28, 194, 52, 6);
            }
        }
      
        $fpdf->Output($folder.'CVI_example_img.pdf','F');
        var_dump($data);
        exit;
       
        
        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename="CVI_test.pdf"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($file));
        @readfile($file);
    }

    /**
     * @return array
     */
    public function whoIs()
    {
        $data = array(
            array('X', 'I am the patient'),
            array('', 'the patient’s representative and my name is (PLEASE PRINT):'),
        );

        return $data;
    }

    /**
     * @return array
     */
    public function copiesInConfidenceTo()
    {
        $data = array(
            array('X', 'Local council / Care trust'),
            array('', 'Patient'),
            array('', 'Patient’s GP'),
            array('', 'Hospital notes'),
            array('', 'Epidemiological analysis'),
        );

        return $data;
    }

    /**
     * @return array
     */
    public function iConsider()
    {
        $data = array(
            array('I consider (tick one)', 'X', 'That this person is sight impaired (partially sighted)'),
            array('', '', 'That this person is severly sight impaired (blind'),
        );

        return $data;
    }

    /**
     * @return array
     */
    public function yesNoQuestions()
    {
        $data = array(
            array('Does the patient live alone', 'Y'),
            array('Does the patient also have a hearing impairment', 'Y'),
            array('Does the patient have poor physical mobility?', 'Y'),

        );

        return $data;
    }

    /**
     * @return array
     */
    public function genTableDatas()
    {
        $data = array(
            array('Retina', 'age-related macular degeneration –subretinal neovascularisation', 'H35.3'),
            array('', 'age-related macular degeneration – atrophic /geographic macular atrophy', 'H35.3', '', ''),
            array('', 'diabetic retinopathy', 'E10.3 – E14.3 H36.0', '', ''),
            array('', 'hereditary retinal dystrophy', 'H35.5', '', ''),
            array('', 'retinal vascular occlusions', '', '', ''),
            array('', 'other retinal : please specify', '', '', ''),
        );

        return $data;
    }

    /**
     * @param $variable
     * @return array
     */
    public function generateTestTablesData($variable)
    {
        $data = array(
            'tables' => array(
                array(
                    'template_variable_name' => $variable,
                    'style' => 'border: 1px solid black;',
                    'table-type' => 'full_table',
                    'rows' => array(
                        array(
                            'row-type' => 'normal',
                            'cells' => array(
                                array('cell-type' => 'normal', 'colspan' => 2, 'rowspan' => 0, 'data' => 'A1:B1', 'style' => 'background-color:red;font-weight:bold;'),
                                array('cell-type' => 'covered', 'colspan' => 0, 'rowspan' => 0),
                                array('cell-type' => 'normal', 'colspan' => 0, 'rowspan' => 0, 'data' => 'C1'),
                            ),
                        ),
                        array(
                            'row-type' => 'normal',
                            'cells' => array(
                                array('cell-type' => 'normal', 'colspan' => 0, 'rowspan' => 2, 'data' => 'A2:A3'),
                                array('cell-type' => 'normal', 'colspan' => 0, 'rowspan' => 0, 'data' => 'B2'),
                                array('cell-type' => 'normal', 'colspan' => 0, 'rowspan' => 0, 'data' => 'C2'),
                            ),
                        ),
                        array(
                            'row-type' => 'lastrow',
                            'cells' => array(
                                array('cell-type' => 'covered'),
                                array('cell-type' => 'normal', 'colspan' => 0, 'rowspan' => 0, 'data' => 'B3'),
                                array('cell-type' => 'normal', 'colspan' => 0, 'rowspan' => 0, 'data' => 'C3'),
                            ),
                        ),
                    ),
                ),
            ),
            'images' => array(),
            'static_content' => array(
                array('template_variable_name' => 'my_name', 'data' => 'Kecskes Peter'),
            ),
        );

        return $data;
    }

    /**
     * @return resource
     */
    public function testGdImage()
    {
        $image = @imagecreate(110, 20) or die("Cannot Initialize new GD image stream");
        $bgColor = imagecolorallocate($image, 0, 0, 0);
        $textColor = imagecolorallocate($image, 255, 255, 255);
        imagestring($image, 2, 10, 5, "Test String", $textColor);

        return $image;
    }

    /**
     * @return string
     */
    public function pdfLink()
    {
        return '<a href="getPDF" target="_blank" > See PDF </a>';
    }
}
