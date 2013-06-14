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
class SectionTypeTest extends CDbTestCase {

                       public $fixtures = array(
                                                'sections' => 'Section',
                                                'sectionTypes' => 'SectionType',
                                                'services' => 'Service',
                                                'specialties' => 'Specialty',
                                                'serviceSubspecialtyAssignment' => 'ServiceSubspecialtyAssignment',
                                                'firms' => 'Firm',
                                                'eventTypes' => 'EventType',
                                                'elementTypes' => 'ElementType',
                                                //'possibleElementTypes' => 'PossibleElementType',
                                                //'siteElementTypes' => 'SiteElementType',
                                                'phraseNames' => 'PhraseName',
                       );

                       /**
                        * Sets up the fixture, for example, opens a network connection.
                        * This method is called before a test is executed.
                        */
                       protected function setUp() {
                                               parent::setUp();
                                              $this->model = new SectionType;
                       }

                       /**
                        * Tears down the fixture, for example, closes a network connection.
                        * This method is called after a test is executed.
                        */
                       protected function tearDown() {
                                              
                       }

                       /**
                        * @covers SectionType::model
                        * @todo   Implement testModel().
                        */
                       public function testModel() {
                                              $this->assertEquals('SectionType', get_class(SectionType::model()), 'Class name should match model.');
                       }

                       /**
                        * @covers SectionType::tableName
                        * @todo   Implement testTableName().
                        */
                       public function testTableName() {
                                              $this->assertEquals('section_type', $this->model->tableName());
                       }

                       /**
                        * @covers SectionType::rules
                        * @todo   Implement testRules().
                        */
                       public function testRules() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers SectionType::relations
                        * @todo   Implement testRelations().
                        */
                       public function testRelations() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers SectionType::attributeLabels
                        * @todo   Implement testAttributeLabels().
                        */
                       public function testAttributeLabels() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers SectionType::search
                        * @todo   Implement testSearch().
                        */
                       public function testSearch() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       public function testGet_InvalidParameters_ReturnsFalse() {
                                              $fakeId = 9999;
                                              $result = SectionType::model()->findByPk($fakeId);
                                              $this->assertNull($result);
                       }

                       public function testGet_ValidParameters_ReturnsCorrectData() {
                                              $fakeId = 9999;

                                              $expected = $this->sectionTypes('sectionType1');
                                              $result = SectionType::model()->findByPk($expected['id']);

                                              $this->assertEquals(get_class($result), 'SectionType');
                                              $this->assertEquals($expected, $result);
                       }

                       public function testCreate() {
                                              $sectionType = new SectionType;
                                              $sectionType->setAttributes(array(
                                                                       'name' => 'Testing phrasename',
                                                                       'section_type_id' => $this->sectionTypes['sectionType1']['id']
                                              ));
                                              $this->assertTrue($sectionType->save(true));
                       }

                       public function testUpdate() {
                                              $expected = 'Testing again';
                                              $sectionType = SectionType::model()->findByPk($this->sectionTypes['sectionType1']['id']);
                                              $sectionType->name = $expected;
                                              $sectionType->save();
                                              $sectionType = SectionType::model()->findByPk($this->sectionTypes['sectionType1']['id']);
                                              $this->assertEquals($expected, $sectionType->name);
                       }

                       public function testDelete() {
                                              $sectionType = SectionType::model()->findByPk($this->sectionTypes['sectionType2']['id']);
                                              $sectionType->delete();
                                              $result = SectionType::model()->findByPk($this->sectionTypes['sectionType2']['id']);
                                              $this->assertNull($result);
                       }

}
