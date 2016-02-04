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

    public $force = false; 
    
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
     * Displaying the help if no action or param passed
     */
    public function actionIndex()
    {
        $this->actionHelp();
    }
    
    /**
     * Help
     */
    public function actionHelp()
    {
        echo "\n";
        echo "HSCIC data importer\n\n";
        echo "USAGE \n  yiic.php processhscicdata [action] [parameter]";
        echo "\n";
        echo "\n";
        echo "Following actions are available:";
        echo "\n";
        echo " - full\n";
        echo " - monthly\n";
        echo " - quarterly\n";
        echo " - import [--type=gp|practice|ccg|ccgAssignment --interval=full|monthly|quarterly] *for ccg and ccgAssignment only full file available\n";
        echo " - checkremovedfromfile [--type=gp|practice]\n";
        echo "\n";
        echo "EXAMPLES\n\n";
        echo " * yiic.php processhscicdata monthly\n";
        echo "   importing the monthly files (gp and practice)\n\n";
        echo " * yiic.php processhscicdata import --type=gp --interval=full --force";
        echo "\n   importing the full GP file, forcing it as it was already processed";
        echo "\n";
        echo "\n";
        
    }
    
    
    
    /**
     * imports a specific file 
     * eg.: ProcessHscicData import --type=Gp --interval=monthly
     * 
     * @param string $type gp|Practice|Ccg|CcgAssignment
     * @param string $interval full|monthly|quarterly
     * 
     */
    public function actionImport($type, $interval = 'full')
    {
        if( !isset(self::$files[$interval]) ){
            echo "Interval not found: $interval\n\n";
            echo "Available intervals:\n";
            foreach( array_keys(self::$files) as $interval ){
                echo "$interval\n";
            }
           
        } else if( !isset(self::$files[$interval][$type]) ){
            
            echo "Type not found: $type\n\n";
            echo "Available types:\n";
            foreach( array_keys(self::$files[$interval]) as $type ){
                echo "$type\n";
            }
            
        } else {
            $this->processFile($type, self::$files[$interval][$type]);
        }      
    }
    
    /**
     * Importing all files listed under the self::files['full']
     * ProcessHscicData full
     */
    public function actionFull(){
        foreach (self::$files['full'] as $type => $file) {
            $this->processFile($type, $file);
        }
    }
    
    /**
     * Importing all files listed under the self::files['monthly']
     * ProcessHscicData monthly
     */
    public function actionMonthly()
    {
        foreach (self::$files['monthly'] as $type => $file) {
            $this->processFile($type, $file);
        }
    }
    
    /**
     * Importing all files listed under the self::files['quarterly']
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
            echo "File not found: " . $tempFile ."\n";
            echo "Please download it (eg. using DownloadHscicData command) " ."\n";
            
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
     * @throws Exception
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
     * @throws Exception
     */
    private function getLineCountFromZip($file){
        $pathInfo = pathinfo($file);
        
        $zip = new ZipArchive();
        if (($res = $zip->open($file)) !== true) {
            throw new Exception("Failed to open zip file at '{$zip_path}': " . $res);
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
        //echo "<pre>" . print_r($pathParts, true) . "</pre>";die;

        $permanentFile = $this->path . '/' . $pathParts['filename'] . '/' . $pathParts['basename'];
        $tempFile =  $this->tempPath . '/' . $pathParts['basename'];

        // I am not sure about the fn name
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
        
        echo  "Type: {$type}\n";
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
        
        // only save when model is dirty
        $gp->save_only_if_changed = true;
        
        $gp->is_active = $data['status'] == 'A' || $data['status'] == 'P' ? '1' : '0';

        if (!$gp->save()) throw new Exception("Failed to save GP: " . print_r($gp->errors, true));

        $contact = $gp->contact;
        $contact->save_only_if_changed = true;
        $contact->primary_phone = $data['phone'];

        if (preg_match("/^([\S]+)\s+([A-Z]{1,4})$/i", trim($data['name']), $m)) {
            $contact->title = 'Dr';
            $contact->first_name = $m[2];
            $contact->last_name = $this->tidy($m[1]);
        } else {
            $contact->last_name = $data['name'];
        }

        if (!$contact->save()) throw new Exception("Failed to save contact: " . print_r($contact->errors, true));

        if (!($address = $contact->address)) {
            $address = new Address;
            $address->contact_id = $contact->id;
        }
        
        $this->importAddress($address, array($data['addr1'], $data['addr2'], $data['addr3'], $data['addr4'], $data['addr5']));
        $address->postcode = $data['postcode'];
        $address->country_id = $this->countryId;

        $address->save_only_if_changed = true;
        if (!$address->save()) throw new Exception("Failed to save address: " . print_r($address->errors, true));
        
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
        
        $practice->is_active = $data['status'] == 'A' || $data['status'] == 'P' ? 1 : 0;
        
        $practice->phone = $data['phone'];
        
        $practice->save_only_if_changed = true;
        if (!$practice->save()) throw new Exception("Failed to save practice: " . print_r($practice->errors, true));

        $contact = $practice->contact;
        $contact->primary_phone = $practice->phone;
        
        $contact->save_only_if_changed = true;
        if (!$contact->save()) throw new Exception("Failed to save contact: " . print_r($contact->errors, true));

        if (!($address = $contact->address)) {
            $address = new Address;
            $address->contact_id = $contact->id;
        }
        $this->importAddress($address, array($data['name'], $data['addr1'], $data['addr2'], $data['addr3'], $data['addr4'], $data['addr5']));
        $address->postcode = $data['postcode'];
        $address->country_id = $this->countryId;
        
        $address->save_only_if_changed = true;
        if (!$address->save()) throw new Exception("Failed to save address: " . print_r($address->errors, true));
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
        $ccg->name = $data['name'];
        
        $ccg->save_only_if_changed = true;
        if (!$ccg->save()) throw new Exception("Failed to save CCG: " . print_r($ccg->errors, true));

        $contact = $ccg->contact;
        $contact->save_only_if_changed = true;
        
        if (!($address = $contact->address)) {
                $address = new Address;
                $address->contact_id = $contact->id;
        }
        $this->importAddress($address, array($data['addr1'], $data['addr2'], $data['addr3'], $data['addr4'], $data['addr5']));
        $address->postcode = $data['postcode'];
        $address->country_id = $this->countryId;
        
        $address->save_only_if_changed = true; 
        if (!$address->save()) throw new Exception("Failed to save address: " . print_r($address->errors, true));
    }
    
    /**
     * imports the 'CcgAssignment' file
     * 
     * @param array $data
     */
    private function importCcgAssignment(array $data)
    {
        $practice = Practice::model()->findByAttributes(array('code' => $data['practice_code']));
        $ccg = CommissioningBody::model()->findByAttributes(array('code' => $data['ccg_code'], 'commissioning_body_type_id' => $this->cbtId));

        if (!$practice || !$ccg) return;

        $found = false;
        foreach ($practice->commissioningbodyassigments as $assignment) {
            if ($assignment->commissioning_body_id == $ccg->id) {
                $found = true;
            } else {
                if ($assignment->commissioning_body->commissioning_body_type_id == $this->cbtId)  $assignment->delete();
            }
        }

        if (!$found) {
            $assignment = new CommissioningBodyPracticeAssignment;
            $assignment->commissioning_body_id = $ccg->id;
            $assignment->practice_id = $practice->id;
            
            if (!$assignment->save()) throw new Exception("Failed to save commissioning body assignment: " . print_r($assignment->errors, true));
        }
    }
    
    /**
     * Imports the Address
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
     * Compare the DB against the DB, if a DB row not exsist in the CSV anymore the script set the is_active flag to 0
     * @param type $type
     * @throws Exception
     */
    public function actionCheckRemovedFromFile($type = 'gp')
    {
        if ( !isset(self::$files['test'][$type]['filename'])){
            throw new Exception("Invalid type: $type");
        }
                
        switch ($type){
            case 'gp':
            case 'practice':
                $dbTable = $type;
                break;
            case 'ccg':
                $dbTable = 'commissioning_body';
            default:
                throw new Exception("Invalid type: $type");
        }
        
        $dbTable = addslashes($dbTable);
        
        // drop temp table if exsist
        $query = "DROP TABLE if exists temp_$dbTable;";
        
        // create temp table
        $query .= "CREATE TEMPORARY TABLE temp_$dbTable LIKE $dbTable";
        
        echo "Creating temp table... ";
        Yii::app()->db->createCommand($query)->execute();
        echo "OK\n";
        
        $i = 0;

        $file = self::$files['full'][$type]['filename'];
        
        $fileHandler = $this->getFilePointer($this->tempPath . '/' . $file);
        
        echo "Inserting rows into temp table... ";
        
        $command = Yii::app()->db->createCommand();
        
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
            
            $command->insert("temp_$dbTable", $insertData);
            
            $command->reset();
        }
        echo "OK\n\n";
        
        $column = $dbTable == 'gp' ? 'nat_id' : 'code';
        
        $criteria=new CDbCriteria(); 
        $criteria->select = array("t.*");
        $criteria->join = "LEFT JOIN temp_$dbTable ON t.$column = temp_$dbTable.$column";
        $criteria->addCondition("temp_$dbTable.$column IS NULL");
        
        $modelName = ucfirst($dbTable);
        $not_in_file = $modelName::model()->findAll($criteria);
       
        echo "Set " . count($not_in_file) . " $type to inactive... ";
        foreach($not_in_file as $removed_instance){
            $removed_instance->is_active = '0';
            $removed_instance->save();
        }
        echo "OK\n\n";
         
        // drop temp table
        $query = "DROP TABLE if exists temp_$dbTable;";
        Yii::app()->db->createCommand($query)->execute(array(':tableName' => $dbTable));
    }
}