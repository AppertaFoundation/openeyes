<?php
class BaseElementTest extends CDbTestCase
{
	public $fixtures = array(
		'users' => 'User',
		'serviceSpecialtyAssignment' => 'ServiceSpecialtyAssignment',
		'firms' => 'Firm',
		'examphrase' => 'ExamPhrase',
		'patients' => 'Patient',
		'episodes' => 'Episode',
		'eventTypes' => 'EventType',
		'events' => 'Event',
	);

	public function testGetSpecialtyId_WithUserFirm_CorrectlySetsSpecialtyId()
	{
		$firm = $this->firms('firm1');
		$baseElement = new BaseElement(1, $firm);
		$this->assertEquals(1, $baseElement->getSpecialtyId());
	}

	public function testGetExamPhraseOptions()
	{
		$firm = $this->firms('firm1');
		$baseElement = new BaseElement(1, $firm);
		$examPhrases = $baseElement->getExamPhraseOptions(ExamPhrase::PART_HISTORY);
		$this->assertTrue(is_array($examPhrases));
		// N.B. there should be one more examphrase than is defined in the fixture
		$this->assertEquals(2, count($examPhrases));
	}
}