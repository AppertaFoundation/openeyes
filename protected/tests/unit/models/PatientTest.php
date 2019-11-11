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
class PatientTest extends CDbTestCase
{
    public $model;
    public $fixtures = array(
        'patients' => 'Patient',
        'addresses' => 'Address',
        'Disorder',
        'SecondaryDiagnosis',
        'Specialty',
        'Event',
        'Episode',
    );

    public function dataProvider_Search()
    {
        return array(
            array(array('first_name' => 'Katherine', 'last_name' => 'muller', 'sortBy' => 'hos_num*1', 'sortDir' => 'asc', 'pageSize' => 20, 'currentPage' => 0), 1, array('patient3')),
            array(array('last_name' => 'jones', 'first_name' => 'muller', 'sortBy' => 'hos_num*1', 'sortDir' => 'asc', 'pageSize' => 20, 'currentPage' => 0), 1, array('patient2')), /* case insensitivity test */
            array(array('hos_num' => 12345, 'last_name' => 'test lastname', 'first_name' => 'test firstname', 'sortBy' => 'hos_num*1', 'sortDir' => 'asc', 'pageSize' => 20, 'currentPage' => 0), 1, array('patient1')),
            array(array('first_name' => 'John', 'last_name' => 'jones', 'sortBy' => 'hos_num*1', 'sortDir' => 'asc', 'pageSize' => 20, 'currentPage' => 0), 2, array('patient1', 'patient2')),
        );
    }

    // 'first_name', 'last_name', 'dob', 'title', 'city', 'postcode', 'telephone', 'mobile', 'email', 'address1', 'address2'
    public function dataProvider_Pseudo()
    {
        return array(
            array(array('hos_num' => 5550101, 'first_name' => 'Rod', 'last_name' => 'Flange', 'dob' => '1979-09-08', 'title' => 'MR', 'primary_phone' => '0208 1111111', 'address_id' => 1)),
            array(array('hos_num' => 5550101, 'first_name' => 'Jane', 'last_name' => 'Hinge', 'dob' => '2000-01-02', 'title' => 'Ms.', 'primary_phone' => '0207 1111111', 'address_id' => 2)),
            array(array('hos_num' => 5550101, 'first_name' => 'Freddy', 'last_name' => 'Globular', 'dob' => '1943-04-05', 'title' => 'WC', 'primary_phone' => '0845 11111111', 'address_id' => 3)),
        );
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->model = new Patient();
    }

    /**
     * @covers Patient::model
     *
     * @todo   Implement testModel().
     */
    public function testModel()
    {
        $this->assertEquals('Patient', get_class(Patient::model()), 'Class name should match model.');
    }


    /**
     * @dataProvider dataProvider_Search
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
                $expectedResults[] = $this->patients($key);
            }
        }
        if (isset($data[0])) {
            $this->assertEquals($expectedResults, array('0' => $data[0]->getAttributes()));
        }
    }


    /**
     * @covers Patient::getAge
     *
     */
    public function testGetAge()
    {
        Yii::app()->params['pseudonymise_patient_details'] = false;

        $patient = $this->patients('patient9'); //dob=1979-09-08
        
        $age = 39; // patient9 died on 2019-07-10, when they were 39

        $this->assertEquals($age, $patient->getAge());

        $patient = $this->patients('patient8'); // dob= 1977-03-04

        $age = date('Y') - 1977;
        if (date('md') < '0304') {
            --$age; // have not had a birthday yet
        }

        $this->assertEquals($age, $patient->getAge());
    }

    public function testRandomData_ParamSetOff_ReturnsFalse()
    {
        Yii::app()->params['pseudonymise_patient_details'] = false;

        $patient = $this->patients('patient9');
        $orig_hos_num = $this->getFixtureData('patients')['patient9']['hos_num'];

        $this->assertEquals($orig_hos_num, $patient->getAttribute('hos_num'), 'Data should not have changed.');
    }

    /**
     * @covers Patient::editOphInfo
     */
    public function testEditOphInfo_Success()
    {
        $cvi_status = ComponentStubGenerator::generate('PatientOphInfoCviStatus', array('id' => 1));
        $this->assertTrue($this->patients('patient1')->editOphInfo($cvi_status, '2000-01-01'));
    }

    public function testEditOphInfo_ValidationFailure()
    {
        $cvi_status = ComponentStubGenerator::generate('PatientOphInfoCviStatus', array('id' => 1));
        $errors = $this->patients('patient1')->editOphInfo($cvi_status, '2000-42-35');
        $this->assertEquals(array('cvi_status_date' => array('This is not a valid date')), $errors);
    }


    /**
     * @covers Patient::getSdl
     */
    public function testGetSdl()
    {
        $this->assertEquals('left myopia, right retinal lattice degeneration and bilateral posterior vitreous detachment', $this->patients('patient2')->getSdl());
    }

    public function testGetSyd()
    {
        $this->assertEquals('diabetes mellitus type 1 and essential hypertension', $this->patients('patient2')->getSyd());
    }


    /**
     * @covers Patient::getLatestEvent
     */
    public function testGetLatestEvent()
    {
        $event = $this->patients('patient1')->getLatestEvent();
        $this->assertEquals('someinfo3', $event->info);
    }

    public function testGetHSCICName_NotBold()
    {
        $this->assertEquals('AYLWARD, Jim (Mr)', $this->patients('patient1')->getHSCICName());
    }

    public function testGetHSCICName_Bold()
    {
        $this->assertEquals('<strong>AYLWARD</strong>, Jim (Mr)', $this->patients('patient1')->getHSCICName(true));
    }
}
