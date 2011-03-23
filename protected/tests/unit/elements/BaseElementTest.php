<?php
class BaseElementTest extends CDbTestCase
{
	public $fixtures = array(
		'users' => 'User',
		'serviceSpecialtyAssignment' => ':service_specialty_assignment',
		'firms' => 'Firm',
		'examphrase' => ':exam_phrase',
	);

	public function testGetSpecialtyId()
	{
		$firm = $this->firms('firm1');
		$baseElement = new BaseElement(1, $firm);
		$this->assertEquals($baseElement->getSpecialtyId(), 1);
	}

	public function testGetExamPhraseOptions()
	{
		$firm = $this->firms('firm1');
		$baseElement = new BaseElement(1, $firm);
		$examPhrases = $baseElement->getExamPhraseOptions(ExamPhrase::PART_HISTORY);
		$this->assertTrue(is_array($examPhrases));
		// N.B. there should be one more examphrase than is defined in the fixture
		$this->assertEquals(count($examPhrases), 2);
	}
}