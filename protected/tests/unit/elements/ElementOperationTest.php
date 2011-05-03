<?php
class ElementOperationTest extends CDbTestCase
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
		'procedures' => 'Procedure',
		'services' => 'Service',
		'subsections' => ':service_subsection',
		'elements' => 'ElementOperation',
		'operationProcedures' => 'OperationProcedureAssignment'
	);

	public function setUp()
	{
		parent::setUp();
		$this->user = $this->users('user1');
		$this->firm = $this->firms('firm1');
		$this->patient = $this->patients('patient1');
		$this->element = new ElementOperation($this->user->id, $this->firm->id, $this->patient->id);
	}

	public function dataProvider_Search()
	{
		return array(
			array(array('eye' => ElementOperation::EYE_BOTH), 1, array('element1')),
			array(array('eye' => ElementOperation::EYE_LEFT), 1, array('element2')),
			array(array('eye' => ElementOperation::EYE_RIGHT), 0, array()),
		);
	}

	public function dataProvider_EyeText()
	{
		return array(
			array(ElementOperation::EYE_LEFT, 'Left'),
			array(ElementOperation::EYE_RIGHT, 'Right'),
			array(ElementOperation::EYE_BOTH, 'Both'),
			array(549813, 'Unknown'),
		);
	}
	
	public function dataProvider_BooleanFields()
	{
		return array(
			array('consultant_required', true),
			array('consultant_required', false),
			array('consultant_required', 5),
			array('anaesthetist_required', true),
			array('anaesthetist_required', false),
			array('anaesthetist_required', 5),
			array('overnight_stay', true),
			array('overnight_stay', false),
			array('overnight_stay', 5),
		);
	}
	
	public function dataProvider_AnaesteticText()
	{
		return array(
			array(ElementOperation::ANAESTHETIC_TOPICAL, 'Topical'),
			array(ElementOperation::ANAESTHETIC_LOCAL, 'Local'),
			array(ElementOperation::ANAESTHETIC_LOCAL_WITH_COVER, 'Local with cover'),
			array(ElementOperation::ANAESTHETIC_LOCAL_WITH_SEDATION, 'Local with sedation'),
			array(ElementOperation::ANAESTHETIC_GENERAL, 'General'),
			array(2847405, 'Unknown'),
		);
	}
	
	public function dataProvider_ScheduleText()
	{
		return array(
			array(ElementOperation::SCHEDULE_IMMEDIATELY, 'Immediately'),
			array(ElementOperation::SCHEDULE_AFTER_1MO, 'After 1 month'),
			array(ElementOperation::SCHEDULE_AFTER_2MO, 'After 2 months'),
			array(ElementOperation::SCHEDULE_AFTER_3MO, 'After 3 months'),
			array(2847405, 'Unknown')
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

	public function testBasicCreate_NoTimeframe_SavesElement()
	{
		$element = $this->element;
		$element->setAttributes(array(
			'event_id' => '1',
			'eye' => ElementOperation::EYE_LEFT,
		));

		$this->assertTrue($element->save(true));
	}

	public function testBasicCreate_WithTimeframe_SavesElement()
	{
		$element = $this->element;
		$element->setAttributes(array(
			'event_id' => '1',
			'eye' => ElementOperation::EYE_LEFT,
		));
		
		$_POST['schedule_timeframe2'] = ElementOperation::SCHEDULE_AFTER_2MO;

		$this->assertTrue($element->save(true));
	}

	public function testAttributeLabels()
	{
		$expected = array(
			'id' => 'ID',
			'event_id' => 'Event',
			'eye' => 'Eye(s)',
			'comments' => 'Comments',
			'total_duration' => 'Total Duration',
			'consultant_required' => 'Consultant Required',
			'anaesthetist_required' => 'Anaesthetist Required',
			'anaesthetic_type' => 'Anaesthetic Type',
			'overnight_stay' => 'Overnight Stay',
			'schedule_timeframe' => 'Schedule Timeframe',
		);

		$this->assertEquals($expected, $this->element->attributeLabels());
	}

	public function testModel()
	{
		$this->assertEquals('ElementOperation', get_class(ElementOperation::model()));
	}

	public function testUpdate()
	{
		$element = $this->elements('element1');

		$element->eye = ElementOperation::EYE_RIGHT;

		$this->assertTrue($element->save(true));
	}

	public function testGetEyeOptions()
	{
		$expected = array(
			ElementOperation::EYE_LEFT => 'Left',
			ElementOperation::EYE_RIGHT => 'Right',
			ElementOperation::EYE_BOTH => 'Both',
		);
		$this->assertEquals($expected, $this->element->getEyeOptions());
	}

	/**
	 * @dataProvider dataProvider_EyeText
	 */
	public function testGetEyeText($newEye, $expectedText)
	{
		$element = $this->element;
		$element->eye = $newEye;
		$element->save();

		$this->assertEquals($expectedText, $element->getEyeText());
	}
	
	public function testSetDefaultOptions_SetsCorrectOptions()
	{
		$this->element->consultant_required = ElementOperation::CONSULTANT_NOT_REQUIRED;
		$this->element->anaesthetic_type = ElementOperation::ANAESTHETIC_GENERAL;
		$this->element->overnight_stay = true;
		$this->element->total_duration = 10;
		
		$this->element->setDefaultOptions();
		$this->assertEquals(ElementOperation::CONSULTANT_REQUIRED, $this->element->consultant_required);
		$this->assertEquals(ElementOperation::ANAESTHETIC_TOPICAL, $this->element->anaesthetic_type);
		$this->assertEquals(0, $this->element->overnight_stay);
		$this->assertEquals(0, $this->element->total_duration);
	}
	
	public function testGetConsultantOptions_ReturnsCorrectData()
	{
		$expected = array(
			ElementOperation::CONSULTANT_REQUIRED => 'Yes',
			ElementOperation::CONSULTANT_NOT_REQUIRED => 'No',
		);
		
		$this->assertEquals($expected, $this->element->getConsultantOptions());
	}
	
	/**
	 * @dataProvider dataProvider_BooleanFields
	 */
	public function testGetBooleanText_ValidInput_ReturnsCorrectData($field, $value)
	{
		$this->element->$field = $value;
		
		$expected = ($value == 1) ? 'Yes' : 'No';
		
		$this->assertEquals($expected, $this->element->getBooleanText($field));
	}
	
	public function testgetAnaestheticOptions_ReturnsValidData()
	{
		$expected = array(
			ElementOperation::ANAESTHETIC_TOPICAL => 'Topical',
			ElementOperation::ANAESTHETIC_LOCAL => 'Local',
			ElementOperation::ANAESTHETIC_LOCAL_WITH_COVER => 'Local with cover',
			ElementOperation::ANAESTHETIC_LOCAL_WITH_SEDATION => 'Local with sedation',
			ElementOperation::ANAESTHETIC_GENERAL => 'General'
		);
		
		$this->assertEquals($expected, $this->element->getAnaestheticOptions());
	}
	
	/**
	 * @dataProvider dataProvider_AnaesteticText
	 */
	public function testGetAnaestheticText_ReturnsCorrectData($type, $text)
	{
		$this->element->anaesthetic_type = $type;
		
		$this->assertEquals($text, $this->element->getAnaestheticText());
	}
	
	public function testGetOvernightOptions_ReturnsCorrectData()
	{
		$expected = array(
			1 => 'Yes',
			0 => 'No',
		);
		
		$this->assertEquals($expected, $this->element->getOvernightOptions());
	}
	
	public function testGetScheduleOptions_ReturnsCorrectData()
	{
		$expected = array(
			0 => 'As soon as possible',
			1 => 'Within timeframe specified by patient',
		);
		
		$this->assertEquals($expected, $this->element->getScheduleOptions());
	}
	
	public function testGetScheduleDelayOptions_ReturnsCorrectData()
	{
		$expected = array(
			ElementOperation::SCHEDULE_AFTER_1MO => 'After 1 Month',
			ElementOperation::SCHEDULE_AFTER_2MO => 'After 2 Months',
			ElementOperation::SCHEDULE_AFTER_3MO => 'After 3 Months',
		);
		
		$this->assertEquals($expected, $this->element->getScheduleDelayOptions());
	}
	
	/**
	 * @dataProvider dataProvider_ScheduleText
	 */
	public function testGetScheduleText_ReturnsCorrectData($timeframe, $text)
	{
		$this->element->schedule_timeframe = $timeframe;
		
		$this->assertEquals($text, $this->element->getScheduleText());
	}
}