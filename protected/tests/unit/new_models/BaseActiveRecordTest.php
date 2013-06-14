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
class BaseActiveRecordTest extends CDbTestCase {

                       /**
                        * @var AddressType
                        */
                       public $model;
                    //   public $fixtures = array(
                    //                            'audit' => 'Audit',
                   //    );
                       public $testattributes = array( 
                                                'action' => 'actiontest',
                                                'target_type' => 'targettypetest',
                                                'patient_id' => 1,
                                                'episode_id' => 1,
                                                'event_id' => 1,
                                                'user_id' => 1,
                                                'data' => 'Data test',
                                                'remote_addr' => 'test',
                                                'http_user_agent' => 'HTTP User Agent test',
                                                'server_name' => 'servernametest',
                                                'request_uri' => 'request/uritest',
                                                'site_id' => 1,
                                                'firm_id' => 1);

                       /**
                        * Sets up the fixture, for example, opens a network connection.
                        * This method is called before a test is executed.
                        */
                       protected function setUp() {
                                              parent::setUp();

                                              //using audit model to test
                                              $this->model = new Audit;
                       }

                       /**
                        * Tears down the fixture, for example, closes a network connection.
                        * This method is called after a test is executed.
                        */
                       protected function tearDown() {
                                              
                       }

                       /**
                        * @covers BaseActiveRecord::behaviors
                        * @todo   Implement testBehaviors().
                        */
                       public function testBehaviors() {


                                              Yii::app()->params['audit_trail'] = true;

                                              $result = $this->model->behaviors();
                                              $this->assertArrayHasKey('LoggableBehavior', $result);
                       }

                       /**
                        * @covers BaseActiveRecord::save
                        * @todo   Implement testSave().
                        */
                       public function testSave() {

                                              $testmodel = new Audit;
                                              $testmodel->setAttributes($this->testattributes);


                                              $testmodel->save(false, $testmodel->getAttributes(), false);

                                              print_r('get attributes');
                                              print_r($testmodel->getAttributes());
                                  //            $result = Audit::model()->findByPk(111);
                       }

                       /**
                        * @covers BaseActiveRecord::NHSDate
                        * @todo   Implement testNHSDate().
                        */
                       public function testNHSDate() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers BaseActiveRecord::NHSDateAsHTML
                        * @todo   Implement testNHSDateAsHTML().
                        */
                       public function testNHSDateAsHTML() {

                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers BaseActiveRecord::getAuditAttributes
                        * @todo   Implement testGetAuditAttributes().
                        */
                       public function testGetAuditAttributes() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers BaseActiveRecord::audit
                        * @todo   Implement testAudit().
                        */
                       public function testAudit() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

}
