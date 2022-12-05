<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\PASAPI\tests\feature;

use Patient;

class PASAPI_PatientAppointment_Test extends PASAPI_BaseTest
{
    protected $base_url_stub = 'PatientAppointment';
    protected $capture_error_responses = true;

    protected $additional_clean_up_models = array('WorklistPatient');

    public $fixtures = array(
    'worklist_definition' => 'WorklistDefinition',
    'worklist_definition_mapping' => 'WorklistDefinitionMapping',
    'worklist_definition_mapping_value' => 'WorklistDefinitionMappingValue',
    'worklist' => 'Worklist',
    'worklist_attribute' => 'WorklistAttribute',
    );

    public function setUp()
    {
        $this->markTestSkipped('Appointment behaviour has been changed such that these tests need attention to make them relevant again.');
        parent::setUp();
        $mgr = Yii::app()->getComponent('fixture');
        $mgr->load($this->fixtures);
    }

    public function tearDown()
    {
        parent::tearDown();
        $mgr = Yii::app()->getComponent('fixture');
        foreach (array_reverse($this->fixtures) as $f) {
            if ($rows = $mgr->getRows($f)) {
                foreach ($rows as $row) {
                    $row->delete();
                }
            }
        }
    }

    public function getTestPatientId()
    {
        $p = Patient::model()->findAll(array('limit' => 1))[0];

        return $p->id;
    }

    public function testMissingID()
    {
        $this->setExpectedHttpError(400);
        $this->put('', null);
        $this->assertXPathFound('/Failure');
    }

    public function testEmptyPatientAppointment()
    {
        $this->setExpectedHttpError(400);
        $this->put('TEST01', null);
    }

    public function testMalformedPatientAppointment()
    {
        $this->setExpectedHttpError(400);
        $this->put('TEST01', '<PatientAppointment>');
    }

    public function testMismatchedRootTag()
    {
        $this->setExpectedHttpError(400);
        $this->put('BADTAG', '<OEAppoint />');
    }

    public function validationProvider()
    {
        $pid = $this->getTestPatientId();

        $base_valid = <<<EOF
<PatientAppointment>
	<PatientId>
		<Id>$pid</Id>
	</PatientId>
	<Appointment>
        <AppointmentDate>2016-05-23</AppointmentDate>
        <AppointmentTime>11:30</AppointmentTime>
        <AppointmentMappingItems>
            <AppointmentMapping>
                <Key>Clinic Code</Key>
                <Value>E-CATARACTS</Value>
            </AppointmentMapping>
            <AppointmentMapping>
                <Key>Doctor Code</Key>
                <Value>RMC01</Value>
            </AppointmentMapping>
            <AppointmentMapping>
                <Key>Doctor Name</Key>
                <Value>Dr G. Aylward</Value>
            </AppointmentMapping>
        </AppointmentMappingItems>
    </Appointment>
</PatientAppointment>
EOF;

        $missing_mapping_item = <<<EOF
<PatientAppointment>
	<PatientId>
		<Id>123456</Id>
	</PatientId>
	<Appointment>
        <AppointmentDate>2016-05-23</AppointmentDate>
        <AppointmentTime>11:30</AppointmentTime>
        <AppointmentMappingItems>
            <AppointmentMapping>
                <Key>Clinic Code</Key>
            </AppointmentMapping>
            <AppointmentMapping>
                <Key>Doctor Code</Key>
                <Value>RMC01</Value>
            </AppointmentMapping>
            <AppointmentMapping>
                <Key>Doctor Name</Key>
                <Value>Dr G. Aylward</Value>
            </AppointmentMapping>
        </AppointmentMappingItems>
    </Appointment>
</PatientAppointment>
EOF;

        return array(
            array('<WrongTag></WrongTag>', false),
            array('<PatientAppointment />', false),
            array($base_valid, true),
            array($missing_mapping_item, false),
            );
    }

    /**
     * This test is possibly in the wrong place - it's not a unit test, but it's not full integration either. It's just
     * handy to be able to throw some XML at the resource and test it handles it.
     *
     * @dataProvider validationProvider
     */
    public function testValidation($xml, $valid)
    {
        $test = \OEModule\PASAPI\resources\PatientAppointment::fromXml('V1', $xml);
        $test->id = 'test';

        $res = $test->validate();
        // just a handy debugger for when adding other XML inputs
        if ($valid && !$res) {
            var_dump($test->errors);
        }
        $this->assertEquals($valid, $res);
    }

    public function assertFailureMessage($message)
    {
        $this->assertXPathFound('/Failure');
        $this->assertXPathFound('/Failure/Errors');
        $this->assertXPathRegExp('/'.$message.'/', 'string(/Failure/Errors)');
    }
    public function testPartialUpdateErrorsForNewRecord()
    {
        $xml = <<<EOF
<PatientAppointment>
    <Appointment>
        <AppointmentDate>2016-07-07</AppointmentDate>
    </Appointment>
</PatientAppointment>
EOF;

        $this->setExpectedHttpError(400);

        $this->put('PartialUpdateError', $xml, array(
        'X-OE-Partial-Record' => 1,
        ));

        $this->assertFailureMessage('Cannot perform partial update on a new record');
    }

    public function partialUpdate_Provider()
    {
        $pid = $this->getTestPatientId();

        // base xml appointment to create for the test
        $xml = <<<EOF
<PatientAppointment>
	<PatientId>
		<Id>$pid</Id>
	</PatientId>
	<Appointment>
        <AppointmentDate>2016-05-23</AppointmentDate>
        <AppointmentTime>11:30</AppointmentTime>
        <AppointmentMappingItems>
            <AppointmentMapping>
                <Key>Clinic Code</Key>
                <Value>A1</Value>
            </AppointmentMapping>
            <AppointmentMapping>
                <Key>Doctor</Key>
                <Value>Dr G. Aylward</Value>
            </AppointmentMapping>
        </AppointmentMappingItems>
    </Appointment>
</PatientAppointment>
EOF;
        // structure for expectation of values which can be merged with a new array to
        // ensure a partial update request has worked as expected.
        $original_expectation = array(
        'when' => '2016-05-23 11:30:00',
        'worklist_attributes' => array(
            'Clinic Code' => 'A1',
            'Doctor' => 'Dr G. Aylward',
        ),
        );

        return array(
        array(
            $xml,
            '<PatientAppointment><Appointment><AppointmentTime>10:30</AppointmentTime></Appointment></PatientAppointment>',
            array_merge(
                $original_expectation,
                array(
                    'when' => '2016-05-23 10:30:00',
                )
            ),
        ),
        array(
            $xml,
            '<PatientAppointment><Appointment><AppointmentMappingItems><AppointmentMapping><Key>Doctor</Key><Value>Dr J. Morgan</Value></AppointmentMapping></AppointmentMappingItems></Appointment></PatientAppointment>',
            array_merge(
                $original_expectation,
                array(
                    'worklist_attributes' => array(
                        'Clinic Code' => 'A1',
                        'Doctor' => 'Dr J. Morgan',
                    ),
                )
            ),
        ),
        array(
            $xml,
            '<PatientAppointment><Appointment><AppointmentMappingItems><AppointmentMapping><Key>Clinic Code</Key><Value>Error code</Value></AppointmentMapping></AppointmentMappingItems></Appointment></PatientAppointment>',
            $original_expectation,
            array(400, 'No worklist found'),
        ),
        array(
            $xml,
            '<PatientAppointment><Appointment><AppointmentMappingItems><AppointmentMapping><Key>Clinic Code</Key><Value>B2</Value></AppointmentMapping></AppointmentMappingItems></Appointment></PatientAppointment>',
            $original_expectation,
            array(400, 'Worklist mapping change not allowed for partial update.'),
        ),
        );
    }

    protected function assertExpectedValuesMatch($expected, $obj)
    {
        foreach ($expected as $k => $v) {
            if ($k == 'worklist_attributes') {
                $obj_attrs = array();
                foreach ($obj->worklist_attributes as $attr) {
                    $obj_attrs[$attr->worklistattribute->name] = $attr->attribute_value;
                }
                foreach ($v as $an => $av) {
                    $this->assertArrayHasKey($an, $obj_attrs);
                    $this->assertEquals($av, $obj_attrs[$an]);
                }
            } else {
                if (is_array($v)) {
                    $this->assertExpectedValuesMatch($v, $obj->$k);
                } else {
                    $this->assertEquals($v, $obj->$k);
                }
            }
        }
    }

    /**
     * @dataProvider partialUpdate_Provider
     *
     * @param $initial_put
     * @param $partial_put
     * @param $expected_values
     */
    public function testPartialUpdate($initial, $partial, $expected_values, $error = null)
    {
        $this->put('PartialUpdate', $initial);

        $id = $this->xPathQuery('/Success//Id')->item(0)->nodeValue;

        $appt = WorklistPatient::model()->findByPk($id);
        $this->assertNotNull($appt);

        if ($error) {
            $this->setExpectedHttpError($error[0]);
        }

        $this->put('PartialUpdate', $partial, array(
        'X-OE-Partial-Record' => 1,
        ));

        //var_dump($this->response->getBody(true));

        $patient = WorklistPatient::model()->findByPk($id);
        $this->assertExpectedValuesMatch($expected_values, $patient);

        if ($error) {
            $this->assertFailureMessage($error[1]);
        }
    }
}
