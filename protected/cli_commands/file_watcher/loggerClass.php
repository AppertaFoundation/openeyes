<?php

class eventLogger
{
    private $databaseConnection;

    public function __construct($db)
    {
        $this->databaseConnection = $db;
    }

    public function addLogEntry($filename, $status, $process)
    {
        $filedata = stat($filename);
        $fileIdQ = $this->databaseConnection->query("SELECT id,id FROM dicom_files WHERE filename = '".$filename."' AND filesize ='".$filedata['size']."' AND filedate='".date('Y-m-d H:i:s', $filedata['mtime'])."'");
        $fileIdArr = $fileIdQ->fetch_array();
        $this->databaseConnection->query("INSERT INTO dicom_file_log (event_date_time, dicom_file_id, status, process_name) VALUES (now(), '".$fileIdArr[0]."', '".$status."', '".$process."')");
    }
}
