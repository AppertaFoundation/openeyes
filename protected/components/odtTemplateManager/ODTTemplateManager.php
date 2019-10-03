<?php
/**
 * OpenEyes
 *
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

// FPDI module is not working properly with the Yii Autoload so we have to include the parser class manually!

require_once str_replace('index.php', 'vendor/setasign/fpdi/pdf_parser.php', Yii::app()->getRequest()->getScriptFile());

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
     * @var DOMDocument
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

    /**
     * @param $filename
     * @param $templateDir
     * @param $outputDir
     * @param $outputName
     */
    public function __construct($filename, $templateDir, $outputDir, $outputName)
    {
        $this->uniqueId = time();
        $this->templateDir = $templateDir;
        if (substr($outputName, -3) === 'odt') {
            $this->generatedOdt = $outputName;
        }
        $this->openedSablonFilename = $filename;
        $this->outDir = realpath(dirname(__FILE__) . '/../..') . '/runtime/cache/cvi';
        // this will be the PDF, the name is the same as the generated ODT by default!
        $this->outFile = str_replace('odt', 'pdf', $this->generatedOdt);
        $this->odtFilename = $this->templateDir . '/' . $filename;

        $this->zippedDir = $outputDir . '/zipped/' . $this->uniqueId . '/';
        $this->unzippedDir = $outputDir . '/unzipped/' . $this->uniqueId . '/';
        $this->newOdtFilename = $this->zippedDir . $this->generatedOdt;

        $this->unZip();
        $this->openContentXML();
    }

    /**
     * Open xml file 
     */
    private function openContentXML()
    {
        $this->contentXml = new DOMDocument();
        $this->contentXml->load($this->openedSablonFilename);
        $this->contentXml->formatOutput = true;
        $this->contentXml->preserveWhiteSpace = false;
        $this->xpath = new DomXpath($this->contentXml);
    }

    /**
     * Save xml file content after edit
     */
    public function saveContentXML()
    {
        $this->contentXml->save($this->unzippedDir . $this->contentFilename);
    }

    /**
     * @param      $node
     * @param      $string
     * @param null $existingStyleName
     */
    private function createSingleOrMultilineTextNode($node, $string, $existingStyleName = null)
    {
        $stringArr = explode('<br/>', $string);

        if (count($stringArr) == 1) {
            $stringArr = explode('<br/>', $string);
        }

        if (count($stringArr) == 1) {
            $stringArr = explode("\\n", $string);
        }

        if (count($stringArr) > 1) { // Is multiline
            foreach ($stringArr as $inc => $oneLine) {
                if ($inc > 0) {
                    $break = $this->contentXml->createElement('text:line-break');
                    $node->appendChild($break);
                }
                $newTextNode = $this->contentXml->createElement('text:span');
                $newTextNode->nodeValue = $oneLine;
                $newTextNode->setAttribute('text:style-name', $existingStyleName);
                $node->appendChild($newTextNode);
            }
        } else { // is single line
            $newTextNode = $this->contentXml->createElement('text:span');
            $newTextNode->nodeValue = $string;
            $newTextNode->setAttribute('text:style-name', $existingStyleName);
            $node->appendChild($newTextNode);
        }
    }

    /**
     * Change ${} variables in odt xml to the param value 
     * @param $data
     */
    public function exchangeStringValues($data)
    {
        /*
        $nodes = $this->xpath->query('//text()');
       
        foreach ($nodes as $node) {
            foreach ($data as $key => $value){
                if (strpos($node->nodeValue, '${'.$key.'}') !== false){
                	$this->createSingleOrMultilineTextNode($value);
                }    
            }
        }
        */
    }

    /**
     * Change variables in odt xml by style-name property
     * @param $styleName
     * @param $value
     */
    public function exchangeStringValueByStyleName($styleName, $value)
    {
        $xpath = new DOMXpath($this->contentXml);
        $existingStyleName = '';

        $elements = $xpath->query('//*[@text:style-name="' . $styleName . '"]');

        if ($elements != null) {
            foreach ($elements as $oneElement) {
                if ($oneElement->hasAttribute('text:style-name')) {
                    $existingStyleName = $oneElement->getAttribute('text:style-name'); // get style
                }

                while ($oneElement->hasChildNodes()) { // Delete all child (normalize)
                    $x = $oneElement->childNodes->item(0);
                    $oneElement->removeChild($x);
                }

                $this->createSingleOrMultilineTextNode($oneElement, $value, $existingStyleName);
            }
        }
    }

    /**
     * @param $texts
     */
    public function exchangeAllStringValuesByStyleName($texts)
    {
        foreach ($texts as $text) {
            // we replace the key with empty string to remove the sample content from the template
            //file_put_contents('kecso.xml',print_r($text,true),FILE_APPEND);

            if (!isset($text['data'])) {
                $text['data'] = '';
            }
            $this->exchangeStringValueByStyleName($text['name'], $text['data']);
        }
    }

    /**
     * Get image path from the xml document
     * @param $imageName
     * @return string
     */
    protected function getImageHrefFromImageNode($imageName)
    {
        $frame = $this->xpath->query('//draw:frame[@draw:name="' . $imageName . '"]')->item(0);
        if ($frame == null) {
            return false;
        }
        $imageNode = $frame->childNodes->item(0);

        return $imageNode->getAttribute('xlink:href');
    }

    /**
     * Change an existing image in document by new image url
     * @param $imageName
     * @param $newImageUrl
     */
    public function imgReplaceByName($imageName, $newImageUrl)
    {
        copy($newImageUrl, $this->unzippedDir . $this->getImageHrefFromImageNode($imageName));
    }

    /**
     * Change an existing image in document by GD object
     * @param $imageName
     * @param $image
     */
    public function changeImageFromGDObject($imageName, $image)
    {
        if ($image !== false) {
            if ($this->getImageHrefFromImageNode($imageName)) {
                imagepng($image, $this->unzippedDir . $this->getImageHrefFromImageNode($imageName));
            }
            imagedestroy($image);
        }
    }

    /**
     * Find and change ${} varibale in table
     *
     * @param $nodeValue
     * @param $text
     *
     * @return bool
     */
    private function getTableVariableNode($nodeValue, $text)
    {
        foreach ($text as $oneNode) {
            if (substr($nodeValue, 0, 2) !== '${' && substr($nodeValue, -1) !== '}') {
                $nodeValue = '${' . $nodeValue . '}';
            }
            if ($nodeValue == $oneNode->nodeValue) {
                return $oneNode;
            }
        }

        return false;
    }

    /**
     * @param $templateVariableName
     * @param $tableXml
     */
    private function replaceTableNode($templateVariableName, $tableXml)
    {
        $table = $tableXml->getElementsByTagName('table:table');
        $text = $this->contentXml->getElementsByTagName('p');
        $targetNode = $this->getTableVariableNode($templateVariableName, $text);

        $node = $this->contentXml->importNode($table->item(0), true);
        if ($targetNode !== false) {
            $targetNode->parentNode->replaceChild($node, $targetNode);
        }
    }

    /**
     * @param $data
     */
    public function exchangeGeneratedTablesWithTextNodes($data)
    {
        //generate tables, if needed..
        if (is_array($data['tables']) && !empty($data['tables'])) {
            $tables = $this->generateXmlTables($data['tables']);
        }

        // replace tables with text-node
        foreach ($tables as $templateVariableName => $tableXml) {
            $this->replaceTableNode($templateVariableName, $tableXml);
        }
    }

    /**
     * Find table in xml and fill it with data values
     * @param $name
     * @param $data
     */
    public function fillTableByName($name, $data, $type = 'name')
    {
        switch ($type) {
            case 'style' :
                $type = 'style-name';
                break;
            case 'name'  :
                $type = 'name';
                break;
            default      :
                $type = 'name';
                break;
        }
        $table = $this->xpath->query('//*[@table:' . $type . '="' . $name . '"]')->item(0);

        $rowCount = 0;
        $colCount = 0;
        if ($table != null) {
            foreach ($table->childNodes as $tableNode) {
                if ($tableNode->nodeName === 'table:table-row') {
                    $cols = $tableNode->childNodes;
                    foreach ($cols as $oneCol) {
                        if ($oneCol->nodeName !== 'table:covered-table-cell') {
                            if (isset($data[$rowCount][$colCount])) {
                                if ($data[$rowCount][$colCount] != '') {
                                    $textNodes = $oneCol->childNodes;
                                    $firstTextNode = $textNodes->item(0);
                                    foreach ($textNodes as $oneTextNode) {
                                        $oneTextNode->nodeValue = '';
                                        //$oneCol->removeChild($oneTextNode);
                                    }
                                    $this->createSingleOrMultilineTextNode($firstTextNode, htmlspecialchars($data[$rowCount][$colCount]));

                                }
                            }
                        }
                        $colCount++;
                    }
                    $rowCount++;
                    $colCount = 0;
                }
            }

        }
    }

    /**
     * Create new element,set attributes and append to xml node
     *
     * @param $xml
     * @param $tag
     * @param $attribs
     * @param $value
     *
     * @return string
     */
    private function createNode($xml, $tag, $attribs, $value = '')
    {
        $element = $xml->createElement($tag, $value);

        foreach ($attribs as $key => $value) {
            $attr = $xml->createAttribute($key);
            $attr->value = $value;
            $element->appendChild($attr);
        }

        return $element;
    }

    /**
     * Count table columns
     * @param $firstRow
     * @return int
     */
    private function getTableColsCount($firstRow)
    {
        $colCount = 0;
        foreach ($firstRow as $oneCell) {
            if ($oneCell['cell-type'] !== 'covered') {
                if (isset($oneCell['colspan'])) {
                    $colCount += ($oneCell['colspan'] > 0) ? $oneCell['colspan'] : 1;
                } else {
                    $colCount++;
                }
            }
        }

        return $colCount;
    }

    /**
     * Add a new style to the office:automatic-styles node
     *
     * @param $style
     */
    private function addStyleToHeader($style)
    {
        $xpath = new DOMXpath($this->contentXml);

        $styleElement = $xpath->query('//office:automatic-styles')->item(0); //:automatic-styles :automatic-styles
        if ($styleElement != null) {
            $newElement = $this->createNode($this->contentXml, 'style:style', $style);
            $styleElement->appendChild($newElement);
        }
    }

    /**
     * Generate xml format table
     *
     * @param $dataTables
     *
     * @return mixed
     */
    protected function generateXmlTables($dataTables)
    {
        $colsLabel = range('A', 'Z');

        foreach ($dataTables as $tableKey => $oneTable) {
            //print '<pre>';print_r($oneTable);die;
            //<style:style style:name="T�bl�zat1" style:family="table">
            $tableXml = new DOMDocument('1.0', 'utf-8');
            $tableName = 'mytable' . $tableKey . uniqid();
            $tableStyleName = $tableName;

            $style = array(
                'style:name' => $tableName,
            );

            $this->addStyleToHeader($style);

            $colsCount = $this->getTableColsCount($oneTable['rows'][0]['cells']); // parameter is the first row.

            $table = $this->createNode($tableXml, 'table:table', array('table:name' => $tableName, 'table:style-name' => $tableStyleName));
            $tableHeader = $this->createNode($tableXml, 'table:table-column', array('table:style-name' => 'T1.A', 'table:number-columns-repeated' => $colsCount));
            $table->appendChild($tableHeader);

            $rowDeep = 0;
            foreach ($oneTable['rows'] as $oneRow) {
                $row = $this->createNode($tableXml, 'table:table-row', array());
                $colDeep = 0;

                foreach ($oneRow['cells'] as $oneCell) {
                    $colDeep++;

                    $params = array();
                    if ($oneCell['cell-type'] !== 'covered') {
                        $rowspan = (isset($oneCell['rowspan']) ? $oneCell['rowspan'] : 0);
                        $colspan = (isset($oneCell['colspan']) ? $oneCell['colspan'] : 0);
                        $cellValue = $oneCell['data'];
                    }

                    $params['table:style-name'] = $tableName . '.' . $colsLabel[$rowDeep] . $colDeep;
                    $params['office:value-type'] = 'string';
                    if ($rowspan != '') {
                        $params['table:number-rows-spanned'] = $rowspan;
                    }
                    if ($colspan != '') {
                        $params['table:number-columns-spanned'] = $colspan;
                    }

                    switch ($oneCell['cell-type']) {
                        case 'normal'    :
                            $cell = $this->createNode($tableXml, 'table:table-cell', $params);
                            break;
                        case 'covered'   :
                            $cell = $this->createNode($tableXml, 'table:covered-table-cell', array());
                            break;
                        default:
                            $cell = $this->createNode($tableXml, 'table:table-cell', $params);
                            break;
                    }

                    $cellVal = $this->createNode($tableXml, 'text:p', array('text:style-name' => 'Table_20_Contents'), $cellValue);

                    $cell->appendChild($cellVal);
                    $row->appendChild($cell);
                }
                $table->appendChild($row);
                $rowDeep++;
            }
            $tableXml->appendChild($table);
            $tables[$tableName] = $tableXml;
            //file_put_contents('kecso.xml',$this->contentXml->saveXML()); die;
        }

        return $tables;
    }

    /**
     * Unzip odt file into the temporary directory
     */
    private function unZip($createZipNameDir = true, $overwrite = true)
    {
        $destDir = $this->unzippedDir;
        $srcFile = $this->odtFilename;

        if ($zip = zip_open($srcFile)) {
            if ($zip) {
                $splitter = ($createZipNameDir === true) ? '.' : '/';
                if ($destDir === false) {
                    $destDir = substr($srcFile, 0, strrpos($srcFile, $splitter)) . '/';
                }

                $this->createDirs($destDir);

                while ($zipEntry = zip_read($zip)) {

                    $posLastSlash = strrpos(zip_entry_name($zipEntry), '/');

                    if ($posLastSlash !== false) {
                        $this->createDirs($destDir . substr(zip_entry_name($zipEntry), 0, $posLastSlash + 1));
                    }

                    if (zip_entry_open($zip, $zipEntry, 'r')) {
                        $fileName = $destDir . zip_entry_name($zipEntry);
                        if ($overwrite === true || ($overwrite === false && !is_file($fileName))) {
                            $fstream = zip_entry_read($zipEntry, zip_entry_filesize($zipEntry));
                            if (!is_dir($fileName)) {
                                file_put_contents($fileName, $fstream);
                                //chmod($fileName, $this -> right );
                            }
                        }
                        zip_entry_close($zipEntry);
                    }
                }
                zip_close($zip);
                $this->openedSablonFilename = $destDir . $this->contentFilename;
            }
        } else {
            throw new CException('Failed unzip ODT. File: ' . $this->templateDir . $this->odtFilename);
        }
    }

    /**
     * Zip xml files 
     * @return string
     */
    private function zipOdtFile()
    {
        $inputFolder = $this->unzippedDir;
        $destPath = $this->zippedDir;
        mkdir($destPath, 0777, true);
        $zip = new ZipArchive();
        $zip->open($this->newOdtFilename, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $inputFolder = str_replace('\\', DIRECTORY_SEPARATOR, realpath($inputFolder));

        if (is_dir($inputFolder) === true) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($inputFolder), RecursiveIteratorIterator::SELF_FIRST);

            foreach ($files as $file) {
                $file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
                if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..'))) {
                    continue;
                }

                $file = realpath($file);

                if (is_dir($file) === true) {
                    $dirName = str_replace($inputFolder . DIRECTORY_SEPARATOR, '', $file . DIRECTORY_SEPARATOR);
                    $zip->addEmptyDir($dirName);
                } else {
                    if (is_file($file) === true) {
                        $fileName = str_replace($inputFolder . DIRECTORY_SEPARATOR, '', $file);
                        $zip->addFromString($fileName, file_get_contents($file));
                    }
                }
            }
        } else {
            if (is_file($inputFolder) === true) {
                $zip->addFromString(basename($inputFolder), file_get_contents($inputFolder));
            }
        }

        $zip->close();
        $this->deleteDir($inputFolder);

        return $destPath . $this->generatedOdt;
    }

    /**
     * Create directory by path
     * @param $path
     */
    private function createDirs($path)
    {
        if (!is_dir($path)) {
            $directoryPath = '';
            $directories = explode('/', $path);
            array_pop($directories);

            foreach ($directories as $directory) {
                $directoryPath .= $directory . '/';
                if (!is_dir($directoryPath)) {
                    mkdir($directoryPath, 0777, true);
                    //chmod($directoryPath, $this -> right );
                }
            }
        }
    }

    /**
     * Delete temporary files by path
     * @param $path
     * @return bool
     */
    private function deleteDir($path)
    {
        if (is_dir($path)) {
            $files = array_diff(scandir($path), array('.', '..'));
            foreach ($files as $file) {
                $this->deleteDir(realpath($path) . '/' . $file);
            }

            return rmdir($path);
        } else {
            if (is_file($path) === true) {
                return unlink($path);
            }
        }
    }

    /**
     * Generate pdf file from odt and delete temporary folder
     */
    public function generatePDF()
    {
        $path = $this->zipOdtFile();
        if ($path !== false) {
            $shell = 'export HOME=/tmp && /usr/bin/libreoffice --headless --convert-to pdf --outdir ' . $this->outDir . '  ' . $path;

            exec($shell, $output, $return);
            if ($return == 0) {
                $odtPath = substr($path, 0, strrpos($path, '/'));
                $this->deleteDir($odtPath);
            }
        }
    }

    /**
     * Get generated pdf
     */
    public function getPDF()
    {
        header('Content-type: application/pdf');
        header('Content-Length: ' . filesize($this->outDir . '/' . $this->outFile));
        @readfile($this->outDir . '/' . $this->outFile);
    }


    /**
     * Convert the generated pdf N page
     * @param $pageNumber 
     */
    public function generatePDFPageN($pageNumber = 1)
    {
        ob_start();

        $pdf = new FPDI();
        $pdf->setSourceFile($this->outDir . '/' . $this->outFile);
        $tplIdx = $pdf->importPage($pageNumber, '/MediaBox');

        $pdf->AddPage();
        $pdf->useTemplate($tplIdx);

        $pdf->Output();

        ob_end_flush();
    }

    /**
     * deletes all the files generated in the process of generating the final output
     *
     * @TODO: determine all the files we should be removing
     */
    protected function cleanUp()
    {
        unlink($this->outDir . '/' . $this->outFile);
    }

    /**
     * Store the PDF as a ProtectedFile in the system
     *
     * @return ProtectedFile
     * @throws Exception
     */
    public function storePDF()
    {
        $file = ProtectedFile::createFromFile($this->outDir . '/' . $this->outFile);
        $file->save();
        $this->cleanUp();

        return $file;
    }
}
