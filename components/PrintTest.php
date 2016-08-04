<?php

namespace OEModule\OphCoCvi\components;

use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use DOMDocument;
use DomXpath;

class PrintTest
{
    
    public $directory;
    public $inputFile = 'example_certificate_3.odt';
    public $xmlDoc;
    public $xpath;
    
    //Using an exist style declaration in xml
    public $textStyleName = 'P400';
    
    public function __construct()
    {
        $this->directory = realpath(__DIR__ . '/..').'/files';
    }
    
    public function getXml()
    {

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
    
    public function saveXML( $xml )
    {
        $xml->save( $this->directory.'/xml/content.xml');
    }
    
    public function convertToPdf()
    {
        if($this->zipFolder($this->directory.'/xml', '/var/www/openeyes/protected/runtime/document.odt') === TRUE){
            exec('/usr/bin/libreoffice --headless --convert-to pdf --outdir /var/www/openeyes/protected/runtime/  /var/www/openeyes/protected/runtime/document.odt');    
        }
    }
    
    public function strReplace( $data )
    {
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
                            $text->setAttribute("text:style-name", $this->textStyleName);
                        }   
                    } else {
                        $node->nodeValue = str_replace('##'.$key.'##', $valArr[0], $node->nodeValue, $count);
                    }
                }    
            }
        }
        
        $this->xmlDoc->saveXML();
    }
    
    public function imgReplace( $oldImage , $newImageUrl )
    {
        $mediaFolder = $this->directory.'/xml/media/';
        $newImage = substr($newImageUrl, strrpos($newImageUrl, '/') + 1);
        
        // If the destination (2nd parameter ) file already exists, it will be overwritten.
        copy( $newImageUrl , $mediaFolder.'/'.$oldImage);
    }
    
    public function fillTable( $prefix , $data, $headerRow = 0 )
    {
        
        foreach ($this->xpath->query('//office:text/table:table[@table:style-name="'.$prefix.'"]') as $table) {
          
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
                        //$cell->nodeValue = "";
                        $text = $this->xmlDoc->createElement('text:p', $data[$rowCount][$c]);
                        $cell->appendChild($text);
                        $text->setAttribute("text:style-name", $this->textStyleName);
                    }
                }
            } 
           
        }
        $this->xmlDoc->saveXML();
    }
    
    public function genTable( $appendTo , $prefix , $rows , $cols ){
       
        $table = $this->xmlDoc->createElementNS('urn:oasis:names:tc:opendocument:xmlns:table:1.0','table:table');
        $newTable = $appendTo->appendChild( $table );
        $newTable->setAttribute("table:style-name", $prefix );
        
        $columns = $this->xmlDoc->createElement('table:table-columns');
        $table->appendChild( $columns );
        
        for($i = 1; $i <= $cols; $i++ ){
            $column = $this->xmlDoc->createElement('table:table-column');
            $columns->appendChild( $column );
        }
        
        for($j = 1; $j <= $rows; $j++ ){
           $row = $this->xmlDoc->createElement('table:table-row');
           $table->appendChild( $row );
           
           for($i = 1; $i <= $cols; $i++){
               $cell = $this->xmlDoc->createElement('table:table-cell');
               $row->appendChild($cell);
           }
        }
    }
    
    public function customSquare( $appendTo ){
        $svgTitle = $this->xmlDoc->createElement('svg:title');
        $svgDesc = $this->xmlDoc->createElement('svg:desc');
        $square = $this->xmlDoc->createElement('draw:custom-shape', 'sas');
        
        $svgDesc->appendChild( $square );
        $svgTitle->appendChild( $square );
        $newSquare = $appendTo->appendChild( $square );
        
        $newSquare->setAttribute("draw:style-name", "a9");
        $newSquare->setAttribute("svg:x", "0.2in");
        $newSquare->setAttribute("svg:y", "0.4in");
        $newSquare->setAttribute("svg:width", "0.40000in");
        $newSquare->setAttribute("svg:height", "0.40000in");
    }
    
    public function createSquareStyle(){
        $nodes = $this->xpath->query('//office:automatic-styles');
        
        foreach($nodes as $node){
            foreach($node->childNodes as $child){
                $style = $this->xmlDoc->createElement('style:style');
                $graph = $this->xmlDoc->createElement('style:graphic-properties');

                $newGraph = $graph->appendChild( $style );
                $newGraph->setAttribute("draw:fill", "solid");
                $newGraph->setAttribute("draw:fill-color", "#000000");

                $newSquare = $style->appendChild( $child );
                $newSquare->setAttribute("style:family", "graphic");
                $newSquare->setAttribute("style:name", "a1000");
            }
        }
    }
    
    public function unzipFile( $zipInputFile, $outputFolder   )
    {
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
    
    public function zipFolder($inputFolder, $zipOutputFile) 
    {
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