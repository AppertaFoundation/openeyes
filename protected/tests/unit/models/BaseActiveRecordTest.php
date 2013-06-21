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
                       /*   public $fixtures = array(
                         'alllergies' => 'Allergy',
                         ); */
                       public $testattributes = array(
                                                'name' => 'allergy test'
                       );

                       /**
                        * Sets up the fixture, for example, opens a network connection.
                        * This method is called before a test is executed.
                        */
                       protected function setUp() {
                                              parent::setUp();

                                              //using allergy model to test the active record
                                              $this->model = new Allergy;
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

                                              //using allergy model to test the active record

                                              $testmodel = new Allergy;
                                              $testmodel->setAttributes($this->testattributes);

                                              $testmodel->save();

                                              $result = Allergy::model()->findByAttributes(array('name' => 'allergy test'))->getAttributes();
                                              $expected = $this->testattributes;

                                              $this->assertEquals($expected['name'], $result['name'], 'attribute match');
                       }

                       /**
                        * @covers BaseActiveRecord::NHSDate
                        * @todo   Implement testNHSDate().
                        */
                       public function testNHSDate() {

                                              //last_modified_date = 1900-01-01 00:00:00 = 1 Jan 1900
                                              $result = $this->model->NHSDate('last_modified_date', $empty_string = '-');

                                              $expected = '1 Jan 1900';

                                              $this->assertEquals($expected, $result);
                       }

                       /**
                        * @covers BaseActiveRecord::NHSDateAsHTML
                        * @todo   Implement testNHSDateAsHTML().
                        */
                       public function testNHSDateAsHTML() {

                                              //last_modified_date = 1900-01-01 00:00:00 = <span class="day">1</span><span class="mth">Jan</span><span class="yr">1900</span>
                                              $result = $this->model->NHSDateAsHTML('last_modified_date', $empty_string = '-');

                                              $expected = '<span class="day">1</span><span class="mth">Jan</span><span class="yr">1900</span>';

                                              $this->assertEquals($expected, $result);
                       }

                       /**
                        * @covers BaseActiveRecord::getAuditAttributes
                        * @todo   Implement testGetAuditAttributes().
                        */
                       public function testGetAuditAttributes() {

                                              $auditmodel = new Audit;

                                              $expected = 'a:18:{s:6:"action";s:0:"";s:11:"target_type";s:0:"";s:11:"remote_addr";s:0:"";s:15:"http_user_agent";s:0:"";s:11:"server_name";s:0:"";s:11:"request_uri";s:0:"";s:21:"last_modified_user_id";s:1:"1";s:18:"last_modified_date";s:19:"1900-01-01 00:00:00";s:15:"created_user_id";s:1:"1";s:12:"created_date";s:19:"1900-01-01 00:00:00";s:2:"id";N;s:10:"patient_id";N;s:10:"episode_id";N;s:8:"event_id";N;s:7:"user_id";N;s:4:"data";N;s:7:"site_id";N;s:7:"firm_id";N;}';
                                              $result = $auditmodel->getAuditAttributes();

                                              $this->assertEquals($expected, $result, 'result match expected');
                       }

                       /**
                        * @covers BaseActiveRecord::audit
                        * @todo   Implement testAudit().
                        */
                       public function testAudit() {
                                              $this->markTestSkipped('this has been already implemented in the audittest model');
                       }

}
