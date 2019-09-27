<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class AnonymiseVFCommand extends CConsoleCommand
{
    public function getHelp()
    {
        return "Usage:\n\n\tanonymisefields [command]\n\nwhere command can be any one of:\n"
            . "\ntransformFmes --fmesDir=[inputdir]  --outputDir=[outputDir] --realPidFile=[pidFile] --anonPidFile=[anonPidFile]\n\n"
            . "\tTake the specified directory of FMES humphrey measurements, unpack the image from it, anonymise\n"
            . "\tthe patient data, swap the recorded patient ID and swap it for the specified anonymous PID\n"
            . "\tTwo files must be specified, each giving a newline separated list of patient IDs to substitute.\n"
            . "\n\n"
            . "anonymisefields redact --pattern=[filePattern]\n\n"
            . "\tFor all files that match the specified pattern in the database, perform image oerations to remove name, PID etc.\n"
            . "\tOnly images of the correct size (2400x3180) are transformed.\n"
            . "\n\n"
            . "anonymisefields redactTif --inDir=[tifInDir] --outDir=[tifOutDir]\n\n"
            . "\tFor all files that match the specified pattern, perform image oerations to remove name, PID etc.\n"
            . "\tOnly images of the correct size (2400x3180) are transformed.\n";
    }

    /**
     * Take a list of real patient identifiers that appear in a collection
     * of FMES files, and remove the 'real' PID in the FMES file in favour
     * of.
     *
     * @param type $realPidFile
     * @param type $anonPidFile
     */
    public function actiontransformFmes($fmesDir, $outputDir, $realPidFile, $anonPidFile)
    {
        foreach (array($fmesDir, $realPidFile, $anonPidFile) as $file) {
            if (!file_exists($file)) {
                echo $file . ' does not exist' . PHP_EOL;
                exit(1);
            }
        }

        $realPids = file_get_contents($realPidFile);
        $anonPids = file_get_contents($anonPidFile);
        $rPids = explode(PHP_EOL, $realPids);
        $aPids = explode(PHP_EOL, $anonPids);
        // make sure PID count is equal:
        if (count($rPids) != count($aPids)) {
            echo 'Error: PID counts do not match; file contents must match 1-1' . PHP_EOL;
            exit(1);
        }
        // check all real patients exist:
        foreach ($aPids as $pid) {
            if ($pid) {
                if (count(Patient::model()->find("hos_num='" . $pid . "'")) < 1) {
                    echo 'Failed to find anonymous patient ' . $pid . PHP_EOL;
                    exit(1);
                }
            }
        }
        // now check that all 'real' patients are listed in the files:
        $entries = array();
        // build up an array of matches we've encountered so far, and if it's
        // been matched before, ignore it.

        $smgr = Yii::app()->service;
        $fhirMarshal = Yii::app()->fhirMarshal;
        if ($entry = glob($fmesDir . '/*.fmes')) {
            foreach ($entry as $file) {
                $field = file_get_contents($file);
                $fieldObject = $fhirMarshal->parseXml($field);
                $match = $this->getHosNum($file, $field);
                if (!in_array($match, $entries)) {
                    // only add it if it's in the list of real patient IDs:
                    if (in_array($match, $rPids)) {
                        array_push($entries, $match);
                    }
                }
            }
        }
        // now create new FMES files
        // need to go through each one, pairing anonymised IDs with real ones,
        // replacing the real ID with the anonymised ID; note that we also
        // need to swap out the image and do some redaction:

        if ($entry = glob($fmesDir . '/*.fmes')) {
            foreach ($entry as $file) {
                $field = file_get_contents($file);
                $fieldObject = $fhirMarshal->parseXml($field);
                // swap out hos nums:
                $match = $this->getHosNum($file, $field);
                if (in_array($match, $rPids)) {
                    $index = array_search($match, $rPids);
                    $anonPid = $aPids[$index];
                    unset($fieldObject->patient_id);
                    $fieldObject->patient_id = '__OE_PATIENT_ID_' . $anonPid . '__';
                    echo 'replacing ' . $match . ' with ' . $anonPid . PHP_EOL;
                } else {
                    // not interested, move on:
                    continue;
                }
                // now swap out the actual image. This is slightly involved -
                // we need to write the image to temporary file, perform
                // image operations on it to anonymise PID, DoB etc.,
                // step 1: extract image:
                $image = base64_decode($fieldObject->image_scan_data);
                unset($fieldObject->image_scan_data);
                // now redact it - we need to perform imagemagick operations:
                $img = 'img.gif';
                file_put_contents($img, $image);
                $image = new Imagick($img);
                $this->fillImage($image);
                $image->writeImage($img);
                $contents = file_get_contents($img);
                $fieldObject->image_scan_data = base64_encode($contents);
                $doc = new DOMDocument();
                file_put_contents($outputDir . '/' . basename($file), $fhirMarshal->renderXml($fieldObject));
                echo 'Successfully written ' . $file . PHP_EOL;
            }
        }
    }

    /**
     * @param type $file
     * @param type $field
     * @param array $matches
     *
     * @return type
     */
    private function getHosNum($file, $field)
    {
        $matches = array();
        preg_match('/__OE_PATIENT_ID_([0-9]*)__/', $field, $matches);
        if (count($matches) < 2) {
            echo 'Failed to extract patient ID in ' . basename($file) . '; moving to ' . $this->errorDir . PHP_EOL;
            $this->move($this->errorDir, $file);
        }

        return str_pad($matches[1], 7, '0', STR_PAD_LEFT);
    }

    /**
     * Fills the image at the specified locations. Designed specifically to
     * grey-out patient name, DoB, PID and HFA serial number,.
     *
     * @param Imagick $image
     */
    private function fillImage($image)
    {
        $draw = new ImagickDraw(); //Create a new drawing class (?)

        $draw->setFillColor('grey');
        // main patient details - name, pid, dob:
        $draw->rectangle(190, 80, 2210, 254);
        // date, time, age:
        $draw->rectangle(1773, 291, 2160, 489);
        // bottom of image - serial number etc.:
        $draw->rectangle(190, 2960, 2160, 3099);
        $image->drawImage($draw);
        $image->setImageFormat('gif');
    }

    /**
     * Take images from the in-directory, anonymise them and place the resulting
     * file in the sepcified out-directory.
     *
     * @param type $inDir
     * @param type $outDir
     */
    public function actionRedactTif($inDir, $outDir)
    {
        if ($entries = glob($inDir . '/*.tif')) {
            foreach ($entries as $entry) {
                $this->anonymiseTif($inDir . '/' . basename($entry), $outDir);
            }
        }
    }

    /**
     * Create a new image based on the image passed in and anonymise.
     *
     * @param type $file specified image file to anonymise; must be a valid path
     *                   and the image must be the correct size.
     * @param type $out the directory to place the anonymised file.
     */
    private function anonymiseTif($file, $out)
    {
        $image = new Imagick($file);
        $geo = $image->getImageGeometry();
        // only modify the main image, not the thumbnails:
        if ($geo['width'] == 2400
            && $geo['height'] == 3180
        ) {
            echo 'Modifying ' . $file . PHP_EOL;
            $this->fillImage($image);
            echo $out . PHP_EOL;
            $image->writeImage($file . '.tmp');
            copy($file . '.tmp', $out . '/' . basename($file, '.tif') . '.gif');
        }
    }

    /**
     * Trawl an existing OE database and find all files that match the given
     * pattern. A pattern MUST be specified.
     *
     * If no pattern is specified, all images that match the standard humphrey
     * image size are processed.
     */
    public function actionRedact($pattern = null)
    {
        $criteria = new CDbCriteria();
        if ($pattern != null) {
            $criteria->condition = 'name like :name';
            $criteria->params = array(':name' => $pattern);
        } else {
            echo 'You MUST specify a file pattern to match.';
        }
        $files = ProtectedFile::model()->findAll($criteria);
        // we can't really filter images, except on size - for now just
        // assume the count is half the amount when taking thumbnails into
        // consideration
        echo (count($files) / 2) . ' files found for modification.';
        if ($files) {
            foreach ($files as $file) {
                if (file_exists($file->getPath())) {
                    $this->anonymiseTif($file->getPath());
                } else {
                    echo 'Could not transform file; ' . $file->getPathName()
                        . ' does not exist.' . PHP_EOL;
                }
            }
        }
    }
}
