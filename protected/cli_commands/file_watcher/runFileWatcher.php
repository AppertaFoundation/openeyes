<?php

include_once('./connectDatabase.php');
include_once('./fileWatcherConfig.php');

// maybe we can move this into the config file!!

// we should be able to start more processes here!

$fam_res = fam_open ();
$dir_res = fam_monitor_directory  ( $fam_res, $dicomConfig["biometry"]["inputFolder"]);
var_dump($dicomConfig["biometry"]["inputFolder"]);

while (TRUE)
{
	$newfile = false;
	while(fam_pending($fam_res)){
		$arr = fam_next_event($fam_res);
		// FAMCreated == 5
		if($arr["code"] == 5){
			echo 'New file arrived: '.$arr["filename"]."\n";
			$newfile = true;
			$mysqli->query("INSERT INTO dicom_file_queue (filename, detected_date, status_id) VALUES ('".$dicomConfig["biometry"]["inputFolder"]."/".$arr["filename"]."', now(), (SELECT id FROM dicom_process_status WHERE name = 'new'))");
		}
	}
	// something changed so we run the queue processor in the background
	if($newfile){
		exec("cd ".$dicomConfig["general"]["PHPdir"]." && /usr/bin/php runQueueProcessor.php"." > /dev/null 2>&1 &");
		echo "Queue Processor has been started successfully\n";
		unset($arr);
	} 
	sleep(5);

}

fam_close($fam_res); 

?>
