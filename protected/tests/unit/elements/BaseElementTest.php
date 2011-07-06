<?php
class BaseElementTest extends CDbTestCase
{
	public $fixtures = array(
		'users' => 'User',
		'serviceSpecialtyAssignment' => 'ServiceSpecialtyAssignment',
		'firms' => 'Firm',
		'patients' => 'Patient',
		'episodes' => 'Episode',
		'eventTypes' => 'EventType',
		'events' => 'Event',
	);

	public function testGetSpecialtyId_WithUserFirm_CorrectlySetsSpecialtyId()
	{
		$firm = $this->firms('firm1');
		$baseElement = new BaseElement($firm);
		$this->assertEquals(1, $baseElement->firm->serviceSpecialtyAssignment->specialty_id);
	}

	/* - exam phrases have been removed, but the requirements for their replacement are as yet unclear
	public function testGetExamPhraseOptions()
	{
		$firm = $this->firms('firm1');
		$baseElement = new BaseElement($firm);
		$examPhrases = $baseElement->getExamPhraseOptions(ExamPhrase::PART_HISTORY);
		$this->assertTrue(is_array($examPhrases));
		// N.B. there should be one more examphrase than is defined in the fixture
		$this->assertEquals(2, count($examPhrases));
	}
	*/
}
