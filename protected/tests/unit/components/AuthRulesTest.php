<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class AuthRulesTest extends PHPUnit_Framework_TestCase
{
	private $rules;

	public function setUp()
	{
		$this->rules = new AuthRules;
	}

	public function testCanEditEpisode_SupportServicesFirm_SupportServicesEpisode()
	{
		$this->assertTrue($this->rules->canEditEpisode($this->getSupportServicesFirm(), $this->getSupportServicesEpisode()));
	}

	public function testCanEditEpisode_NormalFirm_SupportServicesEpisode()
	{
		$this->assertFalse($this->rules->canEditEpisode($this->getNormalFirm(), $this->getSupportServicesEpisode()));
	}

	public function testCanEditEpisode_SupportServicesFirm_LegacyEpisode()
	{
		$this->assertFalse($this->rules->canEditEpisode($this->getSupportServicesFirm(), $this->getLegacyEpisode()));
	}

	public function testCanEditEpisode_NormalFirm_LegacyEpisode()
	{
		$this->assertFalse($this->rules->canEditEpisode($this->getNormalFirm(), $this->getLegacyEpisode()));
	}

	public function testCanEditEpisode_SupportServicesFirm_NormalEpisode()
	{
		$this->assertFalse($this->rules->canEditEpisode($this->getSupportServicesFirm(), $this->getNormalEpisode()));
	}

	public function testCanEditEpisode_NormalFirm_NormalEpisode_MatchingSubspecialty()
	{
		$this->assertTrue($this->rules->canEditEpisode($this->getNormalFirm(42), $this->getNormalEpisode(42)));
	}

	public function testCanEditEpisode_NormalFirm_NormalEpisode_NonMatchingSubspecialty()
	{
		$this->assertFalse($this->rules->canEditEpisode($this->getNormalFirm(42), $this->getNormalEpisode(43)));
	}

	public function testCanCreateEvent_Disabled()
	{
		$this->assertFalse($this->rules->canCreateEvent($this->getNormalFirm(), $this->getNormalEpisode(), $this->getDisabledEventType()));
	}

	public function testCanCreateEvent_SupportServicesFirm_NonSupportServiceEventType()
	{
		$this->assertFalse($this->rules->canCreateEvent($this->getSupportServicesFirm(), $this->getNormalEpisode(), $this->getNonSupportServicesEventType()));
	}

	public function testCanCreateEvent_SupportServicesFirm_SupportServiceEventType()
	{
		$this->assertTrue($this->rules->canCreateEvent($this->getSupportServicesFirm(), $this->getSupportServicesEpisode(), $this->getSupportServicesEventType()));
	}

	public function testCanCreateEvent_NormalFirm_NonSupportServiceEventType()
	{
		$this->assertTrue($this->rules->canCreateEvent($this->getNormalFirm(), $this->getNormalEpisode(), $this->getNonSupportServicesEventType()));
	}

	public function testCanCreateEvent_NormalFirm_SupportServiceEventType()
	{
		$this->assertTrue($this->rules->canCreateEvent($this->getNormalFirm(), $this->getNormalEpisode(), $this->getSupportServicesEventType()));
	}

	public function testCanCreateEvent_LegacyEpisode()
	{
		$this->assertFalse($this->rules->canCreateEvent($this->getNormalFirm(), $this->getLegacyEpisode(), $this->getNonSupportServicesEventType()));
	}

	public function testCanCreateEvent_WrongSubspecialtyEpisode()
	{
		$this->assertFalse($this->rules->canCreateEvent($this->getNormalFirm(42), $this->getNormalEpisode(43), $this->getNonSupportServicesEventType()));
	}

	public function testCanCreateEvent_NoData()
	{
		$this->assertTrue($this->rules->canCreateEvent());
	}

	public function testCanCreateEvent_NoEventType()
	{
		$this->assertTrue($this->rules->canCreateEvent($this->getNormalFirm(), $this->getNormalEpisode()));
	}

	public function testCanCreateEvent_NoEventType_LegacyEpisode()
	{
		$this->assertFalse($this->rules->canCreateEvent($this->getNormalFirm(), $this->getLegacyEpisode()));
	}

	public function testCanCreateEvent_NoEventType_WrongSubspecialtyEpisode()
	{
		$this->assertFalse($this->rules->canCreateEvent($this->getNormalFirm(42), $this->getNormalEpisode(43)));
	}

	public function testCanEditEvent_PatientDeceased()
	{
		$event = $this->getEvent();
		$event->episode->patient->date_of_death = '2013-11-13';
		$this->assertFalse($this->rules->canEditEvent($this->getNormalFirm(), $event));
	}

	public function testCanEditEvent_WrongSubspecialty()
	{
		$event = $this->getEvent(array('episode' => $this->getNormalEpisode(43)));
		$this->assertFalse($this->rules->canEditEvent($this->getNormalFirm(42), $event));
	}

	public function testCanEditEvent_CorrectSubspecialty()
	{
		$event = $this->getEvent(array('episode' => $this->getNormalEpisode(42)));
		$this->assertTrue($this->rules->canEditEvent($this->getNormalFirm(42), $event));
	}

	public function testCanEditEvent_LegacyEpisode()
	{
		$event = $this->getEvent(array('episode' => $this->getLegacyEpisode()));
		$this->assertFalse($this->rules->canEditEvent($this->getNormalFirm(), $event));
	}

	public function testCanDeleteEvent_WrongUser()
	{
		$event = $this->getEvent(array('created_user_id' => 1));
		$this->assertFalse($this->rules->canDeleteEvent($this->getUser(2), $this->getNormalFirm(), $event));
	}

	public function testCanDeleteEvent_TooLate()
	{
		$event = $this->getEvent(array('created_date' => date('Y-m-d', time() - 86401)));
		$this->assertFalse($this->rules->canDeleteEvent($this->getUser(), $this->getNormalFirm(), $event));
	}

	public function testCanDeleteEvent_PatientDeceased()
	{
		$event = $this->getEvent();
		$event->episode->patient->date_of_death = '2013-11-13';
		$this->assertFalse($this->rules->canDeleteEvent($this->getUser(), $this->getNormalFirm(), $event));
	}

	public function testCanDeleteEvent_WrongSubspecialty()
	{
		$event = $this->getEvent(array('episode' => $this->getNormalEpisode(43)));
		$this->assertFalse($this->rules->canDeleteEvent($this->getUser(), $this->getNormalFirm(42), $event));
	}

	public function testCanDeleteEvent_CorrectSubspecialty()
	{
		$event = $this->getEvent(array('episode' => $this->getNormalEpisode(42)));
		$this->assertTrue($this->rules->canDeleteEvent($this->getUser(1), $this->getNormalFirm(42), $event));
	}

	public function testCanDeleteEvent_LegacyEpisode()
	{
		$event = $this->getEvent(array('episode' => $this->getLegacyEpisode()));
		$this->assertFalse($this->rules->canDeleteEvent($this->getUser(), $this->getNormalFirm(), $event));
	}

	private function getSupportServicesFirm()
	{
		$firm = ComponentStubGenerator::generate('Firm', array('subspecialtyID' => null));
		$firm->expects($this->any())->method('isSupportServicesFirm')->will($this->returnValue(true));
		return $firm;
	}

	private function getNormalFirm($subspecialty_id = 42)
	{
		$firm = ComponentStubGenerator::generate('Firm', array('subspecialtyID' => $subspecialty_id));
		$firm->expects($this->any())->method('isSupportServicesFirm')->will($this->returnValue(false));
		return $firm;
	}

	private function getEpisode(array $props = array())
	{
		$props += array('patient' => ComponentStubGenerator::generate('Patient'));
		return ComponentStubGenerator::generate('Episode', $props);
	}

	private function getLegacyEpisode()
	{
		return $this->getEpisode(array('legacy' => true));
	}

	private function getSupportServicesEpisode()
	{
		return $this->getEpisode(array('support_services' => true));
	}

	private function getNormalEpisode($subspecialty_id = 42)
	{
		return $this->getEpisode(
			array(
				'firm' => $this->getNormalFirm($subspecialty_id),
				'support_services' => false,
				'subspecialtyID' => $subspecialty_id
			)
		);
	}

	private function getDisabledEventType()
	{
		return ComponentStubGenerator::generate(
			'EventType',
			array('disabled' => true, 'support_services' => false)
		);
	}

	private function getSupportServicesEventType()
	{
		return ComponentStubGenerator::generate(
			'EventType',
			array('disabled' => false, 'support_services' => true)
		);
	}

	private function getNonSupportServicesEventType()
	{
		return ComponentStubGenerator::generate(
			'EventType',
			array('disabled' => false, 'support_services' => false)
		);
	}

	private function getEvent(array $props = array())
	{
		$props += array(
			'episode' => $this->getNormalEpisode(42),
			'created_user_id' => 1,
			'created_date' => date('Y-m-d'),
		);

		return ComponentStubGenerator::generate('Event', $props);
	}

	private function getUser($id = 1)
	{
		return ComponentStubGenerator::generate('User', array('id' => $id));
	}
}
