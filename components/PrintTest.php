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
    
    public function __construct(){
        $this->directory = realpath(__DIR__ . '/..').'/files';
    }
    
    public function getXml(){

        if($this->unzipFile( $this->directory.'/'.$this->inputFile,  $this->directory.'/xml') === TRUE){
            
            $this->xmlDoc = new DOMDocument();
            $this->xmlDoc->load( $this->directory.'/xml/content.xml');
            $this->xmlDoc->formatOutput = true;
            $this->xmlDoc->preserveWhiteSpace = false;
            $this->xpath = new DomXpath($this->xmlDoc);
           
            return $this;
        } else {
            return FALSE;
        }
    }
    
    public function saveXML( $xml ){
        $xml->save( $this->directory.'/xml/content.xml');
        if($this->zipFolder($this->directory.'/xml', '/var/www/openeyes/protected/runtime/document.odt') === TRUE){
            exec('/usr/bin/libreoffice --headless --convert-to pdf --outdir /var/www/openeyes/protected/runtime/  /var/www/openeyes/protected/runtime/document.odt');    
        }
    }
    
    public function strReplace( $data ){
        $nodes = $this->xpath->query('//text()');
       
        foreach ($nodes as $node) {
            
            foreach ($data as $key => $value){
                if(($node->nodeValue == '##'.$key.'##') || (strpos($node->nodeValue, '##'.$key.'##') !== false)){

                    $valArr = explode("\n",$value);
                    if(array_key_exists(1, $valArr)){
                        foreach ($valArr as $c => $val){
                            $val = str_replace("\r","",$val);
                            $node->nodeValue='';
                            
                            if($c > 0){
                                $break = $this->xmlDoc->createElement('text:line-break');
                                $node->parentNode->appendChild($break);
                            }

                            $text = $this->xmlDoc->createElement('text:span', $val);
                            $node->parentNode->appendChild($text);
                        }   
                    } else {
                        $node->nodeValue = str_replace('##'.$key.'##', $valArr[0], $node->nodeValue, $count);
                        
                    }
                
                }
                /* 
                $val = str_replace("\n","<text:line-break/>",trim($value));
                $node->nodeValue = str_replace('##'.$key.'##', $val, $node->nodeValue, $count);

                */
            }
        }
        
        $this->xmlDoc->saveXML();
    }
    
    public function imgReplace( $oldImage , $newImageUrl ){
        
        $newImage = substr($newImageUrl, strrpos($newImageUrl, '/') + 1);
        copy( $this->directory.'/'.$newImage , $newImageUrl);
        $nodes = $this->xmlDoc->getElementsByTagName("image");
        foreach ($nodes as $node) {
            if($node->getAttribute('xlink:href') == 'media/'.$oldImage){
                $node->removeAttribute('xlink:href');
                $node->setAttribute("xlink:href", $newImageUrl);
                break;
            }
        }
     
        $this->xmlDoc->saveXML();
    
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
                
                foreach($row->childNodes as $c => $cell){

                    if ((array_key_exists($rowCount, $data)) && (array_key_exists($c, $data[$rowCount]))) {
                        $cell->nodeValue = "";
                        $text = $this->xmlDoc->createElement('text:p', $data[$rowCount][$c]);
                        $cell->appendChild($text);
                    }
                }
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