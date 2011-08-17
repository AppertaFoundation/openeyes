<?php

// @todo - there are no tests that run agagint the PAS DB. They would be too slow. One day, create a test PAS
// and run against that.

class ReferralServiceTest extends CDbTestCase
{
	public $fixtures = array(
		'episodes' => 'Episode',
		'events' => 'Event',
		'referrals' => 'Referral',
		'referralEpisodeAssignments' => 'ReferralEpisodeAssignment'
	);
	protected $service;

	protected function setUp()
	{
		$this->service = new ReferralService;
		parent::setUp();
	}
//	public function testManualReferralNeeded_InvalidEventId_ThrowsException()
//	{
//		$fakeId = 12345;
//
//		$this->setExpectedException('Exception', 'No event of that id.');
//		$this->service->ManualReferralNeeded($fakeId);
//	}
//
//	public function testManualReferralNeeded_ValidEventId_ReferralEpisodeAssignmentExists_ReturnsFalse()
//	{
//		$id = 1;
//
//		$result = $this->service->manualReferralNeeded($id);
//
//		$this->assertFalse($result);
//	}
//
//	public function testManualReferralNeeded_ValidEventId_ReferralEpisodeAssignmentNotExists_HasOpenEpisodesForService_ReturnsFalse()
//	{
//		$id = 2;
//
//		$result = $this->service->manualReferralNeeded($id);
//
//		$this->assertFalse($result);
//	}
//
//	public function testManualReferralNeeded_ValidEventId_ReferralEpisodeAssignmentNotExists_HasOpenEpisodesForWrongService_ReturnsEraId()
//	{
//		$id = 4;
//
//		$result = $this->service->manualReferralNeeded($id);
//
//		$this->assertEquals($result, 2);
//	}
//
//	public function testManualReferralNeeded_ValidEventId_ReferralEpisodeAssignmentNotExists_HasOneOpenEpisodeForWrongService_ReturnsFalse()
//	{
//		$id = 5;
//
//		$result = $this->service->manualReferralNeeded($id);
//
//		$this->assertFalse($result);
//	}
}
