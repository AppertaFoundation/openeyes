<?php

include_once './connectDatabase.php';
include_once './fileWatcherConfig.php';
include_once './loggerClass.php';

// default is the non FAM solution
if (!isset($dicomConfig['FAM'])) {
    $dicomConfig['FAM'] = 0;
}

if ($dicomConfig['FAM'] == '1') {
    // we should be able to start more processes here!

    $fam_res = fam_open();
    $dir_res = fam_monitor_directory($fam_res, $dicomConfig['biometry']['inputFolder']);
    // if we have subdirectories we need the monitor collection instead of directory
    //$dir_res = fam_monitor_collection   ( $fam_res, $dicomConfig["biometry"]["inputFolder"], 1, 'dcm');
}

echo 'Monitoring '.$dicomConfig['biometry']['inputFolder']."\n\n";

if ($dicomConfig['FAM'] == '1') {
    checkExistingFiles();
}

while (true) {
    $newfile = false;
    $mysqli = connectDatabase();
    $logger = new eventLogger($mysqli);

    if ($dicomConfig['FAM'] == '1') {
        while (fam_pending($fam_res)) {
            $arr = fam_next_event($fam_res);

            // FAMCreated == 5
            if ($arr['code'] == 5) {
                echo 'New file arrived: '.$arr['filename']."\n";
                $newfile = true;
                $filedata = stat($dicomConfig['biometry']['inputFolder'].'/'.$arr['filename']);
                if (!$fileid = fileEntryExistsFAM($dicomConfig['biometry']['inputFolder'].'/'.$arr['filename'], $filedata, $mysqli)) {
                    $fileid = createFileEntry($dicomConfig['biometry']['inputFolder'].'/'.$arr['filename'], $filedata, $mysqli);
                }
                //var_dump("Fileid: ".$fileid);

                $mysqli->query("INSERT INTO dicom_file_queue (filename, detected_date, last_modified_date, status_id) VALUES ('".$dicomConfig['biometry']['inputFolder'].'/'.$arr['filename']."', now(), now(), (SELECT id FROM dicom_process_status WHERE name = 'new'))");
                $logger->addLogEntry($dicomConfig['biometry']['inputFolder'].'/'.$arr['filename'], 'new', basename($_SERVER['SCRIPT_FILENAME']));
                // add log entry
            } elseif ($arr['code'] == 1) {
                echo 'File has been changed: '.$arr['filename']."\n";
                echo "What we should do now? :)\n";
                //var_dump(stat($dicomConfig["biometry"]["inputFolder"]."/".$arr["filename"]));
            }
        }
    } else {
        unset($allFilesQ);
        unset($allfiles);
        $allFilesQ = $mysqli->query('SELECT * FROM dicom_files ');
        $allfiles = array();
        foreach ($allFilesQ as $fileEntry) {
            $allfiles[$fileEntry['filename']]['filesize'] = $fileEntry['filesize'];
            $allfiles[$fileEntry['filename']]['filedate'] = $fileEntry['filedate'];
        }
        $dir_res = opendir($dicomConfig['biometry']['inputFolder']);
        processDir($dir_res, $dicomConfig['biometry']['inputFolder'], $logger);
    }

    // something changed so we run the queue processor in the background
    if ($newfile) {
        //$logger->addLogEntry($dicomConfig["biometry"]["inputFolder"]."/".$arr["filename"], 'processing', basename($_SERVER["SCRIPT_FILENAME"]));
        exec('cd '.$dicomConfig['general']['PHPdir'].' && /usr/bin/php runQueueProcessor.php'.' > /dev/null 2>&1 &');
        echo "Queue Processor has been started successfully\n";
        unset($arr);
    }
    $mysqli->close();
    sleep(5);
}

function processDir($dirResource, $root, $logger)
{
    global $mysqli;
    global $newfile;
    //echo "Processing: ".$root."\n";
    while ($dirResource && false !== ($entry = readdir($dirResource))) {
        if ($entry != '.' && $entry != '..') {
            if (is_dir($root.'/'.$entry)) {
                $dirToProcess = opendir($root.'/'.$entry);
                processDir($dirToProcess, $root.'/'.$entry, $logger);
            } elseif (is_file($root.'/'.$entry)) {
                $filedata = stat($root.'/'.$entry);
                if (!fileEntryExists($root.'/'.$entry,  $filedata, $mysqli)) {
                    echo 'New file arrived: '.$entry."\n";
                    $newfile = true;
                    createFileEntry($root.'/'.$entry, $filedata, $mysqli);
                    $mysqli->query("INSERT INTO dicom_file_queue (filename, detected_date, last_modified_date, status_id) VALUES ('".$root.'/'.$entry."', now(), now(), (SELECT id FROM dicom_process_status WHERE name = 'new'))");
                    $logger->addLogEntry($root.'/'.$entry, 'new', basename($_SERVER['SCRIPT_FILENAME']));
                }
            }
        }
    }
}

/*
* This function is for read_dir solution
*/
function fileEntryExists($fullfilename, $filedata, $mysqli)
{
    global $allfiles;
    if (isset($allfiles[$fullfilename])) {
        if ($allfiles[$fullfilename]['filesize'] == $filedata['size']) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function fileEntryExistsFAM($fullfilename, $filedata, $mysqli)
{
    //var_dump($filedata);
    $checkQuery = $mysqli->query("SELECT id FROM dicom_files WHERE filename='".$fullfilename."' AND filesize ='".$filedata['size']."' AND filedate='".date('Y-m-d H:i:s', $filedata['mtime'])."'");
    if ($isFileEntryExists = $checkQuery->fetch_row()) {
        return $isFileEntryExists[0];
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

function checkNotProcessed($existing, $processed)
{
    $notFound = array();
    foreach ($existing as $filename => $fileData) {
        if (!(isset($processed[$filename])) || !($processed[$filename]['filesize'] == $fileData['size'] && $processed[$filename]['filedate'] == date('Y-m-d H:i:s', $fileData['mtime']))) {
            $notFound[$filename] = $fileData;
        }
    }

    return $notFound;
}

function checkExistingFiles()
{
    global $fam_res;
    global $logger;
    global $dicomConfig;

    $mysqli = connectDatabase();
    $newfile = false;
    while (fam_pending($fam_res)) {
        $arr = fam_next_event($fam_res);
        var_dump($arr);
        // FAMExists == 8
        if ($arr['code'] == 8) {
            if (is_file($dicomConfig['biometry']['inputFolder'].'/'.$arr['filename'])) {
                // we need file size and date data for all existing files
                $existingFilesData[$dicomConfig['biometry']['inputFolder'].'/'.$arr['filename']] = stat($dicomConfig['biometry']['inputFolder'].'/'.$arr['filename']);
            }
        }
    }

    if (isset($existingFilesData) && is_array($existingFilesData) && count($existingFilesData) > 0) {
        $processedFiles = array();
        $processedFilesQ = $mysqli->query('SELECT id,filename, filesize, filedate FROM dicom_files');
        while ($filerow = $processedFilesQ->fetch_assoc()) {
            $processedFiles[$filerow['filename']] = $filerow;
        }

        $notProcessed = checkNotProcessed($existingFilesData, $processedFiles);

        var_dump($notProcessed);

        foreach ($notProcessed as $filename => $filedata) {
            createFileEntry($filename, $filedata, $mysqli);
        }
    }
    $mysqli->close();
}

fam_close($fam_res);
