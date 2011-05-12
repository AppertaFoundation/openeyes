<?php

class SequenceTest extends CDbTestCase
{
	public $model;
	
	public $fixtures = array(
		'firms' => 'Firm',
		'sites' => 'Site',
		'theatres' => 'Theatre',
		'sequences' => 'Sequence',
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
				'frequency' => Sequence::FREQUENCY_1WEEK), true,
			),
			array(array(
				'start_date' => null,
				'start_time' => '08:00',
				'end_time' => '12:00',
				'end_date' => date('Y-m-d', strtotime('+1 week')),
				'frequency' => Sequence::FREQUENCY_1WEEK), false
			),
			array(array(
				'start_date' => date('Y-m-d', strtotime('+1 week')),
				'start_time' => '08:00',
				'end_time' => '12:00',
				'end_date' => date('Y-m-d', strtotime('+2 weeks')),
				'frequency' => Sequence::FREQUENCY_1WEEK), true
			),
			array(array(
				'start_date' => date('Y-m-d', strtotime('+3 week')),
				'start_time' => '08:00',
				'end_time' => '12:00',
				'end_date' => date('Y-m-d', strtotime('+2 weeks')),
				'frequency' => Sequence::FREQUENCY_1WEEK), false
			),
			array(array(
				'start_date' => date('Y-m-d', strtotime('+1 week')),
				'start_time' => '12:00',
				'end_time' => '10:00',
				'end_date' => date('Y-m-d', strtotime('+2 weeks')),
				'frequency' => Sequence::FREQUENCY_1WEEK), false
			),
		);
	}

	public function dataProvider_Search()
	{
		return array(
			array(array('theatre_id' => 1), 3, array('sequence1', 'sequence2', 'sequence3')),
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
				'frequency' => Sequence::FREQUENCY_1WEEK), true, 
				'Start date is < end date'
			),
			// end date >= start date
			array(array(
				'start_date' => date('Y-m-d', strtotime('+4 weeks')),
				'start_time' => '08:00',
				'end_time' => '12:00',
				'end_date' => date('Y-m-d', strtotime('+52 weeks')),
				'frequency' => Sequence::FREQUENCY_1WEEK), true,
				'End date is > start date'
			),
			// start time between start and end times
			array(array(
				'start_date' => date('Y-m-d', time()),
				'start_time' => '09:00',
				'end_time' => '17:00',
				'end_date' => null,
				'frequency' => Sequence::FREQUENCY_1WEEK), true,
				'Start time is between start ane end times'
			),
			// end time between start and end times
			array(array(
				'start_date' => date('Y-m-d', time()),
				'start_time' => '02:00',
				'end_time' => '11:00',
				'end_date' => null,
				'frequency' => Sequence::FREQUENCY_1WEEK), true,
				'End time is between start and end times'
			),
			// overlap start time by 1hr
			array(array(
				'start_date' => date('Y-m-d', strtotime('+1 week')),
				'start_time' => '05:00',
				'end_time' => '09:00',
				'end_date' => date('m/d/Y', strtotime('+13 weeks')),
				'frequency' => Sequence::FREQUENCY_1WEEK), true,
				'Start time overlaps by 1hr'
			),
			// overlap end time by 1hr
			array(array(
				'start_date' => date('Y-m-d', strtotime('+1 week')),
				'start_time' => '10:00',
				'end_time' => '14:00',
				'end_date' => date('Y-m-d', strtotime('+13 weeks')),
				'frequency' => Sequence::FREQUENCY_1WEEK), true,
				'End time overlaps by 1hr'
			),
			// both times within existing start/end times
			array(array(
				'start_date' => date('Y-m-d', strtotime('+1 week')),
				'start_time' => '09:00',
				'end_time' => '10:00',
				'end_date' => date('Y-m-d', strtotime('+13 weeks')),
				'frequency' => Sequence::FREQUENCY_1WEEK), true,
				'Times are within an existing sequence'
			),
			// both times outside existing start/end times
			array(array(
				'start_date' => date('Y-m-d', strtotime('+1 week')),
				'start_time' => '05:00',
				'end_time' => '15:00',
				'end_date' => date('Y-m-d', strtotime('+13 weeks')),
				'frequency' => Sequence::FREQUENCY_1WEEK), true,
				'Times overlap an existing sequence'
			),
			// different frequency
			array(array(
				'start_date' => date('Y-m-d', strtotime('+2 weeks')),
				'start_time' => '11:00',
				'end_time' => '13:00',
				'end_date' => date('Y-m-d', strtotime('+15 weeks')),
				'frequency' => Sequence::FREQUENCY_3WEEKS), true,
				'Same dates, different frequency'
			),
			// weekday mis-match
			array(array(
				'start_date' => date('Y-m-d', strtotime('+3 days')),
				'start_time' => '08:00',
				'end_time' => '12:00',
				'end_date' => date('Y-m-d', strtotime('+12 days')),
				'frequency' => Sequence::FREQUENCY_1WEEK), false,
				'Different weekday - no conflict'
			),
			// start date <= end date
			array(array(
				'start_date' => date('Y-m-d', strtotime('+28 weeks')),
				'start_time' => '08:00',
				'end_time' => '12:00',
				'end_date' => null,
				'frequency' => Sequence::FREQUENCY_1WEEK), false,
				'Start date >= end date - no conflict'
			),
			// end date >= start date
			array(array(
				'start_date' => date('Y-m-d', strtotime('-8 weeks')),
				'start_time' => '08:00',
				'end_time' => '12:00',
				'end_date' => date('Y-m-d', time()),
				'frequency' => Sequence::FREQUENCY_1WEEK), false,
				'End date <= start date - no conflict'
			),
			// outside the time window
			array(array(
				'start_date' => date('Y-m-d', strtotime('+1 week')),
				'start_time' => '01:00',
				'end_time' => '03:00',
				'end_date' => date('Y-m-d', strtotime('+13 weeks')),
				'frequency' => Sequence::FREQUENCY_1WEEK), false,
				'Different time window - no conflict'
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
			'start_time' => 'Start Time (HH:MM or HH:MM:SS)',
			'end_time' => 'End Time (HH:MM or HH:MM:SS)',
			'end_date' => 'End Date',
			'frequency' => 'Frequency',
		);
		
		$this->assertEquals($expected, $this->model->attributeLabels(), 'Attribute labels should be customised.');
	}
	
	public function testGetTheatreOptions_ReturnsCorrectData()
	{
		$theatre = $this->theatres['theatre1'];
		$site = $this->sites['site1'];
		
		$expected = array();
		foreach ($this->theatres as $theatre) {
			$expected[$theatre['id']] = "{$site['name']} - {$theatre['name']}";
		}
		
		$this->assertEquals($expected, $this->model->getTheatreOptions(), 'Theatre options should fetch from the database.');
	}
	
	public function testGetFrequencyOptions_ReturnsCorrectData()
	{
		$expected = array(
			Sequence::FREQUENCY_1WEEK => 'Every week',
			Sequence::FREQUENCY_2WEEKS => 'Every 2 weeks',
			Sequence::FREQUENCY_3WEEKS => 'Every 3 weeks',
			Sequence::FREQUENCY_4WEEKS => 'Every 4 weeks',
			Sequence::FREQUENCY_ONCE => 'One time',
		);
		
		$this->assertEquals($expected, $this->model->getFrequencyOptions(), 'Frequency options should match the constants.');
	}
	
	/**
	 * @dataProvider dataProvider_FrequencyIntervals
	 */
	public function testGetFrequencyInteget_ReturnsCorrectData($frequency, $timestamp, $expected)
	{
		$this->assertEquals($expected, $this->model->getFrequencyInteger($frequency, $timestamp));
	}

	/**
	 * @dataProvider dataProvider_CreateAttributes
	 */
	public function testCreate_SavesElement($attributes, $validData)
	{
		$model = $this->model;
		$attributes['theatre_id'] = $this->theatres['theatre1']['id'];
		$model->setAttributes($attributes);
		
		$this->assertEquals($validData, $model->validate());
		if ($validData) {
			$this->assertEquals(array(), $model->getErrors());
			$this->assertTrue($model->save(true));
		}
	}
	
	/**
	 * @dataProvider dataProvider_PreExistingSequences
	 */
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
		$model->frequency = Sequence::FREQUENCY_4WEEKS;
		
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
			if ($name == 'sequence3') {
				$expected = 'None';
			} else {
				$expected = $this->firms['firm1']['name'];
			}
			
			$this->assertEquals($expected, $sequence->getFirmName());
		}
	}
}