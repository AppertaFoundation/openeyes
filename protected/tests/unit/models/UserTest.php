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
class UserTest extends CDbTestCase {

                       public $fixtures = array(
                                                'users' => 'User'
                       );

                       public function dataProvider_Search() {
                                              return array(
                                                                       array(array('username' => 'Joe'), 1, array('user1')),
                                                                       array(array('username' => 'Jane'), 1, array('user2')),
                                                                       array(array('last_name' => 'bloggs'), 2, array('user1', 'user2')), /* case insensitivity test - needs _ci column collation */
                                                                       array(array('username' => 'no-one'), 0, array()),
                                              );
                       }

                       /**
                        * Sets up the fixture, for example, opens a network connection.
                        * This method is called before a test is executed.
                        */
                       protected function setUp() {
                                              parent::setUp();
                                              $this->model = new User;
                       }

                       /**
                        * Tears down the fixture, for example, closes a network connection.
                        * This method is called after a test is executed.
                        */
                       protected function tearDown() {
                                              
                       }

                       /**
                        * @covers User::model
                        * @todo   Implement testModel().
                        */
                       public function testModel() {
                                              $this->assertEquals('User', get_class(User::model()), 'Class name should match model.');
                       }

                       /**
                        * @covers User::tableName
                        * @todo   Implement testTableName().
                        */
                       public function testTableName() {
                                              $this->assertEquals('user', $this->model->tableName());
                       }

                       /**
                        * @covers User::behaviors
                        * @todo   Implement testBehaviors().
                        */
                       public function testBehaviors() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers User::rules
                        * @todo   Implement testRules().
                        */
                       public function testRules() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers User::relations
                        * @todo   Implement testRelations().
                        */
                       public function testRelations() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers User::changeFirm
                        * @todo   Implement testChangeFirm().
                        */
                       public function testChangeFirm() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers User::attributeLabels
                        * @todo   Implement testAttributeLabels().
                        */
                       public function testAttributeLabels() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers User::search
                        * @todo   Implement testSearch().
                        */
                       public function testSearch() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers User::save
                        * @todo   Implement testSave().
                        */
                       public function testSave() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @dataProvider dataProvider_Search
                        */
                       public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys) {
                                              $user = new User;
                                              $searchTerms['global_firm_rights'] = null; // ignore what setting global_firm_rights has
                                              $user->setAttributes($searchTerms, true);
                                              $results = $user->search();
                                              $data = $results->getData();

                                              $expectedResults = array();
                                              if (!empty($expectedKeys)) {
                                                                     foreach ($expectedKeys as $key) {
                                                                                            $expectedResults[] = $this->users($key);
                                                                     }
                                              }

                                              $this->assertEquals($numResults, $results->getItemCount());
                                              $this->assertEquals($expectedResults, $data);
                       }

                       /**
                        * @covers User::hashPassword
                        * @todo   Implement testHashPassword().
                        */
                       public function testHashPassword() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers User::validatePassword
                        * @todo   Implement testValidatePassword().
                        */
                       public function testValidatePassword() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers User::getActiveText
                        * @todo   Implement testGetActiveText().
                        */
                       public function testGetActiveText() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers User::getGlobalFirmRightsText
                        * @todo   Implement testGetGlobalFirmRightsText().
                        */
                       public function testGetGlobalFirmRightsText() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers User::getFullName
                        * @todo   Implement testGetFullName().
                        */
                       public function testGetFullName() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers User::getReversedFullName
                        * @todo   Implement testGetReversedFullName().
                        */
                       public function testGetReversedFullName() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers User::getFullNameAndTitle
                        * @todo   Implement testGetFullNameAndTitle().
                        */
                       public function testGetFullNameAndTitle() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers User::getFullNameAndTitleAndQualifications
                        * @todo   Implement testGetFullNameAndTitleAndQualifications().
                        */
                       public function testGetFullNameAndTitleAndQualifications() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers User::getReversedFullNameAndTitle
                        * @todo   Implement testGetReversedFullNameAndTitle().
                        */
                       public function testGetReversedFullNameAndTitle() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers User::isConsultant
                        * @todo   Implement testIsConsultant().
                        */
                       public function testIsConsultant() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers User::getList
                        * @todo   Implement testGetList().
                        */
                       public function testGetList() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers User::getSurgeons
                        * @todo   Implement testGetSurgeons().
                        */
                       public function testGetSurgeons() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers User::audit
                        * @todo   Implement testAudit().
                        */
                       public function testAudit() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers User::getListSurgeons
                        * @todo   Implement testGetListSurgeons().
                        */
                       public function testGetListSurgeons() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers User::getReportDisplay
                        * @todo   Implement testGetReportDisplay().
                        */
                       public function testGetReportDisplay() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers User::beforeValidate
                        * @todo   Implement testBeforeValidate().
                        */
                       public function testBeforeValidate() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers User::randomSalt
                        * @todo   Implement testRandomSalt().
                        */
                       public function testRandomSalt() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers User::getAccesslevelstring
                        * @todo   Implement testGetAccesslevelstring().
                        */
                       public function testGetAccesslevelstring() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

}
