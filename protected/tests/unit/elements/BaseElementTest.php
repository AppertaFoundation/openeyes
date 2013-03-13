<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/* Currently failing as BaseElement doesn't have a db table. Suspect that this file needs replacing
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
*/
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
/*
}
*/
