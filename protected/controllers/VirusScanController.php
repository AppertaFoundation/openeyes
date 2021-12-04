<?php

class VirusScanController extends BaseController
{
    private const SCANSTATUS_ERROR = 0;
    private const SCANSTATUS_CLEAN = 1;
    private const SCANSTATUS_DIRTY = 2;

    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('index', 'scanProtectedFiles', 'removeInfectedFiles'),
                'users' => array('admin'),
            )
        );
    }

    public static function scanString($string)
    {
        try {
            // Create a new socket instance
            $socket_factory = new \Socket\Raw\Factory();
            $socket = $socket_factory->createClient('tcp://clam:3310');

            // Create a new instance of the Client, ensure PHP_NORMAL_READ is passed as PHP_BINARY_READ is deprecated
            $quahog = new \Xenolope\Quahog\Client($socket, 30, PHP_NORMAL_READ);

            $quahog->startSession();
            if ($quahog->ping()) {
                // Scan a stream, and optionally pass the maximum chunk size in bytes
                $stream_result = $quahog->scanStream($string, 1024);
            }
            $quahog->endSession();
        } catch (Exception $e) {
            $stream_result = ['id'=>null, 'filename'=>'stream', 'reason'=>$e->getMessage(), 'status'=>'ERROR'];
        }

        return $stream_result;
    }

    public static function stringIsClean($string)
    {
        $scan_result = self::scanString($string);
        return $scan_result['status'] === 'OK';
    }

    public function scanProtectedFiles()
    {
        $file_paths = array();

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(getcwd() . '/protected/files/'));
        foreach ($iterator as $file) {
            if ($file->isDir() || str_contains($file, 'quarantine')) continue;
            $file_paths[] = $file->getPathname();
        }

        $results = array();

        foreach ($file_paths as $file_path) {
            if (file_exists($file_path)) {
                $scan_result = VirusScanController::scanString(file_get_contents($file_path));
            } else {
                $scan_result = ['status'=>'ERROR', 'reason'=>'The file could not be located'];
            }

            $results[] = ['uid' => $file_path, 'file_name' => basename($file_path), 'status' => $scan_result['status'], 'details'=>$scan_result['reason']];
        }

        return $results;
    }

    public function actionIndex()
    {
        $this->render('/virusscanner/index', array());
    }

    //Will take a set of scan results from the ScanProtectedFilesFunction and then create, populate, save, and return the appropriate models.
    public static function createScanResultModels($scan_results)
    {
        $scan = new VirusScan();
        $scan->save();

        foreach ($scan_results as $scan_result) {
            $result_model = VirusScanItem::newFromScanResult($scan->id, $scan_result);
            $result_model->save();
        }

        return $scan;
    }

    public function actionScanProtectedFiles()
    {
        $raw_scan_result = $this->scanProtectedFiles();

        $scan_result = self::createScanResultModels($raw_scan_result);

        $data = array('scan_id' => $scan_result->id);

        $this->render('/virusscanner/results', array('data' => $data));
    }

    public static function quarantineFile($uid, $reason)
    {
        $quarantined_file = QuarantinedFile::createFromProtectedFile($uid, $reason);
        $quarantined_file->save();

        if (!file_exists(dirname($quarantined_file->getQuarantinedUID()))) {
            mkdir(dirname($quarantined_file->getQuarantinedUID()), 0777, true);
        }
        copy($quarantined_file->getOriginalUID(), $quarantined_file->getQuarantinedUID());

        $placeholder_file = QuarantinedPlaceholderFile::model()->findByAttributes(array('mimetype' => mime_content_type($quarantined_file->getQuarantinedUID())));
        file_put_contents($quarantined_file->getOriginalUID(), $placeholder_file->file_contents);

        chmod($quarantined_file->getQuarantinedUID(), 600);

        return [
            'uid'=>$quarantined_file->original_uid,
            'quarantine_reason'=>$quarantined_file->quarantine_reason
        ];
    }

    public static function restoreFile($uid)
    {
        throw new Exception("Restoring files is not supported at this time");
    }

    public function actionRemoveInfectedFiles($scan_id)
    {
        $scan_model = VirusScan::model()->findByPk($scan_id);

        $dirty_files = $scan_model->getAllDirtyFiles();

        $quarantined_file_details = array();
        foreach ($dirty_files as $dirty_file) {
            $uid = $dirty_file->file_uid;

            $quarantined_file_details[] = self::quarantineFile($uid, $dirty_file->details);
        }

        $this->render('/virusscanner/summary', array('quarantined_file_details'=>$quarantined_file_details));
    }
}
