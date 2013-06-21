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
class AddressTest extends CDbTestCase {

      /**
       * @var Address
       */
      public $model;
      public $fixtures = array(
                               'patients' => 'Patient',
                               'addresses' => 'Address'
      );

      public function dataProvider_Search() {

            return array(
                                     array(array('address1' => 'flat 1'), 1, array('address1')),
                                     array(array('address1' => 'FLAT 1'), 1, array('address1')), /* case insensitivity test */
                                     array(array('address2' => 'bleakley'), 3, array('address1', 'address2', 'address3')),
                                     array(array('city' => 'flitchley'), 3, array('address1', 'address2', 'address3')),
                                     array(array('postcode' => 'ec1v'), 3, array('address1', 'address2', 'address3')),
                                     array(array('county' => 'london'), 3, array('address1', 'address2', 'address3')),
                                     array(array('email' => 'bleakley1'), 1, array('address1')),
                                     array(array('email' => 'foobar'), 0, array()),
            );
      }

      /**
       * Sets up the fixture, for example, opens a network connection.
       * This method is called before a test is executed.
       */
      public function setUp() {

            parent::setUp();
            $this->model = new Address;
      }

      /**
       * Tears down the fixture, for example, closes a network connection.
       * This method is called after a test is executed.
       */
      protected function tearDown() {
            
      }

      /**
       * @covers Address::model
       * @todo   Implement testModel().
       */
      public function testModel() {

            $this->assertEquals('Address', get_class(Address::model()), 'Class name should match model.');
      }

      /**
       * @covers Address::tableName
       * @todo   Implement testTableName().
       */
      public function testTableName() {
            $this->assertEquals('address', $this->model->tableName());
      }

      /**
       * @covers Address::attributeLabels
       * @todo   Implement testAttributeLabels().
       */
      public function testAttributeLabels() {
	         
            $expected = array(
                                     'id' => 'ID',
                                     'address1' => 'Address1',
                                     'address2' => 'Address2',
                                     'city' => 'City',
                                     'postcode' => 'Postcode',
                                     'county' => 'County',
                                     'country_id' => 'Country',
                                     'email' => 'Email',
            );

            $this->assertEquals($expected, $this->model->attributeLabels());
      }

      /**
       * @covers AuditTrail::rules
       * @todo   Implement testRules().
       */
      public function testRules() {

            $this->assertTrue($this->addresses('address3')->validate());
            $this->assertEmpty($this->addresses('address3')->errors);
      }

      /**
       * @covers Address::isCurrent
       * @todo   Implement testIsCurrent().
       */
      public function testIsCurrent() {

            $this->assertTrue($this->model->isCurrent());
      }

      /**
       * @covers Address::getLetterLine
       * @todo   Implement testGetLetterLine().
       */
      public function testGetLetterLine() {

            $expected = $this->addresses('address3')->getLetterLine($include_country = false);

            $this->model->setAttributes($this->addresses('address3')->getAttributes());

            $this->assertEquals($expected, $this->model->getLetterLine($include_country = false));
      }

      /**
       * @covers Address::getSummary
       * @todo   Implement testGetSummary().
       */
      public function testGetSummary() {

            //set attributes
            $this->model->setAttributes($this->addresses('address3')->getAttributes());

            $this->assertEquals($this->addresses('address3')->address1, $this->model->GetSummary());
      }

      /**
       * @covers Address::getLetterArray
       * @todo   Implement testGetLetterArray().
       */
      public function testGetLetterArray() {

            $expected = array(
                                     0 => '1',
                                     1 => 'flat 1',
                                     2 => 'bleakley creek',
                                     3 => 'flitchley',
                                     4 => 'london',
                                     5 => 'ec1v 0dx',
            );

            $this->model->setAttributes($this->addresses('address1')->getAttributes());

            $this->assertEquals($expected, $this->model->getLetterArray($include_country = false, $name = true));
      }

      /**
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

            $this->model->setAttributes($searchTerms);
            $results = $this->model->search();
            $data = $results->getData();

            $expectedResults = array();
            if (!empty($expectedKeys)) {
                  foreach ($expectedKeys as $key) {
                        $expectedResults[] = $this->addresses($key);
                  }
            }

            $this->assertEquals($numResults, $results->getItemCount());
            $this->assertEquals($expectedResults, $data);
      }

}
