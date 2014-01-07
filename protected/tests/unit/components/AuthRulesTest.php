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
		$this->assertFalse($this->rules->canCreateEvent($this->getNormalFirm(), $this->getDisabledEventType()));
	}

	public function testCanCreateEvent_SupportServicesFirm_NonSupportServiceEventType()
	{
		$this->assertFalse($this->rules->canCreateEvent($this->getSupportServicesFirm(), $this->getNonSupportServicesEventType()));
	}

	public function testCanCreateEvent_SupportServicesFirm_SupportServiceEventType()
	{
		$this->assertTrue($this->rules->canCreateEvent($this->getSupportServicesFirm(), $this->getSupportServicesEventType()));
	}

	public function testCanCreateEvent_NormalFirm_NonSupportServiceEventType()
	{
		$this->assertTrue($this->rules->canCreateEvent($this->getNormalFirm(), $this->getNonSupportServicesEventType()));
	}

	public function testCanCreateEvent_NormalFirm_SupportServiceEventType()
	{
		$this->assertTrue($this->rules->canCreateEvent($this->getNormalFirm(), $this->getSupportServicesEventType()));
	}

	public function testCanEditEvent_PatientDeceased()
	{
		$episode = $this->getNormalEpisode(42, '2013-11-13');
		$event = $this->getEvent(array('created_user_id' => 1, 'episode' => $episode));

		$this->assertFalse($this->rules->canEditEvent($this->getNormalFirm(), $event));
	}

	public function testCanEditEvent_WrongSubspecialty()
	{
		$this->assertFalse($this->rules->canEditEvent($this->getNormalFirm(42), $this->getEvent(43)));
	}

	public function testCanEditEvent_CorrectSubspecialty()
	{
		$this->assertTrue($this->rules->canEditEvent($this->getNormalFirm(42), $this->getEvent(42)));
	}

	public function testCanDeleteEvent_WrongUser()
	{
		$event = $this->getEvent(42, array('created_user_id' => 1));
		$this->assertFalse($this->rules->canDeleteEvent($this->getUser(2), $this->getNormalFirm(), $event));
	}

	public function testCanDeleteEvent_TooLate()
	{
		$event = $this->getEvent(42, array('created_user_id' => 1, 'created_date' => date('Y-m-d', time() - 86401)));
		$this->assertFalse($this->rules->canDeleteEvent($this->getUser(1), $this->getNormalFirm(), $event));
	}

	public function testCanDeleteEvent_PatientDeceased()
	{
		$episode = $this->getNormalEpisode(42, '2013-11-13');
		$event = $this->getEvent(42, array('created_user_id' => 1, 'episode' => $episode));

		$this->assertFalse($this->rules->canDeleteEvent($this->getUser(1), $this->getNormalFirm(), $event));
	}

	public function testCanDeleteEvent_WrongSubspecialty()
	{
		$event = $this->getEvent(43, array('created_user_id' => 1));
		$this->assertFalse($this->rules->canDeleteEvent($this->getUser(1), $this->getNormalFirm(42), $this->getEvent(43)));
	}

	public function testCanDeleteEvent_CorrectSubspecialty()
	{
		$event = $this->getEvent(42, array('created_user_id' => 1));
		$this->assertTrue($this->rules->canDeleteEvent($this->getUser(1), $this->getNormalFirm(42), $this->getEvent(42)));
	}

	private function getSupportServicesFirm()
	{
		return ComponentStubGenerator::generate('Firm', array('subspecialtyID' => null));
	}

	private function getNormalFirm($subspecialty_id = 42)
	{
		return ComponentStubGenerator::generate('Firm', array('subspecialtyID' => $subspecialty_id));
	}

	private function getLegacyEpisode()
	{
		return ComponentStubGenerator::generate(
			'Episode', array('firm' => null, 'support_services' => false, 'subspecialtyID' => null)
		);
	}

	private function getSupportServicesEpisode()
	{
		return ComponentStubGenerator::generate(
			'Episode', array('firm' => null, 'support_services' => true, 'subspecialtyID' => null)
		);
	}

	private function getNormalEpisode($subspecialty_id = 42, $patient_date_of_death = null)
	{
		return ComponentStubGenerator::generate(
			'Episode',
			array(
				'patient' => ComponentStubGenerator::generate('Patient', array('date_of_death' => $patient_date_of_death)),
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

	private function getEvent($subspecialty_id = 42, array $props = array())
	{
		$props += array(
				'episode' => $this->getNormalEpisode($subspecialty_id),
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
