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

class ElementRegisteredBlindTest extends CDbTestCase
{
	public $user;
	public $firm;
	public $patient;
	public $element;

	public $fixtures = array(
		'users' => 'User',
		'firms' => 'Firm',
		'patients' => 'Patient',
		'episodes' => 'Episode',
		'eventTypes' => 'EventType',
		'events' => 'Event',
		'elements' => 'ElementRegisteredBlind'
	);

	public function setUp()
	{
		parent::setUp();
		$this->user = $this->users('user1');
		$this->firm = $this->firms('firm1');
		$this->patient = $this->patients('patient1');
		$this->element = new ElementRegisteredBlind($this->user->id, $this->firm->id, $this->patient->id);
	}

	public function dataProvider_Search()
	{
		return array(
			array(array('status' => '1'), 1, array('element1')),
			array(array('status' => '2'), 1, array('element2')),
			array(array('status' => '3'), 0, array()),
		);
	}

	public function dataProvider_StatusText()
	{
		return array(
			array(ElementRegisteredBlind::NOT_REGISTERED, 'Not Registered'),
			array(ElementRegisteredBlind::SIGHT_IMPAIRED, 'Sight Impaired'),
			array(ElementRegisteredBlind::SEVERELY_SIGHT_IMPAIRED, 'Severely Sight Impared')
		);
	}

	/**
	 * @dataProvider dataProvider_Search
	 */
	public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
	{
		$element = $this->element;
		$element->setAttributes($searchTerms);
		$results = $element->search();
		$data = $results->getData();

		$expectedResults = array();
		if (!empty($expectedKeys)) {
			foreach ($expectedKeys as $key) {
				$expectedResults[] = $this->elements($key);
			}
		}

		$this->assertEquals($numResults, $results->getItemCount());
		$this->assertEquals($expectedResults, $data);
	}

	public function testBasicCreate()
	{
		$element = $this->element;
		$element->setAttributes(array(
			'event_id' => 3,
			'status' => ElementRegisteredBlind::NOT_REGISTERED,
		));

		$this->assertTrue($element->save(true));
	}

	public function testAttributeLabels()
	{
		$expected = array(
			'id' => 'ID',
			'event_id' => 'Event',
			'status' => 'Status',
		);

		$this->assertEquals($expected, $this->element->attributeLabels());
	}

	public function testModel()
	{
		$this->assertEquals('ElementRegisteredBlind', get_class(ElementRegisteredBlind::model()));
	}

	public function testUpdate()
	{
		$element = $this->elements('element1');

		$element->status = ElementRegisteredBlind::SIGHT_IMPAIRED;

		$this->assertTrue($element->save(true));
	}

	public function testGetSelectOptions()
	{
		$expected = array(
			ElementRegisteredBlind::NOT_REGISTERED => 'Not Registered',
			ElementRegisteredBlind::SIGHT_IMPAIRED => 'Sight Impaired',
			ElementRegisteredBlind::SEVERELY_SIGHT_IMPAIRED => 'Severely Sight Impaired'
		);
		$this->assertEquals($expected, $this->element->getSelectOptions());
	}

	/**
	 * @dataProvider dataProvider_StatusText
	 */
	public function testGetStatusText($newStatus, $expectedText)
	{
		$element = $this->element;
		$element->event_id = 3;
		$element->status = $newStatus;
		$element->save();

		$this->assertEquals($expectedText, $element->getStatusText());
	}
}
