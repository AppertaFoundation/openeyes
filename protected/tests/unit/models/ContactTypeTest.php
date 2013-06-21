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
class ContactTypeTest extends CDbTestCase {

      /**
       * @var ContactType
       */
      protected $model;
      public $fixtures = array(
                               'contacttypes' => 'ContactType'
      );

      public function dataProvider_Search() {

            return array(
                                     array(array('name' => 'GP', 'id' => 1), 1, array('contacttype1')),
                                     array(array('letter_template_only'), 8, array('contacttype1', 'contacttype2', 'contacttype3', 'contacttype4', 'contacttype5', 'contacttype6', 'contacttype7', 'contacttype8')),
                                     array(array('name' => 'foobar'), 0, array()),
            );
      }

      /**
       * Sets up the fixture, for example, opens a network connection.
       * This method is called before a test is executed.
       */
      protected function setUp() {

            parent::setUp();
            $this->model = new ContactType;
      }

      /**
       * Tears down the fixture, for example, closes a network connection.
       * This method is called after a test is executed.
       */
      protected function tearDown() {
            
      }

      /**
       * @covers ContactType::model
       * @todo   Implement testModel().
       */
      public function testModel() {

            $this->assertEquals('ContactType', get_class(ContactType::model()), 'Class name should match model.');
      }

      /**
       * @covers ContactType::tableName
       * @todo   Implement testTableName().
       */
      public function testTableName() {

            $this->assertEquals('contact_type', $this->model->tableName());
      }

      /**
       * @covers ContactType::rules
       * @todo   Implement testRules().
       */
      public function testRules() {

            $this->assertTrue($this->contacttypes('contacttype1')->validate());
            $this->assertEmpty($this->contacttypes('contacttype1')->errors);
      }

      /**
       * @covers ContactType::relations
       * @todo   Implement testRelations().
       */
      public function testRelations() {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
                      'This test has not been implemented yet.'
            );
      }

      /**
       * @covers ContactType::attributeLabels
       * @todo   Implement testAttributeLabels().
       */
      public function testAttributeLabels() {

            $expected = array(
                                     'id' => 'ID',
                                     'name' => 'Name',
                                     'letter_template_only' => 'Letter Template Only',
            );

            $this->assertEquals($expected, $this->model->attributeLabels());
      }

      /**
       * @covers ContactType::search
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
                        $expectedResults[] = $this->contacttypes($key);
                  }
            }

            $this->assertEquals($numResults, $results->getItemCount());
            $this->assertEquals($expectedResults, $data);
      }

}
