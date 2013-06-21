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
class LetterTemplateTest extends CDbTestCase {

                       public $fixtures = array(
                                                'specialties' => 'Specialty',
                                                'letterTemplates' => 'LetterTemplate'
                       );

                       public function dataProvider_Search() {
                                              return array(
                                                                       array(array('specialty_id' => 1), 1, array('letterTemplate1')),
                                                                       array(array('specialty_id' => 2), 1, array('letterTemplate2')),
                                                                       array(array('name' => 'name'), 2, array('letterTemplate1', 'letterTemplate2')),
                                                                       array(array('phrase' => 'rest'), 1, array('letterTemplate1')),
                                                                       array(array('send_to' => 3), 0, array()),
                                                                       array(array('cc' => 3), 2, array('letterTemplate1', 'letterTemplate2')),
                                              );
                       }

                       /**
                        * Sets up the fixture, for example, opens a network connection.
                        * This method is called before a test is executed.
                        */
                       protected function setUp() {
                                              parent::setUp();
                                              $this->model = new LetterTemplate;
                       }

                       /**
                        * Tears down the fixture, for example, closes a network connection.
                        * This method is called after a test is executed.
                        */
                       protected function tearDown() {
                                              
                       }

                       /**
                        * @covers LetterTemplate::model
                        * @todo   Implement testModel().
                        */
                       public function testModel() {
                                              $this->assertEquals('LetterTemplate', get_class(Address::model()), 'Class name should match model.');
                       }

                       /**
                        * @covers LetterTemplate::tableName
                        * @todo   Implement testTableName().
                        */
                       public function testTableName() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers LetterTemplate::rules
                        * @todo   Implement testRules().
                        */
                       public function testRules() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers LetterTemplate::relations
                        * @todo   Implement testRelations().
                        */
                       public function testRelations() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers LetterTemplate::attributeLabels
                        * @todo   Implement testAttributeLabels().
                        */
                       public function testAttributeLabels() {
                                              $expected = array(
                                                                       'id' => 'ID',
                                                                       'name' => 'Name',
                                                                       'phrase' => 'Phrase',
                                                                       'subspecialty_id' => 'Subspecialty',
                                                                       'send_to' => 'To',
                                                                       'cc' => 'Cc',
                                              );

                                              $this->assertEquals($expected, $this->model->attributeLabels());
                       }

                       /**
                        * @covers LetterTemplate::search
                        * @todo   Implement testSearch().
                        */
                       public function testSearch() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers LetterTemplate::getSubspecialtyText
                        * @todo   Implement testGetSubspecialtyText().
                        */
                       public function testGetSubspecialtyText() {
                                              $expected = CHtml::listData(Specialty::model()->findAll(), 'id', 'name');
                                              $result = $this->model->getSubspecialtyOptions();

                                              $this->assertEquals($expected, $result, 'Returned options should match.');
                                              $this->assertEquals(count($this->specialties), count($result), 'Should have found all the options.');
                       }

                       /**
                        * @covers LetterTemplate::getToText
                        * @todo   Implement testGetToText().
                        */
                       public function testGetToText() {
                                              $letterTemplate = $this->letterTemplates('letterTemplate1');

                                              $this->assertEquals('Ophthalmologist', $letterTemplate->getToText(), 'Returned text should be correct.');
                       }

                       /**
                        * @covers LetterTemplate::getCcText
                        * @todo   Implement testGetCcText().
                        */
                       public function testGetCcText() {
                                              $letterTemplate = LetterTemplate::model()->findByPk(1);
                                              $this->assertEquals($letterTemplate->getCcText(), 'Optometrist');
                       }

                       /**
                        * @covers LetterTemplate::getSubspecialtyOptions
                        * @todo   Implement testGetSubspecialtyOptions().
                        */
                       public function testGetSubspecialtyOptions() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }

                       /**
                        * @covers LetterTemplate::getContactTypeOptions
                        * @todo   Implement testGetContactTypeOptions().
                        */
                       public function testGetContactTypeOptions() {
                                              // Remove the following lines when you implement this test.
                                              $this->markTestIncomplete(
                                                        'This test has not been implemented yet.'
                                              );
                       }
                        /**
                        * @dataProvider dataProvider_Search
                        */
                       public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys) {

	                   $patient = new Patient;
	                   $patient->setAttributes($searchTerms);
	                   $results = $patient->search($searchTerms);

	                   $data = $results->getData();



	                   $expectedResults = array();
	                   if (!empty($expectedKeys)) {
		               foreach ($expectedKeys as $key) {
			           $expectedResults[] = $this->patients($key);
		               }
	                   }
	                   if (isset($data[0])) {
		               $this->assertEquals($expectedResults, array('0' => $data[0]->getAttributes()));
	                   }
                       }
                         public function testRandomData_ParamSetOff_ReturnsFalse() {

	                   Yii::app()->params['pseudonymise_patient_details'] = false;

	                   $attributes = array(
		                 'hos_num' => 5550101,
		                 'first_name' => 'Rod',
		                 'last_name' => 'Flange',
		                 'dob' => '1979-09-08',
		                 'title' => 'MR',
		                 'primary_phone' => '0208 1111111',
		                 'address_id' => 1);

	                   $patient = new Patient;
	                   $patient->setAttributes($attributes);
	                   $patient->save();

 
	                   $this->assertEquals($attributes['hos_num'], $patient->getAttribute('hos_num'), 'Data should not have changed.');
                       }

                       public function testGetAge_ReturnsCorrectValue() {

	                   Yii::app()->params['pseudonymise_patient_details'] = false;

	                   $attributes = array(
		                 'hos_num' => 5550101,
		                 'first_name' => 'Rod',
		                 'last_name' => 'Flange',
		                 'dob' => '1979-09-08',
		                 'title' => 'MR',
		                 'primary_phone' => '0208 1111111',
		                 'address_id' => 1);

	                   $patient = new Patient;
	                   $patient->setAttributes($attributes);
	                   $patient->save();

	                   $age = date('Y') - 1979;
	                   if (date('md') < '0908') {
		               $age--; // have not had a birthday yet
	                   }

	                   $this->assertEquals($age, $patient->getAge());
                       }


}
