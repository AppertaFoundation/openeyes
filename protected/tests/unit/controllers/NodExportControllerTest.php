<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2014
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
class NodExportControllerTest extends CDbTestCase
{
    protected $date;
    protected $mock;
    protected $controller;

    public function setUp()
    {
        parent::setUp();

        $this->date = $date = date('YmdHi');
        $this->mock = $this->getMockBuilder('NodExportController')->disableOriginalConstructor()->getMock();

        $this->mock->method('createAllTempTables');

        $reflectionClass = new ReflectionClass('NodExportController');

        $institutionCode = $reflectionClass->getProperty('institutionCode');
        $institutionCode->setAccessible(true);
        $institutionCode->setValue($this->mock, Yii::app()->params['institution_code']);

        $exportPath = $reflectionClass->getProperty('exportPath');
        $exportPath->setAccessible(true);
        $exportPath->setValue($this->mock, (realpath(dirname(__FILE__).'/../../../').'/runtime/nod-export/test/'.$institutionCode->getValue($this->mock).'/'.$this->date));

        $zipName = $reflectionClass->getProperty('zipName');
        $zipName->setAccessible(true);
        $zipName->setValue($this->mock, ($institutionCode->getValue($this->mock).'_'.$this->date.'_NOD_Export.zip'));

        $this->controller = $reflectionClass;
        $this->exportPath = $exportPath->getValue($this->mock);
        $this->zipName = $zipName->getValue($this->mock);

        if (!file_exists($exportPath->getValue($this->mock))) {
            mkdir($exportPath->getValue($this->mock), 0777, true);
        }

        $createAllTempTablesmethod = $this->controller->getMethod('createAllTempTables');
        $createAllTempTablesmethod->setAccessible(true);
        $createAllTempTablesmethod->invoke($this->mock);
    }

        /**
         * Checking if the string actually is a float value like "24.6"
         * note: floatval("24.0") returns 24.
         * 
         * @param string $string
         *
         * @return bool if the string is a float value
         */
        private function isStringFloat($string)
        {
            if (is_numeric($string) && strpos($string, '.') !== false) {
                return true;
            }

            return false;
        }

    /**
     * Generates CSV and zip file then test the zip if exsist and size > 0.
     */
    public function testgenerateExport()
    {

            /*
             * generateExport will generate all the CSV files
             */
            $generateExportMethod = $this->controller->getMethod('generateExport');
        $generateExportMethod->setAccessible(true);
        $generateExportMethod->invoke($this->mock);

            /*
             * createZipFile will generate the zip file
             */
            $createZipFileMethod = $this->controller->getMethod('createZipFile');
        $createZipFileMethod->setAccessible(true);
        $createZipFileMethod->invoke($this->mock);

            // check if the zip file exsist
            $this->assertFileExists($this->exportPath.'/'.$this->zipName);

            // and not empty
            $this->assertGreaterThan(0, filesize($this->exportPath.'/'.$this->zipName));
    }

        /**
         * Fetch the header of the CSV file (first line).
         * 
         * @param string $file path and name
         *
         * @return type
         */
        protected function getCSVHeader($file)
        {
            $file = fopen($file, 'r');

            $data = fgetcsv($file);
            fclose($file);

            return $data;
        }

        /**
         * Validate date structure.
         * 
         * @param string $date
         *
         * @return bool
         */
        public function validateDate($format, $date)
        {
            $d = DateTime::createFromFormat($format, $date);

            return $d && $d->format($format) == $date;
        }

        /**
         * Test the Surgeon CSV files if they are exsist and the file size > 0
         * also check the headers.
         */
        public function testSurgeons()
        {
            $file = $this->exportPath.'/'.'Surgeon.csv';
            $this->assertFileExists($file);
            $this->assertGreaterThan(0, filesize($file));

            $handle = fopen($file, 'r');
            if ($handle !== false) {
                $header = fgetcsv($handle, 1000, ',');

                $this->assertEquals($header, array(
                    'Surgeonid', 'GMCnumber', 'Title', 'FirstName', 'CurrentGradeId',
                ));

                $doctorGradeData = $user = Yii::app()->db->createCommand()
                                ->select('code')
                                ->from('tmp_doctor_grade')
                                ->queryAll();

                foreach ($doctorGradeData as $grade) {
                    $doctorGrades[] = $grade['code'];
                }

                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    $this->assertTrue(is_numeric($data[0]), 'Surgeon - Surgeonid must be numeric');
                    $this->assertTrue(is_string($data[1]), 'Surgeon - GMCnumber must be string');
                    $this->assertTrue(is_string($data[2]), 'Surgeon - Title must be string');
                    $this->assertTrue(is_string($data[3]), 'Surgeon - FirstName must be string');

                    // doctor grade

                    // $this->assertTrue(is_numeric($data[4]), "Surgeon - CurrentGradeId must be numeric" );
                    // $this->assertContains( $data[4], $doctorGrades, "Surgeon - CurrentGradeId can't find in tmp_doctor_grade table" );
                }
                fclose($handle);
            }
            $this->markTestIncomplete('Surgeon - Tests not implemented yet : CurrentGradeId');
        }

        /**
         * Test the Patient CSV files if they are exsist and the file size > 0.
         */
        public function testPatients()
        {
            $file = $this->exportPath.'/'.'Patient.csv';
            $this->assertFileExists($file);
            $this->assertGreaterThan(0, filesize($file));

            $ethnicityData = $user = Yii::app()->db->createCommand()
                                ->select('code')
                                ->from('ethnic_group')
                                ->queryAll();

            foreach ($ethnicityData as $data) {
                $ethnicity[] = $data['code'];
            }

            $handle = fopen($file, 'r');
            if ($handle !== false) {
                $header = fgetcsv($handle, 1000, ',');
                $this->assertEquals($header, array(
                    'PatientId', 'GenderId', 'EthnicityId', 'DateOfBirth', 'DateOfDeath', 'IMDScore', 'IsPrivate',
                ));

                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    $this->assertTrue(is_numeric($data[0]), 'Patient - PatientId must be numeric');

                    // GenderId
                    $this->assertTrue(is_numeric($data[1]), 'Patient - GenderId must be a string');
                    $this->assertContains($data[1], array(1, 2, 9), "Patient - GenderId if not recorded than it supposed to be '9'");

                    // EthnicityId
                    $this->assertTrue(is_string($data[2]), 'Patient - EthnicityId must be string');
                    $this->assertContains($data[2], $ethnicity, "Patient - EthnicityId if not recorded than it supposed to be 'Z'");

                    // DateOfBirth - anonymised by ±3 months
                    $this->assertTrue(is_string($data[3]), 'Patient - DateOfBirth must be string');

                    $this->assertTrue($this->validateDate('Y-m-d', $data[3]), 'Patient - Invalid date of birth format');

                    if (!empty($data[4])) {
                        $this->assertTrue($this->validateDate('Y-m-d', $data[4]), 'Patient - Invalid date of death format');
                    }

                    // Not part of the minimal dataset so we just return empty string
                    $this->assertEmpty($data[5], 'Patient - IMDScore is not recorded at this time, supposed to be an empty string');
                    $this->assertEmpty($data[6], 'Patient - IsPrivate is not recorded at this time, supposed to be an empty string');
                }
                fclose($handle);
            }
        }

        /**
         * Test the PatientCviStatus CSV files if they are exsist and the file size > 0.
         */
        public function testPatientCviStatus()
        {
            $file = $this->exportPath.'/'.'PatientCviStatus.csv';
            $this->assertFileExists($file);
            $this->assertGreaterThan(0, filesize($file));

            $handle = fopen($file, 'r');
            if ($handle !== false) {
                $header = fgetcsv($handle, 1000, ',');

                $this->assertEquals($header, array(
                  'PatientId', 'Date', 'IsDateApprox', 'IsCVIBlind', 'IsCVIPartial',
                ));

                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    $this->assertTrue(is_numeric($data[0]), 'PatientCviStatus - PatientId must be numeric');
                    $this->assertTrue($this->validateDate('Y-m-d', $data[1]), 'PatientCviStatus - Invalid date');
                    $this->assertContains($data[2], array(1, 2), 'PatientCviStatus - IsDateApprox value must be either 0 or 1');
                    $this->assertContains($data[3], array(1, 2), 'PatientCviStatus - IsCVIBlind value must be either 0 or 1');
                    $this->assertContains($data[4], array(1, 2), 'PatientCviStatus - IsCVIPartial value must be either 0 or 1');
                }
                fclose($handle);
            }
        }

        /**
         * Test the Episodes CSV files if they are exsist and the file size > 0.
         */
        public function testEpisodes()
        {
            $file = $this->exportPath.'/'.'Episode.csv';
            $this->assertFileExists($file);
            $this->assertGreaterThan(0, filesize($file));

            $handle = fopen($file, 'r');

            if ($handle !== false) {
                $header = fgetcsv($handle, 1000, ',');

                $this->assertEquals($header, array(
                    'PatientId', 'EpisodeId', 'Date',
                ));

                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    $this->assertTrue(is_numeric($data[0]), 'Episode - PatientId must be numeric');
                    $this->assertTrue(is_numeric($data[1]), 'Episode - EpisodeId must be numeric');
                    $this->assertTrue($this->validateDate('Y-m-d', $data[2]), 'Episode - Invalid date');
                }
                fclose($handle);
            }
        }

        /**
         * Test the EpisodeDiagnosis CSV files if they are exsist and the file size > 0.
         */
        public function testEpisodeDiagnosis()
        {
            $file = $this->exportPath.'/'.'EpisodeDiagnosis.csv';
            $this->assertFileExists($file);
            $this->assertGreaterThan(0, filesize($file));

            $diagnosisData = $user = Yii::app()->db->createCommand()
                                ->select('rco_condition_id')
                                ->from('tmp_episode_diagnosis')
                                ->queryAll();

            foreach ($diagnosisData as $data) {
                $diagnosisIds[] = $data['rco_condition_id'];
            }

            $handle = fopen($file, 'r');
            if ($handle !== false) {
                $header = fgetcsv($handle, 1000, ',');

                $this->assertEquals($header, array(
                    'EpisodeId', 'Eye', 'Date', 'SurgeonId', 'ConditionId', 'DiagnosisTermId',
                ));

                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    $this->assertTrue(is_numeric($data[0]), 'EpisodeDiagnosis - EpisodeId must be numeric');
                    $this->assertContains($data[1], array('L', 'R', 'B', 'N'), 'EpisodeDiagnosis - Eye must be a letter L,R,B,N');
                    $this->assertTrue($this->validateDate('Y-m-d', $data[2]), 'EpisodeDiagnosis - Invalid date');

                    $this->assertTrue(is_numeric($data[3]), 'EpisodeDiagnosis - SurgeonId must be numeric');

                    // ConditionId
                    // if the ConditionId is empty most likely the episode.firm_id is null
                    $this->assertTrue(is_numeric($data[4]), 'EpisodeDiagnosis - ConditionId must be numeric');
                    $this->assertContains($data[4], $diagnosisIds, 'EpisodeDiagnosis - ConditionId must be part of the RCO ConditionID list');

                    // DiagnosisTermId as episode.disorder_id
                    if (!empty($data[5])) {
                        $this->assertTrue(is_numeric($data[5]), 'EpisodeDiagnosis - DiagnosisTermId must be numeric');
                    }
                }
                fclose($handle);
            }
        }

        /**
         * Test the EpisodeDiagnosis CSV files if they are exsist and the file size > 0.
         */
        public function testEpisodeDiabeticDiagnosis()
        {
            $file = $this->exportPath.'/'.'EpisodeDiabeticDiagnosis.csv';
            $this->assertFileExists($file);
            $this->assertGreaterThan(0, filesize($file));

            $handle = fopen($file, 'r');
            if ($handle !== false) {
                $header = fgetcsv($handle, 1000, ',');

                $this->assertEquals($header, array(
                     'EpisodeId', 'IsDiabetic', 'DiabetesTypeId', 'DiabetesRegimeId', 'AgeAtDiagnosis',
                ));

                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    $this->assertTrue(is_numeric($data[0]), 'EpisodeDiabeticDiagnosis - EpisodeId must be numeric');

                    $this->assertTrue(is_numeric($data[1]), 'EpisodeDiabeticDiagnosis - IsDiabetic must be numeric');
                    $this->assertEquals(1, $data[1], 'EpisodeDiabeticDiagnosis - IsDiabetic always 1 as the sql query always returns 1');
                    //$this->assertContains( $data[1], array(0,1), "EpisodeDiabeticDiagnosis - IsDiabetic must be a boolean 0 or 1" );

                    // DiabetesTypeId
                    $this->assertTrue(is_numeric($data[2]), 'EpisodeDiabeticDiagnosis - DiabetesTypeId must be numeric');
                    $this->assertContains($data[2], array(1, 2, 3, 4, 5, 9), 'EpisodeDiabeticDiagnosis - DiabetesTypeId must be either 1,2,3,4,5 or 9');

                    // Not part of the minimal dataset so we just return empty string
                    $this->assertEmpty($data[3], 'EpisodeDiabeticDiagnosis - DiabetesRegimeId is not recorded at this time, supposed to be an empty string');

                    // AgeAtDiagnosis empty if the secondary_diagnosis.date is null/empty
                    if (!empty($data[4])) {
                        $this->assertTrue(is_numeric($data[4]), 'EpisodeDiabeticDiagnosis - AgeAtDiagnosis must be numeric : '.$data[4]);
                        $this->assertLessThan(130, $data[4], 'EpisodeDiabeticDiagnosis - AgeAtDiagnosis the number is suprisingly heigh');
                        $this->assertGreaterThan(0, $data[4], 'EpisodeDiabeticDiagnosis - AgeAtDiagnosis fetus diagnoses ?');
                    }
                }

                fclose($handle);
            }
        }

        /**
         * Test the EpisodeDrug CSV files if they are exsist and the file size > 0.
         */
        public function testEpisodeDrug()
        {
            $file = $this->exportPath.'/'.'EpisodeDrug.csv';
            $this->assertFileExists($file);
            $this->assertGreaterThan(0, filesize($file));
            $header = $this->getCSVHeader($file);

            $drugRouteData = $user = Yii::app()->db->createCommand()
                                ->select('nod_id')
                                ->from('tmp_episode_drug_route')
                                ->queryAll();

            foreach ($drugRouteData as $data) {
                $drugRouteIds[] = $data['nod_id'];
            }

            $handle = fopen($file, 'r');

            if ($handle !== false) {
                $header = fgetcsv($handle, 1000, ',');

                $this->assertEquals($header, array(
                        'EpisodeId', 'Eye', 'DrugId', 'DrugRouteId', 'StartDate', 'StopDate', 'IsAddedByPrescription', 'IsContinueIndefinitely', 'IsStartDateApprox',
                ));

                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    $this->assertTrue(is_numeric($data[0]), 'EpisodeDrug - EpisodeId must be numeric');
                    $this->assertContains($data[1], array('L', 'R', 'B', 'N'), 'EpisodeDrug - Eye must be a letter L,R,B,N');

                    $this->assertNotEmpty($data[2], 'EpisodeDrug - DrugId should be the name of the drug (i.e., medication_drug.name) see the doc');

                    // DrugRouteId
                    $this->assertTrue(is_numeric($data[3]), 'EpisodeDrug - DrugRouteId must be numeric');
                    $this->assertContains($data[3], $drugRouteIds, 'EpisodeDrug - DrugRouteId must be part of the RCO DrugRouteId list');

                    $this->assertTrue($this->validateDate('Y-m-d', $data[4]), 'EpisodeDrug - Invalid StartDate');
                    $this->assertTrue($this->validateDate('Y-m-d', $data[5]), 'EpisodeDrug - Invalid StopDate');

                    $this->assertEquals('1', $data[6], 'EpisodeDrug - IsAddedByPrescription always 1 as the sql query returns always 1');
                    $this->assertContains($data[7], array(0, 1), 'EpisodeDrug - IsContinueIndefinitely must be eiter 0 or 1 ,boolean');

                    $this->assertEquals('0', $data[8], 'EpisodeDrug - IsStartDateApprox always 0 as the sql query returns always 0');
                }

                fclose($handle);
            }
        }

        /**
         * Test the EpisodeDrug CSV files if they are exsist and the file size > 0.
         */
        public function testEpisodeBiometry()
        {
            $file = $this->exportPath.'/'.'EpisodeBiometry.csv';
            $this->assertFileExists($file);
            $this->assertGreaterThan(0, filesize($file));

            $handle = fopen($file, 'r');

            if ($handle !== false) {
                $header = fgetcsv($handle, 1000, ',');

                $this->assertEquals($header, array(
                    'EpisodeId', 'Eye', 'AxialLength', 'BiometryAScanId', 'BiometryKeratometerId',
                    'BiometryFormulaId', 'K1PreOperative', 'K2PreOperative', 'AxisK1', 'AxisK2', 'ACDepth', 'SNR',
                ));

                // Stop here and mark this test as incomplete.
                $this->markTestIncomplete(
                  'Without any data this test cannot be ran and tested'
                );

                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    $this->assertTrue(is_numeric($data[0]), 'EpisodeBiometry - EpisodeId must be numeric');
                    $this->assertContains($data[1], array('L', 'R'), 'EpisodeBiometry - Eye must be a letter L or R');
                    $this - assertTrue($this->isStringFloat($data[2]), 'EpisodeBiometry - AxialLength must be a float value');
                    $this->assertEmpty($data[3], 'EpisodeBiometry - BiometryAScanId must be an empty string, not recorded in OE');

                    $this->assertTrue(is_numeric($data[4]), 'EpisodeBiometry - BiometryKeratometerId must be numeric');
                    $this->assertTrue(is_numeric($data[5]), 'EpisodeBiometry - BiometryFormulaId must be numeric');

                    $this - assertTrue($this->isStringFloat($data[6]), 'EpisodeBiometry - K1PreOperative must be a float value');
                    $this - assertTrue($this->isStringFloat($data[7]), 'EpisodeBiometry - K2PreOperative must be a float value');

                    $this - assertTrue($this->isStringFloat($data[8]), 'EpisodeBiometry - AxisK1 must be a float value');
                    $this - assertTrue($this->isStringFloat($data[9]), 'EpisodeBiometry - AxisK2 must be a float value');
                    $this - assertTrue($this->isStringFloat($data[10]), 'EpisodeBiometry - ACDepth must be a float value');
                    $this - assertTrue($this->isStringFloat($data[9]), 'EpisodeBiometry - SNR must be a float value');
                }
                fclose($handle);
            }
        }

        /**
         * Test the EpisodeIOP CSV files if they are exsist and the file size > 0.
         */
        public function testEpisodeIOP()
        {
            $file = $this->exportPath.'/'.'EpisodeIOP.csv';
            $this->assertFileExists($file);
            $this->assertGreaterThan(0, filesize($file));

            $handle = fopen($file, 'r');

            if ($handle !== false) {
                $header = fgetcsv($handle, 1000, ',');

                $this->assertEquals($header, array(
                    'EpisodeId', 'Eye', 'Type', 'GlaucomaMedicationStatusId', 'Value',
                ));

                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    $this->assertTrue(is_numeric($data[0]), 'EpisodeIOP - EpisodeId must be numeric');
                    $this->assertContains($data[1], array('L', 'R'), 'EpisodeIOP - Eye must be a letter L or R');
                    $this->assertEmpty($data[2], 'EpisodeIOP - BiometryAScanId must be an empty string, not recorded in OE');
                    $this->assertEquals('9', $data[3], 'EpisodeIOP - GlaucomaMedicationStatusId must be 9 as not known in OE)');

                    $this->assertTrue($this->isStringFloat($data[4]), 'EpisodeIOP - Value must be a float value');
                }

                fclose($handle);
            }
        }

        /**
         * Test the EpisodePreOpAssessment CSV files if they are exsist and the file size > 0.
         */
        public function testEpisodePreOpAssessment()
        {
            $file = $this->exportPath.'/'.'EpisodePreOpAssessment.csv';
            $this->assertFileExists($file);
            $this->assertGreaterThan(0, filesize($file));
            $header = $this->getCSVHeader($file);

            $handle = fopen($file, 'r');

            if ($handle !== false) {
                $header = fgetcsv($handle, 1000, ',');

                $this->assertEquals($header, array(
                    'EpisodeId', 'Eye', 'IsAbleToLieFlat', 'IsInabilityToCooperate',
                ));

                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    $this->assertTrue(is_numeric($data[0]), 'EpisodePreOpAssessment - EpisodeId must be numeric');
                    $this->assertContains($data[1], array('L', 'R', 'B'), 'EpisodePreOpAssessment - Eye must be a letter L or R');
                    $this->assertContains($data[2], array(0, 1), 'EpisodePreOpAssessment - IsAbleToLieFlat must be eiter 0 or 1 ,boolean');
                    $this->assertContains($data[3], array(0, 1), 'EpisodePreOpAssessment - IsInabilityToCooperate must be eiter 0 or 1 ,boolean');
                }
            }
        }

        /**
         * Test the EpisodeRefraction CSV files if they are exsist and the file size > 0.
         */
        public function testEpisodeRefraction()
        {
            $file = $this->exportPath.'/'.'EpisodeRefraction.csv';
            $this->assertFileExists($file);
            $this->assertGreaterThan(0, filesize($file));
            $header = $this->getCSVHeader($file);

            $this->assertEquals($header, array(
                'EpisodeId', 'Eye', 'RefractionTypeId', 'Sphere', 'Cylinder', 'Axis', 'ReadingAdd',
            ));
        }

        /**
         * Test the EpisodeVisualAcuity CSV files if they are exsist and the file size > 0.
         */
        public function testEpisodeVisualAcuity()
        {
            $file = $this->exportPath.'/'.'EpisodeVisualAcuity.csv';
            $this->assertFileExists($file);
            $this->assertGreaterThan(0, filesize($file));
            $header = $this->getCSVHeader($file);

            $this->assertEquals($header, array(
                'EpisodeId', 'Eye', 'NotationRecordedId', 'BestMeasure', 'Unaided', 'Pinhole', 'BestCorrected',
            ));
        }

        /**
         * Test the EpisodeOperation CSV files if they are exsist and the file size > 0.
         */
        public function testEpisodeOperation()
        {
            $file = $this->exportPath.'/'.'EpisodeOperation.csv';
            $this->assertFileExists($file);
            $this->assertGreaterThan(0, filesize($file));
            $header = $this->getCSVHeader($file);

            $this->assertEquals($header, array(
                'OperationId', 'EpisodeId', 'Description', 'IsHypertensive', 'ListedDate', 'SurgeonId', 'SurgeonGradeId', 'AssistantId', 'AssistantGradeId', 'ConsultantId',
            ));
        }

        /**
         * Test the EpisodeOperationComplication CSV files if they are exsist and the file size > 0.
         */
        public function testEpisodeOperationComplication()
        {
            $file = $this->exportPath.'/'.'EpisodeOperationComplication.csv';
            $this->assertFileExists($file);
            $this->assertGreaterThan(0, filesize($file));
            $header = $this->getCSVHeader($file);

            $this->assertEquals($header, array(
                'OperationId', 'Eye', 'ComplicationTypeId',
            ));
        }

        /**
         * Test the EpisodeOperationIndication CSV files if they are exsist and the file size > 0.
         */
        public function testEpisodeOperationIndication()
        {
            $file = $this->exportPath.'/'.'EpisodeOperationIndication.csv';
            $this->assertFileExists($file);
            $this->assertGreaterThan(0, filesize($file));
            $header = $this->getCSVHeader($file);

            $this->assertEquals($header, array(
                'OperationId', 'Eye', 'IndicationId',
            ));
        }

        /**
         * Test the EpisodeOperationCoPathology CSV files if they are exsist and the file size > 0.
         */
        public function testEpisodeOperationCoPathology()
        {
            $file = $this->exportPath.'/'.'EpisodeOperationCoPathology.csv';
            $this->assertFileExists($file);
            $this->assertGreaterThan(0, filesize($file));
            $header = $this->getCSVHeader($file);

            $this->assertEquals($header, array(
                'OperationId', 'Eye', 'CoPathologyId',
            ));
        }

        /**
         * Test the EpisodeOperationAnaesthesia CSV files if they are exsist and the file size > 0.
         */
        public function testEpisodeOperationAnaesthesia()
        {
            $file = $this->exportPath.'/'.'EpisodeOperationAnaesthesia.csv';
            $this->assertFileExists($file);
            $this->assertGreaterThan(0, filesize($file));
            $header = $this->getCSVHeader($file);

            $this->assertEquals($header, array(
                'OperationId', 'AnaesthesiaTypeId', 'AnaesthesiaNeedle', 'Sedation', 'SurgeonId', 'ComplicationId',
            ));
        }

        /**
         * Test the EpisodeTreatment CSV files if they are exsist and the file size > 0.
         */
        public function testEpisodeTreatment()
        {
            $file = $this->exportPath.'/'.'EpisodeTreatment.csv';
            $this->assertFileExists($file);
            $this->assertGreaterThan(0, filesize($file));
            $header = $this->getCSVHeader($file);

            $this->assertEquals($header, array(
                'TreatmentId', 'OperationId', 'Eye', 'TreatmentTypeId',
            ));
        }

        /**
         * Test the EpisodeTreatment CSV files if they are exsist and the file size > 0.
         */
        public function testEpisodeTreatmentCataract()
        {
            $file = $this->exportPath.'/'.'EpisodeTreatmentCataract.csv';
            $this->assertFileExists($file);
            $this->assertGreaterThan(0, filesize($file));
            $header = $this->getCSVHeader($file);

            $this->assertEquals($header, array(
                'TreatmentId', 'IsFirstEye', 'PreparationDrugId', 'IncisionSiteId', 'IncisionLengthId',
                'IncisionPlanesId', 'IncisionMeridean', 'PupilSizeId', 'IOLPositionId', 'IOLModelId', 'IOLPower', 'PredictedPostOperativeRefraction', 'WoundClosureId',
            ));
        }

        /**
         * Test the EpisodePostOpComplication CSV files if they are exsist and the file size > 0.
         */
        public function testEpisodePostOpComplication()
        {
            $file = $this->exportPath.'/'.'EpisodePostOpComplication.csv';
            $this->assertFileExists($file);
            $this->assertGreaterThan(0, filesize($file));
            $header = $this->getCSVHeader($file);

            $this->assertEquals($header, array(
                'EpisodeId', 'OperationId', 'Eye', 'ComplicationTypeId',
            ));
        }

    public function tearDown()
    {
        parent::tearDown();

        $clearAllTempTablesmethod = $this->controller->getMethod('clearAllTempTables');
        $clearAllTempTablesmethod->setAccessible(true);
        $clearAllTempTablesmethod->invoke($this->mock);
    }
}
