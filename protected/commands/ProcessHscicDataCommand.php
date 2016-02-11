<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */    

class ProcessHscicDataCommand extends CConsoleCommand
{
    public $path = null;
    public $tempPath = null;

    /**
     * Force already imported files to process it again
     * @var type 
     */
    public $force = false;
    
    /**
     * Audit can be disabled (eg for the first import we probably don't want to generate 78000 'GP imported' audit rows)
     * @var type
     */
    public $audit = true;
    
    private $pcu;
    private $countryId;
    private $cbtId;
    
    static private $files = array(
        'full' => array(
            'gp' => array(
                    'filename' => 'egpcur.zip',
                    'fields' => array('code', 'name', '', '', 'addr1', 'addr2', 'addr3', 'addr4', 'addr5', 'postcode', '', '', 'status', '', '', '', '', 'phone'),
             ),
            'practice' => array(  // http://systems.hscic.gov.uk/data/ods/supportinginfo/filedescriptions#_Toc350757591
                    'filename' => 'epraccur.zip',
                    'fields' => array('code', 'name', '', '', 'addr1', 'addr2', 'addr3', 'addr4', 'addr5', 'postcode', '', '', 'status', '', '', '', '', 'phone'),
            ),
            'ccg' => array(
                    'filename' => 'eccg.zip',
                    'fields' => array('code', 'name', '', '', 'addr1', 'addr2', 'addr3', 'addr4', 'addr5', 'postcode'),
            ),
            'ccgAssignment' => array(
                    'filename' => 'epcmem.zip',
                    'fields' => array('practice_code', 'ccg_code'),
            ),
        ),
        'monthly' => array(
            'gp' => array(
                'filename' => 'egpam.zip',
                'fields' => array('code', 'name', '', '', 'addr1', 'addr2', 'addr3', 'addr4', 'addr5', 'postcode', '', '', 'status', '', '', '', '', 'phone'),
            ),
        ),
        'quarterly' => array(
            'gp' => array(
                'filename' => 'egpaq.zip',
                'fields' => array('code', 'name', '', '', 'addr1', 'addr2', 'addr3', 'addr4', 'addr5', 'postcode', '', '', 'status', '', '', '', '', 'phone'),
            )
        ),
    );
    
    function __construct()
    {
       $this->path = Yii::app()->basePath . '/data/hscic';
       $this->tempPath = $this->path . '/temp';
       
        if (!file_exists($this->tempPath)) {
            mkdir($this->tempPath, 0777, true);
        }
        

        parent::__construct(null, null);
    }
    
    /**
     * Returns the command name/short description.
     * @return string
     */
    public function getName()
    {
        return 'HSCIC data import Command.';
    }
    
    /**
     * Displaying the help if no action or param passed
     */
    public function actionIndex()
    {
        $this->getHelp();
    }
    
    /**
     * Help
     */
    public function getHelp()
    {        
        echo <<<EOH

        
HSCIC data importer
        
The importer is processing zipped CSV files downloaded from HSCIC websie
http://systems.hscic.gov.uk/data/ods/datadownloads/gppractice

.zip files must be placed to /protected/data/hscic/temp

USAGE
  yiic.php processhscicdata [action] [parameter]
        
Following actions are available:
        
 - full         : Importing the full version of the GP, Practice, 
                  CCG and CCG Assignment files
 - monthly      : Importing the monthly files of the GP
 - quarterly    : Importing the quarterly files of the GP
        
 - import       [--type --interval] : Importing a specific file based on the given type and iterval
                Available intervals by type : 
                    GP              : full|quarterly|monthly
                    Practice        : full
                    CCG:            : full
                    CCGAssignment   : full
        
- checkremovedfromfile  : Checking if a database row no longer exists in the file, and if it's the case, we set the status inactive
                          Supported types : GP and Practice
        
        
Following parameters are available:
        
 - force  :  force import for the give file, even if it was already processed before
 - audit  :  Do not generate audit message (can be useful for the first run, we do not need 78000 'GP imported' audit message)
             Usage: --audit=false

EXAMPLES
        
 * yiic.php processhscicdata full
   importing the full files (GP, Practice, CCG, CCG Assignment)
        
 * yiic.php processhscicdata monthly
   importing the monthly files (GP)
        
 * yiic.php processhscicdata monthly
   importing the quarterly files (GP)
        
 * yiic.php processhscicdata import --type=gp --interval=full --force
   importing the full GP file, forcing it as it was already processed

 * yiic.php processhscicdata import --type=gp --interval=full --audit=false
   importing the full GP file without generating audit message

EOH;
        
    }
    
    
    
    /**
     * imports a specific file based on the given type and interval
     * eg.: ProcessHscicData import --type=Gp --interval=monthly
     * 
     * @param string $type gp|Practice|Ccg|CcgAssignment
     * @param string $interval full|monthly|quarterly
     * 
     */
    public function actionImport($type, $interval = 'full')
    {
        if( !isset(self::$files[$interval]) ){
            $this->usageError("Interval not found: $interval");
        } else if( !isset(self::$files[$interval][$type]) ){
            $this->usageError("Type not found: $type");
        } else {
            $this->processFile($type, self::$files[$interval][$type]);
        }      
    }
    
    /**
     * Importing all files listed under the self::files['full'] as GP, Practice, CCG, CCG Assignment
     * ProcessHscicData full
     */
    public function actionFull(){
        foreach (self::$files['full'] as $type => $file) {
            $this->processFile($type, $file);
        }
    }
    
    /**
     * Importing all files listed under the self::files['monthly'] as GP
     * ProcessHscicData monthly
     */
    public function actionMonthly()
    {
        foreach (self::$files['monthly'] as $type => $file) {
            $this->processFile($type, $file);
        }
    }
    
    /**
     * Importing all files listed under the self::files['quarterly'] as GP
     * ProcessHscicData quarterly
     */
    public function actionQuarterly()
    {
        foreach (self::$files['quarterly'] as $type => $file) {
            $this->processFile($type, $file);
        }
    }
    
    /**
     * Checks if the newly downloaded file is already processed or not
     * by comparing to the previously downloaded one
     * 
     * @param type $tempFile freshly downloaded in the /tmp directory
     * @param type $permanentFile already processed one {$permanentFile}/{$permanentFile}.zip
     * @return bool true if no already processed file found or the new file differ from the previous one in md5 hash
     */
    private function isNewResourceFile($tempFile, $permanentFile)
    {
        $isNewResource = false;
        
        if( !file_exists($tempFile)){
            $this->usageError("File not found: " . $tempFile);
        } else if ( !file_exists($permanentFile) ){
            // No previously processed file found
            $isNewResource = true;
        } else {
            // new and old files are found, lets compare them
            $permanentFileHash = md5_file($permanentFile);
            $tempFileHash = md5_file($tempFile);
            
            $isNewResource = ($permanentFileHash === $tempFileHash ? false : true);
        }
 
        return $this->force ? true : $isNewResource;
    }    
    
    
    /**
     * Opens the zip file and gets the CSV file pointer and returns it
     * 
     * @param string $file path and filename
     * @return resource a file pointer (resource)
     * @throws Exception if fails open the zip or fails to extract the CSV file
     */
    private function getFilePointer($file)
    {
        $pathInfo = pathinfo($file);
        
        $zip = new ZipArchive(); 
        if (($res = $zip->open($file)) !== true) {
            throw new Exception("Failed to open zip file at '{$zip_path}': " . $res);
        }
        
        $fileName = str_replace('.zip', '.csv', $pathInfo['basename']);
      
        if (!($stream = $zip->getStream($fileName))) {
            throw new Exception("Failed to extract '{$fileName}' from zip file at '{$file}'");
        }
        
        return $stream;
    }
    
    /**
     * Gets the line count of the CSV file in the zip
     * 
     * @param string $file file pathe and name
     * @return int line count
     * @throws Exception if fails open the zip or fails to extract the CSV file
     */
    private function getLineCountFromZip($file)
    {
        $pathInfo = pathinfo($file);
        
        $zip = new ZipArchive();
        if (($res = $zip->open($file)) !== true) {
            $this->usageError("Failed to open zip file at '{$zip_path}': " . $res);
        }
        
        $fileName = str_replace('.zip', '.csv', $pathInfo['basename']);
      
        if (!($stream = $zip->getStream($fileName))) {
            throw new Exception("Failed to extract '{$fileName}' from zip file at '{$file}'");
        }
        
        $lineCount = 0;
        while (fgets($stream) !== false){
            $lineCount++;
        }
        $zip->close();
        
        return $lineCount;
    }
    
    /**
     * Checks if the destination folder exsist (makes it if not) than copies the file
     * @param type $tempFile
     * @param type $permanentFile
     */
    private function tempToPermanent($tempFile, $permanentFile)
    {
        $pathParts = pathinfo($permanentFile);     
        
       if (!file_exists($pathParts['dirname'])) {
            mkdir($pathParts['dirname'], 0777, true);
        }
        
        copy($tempFile, $permanentFile);
    }
    
    /**
     * Processing the given file
     * 
     * @param string $type like 'Gp'
     * @param array $file (based on self::files['full']['Gp'])
     */
    private function processFile($type, $file)
    {
        echo "\n";
        $pathParts = pathinfo($file['filename']);

        $permanentFile = $this->path . '/' . $pathParts['filename'] . '/' . $pathParts['basename'];
        $tempFile =  $this->tempPath . '/' . $pathParts['basename'];

        // check if the current file(url) is already processed or not
        if( $this->isNewResourceFile($tempFile, $permanentFile) ){

            echo "Processing " . $file['filename'] . "\n";

            $this->tempToPermanent($tempFile, $permanentFile);

            $this->processCSV($type, $file['fields'], $permanentFile);
        } else {
            echo $type . " - " . basename($permanentFile) . " is already processed\n";
        }
    }
    
    /**
     * Gets the zip file, extracts the CSV file(from the zip) and processes it
     * 
     * @param string $type like 'Gp'
     * @param array $fields 
     * @param string $file the zip file
     */
    private function processCSV($type, $fields, $file)
    {
        $lineCount = $this->getLineCountFromZip($file);
        $fileHandler = $this->getFilePointer($file);
        
        $this->pcu = new PostCodeUtility;
        $this->countryId = Country::model()->findByAttributes(array('code' => 'GB'))->id;
        $this->cbtId = CommissioningBodyType::model()->findByAttributes(array('shortname' => 'CCG'))->id;
        
        echo "Type: $type\n";
        echo "Total rows : $lineCount\n";
        
        echo "Progress :          ";
        
        $i = 1;
        while (($row = fgetcsv($fileHandler))) {
            
            $percent = round((($i/$lineCount)*100), 1);
            
            echo "\033[7D"; // 7 char back
            echo str_pad( $percent , 5, ' ', STR_PAD_LEFT) . " %";
       
            $data = array_combine(array_pad($fields, count($row), ""), $row);
            $transaction = Yii::app()->db->beginTransaction();
            try {
                $this->{"import{$type}"}($data);
                $transaction->commit();
            } catch(Exception $e) {
                $message = "Error processing {$type} row:\n" . CVarDumper::dumpAsString($row) . "\n$e";
                Yii::log($message, CLogger::LEVEL_ERROR);
                print "$message\n";
                $transaction->rollback();
                echo "Progress :          ";
            }
            $i++;
        }
        echo "\n";

    }
    
    /**
     * Imports the 'Gp' CSV file
     * 
     * @param array $data
     */
    private function importGp(array $data)
    {
        if (!($gp = Gp::model()->findbyAttributes(array('nat_id' => $data['code'])))) {
                $gp = new Gp;
                $gp->nat_id = $data['code'];
                $gp->obj_prof = $data['code'];
        }
        
        $isNewRecord = $gp->isNewRecord;
        
        $gp->is_active = $data['status'] == 'A' || $data['status'] == 'P' ? '1' : '0';

        if ($gp->saveOnlyIfDirty()->save()){
            if($this->audit !== 'false') {
                Audit::add('ProcessHscicDataCommand', ($isNewRecord ? 'Insert' : 'Update') . ' GP');
            }
        } else {
            // save has not been carried out, either mode was not dirty or save() failed
        }

        $contact = $gp->contact;
        $contact->primary_phone = $data['phone'];

        /**
         * Regexp
         * the first part match a word (any number of char, no whithespace)
         * than (after the first word) can follow any number of whitepace 
         * and for the last part the string must end 1 to 4 characters[A-Z]
         *
         * The goal is to extract the name initials from the field and use as a first name with the doctor title.
         *
         * Examples (egpam.zip): WELLINGS D, DONOGHUE CA, COLLOMBON MPM
         */
        if (preg_match("/^([\S]+)\s+([A-Z]{1,4})$/i", trim($data['name']), $m)) {
            $contact->title = 'Dr';
            $contact->first_name = $m[2];
            $contact->last_name = $this->tidy($m[1]);
        } else {
            $contact->last_name = $data['name'];
        }
        
        $isNewRecord = $contact->isNewRecord;
        
        if ($contact->saveOnlyIfDirty()->save()){
            if($this->audit !== 'false') {
                Audit::add('ProcessHscicDataCommand', ($isNewRecord ? 'Insert' : 'Update') . ' GP-Contact');
            }
        } else {
            // save has not been carried out, either mode was not dirty or save() failed
        }
        

        if (!($address = $contact->address)) {
            $address = new Address;
            $address->contact_id = $contact->id;
        }
        
        $this->importAddress($address, array($data['addr1'], $data['addr2'], $data['addr3'], $data['addr4'], $data['addr5']));
        $address->postcode = $data['postcode'];
        $address->country_id = $this->countryId;
        
        $isNewRecord = $address->isNewRecord;
        
        if ($address->saveOnlyIfDirty()->save()){
            if($this->audit !== 'false') {
                Audit::add('ProcessHscicDataCommand', ($isNewRecord ? 'Insert' : 'Update') . ' GP-Address');
            }
        } else {
            // save has not been carried out, either mode was not dirty or save() failed
        }
        
        $gp = null;
    }
    
    /**
     * Imports the 'Practice' CSV file
     * @param array $data
     */
    private function importPractice($data)
    {
        if (!($practice = Practice::model()->findByAttributes(array('code' => $data['code'])))) {
           
            $practice = new Practice;
            $practice->code = $data['code'];
        }
        $isNewRecord = $practice->isNewRecord;
        
        $practice->is_active = $data['status'] == 'A' || $data['status'] == 'P' ? '1' : '0';
        
        $practice->phone = $data['phone'];
        
        if ($practice->saveOnlyIfDirty()->save()){
            if($this->audit !== 'false') {
                Audit::add('ProcessHscicDataCommand', ($isNewRecord ? 'Insert' : 'Update') . ' Practice');
            }
        } else {
            // save has not been carried out, either mode was not dirty or save() failed
        }

        $contact = $practice->contact;
        $contact->primary_phone = $practice->phone;
        
        $isNewRecord = $contact->isNewRecord;

        if ($contact->saveOnlyIfDirty()->save()){
            if($this->audit !== 'false') {
                Audit::add('ProcessHscicDataCommand', ($isNewRecord ? 'Insert' : 'Update') . ' Practice-Contact');
            }
        } else {
            // save has not been carried out, either mode was not dirty or save() failed
        }
        

        if (!($address = $contact->address)) {
            $address = new Address;
            $address->contact_id = $contact->id;
        }
        $isNewRecord = $address->isNewRecord;
        
        $this->importAddress($address, array($data['name'], $data['addr1'], $data['addr2'], $data['addr3'], $data['addr4'], $data['addr5']));
        $address->postcode = $data['postcode'];
        $address->country_id = $this->countryId;
        
        if ($address->saveOnlyIfDirty()->save()){
            if($this->audit !== 'false') {
                Audit::add('ProcessHscicDataCommand', ($isNewRecord ? 'Insert' : 'Update') . ' Practice-Address');
            }
        } else {
            // save has not been carried out, either mode was not dirty or save() failed
        }
    }
    
    /**
     * Imports the 'Ccg' CSV file 
     * @param array $data
     */
    private function importCcg(array $data)
    {
        if (!($ccg = CommissioningBody::model()->findByAttributes(array('code' => $data['code'], 'commissioning_body_type_id' => $this->cbtId)))) {
            $ccg = new CommissioningBody;
            $ccg->code = $data['code'];
            $ccg->commissioning_body_type_id = $this->cbtId;
        }
        $isNewRecord = $ccg->isNewRecord;
        $ccg->name = $data['name'];
        
        if ($ccg->saveOnlyIfDirty()->save()){
            if($this->audit !== 'false') {
                Audit::add('ProcessHscicDataCommand', ($isNewRecord ? 'Insert' : 'Update') . ' CCG');
            }
        } else {
            // save has not been carried out, either mode was not dirty or save() failed
        }

        $contact = $ccg->contact;        
        if (!($address = $contact->address)) {
                $address = new Address;
                $address->contact_id = $contact->id;
        }
        $isNewRecord = $address->isNewRecord;
        $this->importAddress($address, array($data['addr1'], $data['addr2'], $data['addr3'], $data['addr4'], $data['addr5']));
        $address->postcode = $data['postcode'];
        $address->country_id = $this->countryId;
        
        if ($address->saveOnlyIfDirty()->save()){
            if($this->audit !== 'false') {
                Audit::add('ProcessHscicDataCommand', ($isNewRecord ? 'Insert' : 'Update') . ' CCG-Address');
            }
        } else {
            // save has not been carried out, either mode was not dirty or save() failed
        }
    }
    
    /**
     * Imports the 'CcgAssignment' file
     * 
     * @param array $data
     * @return NULL if Practice or CCG does not exsist
     * @throws Exception If Failed to save commissioning body assignment
     */
    private function importCcgAssignment(array $data)
    {
        $practice = Practice::model()->findByAttributes(array('code' => $data['practice_code']));
        $ccg = CommissioningBody::model()->findByAttributes(array('code' => $data['ccg_code'], 'commissioning_body_type_id' => $this->cbtId));

        if (!$practice || !$ccg) return null;

        $found = false;
        foreach ($practice->commissioningbodyassigments as $assignment) {
            if ($assignment->commissioning_body_id == $ccg->id) {
                $found = true;
            } else {
                if ($assignment->commissioning_body->commissioning_body_type_id == $this->cbtId){  
                   
                    if( $assignment->delete() && $this->audit !== 'false') {
                        Audit::add('ProcessHscicDataCommand', 'Assignment Deleted');
                    }
                }
            }
        }

        if (!$found) {
            $assignment = new CommissioningBodyPracticeAssignment;
            $assignment->commissioning_body_id = $ccg->id;
            $assignment->practice_id = $practice->id;
            
            if (!$assignment->save()) {
                throw new Exception("Failed to save commissioning body assignment: " . print_r($assignment->errors, true));
            }
            if($this->audit !== 'false') {
                Audit::add('ProcessHscicDataCommand', 'Assignment Saved');
            }
            
        }
    }
    
    /**
     * Imports the Address
     * 
     * @param Address $address
     * @param array $lines
     */
    private function importAddress(Address $address, array $lines)
    {
        $lines = array_unique(array_filter(array_map(array($this, 'tidy'), $lines)));
        if ($lines) $address->address1 = array_shift($lines);
        if ($lines) $address->county = array_pop($lines);
        if ($lines) $address->city = array_pop($lines);
        if ($lines) $address->address2 = implode("\n", $lines);
        
        $lines = null;
    }
    
    /**
     * Transform the address line to uppercase all the words
     * 
     * @param string $string
     * @return string
     */
    private function tidy($string)
    {
        $string = ucwords(strtolower(trim($string)));

        foreach (array('-', '\'', '.') as $delimiter) {
            if (strpos($string, $delimiter) !== false) {
                $string = implode($delimiter, array_map('ucfirst', explode($delimiter, $string)));
            }
        }

        $string = str_replace('\'S ', '\'s ', $string);

        return $string;
    }
    
    /**
     * Checking if a database row no longer exists in the file, and if it's the case, we set the status inactive
     * 
     * @param type $type
     */
    public function actionCheckRemovedFromFile($type = 'gp')
    {
        if ( !isset(self::$files['full'][$type]['filename']) || ($type != 'gp' && $type != 'practice') ){
            $this->usageError("Invalid type: $type");
        }
        
        $dbTable = $this->getTableNameByType($type);
       
        $this->createTempTable($dbTable);

        $file = self::$files['full'][$type]['filename'];
        $this->fillTempTable($type, $file);

        $this->markInactiveMissingModels($dbTable);
         
        // drop temp table
        $query = "DROP TABLE if exists temp_$dbTable;";
        Yii::app()->db->createCommand($query)->execute();
    }
    
    /**
     * Creating temporary table for CheckRemovedFromFile() method
     * 
     * @param type $dbTable
     */
    private function createTempTable($dbTable)
    {
        $tableName = addslashes($dbTable);
        
        // drop temp table if exsist
        $query = "DROP TABLE if exists temp_$tableName;";
        
        // create temp table
        $query .= "CREATE TEMPORARY TABLE temp_$tableName LIKE $tableName;";
        
        $column = $dbTable == 'gp' ? 'nat_id' : 'code';
        
        $query .= "ALTER TABLE temp_$tableName ADD INDEX `$column` (`$column`)";
        
        echo "Creating temp table... ";
        \Yii::app()->db->createCommand($query)->execute();
        echo "OK\n";

    }
    
    /**
     * Returns the database table name based on the give type
     * 
     * @param string $type gp|practice|ccg
     * @return string database table name
     */
    private function getTableNameByType($type)
    {
        switch ($type){
           case 'gp':
           case 'practice':
               $dbTable = $type;
               break;
           case 'ccg':
               $dbTable = 'commissioning_body';
           default:
               $this->usageError("Invalid type: $type");
       }
       
       return $dbTable;
    }
    
    /**
     * Fill temp table for CheckRemovedFromFile() method
     * 
     * @param string $type GP
     * @param type $file
     */
    private function fillTempTable($type, $file)
    {
        $dbTable = $this->getTableNameByType($type);
        
        $fileHandler = $this->getFilePointer($this->tempPath . '/' . $file);
        
        echo "Inserting rows into temp table... ";
        
        $i = 0;
        $insertBulkData = array();
        while (($row = fgetcsv($fileHandler))) {
                    
            $data = array_combine(array_pad(self::$files['full'][$type]['fields'], count($row), ""), $row);
            
            if($dbTable == 'gp'){
               $insertData = array(
                    'obj_prof' => $data['code'],
                    'nat_id'   => $data['code'],
                );
            } else {
                $insertData = array(
                    'code' => $data['code'],
                );
            }
            
            $insertBulkData[] = $insertData;
        }
        
        $builder = Yii::app()->db->schema->commandBuilder;
        
        $command = $builder->createMultipleInsertCommand("temp_$dbTable", $insertBulkData);
        $command->execute();
        
        echo "OK\n\n";
    }
    
    /**
     * Set status to inactive on models missing from the CSV file
     * @param type $type GP
     */
    private function markInactiveMissingModels($type)
    {
        $dbTable = $this->getTableNameByType($type);
        $column = $dbTable == 'gp' ? 'nat_id' : 'code';
        
        $criteria=new CDbCriteria();
        $criteria->select = array("t.*");
        $criteria->join = "LEFT JOIN temp_$dbTable ON t.$column = temp_$dbTable.$column";
        $criteria->addCondition("temp_$dbTable.$column IS NULL");
        $criteria->addCondition("t.is_active = 1");
        
        $modelName = ucfirst($dbTable);
        $not_in_file = $modelName::model()->findAll($criteria);
       
        echo "Set " . count($not_in_file) . " $type to inactive... ";
        foreach($not_in_file as $removed_instance){
            
            $removed_instance->is_active = '0';
            $removed_instance->save();
        }
        echo "OK\n\n";
    }
}
