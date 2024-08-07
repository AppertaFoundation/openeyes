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
use PatientIdentifier;
use OEModule\OphCiExamination\models\Element_OphCiExamination_CommunicationPreferences;

/**
 * @group sample-data
 * @group pasapi
 * @group pas-api
 */
class PASAPI_Patient_Test extends PASAPI_BaseTest
{
    protected $base_url_stub = 'Patient/';

    public const IDENTIFIER_TYPE = 'LOCAL-1-0';

    public function testMissingID()
    {
        $this->setExpectedHttpError(400);
        $this->put('', null);
        $this->assertXPathFound('/Failure');
    }

    public function testEmptyPatient()
    {
        $this->setExpectedHttpError(400);
        $this->put('TEST01', null);
    }

    public function testMalformedPatient()
    {
        $this->setExpectedHttpError(400);
        $this->put('TEST01', '<Patient>');
    }

    public function testMismatchedRootTag()
    {
        $this->setExpectedHttpError(400);
        $this->put('BADTAG', '<OEPatient />');
    }

    public function testPatientResourceValidation()
    {
        $xml = <<<EOF
<Patient>
    <HospitalNumber>92312423</HospitalNumber>
</Patient>
EOF;
        $this->setExpectedHttpError(400);
        $this->put('TESTVALIDATION', $xml);
        $this->assertXPathFound('/Failure');

    }

    public function testPatientModelValidation()
    {
        $xml = <<<EOF
<Patient>
    <HospitalNumber>92312423123123123231231232142342453453453656546456456</HospitalNumber>
    <FirstName>Test</FirstName>
    <Surname>API</Surname>
    <DateOfBirth>1978-03-01</DateOfBirth>
</Patient>
EOF;
        $this->setExpectedHttpError(400);
        $this->put('TESTMODELVALIDATION', $xml);
        $this->assertXPathFound('/Failure');
    }

    /**
     * @TODO: clean this up into separate file and test in more detail (and fix GP references etc)
     */
    public function testCreatePatient()
    {
        $xml = <<<EOF
<Patient>
    <NHSNumber>0123456789</NHSNumber>
    <NHSNumberStatus>02</NHSNumberStatus>
    <HospitalNumber>92312423</HospitalNumber>
    <Title>MRS</Title>
    <FirstName>Test</FirstName>
    <Surname>API</Surname>
    <DateOfBirth>1978-03-01</DateOfBirth>
    <Gender>F</Gender>
    <AddressList>
        <Address>
            <Line1>82 Scarisbrick Lane</Line1>
            <Line2/>
            <City>Bethersden</City>
            <County>West Yorkshire</County>
            <Postcode>QA88 2GC</Postcode>
            <Country>GB</Country>
            <Type>HOME</Type>
        </Address>
    </AddressList>
    <TelephoneNumber>03040 6024378</TelephoneNumber>
    <MobilePhoneNumber>03040 6024378</MobilePhoneNumber>
    <EthnicGroup>A</EthnicGroup>
    <DateOfDeath/>
    <PracticeCode>C82103</PracticeCode>
    <GpCode>G3258868</GpCode>
</Patient>
EOF;
        $this->expected_response_code = 201;
        $this->put('92312423/identifier-type/LOCAL-1-0', $xml);

        $id = $this->xPathQuery('/Success//Id')->item(0)->nodeValue;

        $patient = Patient::model()->findByPk($id);
        $this->assertNotNull($patient);
        $this->assertEquals('API', $patient->last_name);
        $this->assertEquals('030406024378', $patient->contact->mobile_phone, 'Mobile phone has not been mapped correctly');
    }

    public function testUpdateOnlyDoesntCreatePatient()
    {
        $xml = <<<EOF
<Patient updateOnly="1">
    <NHSNumber>0123456789</NHSNumber>
    <HospitalNumber>01010101</HospitalNumber>
    <Title>MRS</Title>
    <FirstName>Test</FirstName>
    <Surname>API</Surname>
    <DateOfBirth>1978-03-01</DateOfBirth>
    <Gender>F</Gender>
    <AddressList>
        <Address>
            <Line1>82 Scarisbrick Lane</Line1>
            <Line2/>
            <City>Bethersden</City>
            <County>West Yorkshire</County>
            <Postcode>QA88 2GC</Postcode>
            <Country>GB</Country>
            <Type>HOME</Type>
        </Address>
    </AddressList>
    <TelephoneNumber>03040 6024378</TelephoneNumber>
    <MobilePhoneNumber>03040 6024378</MobilePhoneNumber>
    <EthnicGroup>A</EthnicGroup>
    <DateOfDeath/>
    <PracticeCode>C82103</PracticeCode>
    <GpCode>G3258868</GpCode>
    <LanguageCode>alb</LanguageCode>
    <InterpreterRequired>alb</InterpreterRequired>
</Patient>
EOF;

        // providing language code in the data triggers an event creation
        $this->additional_clean_up_models = [
            Element_OphCiExamination_CommunicationPreferences::class,
            \Event::class,
            \Episode::class
        ];

        $this->put('01010101/identifier-type/LOCAL-1-0', $xml);

        $this->assertXPathFound('/Success');

        $message = $this->xPathQuery('/Success//Message')->item(0)->nodeValue;

        $this->assertEquals('Patient not created', $message);

        $this->assertNull(
            \PatientIdentifier::model()->with(['patientIdentifierType' => [
                'condition' => 'patientIdentifierType.unique_row_string = :urs',
                'params' => [':urs' => self::IDENTIFIER_TYPE]
            ]])
                ->findByAttributes([
                    'value' => '01010101'
                ])
        );

        $xml = preg_replace('/updateOnly="1"/', '', $xml);

        $this->expected_response_code = 201;
        $this->put('01010101/identifier-type/LOCAL-1-0', $xml);
        $id = $this->xPathQuery('/Success//Id')->item(0)->nodeValue;

        $patient = Patient::model()->findByPk($id);
        $this->assertNotNull($patient);
        $patient_identifier = \PatientIdentifier::model()
            ->with([
                'patientIdentifierType' => [
                    'condition' => 'patientIdentifierType.unique_row_string = :urs',
                    'params' => [':urs' => self::IDENTIFIER_TYPE]
                ]
            ])
            ->findByAttributes([
                'value' => '01010101'
            ]);
        $this->assertNotNull($patient_identifier);
        $this->assertEquals($id, $patient_identifier->patient_id);
    }

    public function testUpdateOnlyDoesUpdatePatient()
    {
        $xml = <<<EOF
<Patient>
    <NHSNumber>0123456789</NHSNumber>
    <HospitalNumber>1234535</HospitalNumber>
    <Title>MRS</Title>
    <FirstName>Test</FirstName>
    <Surname>API</Surname>
    <DateOfBirth>1978-03-01</DateOfBirth>
    <Gender>F</Gender>
    <AddressList>
        <Address>
            <Line1>82 Scarisbrick Lane</Line1>
            <Line2/>
            <City>Bethersden</City>
            <County>West Yorkshire</County>
            <Postcode>QA88 2GC</Postcode>
            <Country>GB</Country>
            <Type>HOME</Type>
        </Address>
    </AddressList>
    <TelephoneNumber>03040 6024378</TelephoneNumber>
    <MobilePhoneNumber>03040 6024378</MobilePhoneNumber>
    <EthnicGroup>A</EthnicGroup>
    <DateOfDeath/>
    <PracticeCode>C82103</PracticeCode>
    <GpCode>G3258868</GpCode>
</Patient>
EOF;
        $this->expected_response_code = 201;
        $this->put('1234535/identifier-type/LOCAL-1-0', $xml);
        $id = $this->xPathQuery('/Success//Id')->item(0)->nodeValue;

        $patient = Patient::model()->findByPk($id);
        $this->assertNotNull($patient);
        $this->assertEquals('1234535', $patient->localIdentifiers[0]->value);

        $new_dob = '1981-02-13';
        $xml = preg_replace('/<Patient/', '<Patient updateOnly="1"', $xml);
        $xml = preg_replace('/DateOfBirth>1978-03-01/', 'DateOfBirth>' . $new_dob, $xml);

        $this->expected_response_code = 200;
        $this->put('1234535/identifier-type/LOCAL-1-0', $xml);

        $patient = Patient::model()->findByPk($id);
        $this->assertNotNull($patient);
        $this->assertEquals($new_dob, $patient->dob);
    }

    public function testUpdateOnlyHeaderDoesNotCreatePatient()
    {
        $xml = <<<EOF
<Patient>
    <NHSNumber>0123456789</NHSNumber>
    <HospitalNumber>010101010</HospitalNumber>
    <Title>MRS</Title>
    <FirstName>Test</FirstName>
    <Surname>API</Surname>
    <DateOfBirth>1978-03-01</DateOfBirth>
    <Gender>F</Gender>
    <AddressList>
        <Address>
            <Line1>82 Scarisbrick Lane</Line1>
            <Line2/>
            <City>Bethersden</City>
            <County>West Yorkshire</County>
            <Postcode>QA88 2GC</Postcode>
            <Country>GB</Country>
            <Type>HOME</Type>
        </Address>
    </AddressList>
    <TelephoneNumber>03040 6024378</TelephoneNumber>
    <MobilePhoneNumber>03040 6024378</MobilePhoneNumber>
    <EthnicGroup>A</EthnicGroup>
    <DateOfDeath/>
    <PracticeCode>C82103</PracticeCode>
    <GpCode>G3258868</GpCode>
</Patient>
EOF;
        $this->put('010101010/identifier-type/LOCAL-1-0', $xml, [
            'X-OE-Update-Only' => 1,
        ]);

        $this->assertXPathFound('/Success');

        $message = $this->xPathQuery('/Success//Message')->item(0)->nodeValue;

        $this->assertEquals('Patient not created', $message);

        $this->assertNull(
            \PatientIdentifier::model()->with(['patientIdentifierType' => [
                'condition' => 'patientIdentifierType.unique_row_string = :urs',
                'params' => [':urs' => self::IDENTIFIER_TYPE]
            ]])
                ->findByAttributes([
                    'value' => '010101010'
                ])
        );
    }

    public function partialUpdate_Provider()
    {
        // base xml patient to create for the test
        $xml = <<<EOF
<Patient>
    <NHSNumber>4567891233</NHSNumber>
    <NHSNumberStatus>01</NHSNumberStatus>
    <HospitalNumber>4534563</HospitalNumber>
    <Title>MRS</Title>
    <FirstName>Partial</FirstName>
    <Surname>Update</Surname>
    <DateOfBirth>1982-03-01</DateOfBirth>
    <Gender>F</Gender>
    <AddressList>
        <Address>
            <Line1>82 Scarisbrick Lane</Line1>
            <Line2/>
            <City>Bethersden</City>
            <County>West Yorkshire</County>
            <Postcode>QA88 2GC</Postcode>
            <Country>GB</Country>
            <Type>HOME</Type>
        </Address>
    </AddressList>
    <TelephoneNumber>03040 6024378</TelephoneNumber>
    <MobilePhoneNumber>03040 6024378</MobilePhoneNumber>
    <EthnicGroup>A</EthnicGroup>
    <DateOfDeath/>
    <PracticeCode>C82103</PracticeCode>
    <GpCode>G3258868</GpCode>
</Patient>
EOF;
        // structure for expectation of values which can be merged with a new array to
        // ensure a partial update request has worked as expected.
        $original_expectation = array(
            'title' => 'MRS',
            'first_name' => 'Partial',
            'last_name' => 'Update',
            'gender' => 'F',
            'globalIdentifiers' => [['value' => '4567891233', 'patientIdentifierStatus' => ['code' => '01']]],
            'localIdentifiers' => [['value' => '4534563']],
            'dob' => '1982-03-01',
            'ethnic_group' => array('code' => 'A'),
            'gp' => array('nat_id' => 'G3258868'),
            'practice' => array('code' => 'C82103'),
        );

        return array(
            array(
                $xml,
                '<Patient><Title>Mr</Title></Patient>',
                array_merge(
                    $original_expectation,
                    array(
                        'title' => 'Mr',
                    )
                ),
            ),
            array(
                $xml,
                '<Patient><Title>Dr</Title><Gender>M</Gender></Patient>',
                array_merge(
                    $original_expectation,
                    array(
                        'title' => 'Dr',
                        'gender' => 'M',
                    )
                ),
            ),
            array(
                $xml,
                '<Patient><Gender/></Patient>',
                array_merge(
                    $original_expectation,
                    array(
                        'gender' => null,
                    )
                ),
            ),
            array(
                $xml,
                '<Patient><EthnicGroup/><GpCode /><PracticeCode /></Patient>',
                array_merge(
                    $original_expectation,
                    array(
                        'ethnic_group' => null,
                        'gp' => null,
                        'practice' => null,
                    )
                ),
            ),
            array(
                $xml,
                '<Patient><NHSNumberStatus>02</NHSNumberStatus><Gender/><DateOfBirth>1990-08-03</DateOfBirth></Patient>',
                array_merge(
                    $original_expectation,
                    array(
                        'gender' => null,
                        'dob' => '1990-08-03',
                        'globalIdentifiers' => [['patientIdentifierStatus' => ['code' => '02']]]
                    )
                ),
            ),
        );
    }

    protected function assertExpectedValuesMatch($expected, $obj)
    {
        foreach ($expected as $k => $v) {
            if (is_array($v)) {
                $this->assertExpectedValuesMatch($v, is_array($obj) ? $obj[$k] : $obj->$k);
            } else {
                $this->assertNotNull($obj, "Expecting attribute value {$v} on null object");
                $this->assertEquals($v, $obj->$k);
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
    public function testPartialUpdate($initial, $partial, $expected_values)
    {
        $this->markTestSkipped('Partial update has been broken by the changes for multi tenancy and identifier resolution');
        $this->expected_response_code = 201;
        $this->put('4534563/identifier-type/LOCAL-1-0', $initial);

        $id = $this->xPathQuery('/Success//Id')->item(0)->nodeValue;

        $patient = Patient::model()->findByPk($id);
        $this->assertNotNull($patient);

        $this->put('4534563/identifier-type/LOCAL-1-0', $partial, array(
            'X-OE-Partial-Record' => 1,
        ));

        $patient = Patient::model()->findByPk($id);

        $this->assertExpectedValuesMatch($expected_values, $patient);
    }

    public function testPartialUpdateAllowedForNewRecord()
    {
        $xml = <<<EOF
<Patient>
    <NHSNumber>0123456789</NHSNumber>
    <HospitalNumber>010101010</HospitalNumber>
    <FirstName>Test First</FirstName>
    <Surname>Test Last</Surname>
    <DateOfBirth>1978-03-01</DateOfBirth>
</Patient>
EOF;
        $this->expected_response_code = 201;

        $this->put('010101010/identifier-type/LOCAL-1-0', $xml, array(
            'X-OE-Partial-Record' => 1,
        ));

        $this->assertXPathFound('/Success');
        $id = $this->xPathQuery('/Success//Id')->item(0)->nodeValue;

        $patient = Patient::model()->findByPk($id);
        $this->assertNotNull($patient);
        $this->assertEquals('Test Last', $patient->last_name);
    }

    public function update_Provider()
    {
        // base xml patient to create for the test
        $xml = <<<EOF
<Patient>
    <NHSNumber>4567891233</NHSNumber>
    <NHSNumberStatus>01</NHSNumberStatus>
    <HospitalNumber>4534563</HospitalNumber>
    <Title>MRS</Title>
    <FirstName>Full</FirstName>
    <Surname>Update</Surname>
    <DateOfBirth>1982-03-01</DateOfBirth>
    <Gender>F</Gender>
    <AddressList>
        <Address>
            <Line1>82 Scarisbrick Lane</Line1>
            <Line2/>
            <City>Bethersden</City>
            <County>West Yorkshire</County>
            <Postcode>QA88 2GC</Postcode>
            <Country>GB</Country>
            <Type>HOME</Type>
        </Address>
    </AddressList>
    <TelephoneNumber>03040 6024378</TelephoneNumber>
    <MobilePhoneNumber>03040 6024378</MobilePhoneNumber>
    <EthnicGroup>A</EthnicGroup>
    <DateOfDeath/>
    <PracticeCode>C82103</PracticeCode>
    <GpCode>G3258868</GpCode>
</Patient>
EOF;
        // structure for expectation of values which can be merged with a new array to
        // ensure a partial update request has worked as expected.
        $original_expectation = array(
            'title' => 'MRS',
            'first_name' => 'Full',
            'last_name' => 'Update',
            'gender' => 'F',
            'globalIdentifier' => ['value' => '4567891233', 'patientIdentifierStatus' => ['code' => '01']],
            'localIdentifiers' => [['value' => '4534563']],
            'dob' => '1982-03-01',
            'ethnic_group' => array('code' => 'A'),
            'gp' => array('nat_id' => 'G3258868'),
            'practice' => array('code' => 'C82103'),
        );

        $update1 = <<<EOF
<Patient>
    <NHSNumber>4567891233</NHSNumber>
    <NHSNumberStatus>01</NHSNumberStatus>
    <HospitalNumber>4534563</HospitalNumber>
    <Title>Mr</Title>
    <FirstName>Full</FirstName>
    <Surname>Update</Surname>
    <DateOfBirth>1982-03-01</DateOfBirth>
    <Gender>F</Gender>
    <AddressList>
        <Address>
            <Line1>82 Scarisbrick Lane</Line1>
            <Line2/>
            <City>Bethersden</City>
            <County>West Yorkshire</County>
            <Postcode>QA88 2GC</Postcode>
            <Country>GB</Country>
            <Type>HOME</Type>
        </Address>
    </AddressList>
    <TelephoneNumber>03040 6024378</TelephoneNumber>
    <MobilePhoneNumber>03040 6024378</MobilePhoneNumber>
</Patient>
EOF;

        return array(
            array(
                $xml,
                $update1,
                array_merge(
                    $original_expectation,
                    array(
                        'title' => 'Mr',
                        'gp' => null,
                        'practice' => null,
                        'ethnic_group' => null,
                    )
                ),
            ),
        );
    }

    /**
     * @dataProvider update_Provider
     *
     * @param $initial
     * @param $update
     * @param $expected_values
     */
    public function testUpdate($initial, $update, $expected_values)
    {
        $this->expected_response_code = 201;

        $this->put('4534563/identifier-type/LOCAL-1-0', $initial);

        $id = $this->xPathQuery('/Success//Id')->item(0)->nodeValue;

        $patient = Patient::model()->findByPk($id);
        $this->assertNotNull($patient);

        $this->expected_response_code = 200;
        $this->put('4534563/identifier-type/LOCAL-1-0', $update);

        $patient = Patient::model()->findByPk($id);

        $this->assertExpectedValuesMatch($expected_values, $patient);
    }
}
