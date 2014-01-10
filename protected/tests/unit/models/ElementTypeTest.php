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
class ElementTypeTest extends CDbTestCase {

	/**
	* @var ElementType
	*/
	protected $model;
	public $fixtures = array(
		'event_type' => 'EventType',
		'elementtypes' => 'ElementType',
	);

	   /**
	    * Sets up the fixture, for example, opens a network connection.
	    * This method is called before a test is executed.
	    */
	   protected function setUp() {
		      parent::setUp();
		      $this->model = new ElementType;
	   }

	   /**
	    * Tears down the fixture, for example, closes a network connection.
	    * This method is called after a test is executed.
	    */
	   protected function tearDown() {

	   }

	   /**
	    * @covers ElementType::model
	    * @todo   Implement testModel().
	    */
	   public function testModel() {

		      $this->assertEquals('ElementType', get_class(ElementType::model()), 'Class name should match model.');
	   }

	   /**
	    * @covers ElementType::tableName
	    * @todo   Implement testTableName().
	    */
	   public function testTableName() {

		      $this->assertEquals('element_type', $this->model->tableName());
	   }

	   /**
	    * @covers ElementType::rules
	    * @todo   Implement testRules().
	    */
	   public function testRules() {

		      $this->assertTrue($this->elementtypes('history')->validate());
		      $this->assertEmpty($this->elementtypes('history')->errors);
	   }

	   /**
	    * @covers ElementType::relations
	    * @todo   Implement testRelations().
	    */
	   public function testRelations() {
		      // Remove the following lines when you implement this test.
		      $this->markTestIncomplete(
		                'This test has not been implemented yet.'
		      );
	   }

	   /**
	    * @covers ElementType::attributeLabels
	    * @todo   Implement testAttributeLabels().
	    */
	   public function testAttributeLabels() {

		      $expected = array(
			       'id' => 'ID',
			       'name' => 'Name',
			       'class_name' => 'Class Name',);

		      $this->assertEquals($expected, $this->model->attributeLabels());
	   }

	   /**
	    * @covers ElementType::getDescendents
	    * @todo   Implement testGetDescendents().
	    */
	   public function testGetDescendents() {

		      $result = $this->model->getDescendents();

		      $expected = array();


		      $this->assertEquals($expected, $result);
	   }

	/**
	* @covers ElementType::search
	* @todo   Implement testSearch().
	*/
	/*
	public function testSearch()
	{
		$this->model->setAttributes($this->elementtypes('history')->getAttributes());
		$results = $this->model->search();
		$data = $results->getData();
		echo $results->getItemCount();
		$expectedKeys = array('history');
		$expectedResults = array();
		if (!empty($expectedKeys)) {
			 foreach ($expectedKeys as $key) {
					$expectedResults[] = $this->elementtypes($key);
			 }
		}
		$this->assertEquals(1, $results->getItemCount());
		$this->assertEquals($expectedResults, $data);
	}
	*/

}
