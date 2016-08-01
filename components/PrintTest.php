<?php

namespace OEModule\OphCoCvi\components;

use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use DOMDocument;
use DomXpath;

class PrintTest{
    
    public $directory = '';
    public $inputFile = 'example_certificate_3.odt';
    public $xmlDoc;
    public $xpath;
    
    
    public function loadData(){
  
        $this->directory = realpath(__DIR__ . '/..').'/files';
        
        if($this->unzipFile( $this->directory.'/'.$this->inputFile,  $this->directory.'/xml') === TRUE){
            
            $this->xmlDoc = new DOMDocument();
            $this->xmlDoc->load( $this->directory.'/xml/content.xml');
            $this->xmlDoc->formatOutput = true;
            $this->xpath = new DomXpath($this->xmlDoc);
        
            $this->strReplace( $_POST );
            $this->imgReplace();
            $dataTable = $this->findTableInPdf("Retina" , $this->genTableDatas() );

            $this->xmlDoc->save( $this->directory.'/xml/content.xml');

            if($this->zipFolder($this->directory.'/xml', '/var/www/openeyes/protected/runtime/document.odt') === TRUE){
                exec('/usr/bin/libreoffice --headless --convert-to pdf --outdir /var/www/openeyes/protected/runtime/  /var/www/openeyes/protected/runtime/document.odt');
                return $this->pdfLink();   
            }
            
        } else {
            return 'Pdf generate error. Please try again.';
        }
    }

    public function pdfLink(){
        $result = '<a href="getPDF" target="_blank" > See PDF </a>';
        return $result;
    }
    
    public function strReplace( $data ){
        
        $nodes = $this->xpath->query('//text()');
        foreach ($nodes as $node) {
            foreach ($data as $key => $value){
                $node->nodeValue = str_replace('##'.$key.'##', $value, $node->nodeValue, $count);
                if($count > 0){
                    break;
                }
            }
        }

        $this->xmlDoc->saveXML();
    }
    
    public function imgReplace(){
        $nodes = $this->xmlDoc->getElementsByTagName("image");
        foreach ($nodes as $node) {
            $node->removeAttribute('xlink:href');
            $node->setAttribute("xlink:href", $this->directory."/image/signature3.png");
        }
        $this->xmlDoc->saveXML();
    }
    
    public function genTableDatas(){
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
    
    public function findTableInPdf( $disease , $data, $headerRow = 0 ){
        
        $tableName = $disease.'-Table';
        
        foreach ($this->xpath->query('//table:table[@table:style-name="'.$tableName.'"]') as $table) {
            
            foreach($table->childNodes as $r => $row) {
                if ($headerRow > 0){
                    if($headerRow >= $r){
                       continue;
                    } 
                    $rowCount = $r-$headerRow-1;
                } else{
                    $rowCount = $r-1;
                }
                
                //if ($row->hasChildNodes()) {
                    foreach($row->childNodes as $c => $cell){

                        if ((array_key_exists($rowCount, $data)) && (array_key_exists($c, $data[$rowCount]))) {
                            $cell->nodeValue = "";
                            $text = $this->xmlDoc->createElement('text:p', $data[$rowCount][$c]);
                            $cell->appendChild($text);
                        }
                    }
               // }
            } 
        }
        
        $this->xmlDoc->saveXML();
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
}