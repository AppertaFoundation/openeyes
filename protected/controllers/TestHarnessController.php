<?php

/**
 * Created by PhpStorm.
 * User: PATELH3
 * Date: 01/12/2015
 * Time: 10:02.
 */
class TestHarnessController extends BaseAdminController
{
    public $msg = 0;

    public function actionDICOMFileWatcher()
    {
        $msg = 0;
        if (!empty($_GET['dicomfiles'])) {
            $file = $_GET['dicomfiles'];
            $ori_dir = '/home/iolmaster/test/';
            $dest_dir = '/home/iolmaster/incoming/';

            if (file_exists($ori_dir.$file)) {
                if (!@copy($ori_dir.$file, $dest_dir.$file)) {
                    $errors = error_get_last();
                    $msg = 'COPY ERROR: '.$errors['type'];
                } else {
                    $msg = 1;
                }
            } else {
                $msg = "The file $file does not exist";
            }
        }

        $dirlist = $this->getFileList('/home/iolmaster/test');
        $this->render('/testharnness/dicom_files_watcher', array('msg' => $msg, 'dirlist' => $dirlist));
    }
    public function actionVF()
    {
        $this->render('/testharnness/vf');
    }

    /**
     * @param $dir
     *
     * @return array
     */
    private function getFileList($dir)
    {
        // array to hold return value
        $retval = array();

        // add trailing slash if missing
        if (substr($dir, -1) != '/') {
            $dir .= '/';
        }

        // open pointer to directory and read list of files
        $d = @dir($dir) or die("getFileList: Failed opening directory $dir for reading");
        while (false !== ($entry = $d->read())) {
            $info = pathinfo($entry);
            $ext = $info['extension'];
            // skip hidden files
            if ($entry[0] == '.') {
                continue;
            }
            if (is_dir("$dir$entry")) {
                $retval[] = array(
                    'name' => "$entry",
                    'fullpath' => "$dir/$entry",
                    'type' => filetype("$dir$entry"),
                    'size' => 0,
                    'lastmod' => filemtime("$dir$entry"),
                    'ext' => $ext,
                );
            } elseif (is_readable("$dir$entry")) {
                $retval[] = array(
                    'name' => "$entry",
                    'fullpath' => "$dir/$entry",
                    'type' => mime_content_type("$dir$entry"),
                    'size' => filesize("$dir$entry"),
                    'lastmod' => filemtime("$dir$entry"),
                    'ext' => $ext,
                );
            }
        }
        $d->close();

        return $this->aasort($retval, 'name');
    }

    private function aasort(&$array, $key)
    {
        $sorter = array();
        $ret = array();
        reset($array);
        foreach ($array as $ii => $va) {
            $sorter[$ii] = $va[$key];
        }
        asort($sorter);
        foreach ($sorter as $ii => $va) {
            $ret[$ii] = $array[$ii];
        }

        return $array = $ret;
    }

//aasort($your_array,"order");
}
