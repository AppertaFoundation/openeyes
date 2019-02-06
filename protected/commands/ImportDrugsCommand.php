<?php
/**
 * OpenEyes.
 *
 *
 * Copyright OpenEyes Foundation, 2018
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2018, OpenEyes Foundation
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

        @unlink('/tmp/ref_medication_set.csv');

        $scripts = [
            'delete', 'copy_amp', 'copy_vmp', 'copy_vtm', 'forms_routes', 'sets', 'ref_medication_sets', 'ref_medication_sets_load', 'add_formulary', 'search_index'
        ];

        foreach ($scripts as $script) {
            $cmd = file_get_contents(Yii::getPathOfAlias('application').'/migrations/data/dmd_import/'.$script.'.sql');
            echo $script;
            $cmd = str_replace(['{prefix}'], [$this->tablePrefix], $cmd);
            Yii::app()->db->createCommand($cmd)->execute();
            echo " OK".PHP_EOL;
        }

        echo "attributes";
        
        $lookup_tables = [
            'COMBINATION_PACK_IND',
            'COMBINATION_PROD_IND',
            'BASIS_OF_NAME',
            'NAMECHANGE_REASON',
            'VIRTUAL_PRODUCT_PRES_STATUS',
            'CONTROL_DRUG_CATEGORY',
            'LICENSING_AUTHORITY',
            'UNIT_OF_MEASURE',
            'FORM',
            'ONT_FORM_ROUTE',
            'ROUTE',
            'DT_PAYMENT_CATEGORY',
            'SUPPLIER',
            'FLAVOUR',
            'COLOUR',
            'BASIS_OF_STRNTH',
            'REIMBURSEMENT_STATUS',
            'SPEC_CONT',
            'DND',
            'VIRTUAL_PRODUCT_NON_AVAIL',
            'DISCONTINUED_IND',
            'DF_INDICATOR',
            'PRICE_BASIS',
            'LEGAL_CATEGORY',
            'AVAILABILITY_RESTRICTION',
            'LICENSING_AUTHORITY_CHANGE_REASON'
        ];

        $cmd = "INSERT INTO  medication_attribute (`name`) VALUES ".implode(",", array_map(function($e){ return "('$e')";}, $lookup_tables));
        Yii::app()->db->createCommand($cmd)->execute();
        foreach ($lookup_tables as $table) {
            $cmd = "INSERT INTO ";
        }

        unlink('/tmp/ref_medication_set.csv');

        echo "Data imported to OE.".PHP_EOL;
    }
}