<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OEModule\OphCoCvi\controllers;

use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 * Description of PrintTestController
 *
 * @author Irvine
 */
class PrintTestController extends \BaseController
{
    
    public function accessRules(){
        return array(
            array('allow',
                'actions'       => array('test'),
                'roles'         => array('admin')
            ),
        );
    }
    
    public function actionTest(){
	$this->render("test");
        
        if(isset($_POST['test_print'])){
           
            $directory = realpath(__DIR__ . '/..').'/files';
           
            if($this->unzipFile($directory.'/example_certificate.odt', $directory.'/xml') === TRUE){
                $source = file_get_contents($directory.'/xml/content.xml');
                
                foreach($_POST as $key => $val){
                    $data = explode('_', $key, 2);
                   
                    $inputType = $data[0];
                    $field = $data[1];
                    
                    switch($inputType){
                        case 'radio':
                            $field .= '_'.$val;
                            $value = 'X';
                        break;
                        case 'textarea':
                            $htmlValue = nl2br($val);
                            
                            $arr = explode('<br />', $htmlValue);
                            
                            $value = '';
                            foreach($arr as $row){
                                $value .= '<text:line-break/>'.$row.'<text:s/>';
                            }
                            
                        break;
                        default:
                            $value = $val;
                    }
                    $source = str_replace('##'.$field.'##', $value, $source); 
                }
                
                $source = preg_replace('/##(.*?)##/i', "", $source);
                
                file_put_contents($directory.'/xml/content.xml', $source);
                
                if($this->zipFolder($directory.'/xml', '/var/www/openeyes/protected/runtime/document.odt') === TRUE){
                    exec('/usr/bin/libreoffice --headless --convert-to pdf --outdir /var/www/openeyes/protected/runtime/  /var/www/openeyes/protected/runtime/document.odt');
                }

            } else {
                var_dump("I am your father");
            }
        }
    }
    
    public function unzipFile( $zipInputFile, $outputFolder   ){
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
    
    public function zipFolder($inputFolder, $zipOutputFile) {
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
