<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

// FPDI module is not working properly with the Yii Autoload so we have to include the parser class manually!

require_once(str_replace("index.php","vendor/setasign/fpdi/pdf_parser.php", Yii::app()->getRequest()->getScriptFile()));

/**
 * A class for odt document modify and generate pdf
 */
class ODTTemplateManager
{
    /**
     * @var string
     */
    private $templateDir;
    
    /**
     * @var string
     */
    private $sourceDir = './files/';
    
    /**
     * @var string
     */
    private $unzippedDir;
    
    /**
     * @var string
     */
    private $zippedDir;
    
    /**
     * @var string
     */
    private $contentFilename = 'content.xml';
    
    /**
     * @var string
     */
    private $generatedOdt = 'document.odt';
    
    /**
     * @var string
     */
    private $outFile;
    
    /**
     * @var string
     */
    private $outDir;
    
    /**
     * @var string
     */
    private $inputFile;
    
    /**
     * @var string
     */
    private $contentXml;
    
    /**
     * @var string
     */
    private $xpath;
    
    /**
     * @var string
     */
    private $uniqueId = null;
    
    /**
     * @var string
     */
    private $odtFilename = '';
    
    /**
     * @var string
     */
    private $newOdtFilename = '';
    
    /*
     * @var string
     */
    private $fpName = '';
  
    /**
     * @param $filename 
     * @param $templateDir
     * @param $outputDir
     * @param $outputName
     */
    public function __construct( $filename , $templateDir, $outputDir , $outputName )
    {
        $this->uniqueId = time();
        $this->templateDir = $templateDir;
        if(substr($outputName, -3) == 'odt'){
            $this->generatedOdt = $outputName;
        }
        $this->openedSablonFilename = $filename;
        $this->outDir = realpath(dirname(__FILE__).'/../..').'/runtime/cache/cvi';
        // this will be the PDF, the name is the same as the generated ODT by default!
        $this->outFile = str_replace('odt','pdf', $this->generatedOdt);
        $this->odtFilename = $this->templateDir.'/'.$filename;
        
        $this->zippedDir = $outputDir.'/zipped/'.$this->uniqueId.'/';
        $this->unzippedDir = $outputDir.'/unzipped/'.$this->uniqueId.'/';
        $this->newOdtFilename = $this->zippedDir.$this->generatedOdt;
        
        $this->unZip();
        $this->openContentXML();
    }
    
    /*
     * Open xml file 
     */
    private function openContentXML()
    {
        $this->contentXml = new DOMDocument();
        $this->contentXml-> load($this->openedSablonFilename);
        $this->contentXml-> formatOutput = true;
        $this->contentXml-> preserveWhiteSpace = false;  
        $this->xpath = new DomXpath($this->contentXml);
    }
    
    /*
     * Save xml file content after edit
     */
    public function saveContentXML( )
    {
        $this->contentXml->save( $this->unzippedDir.$this->contentFilename );
    }
    
    /*
     * Change ${} variables in odt xml to the param value 
     * @param $data
     */
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
                        }   
                    } else {
                        $node->nodeValue = str_replace('${'.$key.'}', $valArr[0], $node->nodeValue, $count);
                    }
                }    
            }
        }
    }
  
    /*
     * Change variables in odt xml by style-name property
     * @param $styleName
     * @param $value
     */
    public function exchangeStringValueByStyleName( $styleName, $value )
    {

        $xpath = new DOMXpath($this->contentXml);
        
        $element    = $xpath->query('//*[@text:style-name="'.$styleName.'"]')->item(0);
        if( $element != null ){
            $firstChild = $element -> childNodes->item(0);
            if($firstChild->hasAttribute('text:style-name')){
                $existingStyleName = $firstChild->getAttribute('text:style-name'); // get firstChild style
            }
            
            while($element->hasChildNodes()) { // Delete all child (normalize)
                $x = $element -> childNodes->item(0);
                $element->removeChild($x);
            }
            
            $valueArray = explode( "<br/>" , $value);
            if(array_key_exists(1, $valueArray)){
                
                foreach ($valueArray as $c => $val){
                    $val = str_replace("\r","",$val);
                    if($c > 0){
                        $break = $this->contentXml->createElement('text:line-break');
                        $element->appendChild($break);
                    }
                    
                    $newTextNode = $this->contentXml->createElement('text:span');
                    $newTextNode -> nodeValue = $val;
                    $newTextNode -> setAttribute('text:style-name',$existingStyleName);
                    $element->appendChild($newTextNode);
                }
            } else {
                $newTextNode = $this->contentXml->createElement('text:span');
                $newTextNode -> nodeValue = $value;
                $newTextNode -> setAttribute('text:style-name',$existingStyleName);
                $element->appendChild($newTextNode);
            }
            
        }
    }     
    
    public function exchangeAllStringValuesByStyleName( $texts )
    {
        foreach( $texts as $text ){
            // we replace the key with empty string to remove the sample content from the template
            if(!isset($text['data']))
            {
                $text['data'] = "";
            }
            $this->exchangeStringValueByStyleName( $text['name'], $text['data'] );   
        }
    }

    /*
     * Get image path from the xml document
     * @param $imageName
     * @return string
     */
    protected function getImageHrefFromImageNode( $imageName )
    {
        $frame = $this->xpath->query('//draw:frame[@draw:name="'.$imageName.'"]')->item(0);
        if( $frame == null ){
            return false;   
        }
        $imageNode = $frame->childNodes->item(0);
        return $imageNode->getAttribute('xlink:href'); 
    }
    
    /*
     * Change an existing image in document by new image url
     * @param $imageName
     * @param $newImageUrl
     */
    public function imgReplaceByName( $imageName , $newImageUrl )
    {
        copy( $newImageUrl , $this->unzippedDir.$this->getImageHrefFromImageNode( $imageName ));
    }
    
    /*
     * Change an existing image in document by GD object
     * @param $imageName
     * @param $image
     */
    public function changeImageFromGDObject( $imageName, $image )
    {
        if ($image !== false) {
            imagepng($image, $this->unzippedDir.$this->getImageHrefFromImageNode( $imageName ) );
            imagedestroy($image);
        }
    }

    /*
     * Find and change ${} varibale in table
     * @param $nodeValue
     * @param $text
     */
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

    public function exchangeGeneratedTablesWithTextNodes( $data )
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
    
    /*
     * Fill table with array data
     * If table has header, you can set the number of header rows
     * @param $name
     * @param $data
     * @param $headerRow
     */
    public function fillTable( $name , $data, $headerRow = 0 )
    {
       
        foreach ($this->xpath->query('//office:text/table:table[@table:name="'.$name.'"]') as $table) {
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
    }
    
    /*
     * Find table in xml and fill it with data values
     * @param $name
     * @param $data
     */
    public function fillTableByName( $name , $data,  $type='name' )
    {
        switch( $type ){
            case 'style' : $type = 'style-name'; break;
            case 'name'  : $type = 'name'; break;
            default      : $type = 'name'; break;
        }
        $table = $this->xpath->query('//*[@table:'.$type.'="'.$name.'"]')->item(0);
        
        $rowCount = 0;
        $colCount = 0;
        if( $table != null ){
            foreach($table->childNodes as $r => $tableNode) {
                if( $tableNode->nodeName == "table:table-row" ){
                    $cols = $tableNode->childNodes;
                    foreach($cols as $oneCol){
                        if($oneCol->nodeName != 'table:covered-table-cell'){
                            $textNode = $oneCol->childNodes->item(0);
                            if(isset($data[$rowCount][$colCount])){
                                if( $data[$rowCount][$colCount] != '' ){      
                                    $textNode->nodeValue = htmlspecialchars($data[$rowCount][$colCount]);    
                                }
                            }
                        }
                        $colCount++;
                    }
                    $rowCount++;
                    $colCount=0;
                }
            } 
          
        }
    }
    
    /*
     * Create new element,set attributes and append to xml node
     * @param $xml
     * @param $tag
     * @param $attribs
     * @param $value
     * @return string
     */
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
    
    /*
     * Count table columns
     * @param $firstRow
     * @return int
     */
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
    
    /*
     * Generate xml format table
     * @param $dataTables
     * @return string
     */
    private function generateXmlTables( $dataTables )
    {
        $colsLabel = range('A', 'Z');
       
        foreach( $dataTables as $tableKey => $oneTable ){
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
    
    /*
     * Unzip odt file into the temporary directory
     */
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
    
    /*
     * Zip xml files 
     * @return string
     */
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
    
    /*
     * Create directoy by path
     * @param $path
     */
    private function createDirs( $path )
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
    
    /*
     * Delete temporary files by path
     * @param $path
     * @return bool
     */
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
    
    /*
     * Generate pdf file from odt and delete temporary folder
     */
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
    
    /*
     * Get generated pdf
     */
    public function getPDF()
    {
        header('Content-type: application/pdf');
        header('Content-Length: ' . filesize($this->outDir.'/'.$this->outFile));
        @readfile($this->outDir.'/'.$this->outFile);
    }
    
    
    /*
     * Convert the generated pdf N page
     * @param $pageNumber 
     */
    public function generatePDFPageN( $pageNumber = 1 )
    {
        ob_start();
        
        $pdf = new FPDI();
        $pageCount = $pdf->setSourceFile($this->outDir.'/'.$this->outFile);
        $tplIdx = $pdf->importPage( $pageNumber , '/MediaBox');

        $pdf->addPage();
        $pdf->useTemplate($tplIdx);

        $pdf->Output();
        
        ob_end_flush(); 
    }
}
