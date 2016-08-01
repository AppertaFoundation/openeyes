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
    
    public function accessRules(){
        return array(
            array('allow',
                'actions'       => array('test', 'getPDF'),
                'roles'         => array('admin')
            ),
        );
    }
    
    public function actionTest(){
        $pdfObj = '';
        $PrintTest = new PrintTest();
        
        if(isset($_POST['test_print'])){
            $pdfObj = $PrintTest->loadData();
        }
        
        $this->render("test", array('pdfObj' => $pdfObj ));
    }

    public function actionGetPDF(){
      
        $file='/var/www/openeyes/protected/runtime/document.pdf';
        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename="document.pdf"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($file));
        @readfile($file);
    }
}
