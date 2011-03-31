<?php
class ElementDiabetesTypeTest extends CDbTestCase
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
		'elements' => 'ElementDiabetesType'
	);

	public function setUp()
	{
		parent::setUp();
		$this->user = $this->users('user1');
		$this->firm = $this->firms('firm1');
		$this->patient = $this->patients('patient1');
		$this->element = new ElementDiabetesType($this->user->id, $this->firm->id, $this->patient->id);
	}

	public function dataProvider_Search()
	{
		return array(
			array(array('type' => '1'), 1, array('element1')),
			array(array('type' => '2'), 0, array()),
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
			'type' => 1,
		));

		$this->assertTrue($element->save(true));
	}

	public function testAttributeLabels()
	{
		$expected = array(
			'id' => 'ID',
			'event_id' => 'Event',
			'type' => 'Type',
		);

		$this->assertEquals($expected, $this->element->attributeLabels());
	}

	public function testModel()
	{
		$this->assertEquals('ElementDiabetesType', get_class(ElementDiabetesType::model()));
	}

	public function testUpdate()
	{
		$element = $this->elements('element1');

		$element->type = 2;

		$this->assertTrue($element->save(true));
	}

	public function testGetSelectOptions()
	{
		$expected = array(
			1 => 'Type 1',
			2 => 'Type 2'
		);
		$this->assertEquals($expected, $this->element->getSelectOptions());
	}
}