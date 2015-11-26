<?php

	class eventLogger{
		private $databaseConnection;
				
	    public function __construct($db)
		{
			$this->databaseConnection = $db;
		}
	
		public function addLogEntry($filename, $status, $process){
			$this->databaseConnection->query("INSERT INTO dicom_file_log (event_date_time, filename, status, process_name) VALUES (now(), '".$filename."', '".$status."', '".$process."')");
			
		}
		
	}
	

?>