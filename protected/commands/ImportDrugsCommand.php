<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class ImportDrugsCommand extends CConsoleCommand
{
    private $action = '';

    private function _getImportDir()
    {
        return Yii::getPathOfAlias('application') . '/data/dmd_data';
    }

    private $row_limit = 0; // FOR DEBUG!! -- 0 = unlimited
    private $params = [];
    private $tablePrefix = 'f_';
    private $nodes = [
        'amp'        => ['ACTUAL_MEDICINAL_PRODUCTS',null],
        'lookup'     => ['LOOKUP','INFO'],
        'vtm'        => ['VIRTUAL_THERAPEUTIC_MOIETIES',null],
        'vmp'        => ['VIRTUAL_MED_PRODUCTS',null],
        'ingredient' => ['INGREDIENT_SUBSTANCES',null],
    ];

    /* SQL TEMPLATES */
    private $createTableTemplate = 'CREATE TABLE IF NOT EXISTS `%s` (%s) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    private $truncateTableTemplate = 'TRUNCATE TABLE `%s`;';
    private $dropTableTemplate = 'DROP TABLE IF EXISTS `%s`;';
    private $insertTemplate = "INSERT INTO `%s` (%s) VALUES (%s);";

    private $columnTypeMap = [
        'integer' => 'varchar(200)',
        'string'  => 'varchar(200)',
        'date'    => 'date',
        'float'   => 'float',
    ];

    public function getHelp()
    {
        return <<<EOD
USAGE
  yiic importdrugs action -[params]

  action:
    createTables (just create tables from xsd, if needed)
    truncateTables (remove all data form DM+D tables `f_...`)
    dropTables (drop all DM+D tables. `f_...`)
    import (create tables if needed and import data from xmls)
    copyToOE (fill Openeyes tables ( `ref_` ) from DM+D ( `f_` ) tables.
  params:
    N/A

DESCRIPTION
  This command build database tables or/and import drugs from DM+D database. Make sure you've copied DM+D data to {$this->_getImportDir()}.

EOD;
    }

    private function halt($msg='', $needHelp = true)
    {
        echo $msg . PHP_EOL;
        if ($needHelp) {
            echo $this->getHelp() . PHP_EOL;
        }
        die();
    }

    public function setParams($params = [])
    {
        $this->params = array_merge($this->params, $params);
    }

    public function printMsg($msg, $needEol=true, $needTime=true){
        echo $needTime?'['.date('Y-m-d H:i:s').']':'';
        echo ' ';
        echo $msg.($needEol?PHP_EOL:'');
    }

    public function getParams($params)
    {
        $paramsArray = [];
        foreach ($params as $oneParam) {
            if (!preg_match("/^\-(.*)=\'?([a-zA-Z0-9_]*\'?)?$/", $oneParam, $output_array)) {
                $this->halt('Invalid parameter format: ' . $oneParam . PHP_EOL);
            }
            $paramsArray[$output_array[1]] = $output_array[2];
        }
        return $paramsArray;
    }

    public function run($params)
    {
        if(empty($params)) {
            $this->halt('Parameter problem.');
        }

        $action = $params[0];
        array_shift($params);
        $this->setParams($this->getParams($params));

        if (method_exists($this, $action)) {
            $this->$action();
        } else {
            $this->halt('Invalid action: ' . $action);
        }
    }

    public function getStructureFromArray($fileName, $XMLdata){
        $arry = explode('_',$fileName);
        $removingTables = [];
        $fileType = $arry[0];
        $version = 0;
        $structure = array();

        if(isset($XMLdata['complexType'])){
            $version = 1;
        } elseif($XMLdata['element']['complexType']['sequence']['element']) {
            $version = 2;
        } else {
            $this->halt('Unknown version.',false);
        }

        if($version==1){
            $parentNode = $XMLdata['complexType'];
            foreach($parentNode as $tables){
                $tableName = strtolower($this->tablePrefix.$fileType.'_'.$tables['@attributes']['name']);
                $structure[$tableName] = [];
                foreach($tables['all']['element'] as $cells){
                    foreach($cells as $cell){
                        $structure[$tableName][strtolower($cell['name'])] = $cell['type'];
                    }
                }
            }

            if(isset($XMLdata['element']['complexType']['sequence']['element'])){
                $parentNode = $XMLdata['element']['complexType']['sequence']['element'];
                foreach($parentNode as $table){
                    $tableName = strtolower($this->tablePrefix.$fileType.'_'.$table['@attributes']['name']);
                    $tableData = $table['complexType']['sequence']['element']['@attributes'];
                    $cells = $structure[strtolower($this->tablePrefix.$fileType.'_'.$tableData['type'])];
                    $removingTables[strtolower($this->tablePrefix.$fileType.'_'.$tableData['type'])] = 1;
                    $structure[$tableName] = $cells;
                }
            }

        } elseif($version==2){
            $parentNode = $XMLdata['element']['complexType']['sequence']['element'];
            $tableName = strtolower($this->tablePrefix.$fileType.'_'.$parentNode['@attributes']['name']);
            $structure[$tableName] = [];
            foreach($parentNode['complexType']['sequence']['element'] as $cell){
                $cell = $cell['@attributes'];
                $structure[$tableName][strtolower($cell['name'])] = $cell['type'];
            }
        }
        $structure = $this->removeUnnecessaryTables($removingTables,$structure);
        return $structure;
    }

    public function getCreateTableSqlCommand($tableName,$cells){
        $cellsString = '';
        foreach($cells as $cellName => $cellType){
            $cellsString .= "`$cellName` ".$this->getSqlColumnType($cellType).',';
        }
        $sqlCommand = sprintf($this->createTableTemplate,strtolower($tableName),trim($cellsString,','));
        return $sqlCommand;
    }

    public function getSqlColumnType($type){
        return $this->columnTypeMap[$type];
    }

    public function XsdToArray($path){
        ini_set('memory_limit', '-1');
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = true;
        $doc->load($path);
        $tempfile = time().rand(30,100).'.xml';
        $doc->save($tempfile);
        $xmlfile = file_get_contents($tempfile);
        unlink($tempfile);
        $parseObj = str_replace($doc->lastChild->prefix.':',"",$xmlfile);
        $ob= simplexml_load_string($parseObj);
        $json  = json_encode($ob);
        $data = json_decode($json, true);
        unset($doc);
        return $data;
    }

    public function removeUnnecessaryTables($removingTables,$structure){
        foreach($removingTables as $table => $dummy){
            unset($structure[$table]);
        }
        return $structure;
    }

    public function createTableData(){
        $tablesData = [];
        $XSDdir = $this->_getImportDir();
        $XSDs = $this->getAllXsdFromDir($XSDdir);

        foreach($XSDs as $fileName => $path){
            $XSDdata = $this->XsdToArray($path);
            $structure = $this->getStructureFromArray($fileName,$XSDdata);
            $tablesData = array_merge($tablesData,$structure);
        }

        return $tablesData;
    }

    public function getAllXsdFromDir($dir)
    {
        $result = array();

        if (substr($dir, -1) != '/') {
            $dir .= '/';
        }

        if (!file_exists($dir)) {
            $this->halt('Missing import directory: ' . $dir, false);
        }
        $cdir = scandir($dir);
        foreach ($cdir as $key => $value) {
            if (!in_array($value, array(".", "..")) && preg_match("/.xsd$/", $value)) {
                $result[$value] = $dir . $value;
            }
        }

        if (empty($result)) {
            $this->halt('Import directory is empty: ' . $dir);
        }

        return $result;
    }

    public function getAllXmlFromDir($dir) {
        $result = array();
        if (substr($dir, -1) != '/') {
            $dir .= '/';
        }
        $cdir = scandir($dir);
        foreach ($cdir as $key => $value)
        {

            if (!in_array($value,array(".","..")) && preg_match("/^f.*.xml$/", $value) )
            {
                $arry=explode('_',$value);
                $result[trim($arry[1],'2')] = $dir.$value;
            }
        }
        if (empty($result)) {
            $this->halt('Import directory is empty: ' . $dir);
        }

        return $result;
    }

    public function getXmlNodeInfo($type){
        return $this->nodes[$type];
    }

    public function createInsertSqlCommands($xmlArray,$type,$tablesData) {

        $limit = $this->row_limit; // FOR DEBUG

        $sqlCommands = [];

        $nodeIinfo = $this->getXmlNodeInfo($type);
        $parentNode = $nodeIinfo[0];
        $subNode = $nodeIinfo[1];

        foreach($xmlArray[$parentNode] as $tableName => $datas){
            $type = strtolower($type);
            $fullTableName = $this->tablePrefix.$type.'_'.strtolower($tableName);
            $fields = '`'.implode('`,`',array_keys($tablesData[$fullTableName])).'`';

            if(isset( $tablesData[$fullTableName] )){
                $i = 0;
                if($subNode){
                    $rows = $datas[$subNode];
                } else {
                    $rows = $datas;
                }

                $k = array_keys($rows);

                if($k[0]=='0'){
                    $subsubnode = $rows;
                } else {
                    $subsubnode = $rows[$k[0]];
                }

                foreach($subsubnode as $oneRow){
                    if( $limit<=$i++ && $limit != 0 ){ break; }
                    $values = '';
                    foreach($tablesData[$fullTableName] as $key => $filedType){

                        if(isset($oneRow[strtoupper($key)])){
                            $value = $oneRow[strtoupper($key)];

                        } else {
                            if($filedType=='date'){
                                $value = '0000-00-00';
                            } else {
                                $value = '';
                            }
                        }

                        if(getType($value)=='array' && empty($value)){
                            $value = '';
                        }

                        $value = str_replace('"',"'",$value);

                        $values .= '"'.$value.'",';
                    }

                    $values = trim($values,',');
                    $sqlCommands[] = sprintf($this->insertTemplate,$fullTableName,$fields,$values);
                }

            } else {
                $this->halt('ERROR: Unknown table name: '.$tableName,false);
            }
        }

        return $sqlCommands;
    }

    public function importDataFromXMLtoSQL($type,$path,$tablesData){
        $xmlSource = file_get_contents($path);
        $parser = new XMLParser($xmlSource);
        $xmlArray = $parser->getOutput();
        unset($parser);

        $sqlCommands = $this->createInsertSqlCommands($xmlArray,$type,$tablesData);

        foreach($sqlCommands as $sqlQuery){
            $sqlCommand = Yii::app()->db->createCommand($sqlQuery);
            $sqlCommand->execute();
        }
    }

    public function copyDrugsToOE(){
        $sqlCommand = "
        
        ";
    }

    // ----- FUNCTIONS ----------------------------------------------------------------------------------------

    public function createTables(){
        $tablesData = $this->createTableData();
        $this->printMsg('Creating tables in database...');
        foreach($tablesData as $tableName => $cells){
            $this->printMsg('   Creating: '.$tableName);
            $sqlQuery = $this->getCreateTableSqlCommand($tableName,$cells);
            $sqlCommand = Yii::app()->db->createCommand($sqlQuery);
            $sqlCommand->execute();
        }
        $this->printMsg('Tables created.');
        return $tablesData;
    }

    public function import(){
        $tablesData = $this->createTables();
        $XMLdir = $this->_getImportDir();
        $XMLs = $this->getAllXmlFromDir( $XMLdir, 'xml' );
        $this->printMsg('Importing data to database...');
        foreach($XMLs as $type => $path){
            $this->printMsg('Importing data from: '.$type.' type.');
            $this->importDataFromXMLtoSQL($type,$path,$tablesData);
        }
        $this->printMsg('Data imported.');
    }

    public function truncateTables(){
        $tablesData = $this->createTableData();
        foreach($tablesData as $tableName => $fields){
            $this->printMsg('Truncating: '.$tableName.'... ',false);
            $sqlQuery = sprintf("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = '%s'",$tableName);
            $sqlCommand = Yii::app()->db->createCommand($sqlQuery);
            $result = $sqlCommand->execute();
            if($result==1) {
                $sqlQuery = sprintf($this->truncateTableTemplate, $tableName);
                $sqlCommand = Yii::app()->db->createCommand($sqlQuery);
                $sqlCommand->execute();
                $this->printMsg('OK.', true, false);
            } else {
                $this->printMsg('Table is not exists. Skipped.', true, false);
            }
        }

    }

    public function dropTables(){
        $tablesData = $this->createTableData();
        foreach($tablesData as $tableName => $fields){
            $this->printMsg('Dropping: '.$tableName.'... ',false);
            $sqlQuery = sprintf($this->dropTableTemplate,$tableName);
            $sqlCommand = Yii::app()->db->createCommand($sqlQuery);
            $sqlCommand->execute();
            $this->printMsg('OK.',true,false);
        }

    }

    public function copyToOE()
    {
        echo "Please wait...".PHP_EOL;
        $sql_commands = file_get_contents(Yii::getPathOfAlias('application').'/migrations/data/dmd_import/import_to_oe.sql');
        Yii::app()->db->createCommand($sql_commands)->execute();
        echo "Data imported to OE.".PHP_EOL;
    }
}


class XMLParser
{
    private $xmlArray = array();

    public function __construct( $xml )
    {
        $this->parse($xml);
    }

    private function parse($contents, $getAttributes=1, $priority = 'tag'){
        if(!$contents) return array();

        if(!function_exists('xml_parser_create')) {
            //print "'xml_parser_create()' function not found!";
            return array();
        }

        //Get the XML parser of PHP - PHP must have this module for the parser to work
        $parser = xml_parser_create('');
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($contents), $xmlValues);
        xml_parser_free($parser);

        if(!$xmlValues) return;//Hmm...

        //Initializations
        $xmlArray = array();

        $current = &$xmlArray; //Refference

        //Go through the tags.
        $repeatedTagIndex = array();//Multiple tags with same name will be turned into an array
        foreach($xmlValues as $data) {
            unset($attributes,$value);//Remove existing values, or there will be trouble

            //This command will extract these variables into the foreach scope
            // tag(string), type(string), level(int), attributes(array).
            extract($data);//We could use the array by itself, but this cooler.

            $result = array();
            $attributesData = array();

            if(isset($value)) {
                if($priority == 'tag') $result = $value;
                else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
            }

            //Set the attributes too.
            if(isset($attributes) and $getAttributes) {
                foreach($attributes as $attr => $val) {
                    if($priority == 'tag') $attributesData[$attr] = $val;
                    else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
                }
            }

            //See tag status and do the needed.
            if($type == "open") {//The starting of the tag '<tag>'
                $parent[$level-1] = &$current;
                if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
                    $current[$tag] = $result;
                    if($attributesData) $current[$tag. '_attr'] = $attributesData;
                    $repeatedTagIndex[$tag.'_'.$level] = 1;

                    $current = &$current[$tag];

                } else { //There was another element with the same tag name

                    if(isset($current[$tag][0])) {//If there is a 0th element it is already an array
                        $current[$tag][$repeatedTagIndex[$tag.'_'.$level]] = $result;
                        $repeatedTagIndex[$tag.'_'.$level]++;
                    } else {//This section will make the value an array if multiple tags with the same name appear together
                        $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
                        $repeatedTagIndex[$tag.'_'.$level] = 2;

                        if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
                            $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                            unset($current[$tag.'_attr']);
                        }

                    }
                    $lastItemIndex = $repeatedTagIndex[$tag.'_'.$level]-1;
                    $current = &$current[$tag][$lastItemIndex];
                }

            } elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
                //See if the key is already taken.
                if(!isset($current[$tag])) { //New Key
                    $current[$tag] = $result;
                    $repeatedTagIndex[$tag.'_'.$level] = 1;
                    if($priority == 'tag' and $attributesData) $current[$tag. '_attr'] = $attributesData;

                } else { //If taken, put all things inside a list(array)
                    if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...

                        // ...push the new element into that array.
                        $current[$tag][$repeatedTagIndex[$tag.'_'.$level]] = $result;

                        if($priority == 'tag' and $getAttributes and $attributesData) {
                            $current[$tag][$repeatedTagIndex[$tag.'_'.$level] . '_attr'] = $attributesData;
                        }
                        $repeatedTagIndex[$tag.'_'.$level]++;

                    } else { //If it is not an array...
                        $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
                        $repeatedTagIndex[$tag.'_'.$level] = 1;
                        if($priority == 'tag' and $getAttributes) {
                            if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well

                                $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                                unset($current[$tag.'_attr']);
                            }

                            if($attributesData) {
                                $current[$tag][$repeatedTagIndex[$tag.'_'.$level] . '_attr'] = $attributesData;
                            }
                        }
                        $repeatedTagIndex[$tag.'_'.$level]++; //0 and 1 index is already taken
                    }
                }

            } elseif($type == 'close') { //End of tag '</tag>'
                $current = &$parent[$level-1];
            }
        }

        $this->xmlArray = $xmlArray;
    }

    public function getOutput()
    {
        return $this->xmlArray;
    }

}