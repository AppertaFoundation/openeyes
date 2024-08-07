<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class ProcessHscicDataCommand extends CConsoleCommand
{
    public $path = null;
    public $tempPath = null;

    public static $DOWNLOAD_FAILED = 1;
    public static $DOWNLOAD_EMPTY = 2;
    public static $UNEXPECTED_FILE_PROBLEM = 3;
    public static $UNRECOGNISED_CODE = 4;
    public static $UNKNOWN_ERROR = 9;

    /**
     * Force already imported files to process it again.
     *
     * @var type
     */
    public $force = false;

    /**
     * Audit can be disabled (eg for the first import we probably don't want to generate 78000 'GP imported' audit rows).
     *
     * @var type
     */
    public $audit = true;

    /**
     * Override the default URL, e.g. to process a specific monthly file.
     *
     * @var type
     */
    public $url = '';

    /**
     * Override the default region setting (England)
     * Options are:
     * - england
     * - scotland
     * - ni
     */
    private $regionName = 'england';

    /**
     * @var int
     */
    public $timeout = 30;

    private $pcu;
    private $countryId;
    private $cbtId;
    public const SCENARIO = 'hscic_import';

    /**
     * Base URL for file retrieval (pre-pended to URLs in the file config below
     *
     * @var string
     */
    private static $base_url = 'https://digital.nhs.uk';


    /**
     * Static config for England - note that any 'url' elements that do not begin with http will have $base_url prepended.
     *
     * @var array
     */
    private static $file_config_england = array(
        'full' => array(
            'gp' => array(
                'url' => 'egpcur',
                'fields' => array('code', 'name', '', '', 'addr1', 'addr2', 'addr3', 'addr4', 'addr5', 'postcode', '', '', 'status', '', '', '', '', 'phone'),
            ),
            'practice' => array(
                'url' => 'epraccur',
                'fields' => array('code', 'name', '', '', 'addr1', 'addr2', 'addr3', 'addr4', 'addr5', 'postcode', '', '', 'status', '', '', '', '', 'phone'),
            ),
            'ccg' => array(
                'url' => 'eccg',
                'fields' => array('code', 'name', '', '', 'addr1', 'addr2', 'addr3', 'addr4', 'addr5', 'postcode'),
            ),
            'ccgAssignment' => array(
                'url' => 'epcmem',
                'fields' => array('practice_code', 'ccg_code'),
            ),
            'optom' => array(
                'url' => 'eoptsite',
                'fields' => array('code', 'name', '', '', 'addr1', 'addr2', 'addr3', 'addr4', 'addr5', 'postcode'),
            ),
        ),
        'monthly' => array(
            'gp' => array(
                'url' => 'egpam',
                'fields' => array('code', 'name', '', '', 'addr1', 'addr2', 'addr3', 'addr4', 'addr5', 'postcode', '', '', 'status', '', '', '', '', 'phone'),
            ),
        ),
        'quarterly' => array(
            'gp' => array(
                'url' => 'egpaq',
                'fields' => array('code', 'name', '', '', 'addr1', 'addr2', 'addr3', 'addr4', 'addr5', 'postcode', '', '', 'status', '', '', '', '', 'phone'),
            ),
        ),
    );

    /**
     * Static config for Scotland- note that any 'url' elements that do not begin with http will have $base_url prepended.
     *
     * @var array
     */
    private static $file_config_scotland = array(
        'full' => array(
            'gp' => array(
                'url' => 'scotgp',
                'fields' => array('code', 'first_name', 'last_name', '', 'addr1', 'addr2', 'addr3', 'addr4', 'addr5', 'postcode', '', '', 'status', '', '', '', '', 'phone'),
            ),
            'practice' => array(
                'url' => 'scotprac',
                'fields' => array('code', 'name', '', '', 'addr1', 'addr2', 'addr3', 'addr4', 'addr5', 'postcode', '', '', 'status', '', '', '', '', 'phone'),
            ),
            'optom' => array(
                'url' => 'eoptsite',
                'fields' => array('code', 'name', '', '', 'addr1', 'addr2', 'addr3', 'addr4', 'addr5', 'postcode'),
            ),
        ),
    );

    /**
     * Static config for Northern Ireland - note that any 'url' elements that do not begin with http will have $base_url prepended.
     *
     * @var array
     */
    private static $file_config_ni = array(
        'full' => array(
            'gp' => array(
                'url' => 'ngpcur',
                'fields' => array('code', 'name', '', '', 'addr1', 'addr2', 'addr3', 'addr4', 'addr5', 'postcode', '', '', 'status', '', '', '', '', 'phone'),
            ),
            'practice' => array(
                'url' => 'npraccur',
                'fields' => array('code', 'name', '', '', 'addr1', 'addr2', 'addr3', 'addr4', 'addr5', 'postcode', '', '', 'status', '', '', '', '', 'phone'),
            ),
            'optom' => array(
                'url' => 'eoptsite',
                'fields' => array('code', 'name', '', '', 'addr1', 'addr2', 'addr3', 'addr4', 'addr5', 'postcode'),
            ),
        ),
    );

    private $files = array();

    public function __construct()
    {
        $this->path = Yii::app()->params['hscic']['data']['path'];
        $this->tempPath = Yii::app()->params['hscic']['data']['temp_path'];

        if (!file_exists($this->path)) {
            mkdir($this->path, 0777, true);
        }

        if (!file_exists($this->tempPath)) {
            mkdir($this->tempPath, 0777, true);
        }
        parent::__construct(null, null);
    }

    /***
     * Sets default options for curl (proxy, followlocation, etc)
     * @param $curl an initialised curl object
     */
    private function setCurlOpts($curl)
    {
        $base_scheme = strtolower(parse_url(curl_getinfo($curl)['url'])['scheme']);

        // Deal with proxy (uses system proxy unless overidden by curl_proxy parameter)
        $curlproxy = Yii::app()->params['curl_proxy'];
        if (!empty($curlproxy)) {
            $urlParts = parse_url($curlproxy);
            $proxyURL = $urlParts['scheme'] . "://" . $urlParts['host'] . (!empty($urlParts['path']) ? "/" . $urlParts['path'] : "");
            $proxyPort = $urlParts['port'] + 0;
            curl_setopt($curl, CURLOPT_PROXY, $proxyURL);
            curl_setopt($curl, CURLOPT_PROXYPORT, $proxyPort);
            echo "USING PROXY '" . $curlproxy . "\n";
        } elseif ($base_scheme == "http" && !empty(getenv('http_proxy'))) {
            echo "Using system http_proxy:: '" . getenv('http_proxy') . "'\n";
        } elseif ($base_scheme == "https" && !empty(getenv('https_proxy'))) {
            echo "Using system https_proxy:: '" . getenv('https_proxy') . "'\n";
        }

        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        return $curl;
    }

    private function getDynamicUrls()
    {
        echo "Identifying dynamic file URLs...\n";
        $error_message = null;

        // Set url for the relevant region's files (default is England)
        switch ($this->regionName) {
            case "scotland":
                $services_path = "/services/organisation-data-service/data-downloads/home-countries";
                $other_path = null;
                $misc_path = '/services/organisation-data-service/data-downloads/miscellaneous';
                $file_config = static::$file_config_scotland;
                break;
            case "ni":
                $services_path = "/services/organisation-data-service/data-downloads/home-countries";
                $other_path = null;
                $misc_path = '/services/organisation-data-service/data-downloads/miscellaneous';
                $file_config = static::$file_config_ni;
                break;
            case "england":
            default:
                $services_path = "/services/organisation-data-service/data-downloads/gp-and-gp-practice-related-data";
                $other_path = '/services/organisation-data-service/data-downloads/other-nhs-organisations';
                $misc_path = '/services/organisation-data-service/data-downloads/miscellaneous';
                $file_config = static::$file_config_england;
                break;
        }


        echo "Downloading from: " . static::$base_url . $services_path . "\n";

        $curl = curl_init(static::$base_url . $services_path);
        $this->setCurlOpts($curl);
        $output = curl_exec($curl);

        if (curl_errno($curl)) {
            $error_message = 'Curl error: ' . curl_errno($curl);
        } else {
            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($status != 200) {
                $error_message = 'Bad Status Code: ' . $status;
            }
        }
        curl_close($curl);

        if ($error_message) {
            throw new Exception($error_message, static::$DOWNLOAD_FAILED);
        }

        if ($other_path) {
            echo "Downloading from: " . static::$base_url . $other_path . "\n";
            $curl = curl_init(static::$base_url . $other_path);
            $this->setCurlOpts($curl);
            $output2 = curl_exec($curl);

            if (curl_errno($curl)) {
                $error_message = 'Curl error: ' . curl_errno($curl);
            } else {
                $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                if ($status != 200) {
                    $error_message = 'Bad Status Code: ' . $status;
                }
            }
            curl_close($curl);

            if ($error_message) {
                throw new Exception($error_message, static::$DOWNLOAD_FAILED);
            }
        } else {
            $output2 = null;
        }

        if ($misc_path) {
            echo "Downloading from: " . static::$base_url . $misc_path . "\n";
            $curl = curl_init(static::$base_url . $misc_path);
            $this->setCurlOpts($curl);
            $output3 = curl_exec($curl);

            if (curl_errno($curl)) {
                $error_message = 'Curl error: ' . curl_errno($curl);
            } else {
                $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                if ($status != 200) {
                    $error_message = 'Bad Status Code: ' . $status;
                }
            }
            curl_close($curl);

            if ($error_message) {
                throw new Exception($error_message, static::$DOWNLOAD_FAILED);
            }
        } else {
            $output3 = null;
        }

        $this->files = $this->mapFileConfig($file_config, $output . $output2 . $output3);
    }

    /**
     * @param $config
     * @return array
     */
    private function mapFileConfig($config, $output)
    {
        $struct = array();
        foreach ($config as $k => $v) {
            if (is_array($v)) {
                $struct[$k] = $this->mapFileConfig($v, $output);
            } else {
                switch ((string)$k) {
                    case 'url':
                        if (preg_match('~href="(.*?/' . $v . '\.zip)"~', $output, $match)) {
                            echo "Found match for $v: $match[1]\n";
                            $struct[$k] = $match[1];
                        } else {
                            throw new Exception("Could not find match for $v", static::$DOWNLOAD_FAILED);
                        }
                        break;
                    default:
                        $struct[$k] = $v;
                }
            }
        }
        return $struct;
    }

    /**
     * Returns the command name/short description.
     *
     * @return string
     */
    public function getName()
    {
        return 'HSCIC data download and import Command.';
    }

    /**
     * Displaying the help if no action or param passed.
     */
    public function actionIndex()
    {
        $this->getHelp();
    }

    /**
     * @return void
     */
    public function getHelp()
    {
        echo <<<EOH


HSCIC data downloader and importer

The command downloads (from HSCIC website) and processes zipped CSV files
http://systems.hscic.gov.uk/data/ods/datadownloads/gppractice

USAGE
  yiic.php processhscicdata [action] [parameter]

Following actions are available:

 - download     [--type --interval] : downloads a specific file based on the given type (e.g.: GP) and interval (e.g.: full)

 - downloadall                      : downloads all the full files, GP, Optom, Practice, CCG, CCG Assignment

 - import       [--type --interval] : Importing a specific file based on the given type and iterval

 - importall                        : imports all the full files, GP, Optom, Practice, CCG, CCG Assignment

 - checkremovedfromfile  [--type]   : Checking if a database row no longer exists in the file, and if it's the case, we set the status inactive
                                      Supported types : GP and Practice

Available intervals by type :
    gp              : full|quarterly|monthly
    optom           : full
    practice        : full
    ccg             : full
    ccgAssignment   : full

Following parameters are available:

 - force  : Force import for the give file, even if it was already processed before
 - audit  : Do not generate audit message (can be useful for the first run, we do not need 78000 'GP imported' audit message)
            Usage: --audit=false
 - url    : Override the default URL, e.g. to process a specific monthly file
            Usage: --url=http://systems.hscic.gov.uk/data/ods/datadownloads/monthamend/december/egpam.zip
 - timeout : Set the connection timeout value downloading a file (defaults to 30 seconds)
 - region : Change between england, scotland, and ni files (note CCGs are only available in england)

EXAMPLES
 * yiic.php processhscicdata download --type=practice --interval=full
   Downloads the full Practice file

 * yiic.php processhscicdata download --type=optom --interval=full
   Downloads the full Optom file

 * yiic.php processhscicdata download --type=gp --interval=monthly
   Downloads the monthly Gp file

 * yiic.php processhscicdata import --type=gp --interval=full --force
   Importing the full GP file, forcing it as it was already processed

 * yiic.php processhscicdata import --type=gp --interval=quarterly --audit=false
   Importing the quarterly GP file without generating audit message

 * yiic.php processhscicdata checkremovedfromfile --type=gp
   Compares the full GP files with database and set models to inactive which are missing from the file

EOH;
    }


    /**
     * Downloads data file from url and imports it.
     *
     * @param string $url Url to the data file
     * @param string $type gp|optom|Practice|Ccg|CcgAssignment
     * @param string $region scotland|england|ni
     * @param string $interval full|monthly|quarterly
     */

    public function actionDownloadAndImportFromUrl($url, $type, $region, $interval = 'full')
    {
        $pathParts = pathinfo($this->getFileFromUrl($url));
        $permanentFile = $this->path . '/' . $pathParts['filename'] . '/' . $pathParts['basename'];
        $permanentDirectory = $this->path . '/' . $pathParts['filename'];

        if (!file_exists($permanentDirectory)) {
            mkdir($permanentDirectory, 0777, true);
        }
        $this->download($url, $permanentFile);
        switch ($region) {
            case "scotland":
                $this->files = static::$file_config_scotland;
                break;
            case "ni":
                $this->files = static::$file_config_ni;
                break;
            case "england":
                $this->files = static::$file_config_england;
                break;
            default:
                throw new Exception("Can't find file config for region: $region", static::$UNKNOWN_ERROR);
        }
        $this->processCSV($type, $interval, $this->files[$interval][$type]['fields'], $permanentFile);
    }

    /**
     * imports a specific file based on the given type and interval
     * eg.: ProcessHscicData import --type=Gp --interval=monthly.
     *
     * @param string $type gp|optom|Practice|Ccg|CcgAssignment
     * @param string $interval full|monthly|quarterly
     */
    public function actionImport($type, $interval = 'full', $region = 'england')
    {
        $this->regionName = strtolower($region);
        $this->getDynamicUrls();

        if (!isset($this->files[$interval])) {
            $this->usageError("Interval not found: $interval");
        } elseif (!isset($this->files[$interval][$type])) {
            $this->usageError("Type not found: $type");
        } else {
            try {
                $this->processFile($type, $interval, $this->files[$interval][$type]);
            } catch (Exception $e) {
                return $this->handleException($e);
            }
        }
    }

    /**
     * Simple routine for consistently handling generated Exceptions.
     *
     * @param $e
     *
     * @return mixed
     */
    protected function handleException(Exception $e)
    {
        echo $e->getMessage();

        return $e->getCode() ?: static::$UNKNOWN_ERROR;
    }

    /**
     * Imports all the full files listed in $this->files['full'], Gp, Optom, Practice, CCG, CCG Assignment.
     */
    public function actionImportall($region = 'england')
    {
        $this->regionName = strtolower($region);
        $this->getDynamicUrls();

        try {
            foreach ($this->files['full'] as $type => $file) {
                $this->processFile($type, 'full', $file);
            }
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Checks if the newly downloaded file is already processed or not
     * by comparing to the previously downloaded one.
     *
     * @param type $tempFile freshly downloaded in the /tmp directory
     * @param type $permanentFile already processed one {$permanentFile}/{$permanentFile}.zip
     *
     * @return bool true if no already processed file found or the new file differ from the previous one in md5 hash
     */
    private function isNewResourceFile($tempFile, $permanentFile)
    {
        $isNewResource = false;

        if (!file_exists($tempFile)) {
            $this->usageError('File not found: ' . $tempFile);
        } elseif (!file_exists($permanentFile)) {
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
     * Opens the zip file and gets the CSV file pointer and returns it.
     * Using this function for some reasons causes the stream to be closed
     *
     * @param string $file path and filename
     *
     * @return resource a file pointer (resource)
     *
     * @throws Exception if fails open the zip or fails to extract the CSV file
     */
    private function getFilePointer($file)
    {
        $pathInfo = pathinfo($file);

        $zip = new ZipArchive();
        if (($res = $zip->open($file)) !== true) {
            throw new Exception("Failed to open zip file '{$file}': " . $res, static::$UNEXPECTED_FILE_PROBLEM);
        }

        $fileName = preg_replace('/\d+/', '', str_replace('.zip', '.csv', $pathInfo['basename']));

        if (!($stream = $zip->getStream($fileName))) {
            throw new Exception("Failed to extract '{$fileName}' from zip file at '{$file}'", static::$UNEXPECTED_FILE_PROBLEM);
        }

        return $stream;
    }

    /**
     * Gets the line count of the CSV file in the zip.
     *
     * @param string $file file pathe and name
     *
     * @return int line count
     *
     * @throws Exception if fails open the zip or fails to extract the CSV file
     */
    private function getLineCountFromZip($file)
    {
        $pathInfo = pathinfo($file);

        $zip = new ZipArchive();
        if (($res = $zip->open($file)) !== true) {
            $this->usageError("Failed to open zip file '{$file}': " . $res);
        }

        $fileName = preg_replace('/\d+/', '', str_replace('.zip', '.csv', $pathInfo['basename']));

        if (!($stream = $zip->getStream($fileName))) {
            throw new Exception("Failed to extract '{$fileName}' from zip file at '{$file}'", static::$UNEXPECTED_FILE_PROBLEM);
        }

        $lineCount = 0;
        while (fgets($stream) !== false) {
            ++$lineCount;
        }
        $zip->close();

        return $lineCount;
    }

    /**
     * Checks if the destination folder exists (makes it if not) than copies the file.
     *
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
     * Processing the given file.
     *
     * @param string $type like 'Gp'
     * @param string $interval full|monthly|quarterly
     * @param array $file (based on self::files['full']['Gp'])
     */
    private function processFile($type, $interval, $file)
    {
        echo "\n";

        $pathParts = pathinfo($this->getFileFromUrl($file['url']));

        $permanentFile = $this->path . '/' . $pathParts['filename'] . '/' . $pathParts['basename'];
        $tempFile = $this->tempPath . '/' . $pathParts['basename'];

        // check if the current file(url) is already processed or not
        if ($this->isNewResourceFile($tempFile, $permanentFile)) {
            echo 'Processing ' . $pathParts['basename'] . "\n";

            $this->tempToPermanent($tempFile, $permanentFile);

            $this->processCSV($type, $interval, $file['fields'], $permanentFile);
        } else {
            echo $type . ' - ' . basename($permanentFile) . " is already processed\n";
        }
    }

    /**
     * Gets the zip file, extracts the CSV file (from the zip) and processes it.
     *
     * @param string $type like 'Gp'
     * @param string $interval full|monthly|quarterly
     * @param array $fields
     * @param string $file the zip file
     */
    private function processCSV($type, $interval, $fields, $file)
    {
        $lineCount = $this->getLineCountFromZip($file);

        $pathInfo = pathinfo($file);
        $zip = new ZipArchive();
        $res = $zip->open($file);

        if ($res !== true) {
            throw new Exception("Failed to open zip file '{$file}': " . $res, static::$UNEXPECTED_FILE_PROBLEM);
        }

        $fileName = preg_replace('/\d+/', '', str_replace('.zip', '.csv', $pathInfo['basename']));

        //Stream needs to be opened at the same function where it's otherwise it will cause an error
        $stream = $zip->getStream($fileName);

        if (!$stream) {
            throw new Exception("Failed to extract '{$fileName}' from zip file at '{$file}'", static::$UNEXPECTED_FILE_PROBLEM);
        }

        $this->pcu = new PostCodeUtility();
        $this->countryId = Country::model()->findByAttributes(array('code' => 'GB'))->id;
        $this->cbtId = CommissioningBodyType::model()->findByAttributes(array('shortname' => 'CCG'))->id;

        echo "Type: $type\n";
        echo "Total rows : $lineCount\n";

        echo 'Progress :          ';

        $i = 1;
        while (($row = fgetcsv($stream))) {
            $percent = round((($i / $lineCount) * 100), 1);

            echo "\033[7D"; // 7 char back
            echo str_pad($percent, 5, ' ', STR_PAD_LEFT) . ' %';

            $data = array_combine(array_pad($fields, count($row), ''), $row);
            $transaction = Yii::app()->db->beginTransaction();
            try {
                if ($type == 'gp' && ($interval == 'monthly' || $interval == 'quarterly')) {
                    // Monthly and quarterly files contain both GPs and Practices.
                    if (preg_match('/^G\d{7}$/', $data['code'])) {
                        $this->importGp($data);
                    } elseif (preg_match('/^([A-Z]\d{5}|[A-Z]{3}\d{3})$/', $data['code'])) {
                        $this->importPractice($data);
                    } else {
                        throw new Exception("Unknown code format: {$data['code']}", static::$UNRECOGNISED_CODE);
                    }
                } else {
                    $this->{"import{$type}"}($data);
                }
                $transaction->commit();
            } catch (Exception $e) {
                $message = "Error processing {$type} row:\n" . CVarDumper::dumpAsString($row) . "\n$e";
                Yii::log($message, CLogger::LEVEL_ERROR);
                echo "\n$message\n";
                $transaction->rollback();
                throw $e;
            }
            ++$i;
        }
        echo "\n";
    }

    /**
     * Imports the 'Gp' CSV file.
     *
     * @param array $data
     */
    private function importGp(array $data)
    {
        if (!($gp = Gp::model()->findbyAttributes(array('nat_id' => $data['code'])))) {
            $gp = new Gp();
            $gp->nat_id = $data['code'];
        }

        $is_new_record = $gp->isNewRecord;

        $gp->is_active = $data['status'] == 'A' || $data['status'] == 'P' ? '1' : '0';

        if ($gp->saveOnlyIfDirty()->save()) {
            if ($this->audit !== 'false') {
                Audit::add('ProcessHscicDataCommand', ($is_new_record ? 'Insert' : 'Update') . \SettingMetadata::model()->getSetting('gp_label'));
            }
        }

        $contact = $gp->contact;
        $contact->primary_phone = $data['phone'];

        /*
         * First checks if first name and surname are defined seperately. If not uses regex to get them from fullname.
         *
         * Regexp
         * the first part match a word (any number of char, no whithespace)
         * than (after the first word) can follow any number of whitepace
         * and for the last part the string must end 1 to 4 characters[A-Z]
         *
         * The goal is to extract the name initials from the field and use as a first name with the doctor title.
         *
         * Examples (egpam.zip): WELLINGS D, DONOGHUE CA, COLLOMBON MPM
         */
        if (isset($data['first_name']) && isset($data['last_name'])) {
            $contact->title = 'Dr';
            $contact->first_name = $data['first_name'];
            $contact->last_name = $data['last_name'];
        } elseif (preg_match("/^([\S]+)\s+([A-Z]{1,4})$/i", trim($data['name']), $m)) {
            $contact->title = 'Dr';
            $contact->first_name = $m[2];
            $contact->last_name = $this->tidy($m[1]);
        } else {
            $contact->last_name = $data['name'];
        }

        $contact->contact_label_id = \ContactLabel::model()->find('name=:name', array(':name' => 'General Practitioner'))->id;

        $is_new_record = $contact->isNewRecord;
        // setup the scenario to skip some of the validation to ensure all the data is imported
        $contact->setScenario(self::SCENARIO);
        if ($contact->saveOnlyIfDirty()->save()) {
            if ($this->audit !== 'false') {
                Audit::add('ProcessHscicDataCommand', ($is_new_record ? 'Insert' : 'Update') . ' ' . \SettingMetadata::model()->getSetting('gp_label') . '-Contact');
            }
        }

        if (!($address = $contact->address)) {
            $address = new Address();
            $address->contact_id = $contact->id;
        }
        $this->importAddress(
            $address,
            array(
                'addr1' => $data['addr1'],
                'addr2' => $data['addr2'],
                'addr3' => $data['addr3'],
                'addr4' => $data['addr4'],
                'addr5' => $data['addr5']
            )
        );
        $address->postcode = $data['postcode'];
        $address->country_id = $this->countryId;

        $is_new_record = $address->isNewRecord;

        if ($address->saveOnlyIfDirty()->save()) {
            if ($this->audit !== 'false') {
                Audit::add('ProcessHscicDataCommand', ($is_new_record ? 'Insert' : 'Update') . ' ' . \SettingMetadata::model()->getSetting('gp_label') . '-Address');
            }
        }

        $gp = null;
    }

    /**
     * Imports the 'Optom' CSV file.
     *
     * @param array $data
     */
    private function importOptom($data)
    {
        $contact = \Contact::model()->findbyAttributes(array('national_code' => $data['code'])) ?? new \Contact();
        $contact_label_id = \ContactLabel::model()->findbyAttributes(array('name' => 'Optometrist'))->id ?? \ContactLabel::model()->findbyAttributes(array('name' => 'Optician'))->id;

        $is_new_record = $contact->isNewRecord;

        $contact->national_code = $data['code'];
        $contact->first_name = $data['name'];
        $contact->contact_label_id = $contact_label_id;

        // setup the scenario to skip some of the validation to ensure all the data is imported
        $contact->setScenario(self::SCENARIO);
        if ($contact->saveOnlyIfDirty()->save()) {
            if ($this->audit !== 'false') {
                Audit::add('ProcessHscicDataCommand', ($is_new_record ? 'Insert' : 'Update') . ' Optom-Contact');
            }
        }

        if (!($address = $contact->address)) {
            $address = new Address();
            $address->contact_id = $contact->id;
        }
        $is_new_record = $address->isNewRecord;
        $this->importAddress(
            $address,
            array(
                'addr1' => $data['addr1'],
                'addr2' => $data['addr2'],
                'addr3' => $data['addr3'],
                'addr4' => $data['addr4'],
                'addr5' => $data['addr5']
            )
        );
        $address->postcode = $data['postcode'];
        $address->country_id = $this->countryId;

        if ($address->saveOnlyIfDirty()->save()) {
            if ($this->audit !== 'false') {
                Audit::add('ProcessHscicDataCommand', ($is_new_record ? 'Insert' : 'Update') . ' Optom-Address');
            }
        }
    }

    /**
     * Imports the 'Practice' CSV file.
     *
     * @param array $data
     */
    private function importPractice($data)
    {
        $practice = Practice::model()->findByAttributes(array('code' => $data['code']));
        if (!$practice) {
            $practice = new Practice();
            $practice->code = $data['code'];
        }
        $is_new_record = $practice->isNewRecord;

        $practice->is_active = $data['status'] == 'A' || $data['status'] == 'P' ? '1' : '0';

        $practice->phone = $data['phone'];

        if ($practice->saveOnlyIfDirty()->save()) {
            if ($this->audit !== 'false') {
                Audit::add('ProcessHscicDataCommand', ($is_new_record ? 'Insert' : 'Update') . ' Practice');
            }
        }

        $contact = $practice->contact;
        $contact->primary_phone = $practice->phone;

        $is_new_record = $contact->isNewRecord;
        // setup the scenario to skip some of the validation to ensure all the data is imported
        $contact->setScenario(self::SCENARIO);
        if ($contact->saveOnlyIfDirty()->save()) {
            if ($this->audit !== 'false') {
                Audit::add('ProcessHscicDataCommand', ($is_new_record ? 'Insert' : 'Update') . ' Practice-Contact');
            }
        }

        if (!($address = $contact->address)) {
            $address = new Address();
            $address->contact_id = $contact->id;
        }
        $is_new_record = $address->isNewRecord;
        $this->importAddress(
            $address,
            array(
                'name' => $data['name'],
                'addr1' => $data['addr1'],
                'addr2' => $data['addr2'],
                'addr3' => $data['addr3'],
                'addr4' => $data['addr4'],
                'addr5' => $data['addr5']
            )
        );
        $address->postcode = $data['postcode'];
        $address->country_id = $this->countryId;

        if ($address->saveOnlyIfDirty()->save()) {
            if ($this->audit !== 'false') {
                Audit::add('ProcessHscicDataCommand', ($is_new_record ? 'Insert' : 'Update') . ' Practice-Address');
            }
        }
    }

    /**
     * Imports the 'Ccg' CSV file.
     *
     * @param array $data
     */
    private function importCcg(array $data)
    {
        if (!($ccg = CommissioningBody::model()->findByAttributes(array('code' => $data['code'], 'commissioning_body_type_id' => $this->cbtId)))) {
            $ccg = new CommissioningBody();
            $ccg->code = $data['code'];
            $ccg->commissioning_body_type_id = $this->cbtId;
        }
        $is_new_record = $ccg->isNewRecord;
        $ccg->name = $data['name'];

        if ($ccg->saveOnlyIfDirty()->save()) {
            if ($this->audit !== 'false') {
                Audit::add('ProcessHscicDataCommand', ($is_new_record ? 'Insert' : 'Update') . ' CCG');
            }
        }

        $contact = $ccg->contact;
        if (!($address = $contact->address)) {
            $address = new Address();
            $address->contact_id = $contact->id;
        }
        $is_new_record = $address->isNewRecord;
        $this->importAddress(
            $address,
            array(
                'addr1' => $data['addr1'],
                'addr2' => $data['addr2'],
                'addr3' => $data['addr3'],
                'addr4' => $data['addr4'],
                'addr5' => $data['addr5']
            )
        );
        $address->postcode = $data['postcode'];
        $address->country_id = $this->countryId;

        if ($address->saveOnlyIfDirty()->save()) {
            if ($this->audit !== 'false') {
                Audit::add('ProcessHscicDataCommand', ($is_new_record ? 'Insert' : 'Update') . ' CCG-Address');
            }
        }
    }

    /**
     * Imports the 'CcgAssignment' file.
     *
     * @param array $data
     *
     * @throws Exception If Failed to save commissioning body assignment
     */
    private function importCcgAssignment(array $data)
    {
        $practice = Practice::model()->findByAttributes(array('code' => $data['practice_code']));
        $ccg = CommissioningBody::model()->findByAttributes(array('code' => $data['ccg_code'], 'commissioning_body_type_id' => $this->cbtId));

        if (!$practice || !$ccg) {
            return;
        }

        $found = false;
        foreach ($practice->commissioningbodyassigments as $assignment) {
            if ($assignment->commissioning_body_id == $ccg->id) {
                $found = true;
            } else {
                if ($assignment->commissioning_body->commissioning_body_type_id == $this->cbtId) {
                    if ($assignment->delete() && $this->audit !== 'false') {
                        Audit::add('ProcessHscicDataCommand', 'Assignment Deleted');
                    }
                }
            }
        }

        if (!$found) {
            $assignment = new CommissioningBodyPracticeAssignment();
            $assignment->commissioning_body_id = $ccg->id;
            $assignment->practice_id = $practice->id;

            if (!$assignment->save()) {
                throw new Exception('Failed to save commissioning body assignment: ' . print_r($assignment->errors, true));
            }
            if ($this->audit !== 'false') {
                Audit::add('ProcessHscicDataCommand', 'Assignment Saved');
            }
        }
    }

    /**
     * Imports the Address.
     *
     * @param Address $address
     * @param array $lines
     */
    private function importAddress(Address $address, array $lines)
    {
        // capitalised each item, but not removing empty item
        $lines = array_map(array($this, 'tidy'), $lines);
        // if the lines['addr1'] started with alphabets, assign it to address->address1
        // otherwise check if there is any name available
        if (preg_match('/^[A-Za-z]+/i', $lines['addr1'])) {
            $address1 = $lines['addr1'];
            $address2 = "{$lines['addr2']}\n{$lines['addr3']}";
        } else {
            $address1 = $lines['addr1'];
            $address2 = "{$lines['addr2']}\n{$lines['addr3']}";
            if (isset($lines['name']) && strpos(strtolower($lines['addr1']), strtolower($lines['name'])) === false) {
                $address1 = $lines['name'];
                $address2 = "{$lines['addr1']}\n{$lines['addr2']}\n{$lines['addr3']}";
            }
        }

        $address->address1 = $address1;
        $address->county = $lines['addr5'];

        // sometimes the addr4 contains numbers, which will fail the validation
        // if addr4 contains number, append to address2
        // otherwise assign it to city field
        if (preg_match('~[0-9]~', $lines['addr4'])) {
            $address2 .= "\n{$lines['addr4']}";
        } else {
            $address->city = $lines['addr4'];
        }
        $address->address2 = $address2;

        $lines = null;
    }

    /**
     * Transform the address line to uppercase all the words.
     *
     * @param string $string
     *
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
     * Checking if a database row no longer exists in the file, and if it's the case, we set the status inactive.
     *
     * @param type $type
     */
    public function actionCheckRemovedFromFile($type = 'gp')
    {
        if (!isset($this->files['full'][$type]['url']) || ($type != 'gp' && $type != 'practice')) {
            $this->usageError("Invalid type: $type");
        }

        try {
            $dbTable = $this->getTableNameByType($type);

            $this->createTempTable($dbTable);

            $file = $this->getFileFromUrl($this->files['full'][$type]['url']);
            $this->fillTempTable($type, $file);

            $this->markInactiveMissingModels($dbTable);

            // drop temp table
            $query = "DROP TABLE if exists temp_$dbTable;";
            Yii::app()->db->createCommand($query)->execute();
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Creating temporary table for CheckRemovedFromFile() method.
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

        echo 'Creating temp table... ';
        \Yii::app()->db->createCommand($query)->execute();
        echo "OK\n";
    }

    /**
     * Returns the database table name based on the give type.
     *
     * @param string $type gp|practice|ccg
     *
     * @return string database table name
     */
    private function getTableNameByType($type)
    {
        switch ($type) {
            case 'gp':
            case 'practice':
                $dbTable = $type;
                break;
            case 'ccg':
                $dbTable = 'commissioning_body';
                break;
            default:
                $this->usageError("Invalid type: $type");
                break;
        }

        return $dbTable;
    }

    /**
     * Fill temp table for CheckRemovedFromFile() method.
     *
     * @param string $type GP
     * @param type $file
     */
    private function fillTempTable($type, $file)
    {
        $dbTable = $this->getTableNameByType($type);

        $pathInfo = pathinfo($file);

        $zip = new ZipArchive();
        $res = $zip->open($file);

        if ($res !== true) {
            throw new Exception("Failed to open zip file '{$file}': " . $res, static::$UNEXPECTED_FILE_PROBLEM);
        }

        $fileName = preg_replace('/\d+/', '', str_replace('.zip', '.csv', $pathInfo['basename']));

        $stream = $zip->getStream($fileName);

        if (!$stream) {
            throw new Exception("Failed to extract '{$fileName}' from zip file at '{$file}'", static::$UNEXPECTED_FILE_PROBLEM);
        }


        echo 'Inserting rows into temp table... ';

        $i = 0;
        $insertBulkData = array();
        while (($row = fgetcsv($stream))) {
            $data = array_combine(array_pad($this->files['full'][$type]['fields'], count($row), ''), $row);

            if ($dbTable == 'gp') {
                $insertData = array(
                    'nat_id' => $data['code'],
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
     * Set status to inactive on models missing from the CSV file.
     *
     * @param type $type GP
     */
    private function markInactiveMissingModels($type)
    {
        $dbTable = $this->getTableNameByType($type);
        $column = $dbTable == 'gp' ? 'nat_id' : 'code';

        $criteria = new CDbCriteria();
        $criteria->select = array('t.*');
        $criteria->join = "LEFT JOIN temp_$dbTable ON t.$column = temp_$dbTable.$column";
        $criteria->addCondition("temp_$dbTable.$column IS NULL");
        $criteria->addCondition('t.is_active = 1');

        $modelName = ucfirst($dbTable);
        $not_in_file = $modelName::model()->findAll($criteria);

        echo 'Set ' . count($not_in_file) . " $type to inactive... ";
        foreach ($not_in_file as $removed_instance) {
            $removed_instance->is_active = '0';
            if ($removed_instance->save() && $this->audit !== 'false') {
                Audit::add('ProcessHscicDataCommand', "$type ({$removed_instance->$column}) set to inactive");
            }
        }
        echo "OK\n\n";
    }

    /**
     * Assamble the file path and name from the url.
     *
     * @param string $url
     *
     * @return string the path and file name
     */
    private function getFileFromUrl($url)
    {
        $urlParts = parse_url($url);
        $pathParts = pathinfo($urlParts['path']);

        return $pathParts['basename'];
    }

    /***    Download HSCIC Data    ***/

    /**
     * Allows to download a specified file based on the type and interval.
     *
     * @param string $type like 'Gp'
     * @param string $interval like 'monthly'
     */
    public function actionDownload($type, $interval = 'full', $region = 'england')
    {
        $this->regionName = strtolower($region);
        $this->getDynamicUrls();

        if (!isset($this->files[$interval])) {
            $this->usageError("Interval not found: $interval");
        } elseif (!isset($this->files[$interval][$type])) {
            $this->usageError("\n$type has no $interval file");
        } else {
            try {
                $fileName = $this->getFileFromUrl($this->url == '' ? $this->files[$interval][$type]['url'] : $this->url);
                $this->download($this->url == '' ? $this->files[$interval][$type]['url'] : $this->url, $this->tempPath . '/' . $fileName);
            } catch (Exception $e) {
                return $this->handleException($e);
            }
        }
    }

    /**
     * Downloads all the full files listed in $this->files['full'] , Gp, Optom, Practice, CCG, CCG Assignment
     * can be useful on the first run.
     */
    public function actionDownloadall($region = 'england')
    {
        $this->regionName = strtolower($region);
        $this->getDynamicUrls();

        try {
            foreach ($this->files['full'] as $file) {
                $fileName = $this->getFileFromUrl($file['url']);
                $this->download($file['url'], $this->tempPath . '/' . $fileName);
            }
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Downloads the file(url) and puts to the provided path/filename.
     *
     * @param type $url
     * @param type $file
     *
     * @throws Exception
     */
    private function download($url, $file)
    {
        echo "Downloading $url to $file\n";
        $error_message = null;

        $file_handler = fopen($file, 'w');

        $curl = curl_init($url);
        $this->setCurlOpts($curl);
        curl_setopt($curl, CURLOPT_FILE, $file_handler);
        curl_exec($curl);

        if (curl_errno($curl)) {
            $error_message = ' Curl error: ' . curl_errno($curl);
        } else {
            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($status != 200) {
                $error_message = ' Bad Status Code: ' . $status;
            }
        }
        curl_close($curl);

        fclose($file_handler);
        if ($error_message) {
            throw new Exception($error_message, static::$DOWNLOAD_FAILED);
        }

        if (!is_readable($file) || !filesize($file)) {
            throw new Exception('Downloaded file is empty/unreadable', static::$DOWNLOAD_EMPTY);
        }

        echo ' ... OK';
        echo "\n";
    }
}
