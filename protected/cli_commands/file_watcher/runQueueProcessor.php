<?php
    include_once './queueProcessorClass.php';
    include_once './connectDatabase.php';
    include_once './fileWatcherConfig.php';
    include_once './loggerClass.php';

    // TODO: change this to be able to process other type of input files, not just biometry

    $pidfile = '/tmp/DicomFileQueue.pid';
    $mysqli = connectDatabase();

    checkFileWatcher();

if (file_exists($pidfile)) {
    $currentPid = implode('', file($pidfile));
} else {
    $currentPid = 0;
}
    //exec(sprintf("%s > %s 2>&1 & echo $! >> %s", $cmd, $outputfile, $pidfile));

if ($currentPid > 0) {
    echo 'CurrentPID: '.$currentPid."\n";
    if (!isRunning($currentPid)) {
        saveMyPid($pidfile);
        $logger = new eventLogger($mysqli);
        processQueue($mysqli, $dicomConfig, $logger);
    } else {
        echo "Process still running! Exiting.\n";
        die;
    }
} else {
    saveMyPid($pidfile);
    processQueue($mysqli, $dicomConfig);
}

function checkFileWatcher()
{
    echo "Checking file watcher status...\n";
    $result = shell_exec('sudo service dicom-file-watcher status');
    echo 'result: '.$result;
    if (trim($result) == 'dicom-file-watcher stop/waiting' || (stripos($result, 'dicom-file-watcher.service') !== false && stripos($result, 'active (running)') === false)) {
        echo "File watcher is not running, trying to start...\n";
        shell_exec('sudo service dicom-file-watcher start');
        checkFileWatcher();
    }
}

function isRunning($pid)
{
    try {
        // get elapsed time
        $result = shell_exec(sprintf('ps -p %d -o etimes=', $pid));
        if ($result != '') {
            return true;
        }
    } catch (Exception $e) {
    }

    return false;
}

function saveMyPid($pidfile)
{
    $currentPid = getmypid();
    echo 'New PID file created: '.$pidfile.' : '.$currentPid."\n";
    $fh = fopen($pidfile, 'w');
    fwrite($fh, $currentPid);
    fclose($fh);
}

function processQueue($mysqli, $dicomConfig, $logger)
{
    $queueProcessor = new queueProcessor($mysqli, $dicomConfig['biometry']['importerCommand'], $logger);
    $queueProcessor->checkEntries();
}
