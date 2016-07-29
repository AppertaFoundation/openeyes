<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OEModule\OphCoCvi\controllers;

use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use DOMDocument;
use DomXpath;

/**
 * Description of PrintTestController
 *
 * @author Irvine
 */
class PrintTestController extends \BaseController
{
    
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
        
        if(isset($_POST['test_print'])){
            
            $directory = realpath(__DIR__ . '/..').'/files';
            if($this->unzipFile($directory.'/example_certificate_3.odt', $directory.'/xml') === TRUE){
                $source = file_get_contents($directory.'/xml/content.xml');
                
                //Fill the pdf with post datas
                foreach($_POST as $key => $val){
                    $data = explode('_', $key, 2);
                   
                    $inputType = $data[0];
                    $field = $data[1];
                    
                    switch($inputType){
                        case 'radio':
                            $field .= '_'.$val;
                            $value = 'X';
                        break;
                        case 'textarea':
                            $value = str_replace("\n","<text:line-break/>",trim($val));
                        break;
                        default:
                            $value = $val;
                    }
                    $source = str_replace('##'.$field.'##', $value, $source); 
                }

                //Remove all tokens which are empty
                $source = preg_replace('/##(.*?)##/i', "", $source);
                
                file_put_contents($directory.'/xml/content.xml', $source);
                
                $dataTable = $this->findTableInPdf("Retina" , $this->genTableDatas() );
                $dataTable->save($directory.'/xml/content.xml');
                
                if($this->zipFolder($directory.'/xml', '/var/www/openeyes/protected/runtime/document.odt') === TRUE){
                    exec('/usr/bin/libreoffice --headless --convert-to pdf --outdir /var/www/openeyes/protected/runtime/  /var/www/openeyes/protected/runtime/document.odt');
                    $pdfObj = $this->pdfLink();   
                }
                
            } else {
                $pdfObj = 'Pdf generate error. Please try again.';
            }
        }
        
        $this->render("test", array('pdfObj' => $pdfObj ));
    }
    
    public function unzipFile( $zipInputFile, $outputFolder   ){
        $zip = new ZipArchive();
     
        $res = $zip->open($zipInputFile);
        if ($res === TRUE) {
            $zip->extractTo($outputFolder);
            $zip->close();
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function zipFolder($inputFolder, $zipOutputFile) {
        if (!extension_loaded('zip') || !file_exists($inputFolder)) {
            return FALSE;
        }

        $zip = new ZipArchive();
        if (!$zip->open($zipOutputFile, ZIPARCHIVE::CREATE)) {
            return FALSE;
        }
        
        $inputFolder = str_replace('\\', DIRECTORY_SEPARATOR, realpath($inputFolder));
       
        if (is_dir($inputFolder) === TRUE) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($inputFolder), RecursiveIteratorIterator::SELF_FIRST);

            foreach ($files as $file) {
                $file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
                if (in_array(substr($file, strrpos($file, '/')+1), array('.', '..'))) {
                    continue;
                }

                $file = realpath($file);

                if (is_dir($file) === TRUE) {
                    $dirName = str_replace($inputFolder.DIRECTORY_SEPARATOR, '', $file.DIRECTORY_SEPARATOR);
                    $zip->addEmptyDir($dirName);
                }
                else if (is_file($file) === TRUE) {
                    $fileName = str_replace($inputFolder.DIRECTORY_SEPARATOR, '', $file);
                    $zip->addFromString($fileName, file_get_contents($file));
                }
            }
        } else if (is_file($inputFolder) === TRUE) {
            $zip->addFromString(basename($inputFolder), file_get_contents($inputFolder));
        }
        $zip->close();
        return TRUE;
    }
    
    public function actionGetPDF(){
      
        $file='/var/www/openeyes/protected/runtime/document.pdf';
        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename="document.pdf"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($file));
        @readfile($file);
    }
    

    public function pdfLink(){
        $result = '<a href="getPDF" target="_blank" > See PDF </a>';
        return $result;
    }
    
    public function genTableDatas(){
        $data = array(
            'diseaseName'   => 'Retina',
            'diseaseDesc'   => array(
                'age-related macular degeneration –subretinal neovascularisation',
                'age-related macular degeneration – atrophic /geographic macular atrophy',
                'diabetic retinopathy',
                'hereditary retinal dystrophy',
                'retinal vascular occlusions',
                'other retinal : please specify'
            ),
            'diseaseVal'    => array(
                'H35.3',
                'H35.3',
                'E10.3 – E14.3 H36.0',
                'H35.5',
                '',
                ''
            ),
            'rightEye'      => array(
                '1',
                '2',
                '3',
                '4',
                '5',
                '',
            ),
            'leftEye'       => array(
                '1',
                '2',
                '3',
                '4',
                '5',
                '',
            ),
        );
       
        return $data;
    }
    public function findTableInPdf( $disease , $data, $headerRow = 0 ){
        $directory = realpath(__DIR__ . '/..').'/files';
        $tableName = $disease.'-Table';
      
        $xmlDoc = new DOMDocument();
        $xmlDoc->load($directory.'/xml/content.xml');
        $xmlDoc->formatOutput = true;
        $xpath = new DomXpath($xmlDoc);
        
        foreach ($xpath->query('//table:table[@table:style-name="'.$tableName.'"]') as $table) {
            foreach($table->childNodes as $r => $row) {
 
                if($row->textContent == ''){
                    continue;
                }
                if($headerRow >= $r){
                    continue;
                }
                
                foreach($row->childNodes as $c => $cell){
                    
                    if($cell->tagName == 'table:covered-table-cell'){
                        continue;
                    } 
                   
                    switch($c){
                        case 0:
                            $cell->nodeValue = "";
                            $text = $xmlDoc->createElement('text:p', $data['diseaseName']);
                            $cell->appendChild($text);
                        break;
                        case 1:
                            $cell->nodeValue = "";
                            $text = $xmlDoc->createElement('text:p', $data['diseaseDesc'][$r-1]);
                            $cell->appendChild($text);
                        break;
                        case 2:
                            $cell->nodeValue = "";
                            $text = $xmlDoc->createElement('text:p', $data['diseaseVal'][$r-1]);
                            $cell->appendChild($text);
                        break;
                        case 3:
                            $cell->nodeValue = "";
                            $text = $xmlDoc->createElement('text:p', $data['rightEye'][$r-1]);
                            $cell->appendChild($text);
                        break;
                        case 4:
                            $cell->nodeValue = "";
                            $text = $xmlDoc->createElement('text:p', $data['leftEye'][$r-1]);
                            $cell->appendChild($text);
                        break;
                    }
                }
            } 
        }
        
        $content = $xmlDoc->saveXML();
        return $xmlDoc;
    }
}
