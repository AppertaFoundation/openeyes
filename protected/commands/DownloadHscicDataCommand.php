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

class DownloadHscicDataCommand extends CConsoleCommand
{
    public $tempPath = null;
    
    static private $files = array(
        'full' => array(
            'Gp' => array(
                'url' => 'http://systems.hscic.gov.uk/data/ods/datadownloads/data-files/egpcur.zip',
            ),
            'Practice' => array(
                'url' => 'http://systems.hscic.gov.uk/data/ods/datadownloads/data-files/epraccur.zip',
            ),
            'Ccg' => array(
                'url' => 'http://systems.hscic.gov.uk/data/ods/datadownloads/data-files/eccg.zip',
            ),
            'CcgAssignment' => array(
                'url' => 'http://systems.hscic.gov.uk/data/ods/datadownloads/data-files/epcmem.zip',
            ),
        ),
        'monthly' => array(
            'Gp' => array(
                'url' => 'http://systems.hscic.gov.uk/data/ods/datadownloads/monthamend/current/egpam.zip',
            ),
        ),
        'quarterly' => array(
            'Gp' => array(
                'url' => 'http://systems.hscic.gov.uk/data/ods/datadownloads/quartamend/current/egpaq.zip',
            )
        ),
    );
    
    function __construct()
    {
        $this->tempPath = Yii::app()->params['hscic']['data']['temp_path'];
             
        if (!file_exists($this->tempPath)) {
            mkdir($this->tempPath, 0777, true);
        }
        
        parent::__construct(null, null);
    }
    
    public function getName()
    {
        return 'HSCIC data download Command.';
    }
    
    /**
     * Downloads the urls listed under the self::$files['full']
     */
    public function actionIndex()
    {
        $this->getHelp();
    }
    
    public function getHelp()
    {        
        echo <<<EOH
        

HSCIC data downloader
        
USAGE \n  yiic.php downloadhscicdata [action] [parameter]
        
Following actions are available:
 - full
 - monthly
 - quarterly
 - import [--type=gp|practice|ccg|ccgAssignment --interval=full|monthly|quarterly] *for ccg and ccgAssignment only full file available


EOH;
    }
    
    /**
     * Downloads the urls listed under the self::$files['full']
     */
    public function actionFull()
    {
        foreach (self::$files['full'] as $file) {
            $fileName = $this->getFileFromUrl($file['url']);
            $this->download($file['url'], $fileName);
        }
    }
    
    /**
     * Downloads the urls listed under the self::$files['monthly']
     */
    public function actionMonthly()
    {
        foreach (self::$files['monthly'] as $file) {
            $fileName = $this->getFileFromUrl($file['url']);
            $this->download($file['url'], $fileName);
        }
    }
    
    /**
     * Downloads the urls listed under the self::$files['quarterly']
     */
    public function actionQuarterly()
    {
        foreach (self::$files['quarterly'] as $file) {
            $fileName = $this->getFileFromUrl($file['url']);
            $this->download($file['url'], $fileName);
        }
    }
    
    /**
     * Allows to download a specified file based on the type and interval
     * 
     * @param string $type like 'Gp'
     * @param string $interval like 'monthly'
     */
    public function actionDownload($type, $interval)
    {
        if( !isset(self::$files[$interval]) ){
            $this->usageError("Interval not found: $interval");
           
        } else if( !isset(self::$files[$interval][$type]) ){
            
            echo "Type not found: $type\n\n";
            $this->usageError("Type not found: $type");
            
        } else {
            $fileName = $this->getFileFromUrl(self::$files[$interval][$type]['url']);
            $this->download(self::$files[$interval][$type]['url'], $fileName);
        }      
    }
    
    /**
     * Downloads the file(url) and puts to the provided path/filename
     * 
     * @param type $url
     * @param type $file
     * @return bool tru if file is readabe and size > 0
     */
    private function download($url, $file)
    {
        echo "Downloading... " . basename($file);
        $file_handler = fopen($file, 'w');
        
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_FILE, $file_handler);
        curl_exec($curl);
        curl_close($curl);
        
        fclose($file_handler);
        $result = is_readable($file) && filesize($file);
        
        echo ($result ? ' ... OK' : '... ERROR');
        echo "\n";
        
        return $result;
    }
    
        
    /**
     * Assamble the file path and name from the url
     * @param string $url
     * @return string the path and file name
     */
    private function getFileFromUrl($url)
    {
        $urlParts = parse_url($url);
        $pathParts = pathinfo($urlParts['path']);
          
        return $this->tempPath . '/' . $pathParts['basename'];
    }
}