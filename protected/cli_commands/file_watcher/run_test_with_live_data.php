<?php

include_once './connectDatabase.php';
include_once './fileWatcherConfig.php';
include_once './loggerClass.php';

$dicomConfig['biometry']['inputFolder'] = '/home/iolmaster/test/testsubdir';
$dir_res = opendir($dicomConfig['biometry']['inputFolder']);
$mysqli = connectDatabase();

$allFilesQ = $mysqli->query('SELECT * FROM dicom_files ');
$allfiles = array();
foreach ($allFilesQ as $fileEntry) {
    $allfiles[$fileEntry['filename']]['filesize'] = $fileEntry['filesize'];
    $allfiles[$fileEntry['filename']]['filedate'] = $fileEntry['filedate'];
}
var_dump($allfiles['/home/iolmaster/test/testsubdir/699653/2673963']['filesize']);
var_dump($allfiles['/home/iolmaster/test/testsubdir/699653/2673963']['filedate']);
//die;

processDir($dir_res, $dicomConfig['biometry']['inputFolder']);

function processDir($dirResource, $root)
{
    global $mysqli;
    while (false !== ($entry = readdir($dirResource))) {
        if ($entry != '.' && $entry != '..') {
            if (is_dir($root.'/'.$entry)) {
                $dirToProcess = opendir($root.'/'.$entry);
                processDir($dirToProcess, $root.'/'.$entry);
            } elseif (is_file($root.'/'.$entry)) {
                $filedata = stat($root.'/'.$entry);
                if (!fileEntryExists($root.'/'.$entry, $filedata, $mysqli)) {
                    createFileEntry($root.'/'.$entry, $filedata, $mysqli);
                    $mysqli->query("INSERT INTO dicom_file_queue (filename, detected_date, last_modified_date, status_id) VALUES ('".$root.'/'.$entry."', now(), now(), (SELECT id FROM dicom_process_status WHERE name = 'new'))");
                }
            }
        }
    }
}

function fileEntryExists($fullfilename, $filedata, $mysqli)
{
    //var_dump($filedata);
    /*

    $checkQuery = $mysqli->query("SELECT id FROM dicom_files WHERE filename='".$fullfilename."' AND filesize ='".$filedata["size"]."' AND filedate='".date("Y-m-d H:i:s",$filedata["mtime"])."'");
    if($isFileEntryExists = $checkQuery->fetch_row()){
        return $isFileEntryExists[0];
    }else{
        return false;
    }

    */

    global $allfiles;
    if (isset($allfiles[$fullfilename])) {
        if ($allfiles[$fullfilename]['filesize'] == $filedata['size'] && $allfiles[$fullfilename]['filedate'] == date('Y-m-d H:i:s', $filedata['mtime'])) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function createFileEntry($fullfilename, $filedata, $mysqli)
{
    // todo: add insert here! :)
    $mysqli->query("INSERT INTO dicom_files (filename, filesize, filedate, processor_id) VALUES ('".$fullfilename."', '".$filedata['size']."','".date('Y-m-d H:i:s', $filedata['mtime'])."','".php_uname('n')."')");

    return $mysqli->insert_id;
}
