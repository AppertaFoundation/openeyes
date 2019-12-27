<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class LegacyFieldsCommandTest extends CDbTestCase
{
    protected $legacyFieldCommand;
    protected $importDir;
    protected $originalImportDir;
    protected $archiveDir;
    protected $errorDir;
    protected $dupDir;

    public $fixtures = array(
        'patients' => 'Patient',
        'measurement_type' => 'MeasurementType',
    );

    /**
     * Each file needs to be uniquely named, so we use time to determine file names.
     * However, since the file contents references a unique image name too,
     * this also needs to be changed.
     *
     * @param type $file
     */
    private function prepareFile($file)
    {
        $newname = str_replace(' ', '_', microtime());
        $contents = file_get_contents($file);
        $name = basename($file, '.fmes');
        $contents = str_replace(basename($name), $newname, $contents);
        file_put_contents($this->importDir.'/'.$newname.'.fmes', $contents);
    }

    public function setUp()
    {
        parent::setUp();
        $this->cleanDirectories();
        require_once dirname(__FILE__).'/../../../commands/ImportLegacyVFCommand.php';
        $this->archiveDir = sys_get_temp_dir().'/openeyes_vis_fields_tmp_archive';
        $this->errorDir = sys_get_temp_dir().'/openeyes_vis_fields_tmp_error';
        $this->dupDir = sys_get_temp_dir().'/openeyes_vis_fields_tmp_dups';
        $this->importDir = sys_get_temp_dir().'/openeyes_vis_fields_tmp_in';
        $this->originalImportDir = dirname(__FILE__).'/../../fields/legacy';

        if (!file_exists($this->archiveDir)) {
            mkdir($this->archiveDir, 0777, true);
        }
        if (!file_exists($this->errorDir)) {
            mkdir($this->errorDir, 0777, true);
        }
        if (!file_exists($this->dupDir)) {
            mkdir($this->dupDir, 0777, true);
        }
        if (!file_exists($this->importDir)) {
            mkdir($this->importDir, 0777, true);
        }
        $CCRunner = new CConsoleCommandRunner();

        // test needs to make sure all new files that come in look like new
        // file names (unique, based on time), otherwise there will be errors:
        foreach (glob($this->originalImportDir.'/*.fmes') as $file) {
            $this->prepareFile($file);
        }

        $this->legacyFieldCommand = new ImportLegacyVFCommand('LegacyFields', $CCRunner);
    }

    public function testImport()
    {
        $patient_id = '0012345';
        $field_measurements = count($this->getPatientFieldMeasurements($patient_id));
        $patient_measurements = count($this->getPatientMeasurements($patient_id));

        $this->assertEquals(0, count(glob($this->archiveDir.'/*.fmes')));
        $this->legacyFieldCommand->run(array('import', '--importDir='.$this->importDir, '--archiveDir='.$this->archiveDir,
            '--errorDir='.$this->errorDir, '--dupDir='.$this->dupDir, '--interval=PT10M', ));
        // should be 8 files in the directory:
        $this->assertEquals(8, count(glob($this->archiveDir.'/*.fmes')));
        $this->assertEquals($field_measurements + 8, count($this->getPatientFieldMeasurements($patient_id)));
        $this->assertEquals($patient_measurements + 8, count($this->getPatientMeasurements($patient_id)));
    }

    public function testImportWithDuplicate()
    {
        $patient_id = '0012345';
        $field_measurements = count($this->getPatientFieldMeasurements($patient_id));
        $patient_measurements = count($this->getPatientMeasurements($patient_id));
        // a duplicate file name should be rejected:
        $this->assertEquals(0, count(glob($this->dupDir.'/*.fmes')));
        $this->legacyFieldCommand->run(array('import', '--importDir='.$this->importDir, '--archiveDir='.$this->archiveDir,
            '--errorDir='.$this->errorDir, '--dupDir='.$this->dupDir, '--interval=PT10M', ));
        $files = scandir($this->archiveDir);
        rename($this->archiveDir.'/'.$files[2], $this->importDir.'/'.basename($files[2]));
        $this->legacyFieldCommand->run(array('import', '--importDir='.$this->importDir, '--archiveDir='.$this->archiveDir,
            '--errorDir='.$this->errorDir, '--dupDir='.$this->dupDir, '--interval=PT10M', ));

        // should be one file in the duplicates directory:
        $this->assertEquals(1, count(glob($this->dupDir.'/*.fmes')));
        // no measurements should have been recorded:
        $this->assertEquals($field_measurements + 8, count($this->getPatientFieldMeasurements($patient_id)));
        $this->assertEquals($patient_measurements + 8, count($this->getPatientMeasurements($patient_id)));
    }

    public function testWithNoSuchPatient()
    {
        $patient_id = '0012345';
        $field_measurements = count($this->getPatientFieldMeasurements($patient_id));
        $patient_measurements = count($this->getPatientMeasurements($patient_id));
        $this->assertEquals(0, count(glob($this->errorDir.'/*.fmes')));
        $files = glob($this->importDir.'/*.fmes');
        foreach ($files as $file) {
            $contents = file_get_contents($file);
            $contents = str_replace('_12345__', '_99876__', $contents);
            file_put_contents($file, $contents);
        }

        $this->legacyFieldCommand->run(array('import', '--importDir='.$this->importDir, '--archiveDir='.$this->archiveDir,
            '--errorDir='.$this->errorDir, '--dupDir='.$this->dupDir, '--interval=PT10M', ));
        $this->assertEquals(8, count(glob($this->errorDir.'/*.fmes')));
        // should be no extra measurements:
        $this->assertEquals($field_measurements, count($this->getPatientFieldMeasurements($patient_id)));
        $this->assertEquals($patient_measurements, count($this->getPatientMeasurements($patient_id)));
    }

    public function testWithBadHosNum()
    {
        $this->assertEquals(0, count(glob($this->errorDir.'/*.fmes')));
        $files = scandir($this->importDir);
        $contents = file_get_contents($this->importDir.'/'.$files[2]);
        $contents = str_replace('_12345__', '_abcde__', $contents);
        file_put_contents($this->importDir.'/'.$files[2], $contents);

        $this->legacyFieldCommand->run(array('import', '--importDir='.$this->importDir, '--archiveDir='.$this->archiveDir,
            '--errorDir='.$this->errorDir, '--dupDir='.$this->dupDir, '--interval=PT10M', ));
        $this->assertEquals(1, count(glob($this->errorDir.'/*.fmes')));
    }

    /**
     * Delete temporary files and directories.
     */
    protected function tearDown()
    {
        $this->cleanDirectories();
    }

    protected function cleanDirectories()
    {
        foreach (glob($this->archiveDir.'/*.fmes') as $file) {
            unlink($file);
        }
        foreach (glob($this->errorDir.'/*.fmes') as $file) {
            unlink($file);
        }
        foreach (glob($this->dupDir.'/*.fmes') as $file) {
            unlink($file);
        }
        foreach (glob($this->importDir.'/*.fmes') as $file) {
            unlink($file);
        }
        foreach (array($this->archiveDir, $this->errorDir, $this->dupDir, $this->importDir)  as $file) {
            if (file_exists($file)) {
                rmdir($file);
            }
        }
    }

    /**
     * @param type $patient_id
     *
     * @return type
     */
    private function getPatientFieldMeasurements($patient_id)
    {
        $patient = Patient::model()->find('hos_num=:hos_num',
                array(':hos_num' => $patient_id));
        if ($patient) {
            $criteria = new CDbCriteria();
            $criteria->join = 'join patient_measurement on patient_measurement.id=patient_measurement_id';
            $criteria->condition = 'patient_measurement.patient_id='.$patient->id;

            return OphInVisualfields_Field_Measurement::model()->findAll($criteria);
        }

        return array();
    }

    /**
     * @param type $patient_id
     *
     * @return type
     */
    private function getPatientMeasurements($patient_id)
    {
        $patient = Patient::model()->find('hos_num=:hos_num',
                array(':hos_num' => $patient_id));

        return PatientMeasurement::model()->findAll('patient_id=:patient_id',
                array(':patient_id' => $patient->id));
    }
}
