<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

use Guzzle\Http\Client;

class PASAPI_Patient_Test extends RestTestCase
{

    static protected $namespaces = array(
        'atom' => 'http://www.w3.org/2005/Atom'
    );

    /**
     * @var Client
     */
    protected $client;
    /**
     * @var User
     */
    protected $user;

    protected function cleanUpTestUser()
    {
        if (!$this->user) {
            $this->user = User::model()->findByAttributes(array('username' => 'autotestapi'));
            if (!$this->user)
                return;
        }

        // clear out all the data we've touched, and the user
        foreach (array('Audit', 'OEModule\\PASAPI\\models\\PasApiAssignment', 'Patient', 'Address', 'Contact') as $cls) {
            $cls::model()->deleteAllByAttributes(array('created_user_id' => $this->user->id));
        }

        Audit::model()->deleteAllByAttributes(array('user_id' => $this->user->id));
        $this->user->saveRoles(array());
        $this->user->delete();
    }

    public function setUp()
    {
        // do this so if there was an error that prevented clean up in the last test run we can still test again.
        $this->cleanUpTestUser();

        $this->user = new User();
        $this->user->attributes = array(
            'active' => 1,
            'global_firm_rights' => 1,
            'first_name' => 'Auto-Test',
            'last_name' => 'API',
            'password' => 'password',
            'password_repeat' => 'password',
            'username' => 'autotestapi',
            'email' => 'auto@test.com'
        );

        $this->user->noVersion()->save();
        $this->user->saveRoles(array('User', 'API access'));

        $this->client = new Client(
            Yii::app()->params['pas_api_test_base_url'] . '/Patient',
            array(
                Client::REQUEST_OPTIONS => array(
                    'auth' => array($this->user->username, 'password'),
                    'headers' => array(
                        'Accept' => 'application/xml',
                    )
                )
            )
        );
    }

    public function tearDown()
    {
        $this->cleanUpTestUser();
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
        $this->assertXPathFound("/Failure");
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
        $this->assertXPathFound("/Failure");
    }

    /**
     * @TODO: clean this up into separate file and test in more detail (and fix GP references etc)
     */
    public function testCreatePatient()
    {
        $xml = <<<EOF
<Patient>
    <NHSNumber>0123456789</NHSNumber>
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
    <EthnicGroup>A</EthnicGroup>
    <DateOfDeath/>
    <PracticeCode>F001</PracticeCode>
    <GpCode>G0102926</GpCode>
</Patient>
EOF;
        $this->put('TEST02', $xml);

        $id = $this->xPathQuery("/Success//Id")->item(0)->nodeValue;

        $patient = Patient::model()->findByPk($id);
        $this->assertNotNull($patient);
        $this->assertEquals('API', $patient->last_name);

    }

    public function testUpdateOnlyDoesntCreatePatient()
    {
        $xml = <<<EOF
<Patient updateOnly="1">
    <NHSNumber>0123456789</NHSNumber>
    <HospitalNumber>010101010010101</HospitalNumber>
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
    <EthnicGroup>A</EthnicGroup>
    <DateOfDeath/>
    <PracticeCode>F001</PracticeCode>
    <GpCode>G0102926</GpCode>
</Patient>
EOF;
        $this->put("TESTUpdateOnly", $xml);

        $this->assertXPathFound("/Success");

        $message = $this->xPathQuery("/Success//Message")->item(0)->nodeValue;

        $this->assertEquals("Patient not created", $message);

        $this->assertNull(Patient::model()->findByAttributes(
            array('hos_num' => '010101010010101')));

        $xml = preg_replace('/updateOnly="1"/', "", $xml);

        $this->put("TestCreate", $xml);
        $id = $this->xPathQuery("/Success//Id")->item(0)->nodeValue;

        $patient = Patient::model()->findByPk($id);
        $this->assertNotNull($patient);
        $this->assertEquals('010101010010101', $patient->hos_num);
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
    <EthnicGroup>A</EthnicGroup>
    <DateOfDeath/>
    <PracticeCode>F001</PracticeCode>
    <GpCode>G0102926</GpCode>
</Patient>
EOF;
        $this->put("TEST03", $xml);
        $id = $this->xPathQuery("/Success//Id")->item(0)->nodeValue;

        $patient = Patient::model()->findByPk($id);
        $this->assertNotNull($patient);
        $this->assertEquals('1234535', $patient->hos_num);

        $new_hos_num = '65432';
        $xml = preg_replace('/<Patient/', '<Patient updateOnly="1"', $xml);
        $xml = preg_replace('/HospitalNumber>1234535/', 'HospitalNumber>' . $new_hos_num, $xml);

        $this->put("TEST03", $xml);

        $patient = Patient::model()->findByPk($id);
        $this->assertNotNull($patient);
        $this->assertEquals($new_hos_num, $patient->hos_num);

    }
}