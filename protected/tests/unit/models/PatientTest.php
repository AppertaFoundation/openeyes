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

/**
 * Class PatientTest
 *
 * @method patient($fixtureId)
 * @method patient_identifier($fixtureId)
 */
class PatientTest extends ActiveRecordTestCase
{
    public Patient $model;
    public $fixtures = array(
        'institution' => Institution::class,
        'site' => Site::class,
        'patient_identifier_type' => PatientIdentifierType::class,
        'patient' => Patient::class,
        'patient_identifier' => PatientIdentifier::class,
        'addresses' => 'Address',
        'Contact',
        'Disorder',
        'SecondaryDiagnosis',
        'Specialty',
        'Event',
        'Episode',
    );

    public function getModel()
    {
        return $this->model;
    }

    public function dataProvider_Search()
    {
        return array(
            array(array('first_name' => 'Edward', 'last_name' => 'Allan', 'sortBy' => 'value*1', 'sortDir' => 'asc', 'pageSize' => 20, 'currentPage' => 0), 1, array('patient3')),
            array(array('first_name' => '', 'last_name' => 'Collin', 'sortBy' => 'value*1', 'sortDir' => 'asc', 'pageSize' => 20, 'currentPage' => 0), 1, array('patient2')), /* case insensitivity test */
            array([
                'is_name_search' => false,
                'terms_with_types' => [
                    [
                        'term' => '12345',
                        'patient_identifier_type' => [
                            'id' => 1,
                        ],
                    ],
                ],
                'sortBy' => 'value*1',
                'sortDir' => 'asc',
                'pageSize' => 20,
                'currentPage' => 0
            ], 1, array('patient1')),
            array(array('first_name' => 'Bob', 'sortBy' => 'value*1', 'sortDir' => 'asc', 'pageSize' => 20, 'currentPage' => 0), 3, array('patient2', 'patient5', 'patient7')),
        );
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();
        $this->model = new Patient();
    }

    /**
     * @covers Patient
     *
     */
    public function testModel()
    {
        $this->assertEquals('Patient', get_class(Patient::model()), 'Class name should match model.');
    }


    /**
     * @covers Patient
     * @dataProvider dataProvider_Search
     * @param $searchTerms
     * @param $numResults
     * @param $expectedKeys
     */
    public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
    {
        $patient = new Patient();
        $patient->setAttributes($searchTerms);
        $results = $patient->search($searchTerms);

        $data = $results->getData();

        $expectedResults = array();
        if (!empty($expectedKeys)) {
            foreach ($expectedKeys as $key) {
                $expectedResults[] = $this->patient($key);
            }
        }

        $this->assertEquals($numResults, $results->totalItemCount);
        $this->assertEquals($expectedResults, $data);
    }


    /**
     * @covers Patient
     *
     */
    public function testGetAge()
    {
        Yii::app()->params['pseudonymise_patient_details'] = false;

        $patient = $this->patient('patient9'); //dob=1979-09-08

        $age = 39; // patient9 died on 2019-07-10, when they were 39

        $this->assertEquals($age, $patient->getAge());

        $patient = $this->patient('patient8'); // dob= 1977-03-04

        $age = date('Y') - 1977;
        if (date('md') < '0304') {
            --$age; // have not had a birthday yet
        }

        $this->assertEquals($age, $patient->getAge());
    }

    /**
     * @covers Patient
     */
    public function testRandomData_ParamSetOff_ReturnsFalse()
    {
        Yii::app()->params['pseudonymise_patient_details'] = false;

        $patient = $this->patient('patient9');

        $orig_local_id = $this->patient_identifier('patient9ID')['value'];

        $this->assertEquals($orig_local_id, $patient->localIdentifiers[0]->value, 'Data should not have changed.');
    }

    /**
     * @covers Patient
     * @throws ReflectionException
     */
    public function testEditOphInfo_Success()
    {
        $cvi_status = ComponentStubGenerator::generate('PatientOphInfoCviStatus', array('id' => 1));
        $this->assertTrue($this->patient('patient1')->editOphInfo($cvi_status, '2000-01-01'));
    }

    /**
     * @covers Patient
     * @throws ReflectionException
     */
    public function testEditOphInfo_ValidationFailure()
    {
        $cvi_status = ComponentStubGenerator::generate('PatientOphInfoCviStatus', array('id' => 1));
        $errors = $this->patient('patient1')->editOphInfo($cvi_status, '2000-42-35');
        $this->assertEquals(array('cvi_status_date' => array('This is not a valid date')), $errors);
    }

    /**
     * @covers Patient
     */
    public function testGetSdl()
    {
        $this->assertEquals('left myopia, right retinal lattice degeneration and bilateral posterior vitreous detachment', $this->patient('patient2')->getSdl());
    }

    /**
     * @covers Patient
     */
    public function testGetSyd()
    {
        $this->assertEquals('diabetes mellitus type 1 and essential hypertension', $this->patient('patient2')->getSyd());
    }


    /**
     * @covers Patient
     */
    public function testGetLatestEvent()
    {
        $event = $this->patient('patient1')->getLatestEvent();
        $this->assertEquals('someinfo3', $event->info);
    }

    /**
     * @covers Patient
     */
    public function testGetHSCICName_NotBold()
    {
        $this->assertEquals('AYLWARD, Jim (Mr)', $this->patient('patient1')->getHSCICName());
    }

    /**
     * @covers Patient
     */
    public function testGetHSCICName_Bold()
    {
        $this->assertEquals('<strong>AYLWARD</strong>, Jim (Mr)', $this->patient('patient1')->getHSCICName(true));
    }
}
