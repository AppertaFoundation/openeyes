<?php

class LetterTemplateTest extends CDbTestCase
{
	public $fixtures = array(
		'specialties' => 'Specialty',
		'contactTypes' => 'ContactType',
		'letterTemplates' => 'LetterTemplate'
	);

	public function dataProvider_Search()
	{
		return array(
			array(array('specialty_id' => 1), 1, array('letterTemplate1')),
			array(array('specialty_id' => 2), 1, array('letterTemplate2')),
			array(array('name' => 'name'), 2, array('letterTemplate1', 'letterTemplate2')),
			array(array('phrase' => 'rest'), 1, array('letterTemplate1')),
			array(array('send_to' => 3), 0, array()),
			array(array('cc' => 3), 2, array('letterTemplate1', 'letterTemplate2')),
		);
	}
	
	public function setUp()
	{
		parent::setUp();
		$this->model = new LetterTemplate;
	}

	public function testGetSpecialtyOptions()
	{
		$expected = CHtml::listData(Specialty::model()->findAll(), 'id', 'name');
		$result = $this->model->getSpecialtyOptions();
		
		$this->assertEquals($expected, $result, 'Returned options should match.');
		$this->assertEquals(count($this->specialties), count($result), 'Should have found all the options.');
	}

	public function testGetContactTypeOptions()
	{
		$expected = CHtml::listData(ContactType::model()->findAll(), 'id', 'name');
		$result = $this->model->getContactTypeOptions();
		
		$this->assertEquals($expected, $result, 'Returned options should match.');
		$this->assertEquals(count($this->contactTypes), count($result), 'Should have found all the options.');
	}

	public function testGetSpecialtyText()
	{
		$letterTemplate = LetterTemplate::model()->findByPk(1);

		$this->assertEquals($letterTemplate->getSpecialtyText(), 'Accident & Emergency');
	}

	public function testGetCCText()
	{
		$letterTemplate = LetterTemplate::model()->findByPk(1);

		$this->assertEquals($letterTemplate->getCcText(), 'Optometrist');
	}

	public function testGetToText()
	{
		$letterTemplate = $this->letterTemplates('letterTemplate1');

		$this->assertEquals('Ophthalmologist', $letterTemplate->getToText(), 'Returned text should be correct.');
	}
	
	public function testAttributeLabels()
	{
		$expected = array(
			'id' => 'ID',
			'name' => 'Name',
			'phrase' => 'Phrase',
			'specialty_id' => 'Specialty',
			'send_to' => 'To',
			'cc' => 'Cc',
		);
		
		$this->assertEquals($expected, $this->model->attributeLabels());
	}

	/**
	 * @dataProvider dataProvider_Search
	 */
	public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
	{
		$letter = new LetterTemplate;
		$letter->setAttributes($searchTerms);
		$results = $letter->search();
		$data = $results->getData();

		$expectedResults = array();
		if (!empty($expectedKeys)) {
			foreach ($expectedKeys as $key) {
				$expectedResults[] = $this->letterTemplates($key);
			}
		}

		$this->assertEquals($numResults, $results->getItemCount());
		$this->assertEquals($expectedResults, $data);
	}
}
