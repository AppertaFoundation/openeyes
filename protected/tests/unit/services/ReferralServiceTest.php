<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/


// @todo - there are no tests that run agagint the PAS DB. They would be too slow. One day, create a test PAS
// and run against that.

class ReferralServiceTest extends CDbTestCase
{
	public $fixtures = array(
		'firms' => 'Firm',
		'patients' => 'Patient',
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
