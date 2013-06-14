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
class ContactTest extends CDbTestCase {

                       public $model;
                       public $fixtures = array(
                                                'contacts' => 'Contact',
                                                'addresses' => 'Address'
                       );

                       public function dataProvider_Search() {
                                              return array(
                                                                       array(array('nick_name' => 'Aylward'), 1, array('contact1')),
                                                                       array(array('nick_name' => 'Collin'), 1, array('contact2')),
                                                                       array(array('nick_name' => 'Allan'), 1, array('contact3')),
                                                                       array(array('nick_name' => 'Blah'), 0, array()),
                                              );
                       }

                       /**
                        * Sets up the fixture, for example, opens a network connection.
                        * This method is called before a test is executed.
                        */
                       protected function setUp() {
                                              parent::setUp();
                                              $this->model = new Contact;
                       }

                       /**
                        * Tears down the fixture, for example, closes a network connection.
                        * This method is called after a test is executed.
                        */
                       protected function tearDown() {
                                              
                       }

                       /**
                        * @covers Contact::model
                        * @todo   Implement testModel().
                        */
                       public function testModel() {
                                              $this->assertEquals('Contact', get_class(Contact::model()), 'Class name should match model.');
                       }

                       /**
                        * @covers Contact::tableName
                        * @todo   Implement testTableName().
                        */
                       public function testTableName() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers Contact::rules
                        * @todo   Implement testRules().
                        */
                       public function testRules() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers Contact::relations
                        * @todo   Implement testRelations().
                        */
                       public function testRelations() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers Contact::attributeLabels
                        * @todo   Implement testAttributeLabels().
                        */
                       public function testAttributeLabels() {
                                              $expected = array(
                                                                       'id' => 'ID',
                                                                       'nick_name' => 'Nickname',
                                                                       'primary_phone' => 'Phone number',
                                                                       'title' => 'Title',
                                                                       'first_name' => 'First name',
                                                                       'last_name' => 'Last name',
                                                                       'qualifications' => 'Qualifications',
                                                                       'contact_label_id' => 'Label',
                                              );

                                              $this->assertEquals($expected, $this->model->attributeLabels(), 'Attribute labels should match.');
                       }

                       /**
                        * @covers Contact::search
                        * @todo   Implement testSearch().
                        */
                       public function testSearch() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers Contact::getFullName
                        * @todo   Implement testGetFullName().
                        */
                       public function testGetFullName() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers Contact::getReversedFullName
                        * @todo   Implement testGetReversedFullName().
                        */
                       public function testGetReversedFullName() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers Contact::getSalutationName
                        * @todo   Implement testGetSalutationName().
                        */
                       public function testGetSalutationName() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers Contact::contactLine
                        * @todo   Implement testContactLine().
                        */
                       public function testContactLine() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers Contact::contactsByLabel
                        * @todo   Implement testContactsByLabel().
                        */
                       public function testContactsByLabel() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers Contact::contactsByModel
                        * @todo   Implement testContactsByModel().
                        */
                       public function testContactsByModel() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers Contact::getType
                        * @todo   Implement testGetType().
                        */
                       public function testGetType() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @dataProvider dataProvider_Search
                        */
                       public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys) {
                                              $contact = new Contact;
                                              $contact->setAttributes($searchTerms);
                                              $results = $contact->search();
                                              $data = $results->getData();

                                              $expectedResults = array();
                                              if (!empty($expectedKeys)) {
                                                                     foreach ($expectedKeys as $key) {
                                                                                            $expectedResults[] = $this->contacts($key);
                                                                     }
                                              }

                                              $this->assertEquals($numResults, $results->getItemCount(), 'Number of results should match.');
                                              $this->assertEquals($expectedResults, $data, 'Actual results should match.');
                       }

}
