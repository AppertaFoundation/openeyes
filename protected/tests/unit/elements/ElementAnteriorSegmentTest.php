<?php
class ElementAnteriorSegmentTest extends CDbTestCase
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
		'elements' => 'ElementAnteriorSegment'
	);

	public function setUp()
	{
		parent::setUp();
		$this->user = $this->users('user1');
		$this->firm = $this->firms('firm1');
		$this->patient = $this->patients('patient1');
		$this->element = new ElementAnteriorSegment($this->user->id, $this->firm->id, $this->patient->id);
	}

	public function dataProvider_Search()
	{
		return array(
			array(array(
				'description_left' => 'The road to wisdom; Well it\'s plain,',
				'description_right' => 'And simple to express',
				'image_string_left' => 'Err. And err. And err again.',
				'image_string_right' => 'But less. And less. And less.'
			), 
			1, array('elementAnteriorSegment1')),
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
			'event_id' => '1',
			'description_left' => 'aaa',
			'description_right' => 'bbb',
			'image_string_left' => 'ccc',
			'image_string_right' => 'ddd',
		));
		$this->assertTrue($element->save(true));
	}

	public function testAttributeLabels()
	{
		$expected = array(
			'id' => 'ID',
			'event_id' => 'Event',
			'description_left' => 'Description (left)',
			'description_right' => 'Description (right)',
			'image_string_left' => 'EyeDraw (left)',
			'image_string_right' => 'EyeDraw (right)'
		);

		$this->assertEquals($expected, $this->element->attributeLabels());
	}

	public function testModel()
	{
		$this->assertEquals('ElementAnteriorSegment', get_class(ElementAnteriorSegment::model()));
	}

	public function testUpdate()
	{
		$element = $this->elements('elementAnteriorSegment1');

		$element->description_right = 'fish';

		$this->assertTrue($element->save(true));
	}
}
