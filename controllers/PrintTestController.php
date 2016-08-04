<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OEModule\OphCoCvi\controllers;
use \OEModule\OphCoCvi\components\PrintTest;


class PrintTestController extends \BaseController
{
    public $directory = '';
    public $inputFile = 'example_certificate_3.odt';
    public $xmlDoc;
    public $xpath;
    public $printTestXml;
    public $xml;
    
    public function accessRules()
    {
        return array(
            array('allow',
                'actions'       => array('test', 'getPDF'),
                'roles'         => array('admin')
            ),
        );
    }
    
    public function actionTest()
    {
        
        $pdfLink = '';
        $this->printTestXml = new PrintTest();
        
        if(isset($_POST['test_print'])){
            
            $this->xml = $this->printTestXml->getXml();
            
            $this->printTestXml->strReplace( $_POST );
            $this->printTestXml->imgReplace( 'image1.png' , $this->printTestXml->directory.'/signature3.png');
            
            $this->printTestXml->genTable( 
                $this->printTestXml->xpath->query('//office:text')->item(0) , 
                "TestTable" , 
                2 , 
                2
            );
            
            $this->printTestXml->fillTable("TestTable" , $this->whoIs() );
            $this->printTestXml->fillTable("Radio1" , $this->whoIs() );
            $this->printTestXml->fillTable("Table716" , $this->yesNoQuestions() , 1 );
            $this->printTestXml->fillTable("Retina" , $this->genTableDatas() , 1 );

            $this->printTestXml->saveXML( $this->printTestXml->xmlDoc );
           
            $this->printTestXml->convertToPdf();
            $pdfLink = $this->pdfLink();
        }
       
        $this->render("test", array( 'pdfLink' => $pdfLink, 'imageSrc' => $this->getImage()) );
    }
    public function getImage()
    {
        $Data = file_get_contents($this->printTestXml->directory.'/signature3.png');
        return '<div style="width:30%;height:30%;position:relative;"/><img src="data:image/jpeg;base64,'.base64_encode($Data).'"/></div>';
    }
   
    public function actionGetPDF()
    {
        $file = '/var/www/openeyes/protected/runtime/document.pdf';
        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename="document.pdf"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($file));
        @readfile($file);
    }
    
    public function yesNoQuestions()
    {
        $data = array(
            array('Does the patient live alone', 'Y'),
            array('Does the patient also have a hearing impairment','Y'),
            array('Does the patient have poor physical mobility?','Y'),
           
        );
        return $data;
    }
    
    public function whoIs(){
        $data = array(
            array('X', 'I am the patient'),
            array('X','the patient’s representative and my name is (PLEASE PRINT):'),
        );
        return $data;
    }
    
    public function genTableDatas()
    {
        $data = array(
            array('Retina', 'age-related macular degeneration –subretinal neovascularisation','H35.3'),
            array('','age-related macular degeneration – atrophic /geographic macular atrophy','H35.3','', ''),
            array('','diabetic retinopathy','E10.3 – E14.3 H36.0','', ''),
            array('','hereditary retinal dystrophy','H35.5','', ''),
            array('','retinal vascular occlusions','','', ''),
            array('','other retinal : please specify','','', ''),
        );
        return $data;
    }
    
    
    public function pdfLink()
    {
        return '<a href="getPDF" target="_blank" > See PDF </a>';
    }
}
