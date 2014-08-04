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
class ContactLocationTest extends CDbTestCase
{
	/**
	 * @var ContactLocation
	 */
	public $model;
	public $fixtures = array(
		'Address',
		'Contact',
		'Institution',
		'Site',
		'contactlocations' => 'ContactLocation',
	);

	public $expectedLetterAddress;

	public function dataProvider_Search()
	{
		return array(
			array(array('contact_id' => 1, 'site_id' => 1), 1, array('contactlocation1')),
			array(array('contact_id' => 2, 'site_id' => 2), 1, array('contactlocation2')),
			array(array('institution_id' => 1), 3, array('contactlocation1', 'contactlocation2', 'contactlocation3')),
		);
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->model = new ContactLocation;
		$this->expectedLetterAddress =Array(
			'Moorfields at City Road',
			'flat 1',
			'bleakley creek',
			'flitchley',
			'london',
			'ec1v 0dx'
		);
	}

	/**
	 * @covers ContactLocation::model
	 */
	public function testModel()
	{
		$this->assertEquals('ContactLocation', get_class(ContactLocation::model()), 'Class name should match model.');
	}

	/**
	 * @covers ContactLocation::tableName
	 */
	public function testTableName()
	{
		$this->assertEquals('contact_location', $this->model->tableName());
	}

	/**
	 * @covers ContactLocation::behaviors
	 */
	public function testBehaviors()
	{
		$expected = array('ContactBehavior' => array('class' => 'application.behaviors.ContactBehavior'));
		$this->assertEquals($expected, $this->model->behaviors());
	}

	/**
	 * @covers ContactLocation::rules
	 */
	public function testRules()
	{
		$this->assertTrue($this->contactlocations('contactlocation1')->validate());
		$this->assertEmpty($this->contactlocations('contactlocation2')->errors);
	}

	/**
	 * @covers ContactLocation::attributeLocations
	 */
	public function testAttributeLocations()
	{
		$expected = array('id' => 'ID', 'name' => 'Name');

		$this->assertEquals($expected, $this->model->attributeLocations());
	}

	/**
	 * @covers ContactLocation::search
	 */
	public function testSearch()
	{
		$conLoc = $this->contactlocations('contactlocation1');
		$conLoc->id = 2;
		$result = $this->contactlocations('contactlocation1')->search();
		$result = $result->getData();
		$this->assertEquals('Collin', $result[0]->contact->nick_name);
	}

	/**
	 * @covers ContactLocation::__toString
	 */
	public function test__toString()
	{
		$expected = 'City Road';
		$result = $this->contactlocations('contactlocation1')->__toString();

		$this->assertContains($expected, $result, $expected . " not found");
		$this->assertGreaterThan(0 ,strlen($result));
		$this->assertNotNull($result);
	}

	/**
	 * @covers ContactLocation::getLetterAddress
	 */
	public function testGetLetterAddress()
	{
		$expected = $this->expectedLetterAddress ;
		$result = $this->contactlocations('contactlocation1')->getLetterAddress();
		$this->assertEquals($expected, $result);
	}

	/**
	 * @covers ContactLocation::getLetterArray
	 */
	public function testGetLetterArray()
	{
		Yii::app()->session['selected_site_id'] = 1;

		$expected = $this->expectedLetterAddress ;

		$result = $this->contactlocations('contactlocation1')->getLetterArray(true);

		$this->assertEquals($expected, $result);
	}

	/**
	 * @covers ContactLocation::getPatients
	 */
	public function testGetPatients()
	{
		$this->model->setAttribute('contact_id', 1);
		$result = $this->contactlocations('contactlocation1')->GetPatients();
		$expected = $this->model->getPatients();

		$this->assertEquals($expected, $result);
	}

}
