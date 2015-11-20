<?php
	
	class queueProcessor{
		private $databaseConnection;
		private $processToRun;
		
	    public function __construct($db, $process)
		{
			$this->databaseConnection = $db;
			$this->processToRun = $process;
		}
	
		public function checkEntries(){
			$needToProcess = $this->databaseConnection->query("SELECT * FROM dicom_file_queue WHERE status_id=(SELECT id FROM dicom_process_status WHERE name='new')");
			
			if($needToProcess){
				while ($fileEntry = $needToProcess->fetch_assoc()) {
					echo "Running: ".$this->processToRun." ".$fileEntry["filename"]."\n\n";
					$this->databaseConnection->query("UPDATE dicom_file_queue SET status_id= (SELECT id FROM dicom_process_status WHERE name='in_progress') WHERE id='".$fileEntry["id"]."'");
					exec($this->processToRun." ".$fileEntry["filename"], $results, $exitcode);
					// need to check exit code and update the record
					// if not 0 than we have an error
					var_dump($exitcode);
					if($exitcode != 0){
						$this->databaseConnection->query("UPDATE dicom_file_queue SET status_id= (SELECT id FROM dicom_process_status WHERE name='failed') WHERE id='".$fileEntry["id"]."'");
						echo "Process failed, exit code: ".$exitcode."\n";
						echo implode("\n", $results)."\n";
					}else{
						$this->databaseConnection->query("UPDATE dicom_file_queue SET status_id= (SELECT id FROM dicom_process_status WHERE name='success') WHERE id='".$fileEntry["id"]."'");
						echo "File import was successfull\n";
					}
				}
			}
		}
	
	}


?>