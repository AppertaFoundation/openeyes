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
class AuditTest extends ActiveRecordTestCase
{
    /**
     * @var AddressType
     */
    public $model;
    public $fixtures = array(
        'audit' => 'Audit',
    );

    public function getModel()
    {
        return Audit::model();
    }

    public function dataProvider_Search()
    {
        return array(
            array(array('action' => 'action1'), 3, array('audit1')),
        );
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();
        $this->model = new Audit();
    }

    /**
     * @covers Audit
     */
    public function testModel()
    {
        $this->assertEquals('Audit', get_class(Audit::model()), 'Class name should match model.');
    }

    /**
     * @covers Audit
     */
    public function testTableName()
    {
        $this->assertEquals('audit', $this->model->tableName());
    }

    /**
     * @covers Audit
     */
    public function testAttributeLabels()
    {
        $expected = array(
            'id' => 'ID',
            'action' => 'Action',
            'target_type' => 'Target type',
            'patient_id' => 'Patient',
            'episode_id' => 'Episode',
            'event_id' => 'Event',
            'user_id' => 'User',
            'data' => 'Data',
            'remote_addr' => 'Remote address',
            'http_user_agent' => 'HTTP User Agent',
            'server_name' => 'Server name',
            'request_uri' => 'Request URI',
            'site_id' => 'Site',
            'firm_id' => 'Firm',
            'event_type_id' => 'Event Type'
        );

        $this->assertEquals($expected, $this->model->attributeLabels());
    }

    /**
     * @covers       Audit
     * @dataProvider dataProvider_Search
     * @param $searchTerms
     * @param $numResults
     * @param $expectedKeys
     */
    public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
    {
        $audit = new Audit();
        $audit->setAttributes($searchTerms);
        $results = $audit->search();
        $data = $results->getData();

        $expectedResults = array();
        if (!empty($expectedKeys)) {
            foreach ($expectedKeys as $key) {
                $expectedResults[] = $this->audit($key);
            }
        }
        $this->assertEquals($numResults, $results->getItemCount());
        $this->assertEquals($expectedResults, array('0' => $data[0]));
    }

    /**
     * @covers Audit
     */
    public function testGetColour()
    {
        //test the error color
        $audit = new Audit();
        $audit->action = ComponentStubGenerator::generate('AuditAction', array('name' => 'search-error'));
        $result = $audit->getColour();
        $expected = 'fail';

        $this->assertEquals($expected, $result);

        //test the success color
        $audit = new Audit();
        $audit->action = ComponentStubGenerator::generate('AuditAction', array('name' => 'login-successful'));
        $result = $audit->getColour();
        $expected = 'success';

        $this->assertEquals($expected, $result);

        //test the warn color
        $audit = new Audit();
        $audit->action = ComponentStubGenerator::generate('AuditAction', array('name' => 'create-failed'));
        $result = $audit->getColour();
        $expected = 'warn';

        $this->assertEquals($expected, $result);
    }
}
