<?php

include_once('./connectDatabase.php');
include_once('./fileWatcherConfig.php');
include_once('./loggerClass.php');

$dir_res = opendir($dicomConfig["biometry"]["inputFolder"]);
$mysqli = connectDatabase();

processDir($dir_res, $dicomConfig["biometry"]["inputFolder"]);

function processDir($dirResource, $root){
	global $mysqli;
	while(false !== ($entry = readdir($dirResource))){
		if($entry != '.' && $entry!='..'){
			if(is_dir($root.'/'.$entry)){
				$dirToProcess = opendir($root.'/'.$entry);
				processDir($dirToProcess, $root.'/'.$entry);
			}else if(is_file($root.'/'.$entry)){
				$filedata = stat($root.'/'.$entry);
				createFileEntry($root.'/'.$entry, $filedata, $mysqli);
				$mysqli->query("INSERT INTO dicom_file_queue (filename, detected_date, last_modified_date, status_id) VALUES ('".$root."/".$entry."', now(), now(), (SELECT id FROM dicom_process_status WHERE name = 'new'))");
			}
		}
	}
}

function createFileEntry($fullfilename, $filedata, $mysqli){
	// todo: add insert here! :)
	$mysqli->query("INSERT INTO dicom_files (filename, filesize, filedate, processor_id) VALUES ('".$fullfilename."', '".$filedata["size"]."','".date("Y-m-d H:i:s",$filedata["mtime"])."','".php_uname("n")."')");
	return $mysqli->insert_id;
}

?>