<?php

/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
class AuditTest extends CDbTestCase {

                       /**
                        * @var AddressType
                        */
                       public $model;
                       public $fixtures = array(
                                                'audit' => 'Audit',
                       );

                       public function dataProvider_Search() {

                                              return array(
                                                                       array(array('action' => 'action1'), 3, array('audit1')), 
                                              );
                       }

                       /**
                        * Sets up the fixture, for example, opens a network connection.
                        * This method is called before a test is executed.
                        */
                       protected function setUp() {
                                              parent::setUp();
                                              $this->model = new Audit;
                       }

                       /**
                        * Tears down the fixture, for example, closes a network connection.
                        * This method is called after a test is executed.
                        */
                       protected function tearDown() {
                                              
                       }

                       /**
                        * @covers Audit::model
                        * @todo   Implement testModel().
                        */
                       public function testModel() {
                                              $this->assertEquals('Audit', get_class(Audit::model()), 'Class name should match model.');
                       }

                       /**
                        * @covers Audit::tableName
                        * @todo   Implement testTableName().
                        */
                       public function testTableName() {
                                              $this->assertEquals('audit', $this->model->tableName());
                       }

                       /**
                        * @covers Audit::rules
                        * @todo   Implement testRules().
                        */
                       public function testRules() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers Audit::relations
                        * @todo   Implement testRelations().
                        */
                       public function testRelations() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers Audit::attributeLabels
                        * @todo   Implement testAttributeLabels().
                        */
                       public function testAttributeLabels() {

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
                                              );

                                              $this->assertEquals($expected, $this->model->attributeLabels());
                       }

                       /*
                        * @covers Address::search
                        * @todo   Implement testSearch().
                        */

                       public function testSearch() {

                                              $this->markTestSkipped(
                                                        'already implemented as "testSearch_WithValidTerms_ReturnsExpectedResults" '
                                              );
                       }

                       /**
                        * @dataProvider dataProvider_Search
                        */
                       public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys) {

                                              $audit = new Audit;
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
                        * @covers Audit::getColour
                        * @todo   Implement testGetColour().
                        */
                       public function testGetColour() {

                                              //test the error's color
                                              $audit = new Audit;
                                              $audit->action = 'search-error';
                                              $result = $audit->getColour();
                                              $expected = 'Red';

                                              $this->assertEquals($expected, $result);
                       }

                       /**
                        * @covers Audit::add
                        * @todo   Implement testAdd().
                        */
                       public function testAdd() {

                                              $audit = new Audit;

                                              //values to insert
                                              $target = $this->audit('audit3')->getAttribute('target_type');
                                              $action = $this->audit('audit3')->getAttribute('action');
                                              $data = $this->audit('audit3')->getAttribute('data');

                                              $_SERVER['REMOTE_ADDR'] = $this->audit('audit3')->getAttribute('remote_addr');
                                              $_SERVER['HTTP_USER_AGENT'] = $this->audit('audit3')->getAttribute('http_user_agent');
                                              $_SERVER['SERVER_NAME'] = $this->audit('audit3')->getAttribute('server_name');
                                              $_SERVER['REQUEST_URI'] = $this->audit('audit3')->getAttribute('request_uri');

                                              $propreties = array(
                                                                       'id' => '99',
                                                                       'patient_id' => '3',
                                                                       'episode_id' => '3',
                                                                       'event_id' => '3',
                                                                       'user_id' => '3',
                                                                       'data' => 'Data 3',
                                                                       'remote_addr' => 'test',
                                                                       'http_user_agent' => 'HTTP User Agent3',
                                                                       'server_name' => 'Server name3',
                                                                       'request_uri' => 'Request URI3',
                                                                       'site_id' => '3',
                                                                       'firm_id' => '3'
                                              );

                                              $result = $audit->add($target, $action, $data, false, $propreties)->getAttributes();
                                              $expected = Audit::model()->findByPk(99)->getAttributes();

                                              $this->assertEquals($expected, $result);
                       }

}
