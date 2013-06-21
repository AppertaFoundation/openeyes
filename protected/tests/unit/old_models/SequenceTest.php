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


class SequenceTest extends CDbTestCase
{
	public $model;

	public $fixtures = array(
		'firms' => 'Firm',
		'sites' => 'Site',
		'theatres' => 'Theatre',
		'sequences' => 'Sequence',
		'specialties' => 'Specialty'
	);

	public function dataProvider_FrequencyIntervals()
	{
		$timestamp = time();
		return array(
			array(Sequence::FREQUENCY_1WEEK, $timestamp, (60 * 60 * 24 * 7)),
			array(Sequence::FREQUENCY_2WEEKS, $timestamp, (60 * 60 * 24 * 14)),
			array(Sequence::FREQUENCY_3WEEKS, $timestamp, (60 * 60 * 24 * 21)),
			array(Sequence::FREQUENCY_4WEEKS, $timestamp, (60 * 60 * 24 * 28)),
			array(Sequence::FREQUENCY_ONCE, $timestamp, $timestamp + 1),
		);
	}

	public function dataProvider_CreateAttributes()
	{
		return array(
			array(array(
				'start_date' => date('Y-m-d', strtotime('+1 week')),
				'start_time' => '08:00',
				'end_time' => '12:00',
				'end_date' => null,
				'repeat_interval' => Sequence::FREQUENCY_1WEEK), true,
			),
			array(array(
				'start_date' => null,
				'start_time' => '08:00',
				'end_time' => '12:00',
				'end_date' => date('Y-m-d', strtotime('+1 week')),
				'repeat_interval' => Sequence::FREQUENCY_1WEEK), false
			),
			array(array(
				'start_date' => date('Y-m-d', strtotime('+1 week')),
				'start_time' => '08:00',
				'end_time' => '12:00',
				'end_date' => date('Y-m-d', strtotime('+2 weeks')),
				'repeat_interval' => Sequence::FREQUENCY_1WEEK), true
			),
			array(array(
				'start_date' => date('Y-m-d', strtotime('+3 week')),
				'start_time' => '08:00',
				'end_time' => '12:00',
				'end_date' => date('Y-m-d', strtotime('+2 weeks')),
				'repeat_interval' => Sequence::FREQUENCY_1WEEK), false
			),
			array(array(
				'start_date' => date('Y-m-d', strtotime('+1 week')),
				'start_time' => '12:00',
				'end_time' => '10:00',
				'end_date' => date('Y-m-d', strtotime('+2 weeks')),
				'repeat_interval' => Sequence::FREQUENCY_1WEEK), false
			),
		);
	}

	public function dataProvider_Search()
	{
		return array(
			array(array('theatre_id' => 1), 4, array('sequence1', 'sequence2', 'sequence3', 'sequence4')),
			array(array('start_date' => date('Y-m-d', strtotime('+1 day'))), 1, array('sequence1')),
			array(array('end_date' => date('Y-m-d', strtotime('-1 day'))), 1, array('sequence3')),
			array(array('end_date' => date('Y-m-d', strtotime('-1 year'))), 0, array()),
		);
	}

	public function dataProvider_PreExistingSequences()
	{
		return array(
			// start date <= end date
			array(array(
				'start_date' => date('Y-m-d', strtotime('-4 weeks')),
				'start_time' => '08:00',
				'end_time' => '12:00',
				'end_date' => null,
				'repeat_interval' => Sequence::FREQUENCY_1WEEK), true,
				'Start date is < end date'
			),
			// end date >= start date
			array(array(
				'start_date' => date('Y-m-d', strtotime('+4 weeks')),
				'start_time' => '08:00',
				'end_time' => '12:00',
				'end_date' => date('Y-m-d', strtotime('+52 weeks')),
				'repeat_interval' => Sequence::FREQUENCY_1WEEK), false,
				'End date is > start date'
			),
			// start time between start and end times
			array(array(
				'start_date' => date('Y-m-d', time()),
				'start_time' => '09:00',
				'end_time' => '17:00',
				'end_date' => null,
				'repeat_interval' => Sequence::FREQUENCY_1WEEK), true,
				'Start time is between start ane end times'
			),
			// end time between start and end times
			array(array(
				'start_date' => date('Y-m-d', time()),
				'start_time' => '02:00',
				'end_time' => '11:00',
				'end_date' => null,
				'repeat_interval' => Sequence::FREQUENCY_1WEEK), true,
				'End time is between start and end times'
			),
			// overlap start time by 1hr
			array(array(
				'start_date' => date('Y-m-d', strtotime('+1 week')),
				'start_time' => '05:00',
				'end_time' => '09:00',
				'end_date' => date('m/d/Y', strtotime('+13 weeks')),
				'repeat_interval' => Sequence::FREQUENCY_1WEEK), true,
				'Start time overlaps by 1hr'
			),
			// overlap end time by 1hr
			array(array(
				'start_date' => date('Y-m-d', strtotime('+1 week')),
				'start_time' => '10:00',
				'end_time' => '14:00',
				'end_date' => date('Y-m-d', strtotime('+13 weeks')),
				'repeat_interval' => Sequence::FREQUENCY_1WEEK), true,
				'End time overlaps by 1hr'
			),
			// both times within existing start/end times
			array(array(
				'start_date' => date('Y-m-d', strtotime('+1 week')),
				'start_time' => '09:00',
				'end_time' => '10:00',
				'end_date' => date('Y-m-d', strtotime('+13 weeks')),
				'repeat_interval' => Sequence::FREQUENCY_1WEEK), true,
				'Times are within an existing sequence'
			),
			// both times outside existing start/end times
			array(array(
				'start_date' => date('Y-m-d', strtotime('+1 week')),
				'start_time' => '05:00',
				'end_time' => '15:00',
				'end_date' => date('Y-m-d', strtotime('+13 weeks')),
				'repeat_interval' => Sequence::FREQUENCY_1WEEK), true,
				'Times overlap an existing sequence'
			),
			// different repeat_interval
			array(array(
				'start_date' => date('Y-m-d', strtotime('+2 weeks')),
				'start_time' => '11:00',
				'end_time' => '13:00',
				'end_date' => date('Y-m-d', strtotime('+15 weeks')),
				'repeat_interval' => Sequence::FREQUENCY_3WEEKS), true,
				'Same dates, different repeat_interval'
			),
			// weekday mis-match
			array(array(
				'start_date' => date('Y-m-d', strtotime('+3 days')),
				'start_time' => '08:00',
				'end_time' => '12:00',
				'end_date' => date('Y-m-d', strtotime('+12 days')),
				'repeat_interval' => Sequence::FREQUENCY_1WEEK), false,
				'Different weekday - no conflict'
			),
			// start date <= end date
			array(array(
				'start_date' => date('Y-m-d', strtotime('+28 weeks')),
				'start_time' => '08:00',
				'end_time' => '12:00',
				'end_date' => null,
				'repeat_interval' => Sequence::FREQUENCY_1WEEK), false,
				'Start date >= end date - no conflict'
			),
			// end date >= start date
			array(array(
				'start_date' => date('Y-m-d', strtotime('-8 weeks')),
				'start_time' => '08:00',
				'end_time' => '12:00',
				'end_date' => date('Y-m-d', time()),
				'repeat_interval' => Sequence::FREQUENCY_1WEEK), false,
				'End date <= start date - no conflict'
			),
			// outside the time window
			array(array(
				'start_date' => date('Y-m-d', strtotime('+1 week')),
				'start_time' => '01:00',
				'end_time' => '03:00',
				'end_date' => date('Y-m-d', strtotime('+13 weeks')),
				'repeat_interval' => Sequence::FREQUENCY_1WEEK), false,
				'Different time window - no conflict'
			),
		);
	}

	public function dataProvider_PreExistingSequences_Weeks()
	{
		return array(
			// different week selections
			array(array(
				'start_date' => date('Y-m-d', strtotime('+2 weeks')),
				'start_time' => '11:00',
				'end_time' => '13:00',
				'end_date' => date('Y-m-d', strtotime('+15 weeks')),
				'repeat_interval' => 0,
				'week_selection' => Sequence::SELECT_1STWEEK), false,
				'Different week selections'
			),
			// weekday mis-match
			array(array(
				'start_date' => date('Y-m-d', strtotime('+3 days')),
				'start_time' => '08:00',
				'end_time' => '12:00',
				'end_date' => date('Y-m-d', strtotime('+12 days')),
				'repeat_interval' => 0,
				'week_selection' => Sequence::SELECT_2NDWEEK + Sequence::SELECT_4THWEEK + Sequence::SELECT_5THWEEK), false,
				'Different weekday - no conflict'
			),
			// outside the time window
			array(array(
				'start_date' => date('Y-m-d', strtotime('+1 week')),
				'start_time' => '01:00',
				'end_time' => '03:00',
				'end_date' => date('Y-m-d', strtotime('+13 weeks')),
				'repeat_interval' => 0,
				'week_selection' => Sequence::SELECT_2NDWEEK + Sequence::SELECT_4THWEEK + Sequence::SELECT_5THWEEK), false,
				'Different time window - no conflict'
			),
			// both times inside existing start/end times
			array(array(
				'start_date' => date('Y-m-d', strtotime('+1 week')),
				'start_time' => '10:00',
				'end_time' => '11:00',
				'end_date' => date('Y-m-d', strtotime('+13 weeks')),
				'repeat_interval' => 0,
				'week_selection' => Sequence::SELECT_2NDWEEK + Sequence::SELECT_4THWEEK + Sequence::SELECT_5THWEEK), true,
				'Times overlap an existing sequence'
			),
		);
	}

	public function setUp()
	{
		parent::setUp();
		$this->model = new Sequence;
	}

	public function testModel()
	{
		$this->assertEquals('Sequence', get_class(Sequence::model()), 'Class name should match model.');
	}

	public function testTableName()
	{
		$this->assertEquals('sequence', $this->model->tableName(), 'Table name should be singular.');
	}

	public function testAttributeLabels()
	{
		$expected = array(
			'id' => 'ID',
			'theatre_id' => 'Theatre',
			'start_date' => 'Start Date',
			'start_time' => 'Start Time',
			'end_time' => 'End Time',
			'end_date' => 'End Date',
			'repeat_interval' => 'Repeat',
			'anaesthetist' => 'Anaesthetist Present',
			'consultant' => 'Consultant Present',
			'general_anaesthetic' => 'GA Available',
		);

		$this->assertEquals($expected, $this->model->attributeLabels(), 'Attribute labels should be customised.');
	}

	public function testGetFrequencyOptions_ReturnsCorrectData()
	{
		$expected = array(
			Sequence::FREQUENCY_1WEEK => 'Every week',
			Sequence::FREQUENCY_2WEEKS => 'Every 2 weeks',
			Sequence::FREQUENCY_3WEEKS => 'Every 3 weeks',
			Sequence::FREQUENCY_4WEEKS => 'Every 4 weeks',
			Sequence::FREQUENCY_ONCE => 'One time',
			Sequence::FREQUENCY_MONTHLY => 'Monthly',
		);

		$this->assertEquals($expected, $this->model->getFrequencyOptions(), 'Frequency options should match the constants.');
	}

	public function testGetWeekSelectionOptions_ReturnsCorrectData()
	{
		$expected = array(
			Sequence::SELECT_1STWEEK => '1st in month',
			Sequence::SELECT_2NDWEEK => '2nd in month',
			Sequence::SELECT_3RDWEEK => '3rd in month',
			Sequence::SELECT_4THWEEK => '4th in month',
			Sequence::SELECT_5THWEEK => '5th in month',
		);

		$this->assertEquals($expected, $this->model->getWeekSelectionOptions(), 'Week selection options should match the constants.');
	}

	/**
	 * @dataProvider dataProvider_FrequencyIntervals
	 */
	public function testGetFrequencyInteget_ReturnsCorrectData($repeat_interval, $timestamp, $expected)
	{
		$this->assertEquals($expected, $this->model->getFrequencyInteger($repeat_interval, $timestamp));
	}

	/**
	 * @dataProvider dataProvider_CreateAttributes
	 */
	public function testCreate_SavesElement($attributes, $validData)
	{
		$model = $this->model;
		$attributes['theatre_id'] = $this->theatres['theatre1']['id'];
		$model->setAttributes($attributes);

		$_POST['Sequence']['week_selection'] = array(
			Sequence::SELECT_2NDWEEK,
			Sequence::SELECT_4THWEEK,
			Sequence::SELECT_5THWEEK
		);

		$expectedValue = Sequence::SELECT_2NDWEEK + Sequence::SELECT_4THWEEK + Sequence::SELECT_5THWEEK;
		$this->assertEquals($validData, $model->validate());
		if ($validData) {
			$this->assertEquals(array(), $model->getErrors());
			$this->assertTrue($model->save(true));
			$this->assertEquals($expectedValue, $model->week_selection);
		}
	}

	/**
	 * @dataProvider dataProvider_PreExistingSequences
	 */
/*
	public function testCreate_ConflictingSequences_ReturnsError($attributes, $conflictsExist)
	{
		$model = new Sequence;
		$attributes['theatre_id'] = $this->theatres['theatre1']['id'];
		$model->setAttributes($attributes);
		$model->save(true);

		$model = $this->model;
		$model->theatre_id = $this->theatres['theatre1']['id'];
		$model->start_date = date('Y-m-d', strtotime('+1 week'));
		$model->start_time = '08:00';
		$model->end_time = '11:00';
		$model->end_date = date('Y-m-d', strtotime('+13 weeks'));
		$model->repeat_interval = Sequence::FREQUENCY_4WEEKS;

		$this->assertEquals(!$conflictsExist, $model->validate());
		if (!$conflictsExist) {
			$this->assertEquals(array(), $model->getErrors());
			$this->assertTrue($model->save(true));
		}
	}
*/
	/**
	 * @dataProvider dataProvider_PreExistingSequences_Weeks
	 */
	public function testCreate_WithWeekSelections_ConflictingSequences_ReturnsError($attributes, $conflictsExist)
	{
		$selection = array();
		for ($j = Sequence::SELECT_1STWEEK; $j <= Sequence::SELECT_5THWEEK; $j *= 2) {
			if (($attributes['week_selection'] & $j) != 0) {
				$selection[] = $j;
			}
		}
		$_POST['Sequence']['week_selection'] = $selection;

		$model = new Sequence;
		$attributes['theatre_id'] = $this->theatres['theatre1']['id'];
		$model->setAttributes($attributes);
		$model->save(true);

		$model = $this->model;
		$model->theatre_id = $this->theatres['theatre1']['id'];
		$model->start_date = date('Y-m-d', strtotime('+1 week'));
		$model->start_time = '08:00';
		$model->end_time = '11:00';
		$model->end_date = date('Y-m-d', strtotime('+13 weeks'));
		$model->repeat_interval = 0;
		$model->week_selection = Sequence::SELECT_2NDWEEK + Sequence::SELECT_4THWEEK + Sequence::SELECT_5THWEEK;

		$this->assertEquals(!$conflictsExist, $model->validate());
		if (!$conflictsExist) {
			$this->assertEquals(array(), $model->getErrors());
			$this->assertTrue($model->save(true));
		}
	}

	/**
	 * @dataProvider dataProvider_Search
	 */
	public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
	{
		$element = $this->model;
		$element->setAttributes($searchTerms);
		$results = $element->search();
		$data = $results->getData();

		$expectedResults = array();
		if (!empty($expectedKeys)) {
			foreach ($expectedKeys as $key) {
				$expectedResults[] = $this->sequences($key);
			}
		}

		$this->assertEquals($numResults, $results->getItemCount());
		$this->assertEquals($expectedResults, $data);
	}

	public function testGetFirmName()
	{
		foreach ($this->sequences as $name => $data) {
			$sequence = $this->sequences($name);
			if ($name == 'sequence3' || $name == 'sequence4') {
				$expected = 'None';
			} else {
				$expected = $this->firms['firm1']['name'] . ' (' . $this->specialties['specialty1']['name'] . ')';
			}

			$this->assertEquals($expected, $sequence->getFirmName());
		}
	}
}
