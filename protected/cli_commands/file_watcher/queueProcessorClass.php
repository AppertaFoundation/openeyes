<?php

    class queueProcessor
    {
        private $databaseConnection;
        private $processToRun;
        private $logger;

        public function __construct($db, $process, $logger)
        {
            $this->databaseConnection = $db;
            $this->processToRun = $process;
            $this->logger = $logger;
        }

        public function checkEntries()
        {
            $needToProcess = $this->databaseConnection->query("SELECT * FROM dicom_file_queue WHERE status_id=(SELECT id FROM dicom_process_status WHERE name='new') ORDER BY id DESC LIMIT 10");
            if ($needToProcess->num_rows) {
                while ($fileEntry = $needToProcess->fetch_assoc()) {
                    echo 'Running: '.$this->processToRun.' '.$fileEntry['filename']."\n\n";
                    $this->databaseConnection->query("UPDATE dicom_file_queue SET status_id= (SELECT id FROM dicom_process_status WHERE name='in_progress'), last_modified_date=now() WHERE id='".$fileEntry['id']."'");
                    $this->logger->addLogEntry($fileEntry['filename'], 'in_progress', basename($_SERVER['SCRIPT_FILENAME']));
                    exec($this->processToRun.' '.$fileEntry['filename'], $results, $exitcode);
                    // need to check exit code and update the record
                    // if not 0 than we have an error
                    var_dump($exitcode);
                    if ($exitcode != 0) {
                        $this->databaseConnection->query("UPDATE dicom_file_queue SET status_id= (SELECT id FROM dicom_process_status WHERE name='failed'), last_modified_date=now() WHERE id='".$fileEntry['id']."'");
                        $this->logger->addLogEntry($fileEntry['filename'], 'failed', basename($_SERVER['SCRIPT_FILENAME']));
                        echo 'Process failed, exit code: '.$exitcode."\n";
                        echo implode("\n", $results)."\n";
                    } else {
                        $this->databaseConnection->query("UPDATE dicom_file_queue SET status_id= (SELECT id FROM dicom_process_status WHERE name='success'), last_modified_date=now() WHERE id='".$fileEntry['id']."'");
                        $this->logger->addLogEntry($fileEntry['filename'], 'success', basename($_SERVER['SCRIPT_FILENAME']));
                        echo "File import was successful\n";
                    }
                }
                $this->checkEntries();
            }
        }
    }
