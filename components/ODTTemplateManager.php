<?php

namespace OEModule\OphCoCvi\components;

use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use DOMDocument;
use DomXpath;

class ODTTemplateManager
{
    private $templateDir;
    private $sourceDir = './files/';
    private $unzippedDir;
    private $zippedDir;
    private $mediaDir;
    private $contentFilename = 'content.xml';
    private $generatedOdt = 'document.odt';

    private $outFile;
    private $outDir;
    private $inputFile;
    private $contentXml;
    private $xpath;
    
    private $uniqueId = null;
    private $odtFilename = '';
    private $newOdtFilename = '';
    //private $contentXml = '';
    private $right = 777;
    private $unzippedSablonFilename = '';
    
    //Using an exist style declaration in xml
    public $textStyleName = 'P7';
    
    public function __construct( $filename , $templateDir, $outputName )
    {
        $this->uniqueId = time();
        $this->templateDir = $templateDir;
        if(substr($outputName, -3) == 'odt'){
            $this->generatedOdt = $outputName;
        }
        $this->outDir = realpath(dirname(__FILE__).'/..').'/runtime/cache';
        // this will be the PDF, the name is the same as the generated ODT by default!
        $this->outFile = str_replace('odt','pdf', $this->generatedOdt);
        $this->odtFilename = $this->templateDir.'/'.$filename;
        $this->zippedDir = $this->templateDir.'/zipped/'.$this->uniqueId.'/';
        $this->unzippedDir = $this->templateDir.'/unzipped/'.$this->uniqueId.'/';
        $this->mediaDir = $this->unzippedDir.'media/';
        $this->newOdtFilename = $this->zippedDir.$this->generatedOdt;
        
        $this->unZip();
        $this->openContentXML();
       
    }
    
    private function openContentXML()
    {
        $this->contentXml = new DOMDocument();
        $this->contentXml-> load($this->openedSablonFilename);
        $this->contentXml-> formatOutput = true;
        $this->contentXml-> preserveWhiteSpace = false;  
        $this->xpath = new DomXpath($this->contentXml);
    }
    
    public function saveContentXML( )
    {
        $this->contentXml->save( $this->unzippedDir.$this->contentFilename );
    }
    
    public function generatePDF()
    {
        $path = $this->zipOdtFile();
        if($path !== FALSE){
            $shell = '/usr/bin/libreoffice --headless --convert-to pdf --outdir '.$this->outDir.'  '.$path;
            exec($shell, $output, $return); 
           
            if($return == 0){
                $odtPath = substr($path, 0, strrpos( $path, '/'));
                $this->deleteDir( $odtPath );
            }
        }
     
    }

    public function exchangeStringValues( $data )
    {
        $nodes = $this->xpath->query('//text()');
       
        foreach ($nodes as $node) {
            foreach ($data as $key => $value){
                if(strpos($node->nodeValue, '${'.$key.'}') !== false){

                    $valArr = explode("\n",$value);
                    if(array_key_exists(1, $valArr)){
                        foreach ($valArr as $c => $val){
                            $val = str_replace("\r","",$val);
                            $node->nodeValue='';
                            
                            if($c > 0){
                                $break = $this->contentXml->createElement('text:line-break');
                                $node->parentNode->appendChild($break);
                            }

                            $text = $this->contentXml->createElement('text:span', $val);
                            $node->parentNode->appendChild($text);
                            $text->setAttribute("text:style-name", $this->textStyleName);
                        }   
                    } else {
                        $node->nodeValue = str_replace('${'.$key.'}', $valArr[0], $node->nodeValue, $count);
                    }
                }    
            }
        }
        
        $this->contentXml->saveXML();
    }
    
    public function exchangeStringValueByStyleName( $styleName, $value )
    {

        $xpath = new DOMXpath($this->contentXml);
        
        $element    = $xpath->query('//*[@text:style-name="'.$styleName.'"]')->item(0);
        if( $element != null ){
            $firstChild = $element -> childNodes->item(0);
            $existingStyleName = $firstChild->getAttribute('text:style-name'); // get firstChild style
    
            while($element->hasChildNodes()) { // Delete all child (normalize)
                $x = $element -> childNodes->item(0);
                $element->removeChild($x);
            }
            
            $newTextNode = $this->contentXml->createElement('text:span');
            $newTextNode -> nodeValue = $value;
            $newTextNode -> setAttribute('text:style-name',$existingStyleName);
            $element->appendChild($newTextNode);
        }
    }     
    
    public function exchangeAllStringValuesByStyleName( $data ){
        
        foreach( $data as $key => $value ){
            $this->exchangeStringValueByStyleName( $key, $value );   
        }
    }

    protected function getImageHrefFromImageNode( $imageName ){
        $frame = $this->xpath->query('//draw:frame[@draw:name="'.$imageName.'"]')->item(0);
        if( $frame == null ){
            return false;   
        }
        $imageNode = $frame->childNodes->item(0);
        return $imageNode->getAttribute('xlink:href'); 
    }
    
    public function imgReplaceByName( $imageName , $newImageUrl )
    {
        copy( $newImageUrl , $this->unzippedDir.$this->getImageHrefFromImageNode( $imageName ));
    }
    
    public function changeImageFromGDObject( $imageName, $image )
    {
        if ($image !== false) {
            imagepng($image, $this->unzippedDir.$this->getImageHrefFromImageNode( $imageName ) );
            imagedestroy($image);
        } else {
            echo 'An error occurred.';
        }
    }

    private function getTableVariableNode( $nodeValue, $text )
    {
      
        foreach( $text as $oneNode ){
            if(substr($nodeValue, 0, 2) !== '${' && substr($nodeValue, -1) !== '}') {
                $nodeValue = '${' . $nodeValue . '}';
            }
            if( $nodeValue == $oneNode->nodeValue ){ return $oneNode; }
        } 
        return false;
    }

    private function replaceTableNode( $templateVariableName, $tableXml )
    {
        $tableNodeStr = "table:table";
        $table  = $tableXml->getElementsByTagName('table:table');
        $text = $this->contentXml->getElementsByTagName('p');
        $targetNode = $this->getTableVariableNode( $templateVariableName, $text );
        
        $node = $this->contentXml->importNode($table->item(0), true);
        if($targetNode != FALSE){
            $targetNode->parentNode->replaceChild($node, $targetNode); 
        }
        
    }

    public function exchangeGeneratedTablesWithTextNodes($data)
    {
        //generate tables, if needed..
        if(is_array($data['tables']) && !empty($data['tables'])){
            $tables = $this->generateXmlTables( $data['tables'] );
        }
        
        // replace tables with text-node
        foreach( $tables as $templateVariableName => $tableXml ){
            $this->replaceTableNode( $templateVariableName, $tableXml );
        }
    }
    
    public function fillTable( $prefix , $data, $headerRow = 0 )
    {
        //Or table:style-name
        foreach ($this->xpath->query('//office:text/table:table[@table:name="'.$prefix.'"]') as $table) {
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
                        if($data[$rowCount][$c] != "") {
                            $text = $this->contentXml->createElement('text:p', $data[$rowCount][$c]);
                            $cell->appendChild($text);
                            $text->setAttribute("text:style-name", $this->textStyleName);
                        }
                    }
                }
            } 
           
        }
        $this->contentXml->saveXML();
    }

    private function createNode( $xml, $tag, $attribs, $value = '' )
    {
        $element = $xml -> createElement( $tag , $value );
       
        foreach( $attribs as $key => $value ){
            $attr = $xml -> createAttribute($key);
            $attr -> value = $value;
            $element -> appendChild($attr);
        }
        return $element;
    }
    
    private function getTableColsCount( $firstRow )
    {
        $colCount=0;
        foreach($firstRow as $oneCell){
            if($oneCell['cell-type'] != 'covered'){
                $colCount += ($oneCell['colspan'] > 0 ) ? $oneCell['colspan'] : 1;
            }
        }
        return $colCount;
    }
    
    private function generateXmlTables( $tablesData )
    {
        $colsLabel = range('A', 'Z');
       
        foreach( $tablesData as $tableKey => $oneTable ){
            $tableXml = new DOMDocument('1.0', 'utf-8');
            $tableName = 'mytable'.$tableKey;
            $tableStyleName = 'mytable'.$tableKey;
            $colsCount = $this -> getTableColsCount( $oneTable['rows'][0]['cells'] ); // parameter is the first row.

            $table  = $this -> createNode( $tableXml, 'table:table', array( 'table:name'=>$tableName, 'table:style-name' => $tableStyleName ) );
            $tableHeader = $this -> createNode( $tableXml, 'table:table-column', array( 'table:style-name'=>"T1.A", 'table:number-columns-repeated' => $colsCount) );
            $table -> appendChild( $tableHeader );
            
            $rowDeep = 0;
            foreach( $oneTable['rows'] as $oneRow ) {
                $row  = $this -> createNode( $tableXml, 'table:table-row', array() );
                $colDeep = 0;
               
                foreach( $oneRow['cells'] as $cellKey => $oneCell ){
                    $colDeep++;
                    
                    $params = array();
                    if($oneCell['cell-type'] != 'covered'){
                        $rowspan = $oneCell['rowspan'];
                        $colspan = $oneCell['colspan'];
                        $cellValue = $oneCell['data'];
                    }
                    
                    $params[ 'table:style-name'] = $tableName.'.'.$colsLabel[$rowDeep].$colDeep;
                    $params[ 'office:value-type'] = "string";
                    if( $rowspan != '' ) $params['table:number-rows-spanned'   ] = $rowspan;
                    if( $colspan != '' ) $params['table:number-columns-spanned'] = $colspan;
                  
                    switch($oneCell['cell-type']){
                        case 'normal'    : 
                            $cell = $this -> createNode( $tableXml, 'table:table-cell', $params ); 
                        break;
                        case 'covered'   : 
                            $cell = $this -> createNode( $tableXml, 'table:covered-table-cell', array() ); 
                        break;
                    }
                    
                    $cellVal = $this -> createNode( $tableXml, 'text:p', array('text:style-name' => 'Table_20_Contents'), $cellValue );

                    $cell  -> appendChild( $cellVal );
                    $row   -> appendChild( $cell );
                }
                $colDeep = 0;
                $table -> appendChild( $row );
                $rowDeep++;
            }
            $tableXml  -> appendChild( $table );
            $tables[$oneTable['template_variable_name']] = $tableXml;
        }
       
        return $tables;
    }
    
    public function customSquare( $appendTo )
    {
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
    
    public function createSquareStyle()
    {
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
    
    private function unZip( $createZipNameDir=true, $overwrite=true )
    {
        $zip = new ZipArchive;
        $destDir = $this->unzippedDir;
        $srcFile = $this->odtFilename;
        
      
        if( $zip = zip_open( $srcFile ) ) {
            if( $zip ) {
                $splitter = ($createZipNameDir === true) ? "." : "/";
                if($destDir === false) $destDir = substr($srcFile, 0, strrpos($srcFile, $splitter))."/";

                $this -> createDirs($destDir);
                 
                while($zipEntry = zip_read($zip)){
                    
                    $posLastSlash = strrpos(zip_entry_name($zipEntry), "/");

                    if ($posLastSlash !== false) {
                        $this -> createDirs($destDir.substr(zip_entry_name($zipEntry), 0, $posLastSlash+1));
                    }

                    if (zip_entry_open($zip,$zipEntry,"r")) {
                        $fileName = $destDir.zip_entry_name($zipEntry);
                        if ($overwrite === true || ($overwrite === false && !is_file($fileName))) {
                            $fstream = zip_entry_read($zipEntry, zip_entry_filesize($zipEntry));
                            if(!is_dir($fileName)){
                                file_put_contents($fileName, $fstream );
                                //chmod($fileName, $this -> right );
                            }
                        }
                        zip_entry_close($zipEntry);
                    }       
                }
                zip_close($zip);
                $this->openedSablonFilename = $destDir.$this->contentFilename;
            }
        } else {
            $this->dropException( 'Failed unzip ODT. File: '.$this->templateDir.$this->odtFilename );
        }
    }
    
    private function zipOdtFile()
    {
        $inputFolder  = $this -> unzippedDir;
        $destPath = $this -> zippedDir;
        mkdir($destPath, 0777, true);
        $zip   = new ZipArchive();
       
        
        $zip  -> open( $this->newOdtFilename, ZipArchive::CREATE | ZipArchive::OVERWRITE);
      
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
        $this->deleteDir( $inputFolder );
        return $destPath.$this->generatedOdt;
    }
    
    private function createDirs($path)
    {
        if (!is_dir($path)){
            $directoryPath = "";
            $directories = explode("/",$path);
            array_pop($directories);

            foreach($directories as $directory) {
                $directoryPath .= $directory."/";
                if (!is_dir($directoryPath)) {
                    mkdir($directoryPath, 0777, true);
                    //chmod($directoryPath, $this -> right );
                }
            }
        }
    }
    
    private function deleteDir( $path )
    {
        if(is_dir($path)){
            $files = array_diff(scandir($path), array('.', '..'));
            foreach ($files as $file){
                $this->deleteDir(realpath($path) . '/' . $file);
            }
            return rmdir($path);
        } else if (is_file($path) === true){
            return unlink($path);
        }
    }

    public function getPDF()
    {
        header('Content-type: application/pdf');
        //header('Content-Disposition: download; filename="certificate.pdf"');
        //header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($this->outDir.'/'.$this->outFile));
        @readfile($this->outDir.'/'.$this->outFile);
    }
}
