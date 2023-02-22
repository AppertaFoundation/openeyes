<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OESysEvent\tests\unit\components;

use OEModule\OphCoCvi\components\OphCoCvi_Manager;
use OEModule\OphCoCvi\models\Element_OphCoCvi_Esign;
use OEModule\OphCoCvi\models\Element_OphCoCvi_EventInfo;
use OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo;
use OEModule\OphCoCvi\models\Element_OphCoCvi_ClericalInfo;
use OEModule\OphCoCvi\models\Element_OphCoCvi_Demographics;
use \OphCoCvi_Signature;
use OEDbTestCase;

/**
 * @group sample-data
 */
class OphCoCvi_ManagerTest extends OEDbTestCase
{
    use \InteractsWithEventTypeElements;

    private $manager;
    private $patient;
    private $event;
    private $esign_element;

    public function setUp(): void
    {
        parent::setUp();
        $this->manager = new OphCoCvi_Manager(\Yii::app());

        $this->patient = $this->getPatientWithEpisodes();
        $this->event = $this->getEventToSaveWith($this->patient);
        $this->esign_element = $this->createElement_OphCoCvi_Esign();

        $this->createElement_OphCoCvi_EventInfo();
        $this->createMocks();
    }

    protected function createMocks()
    {
        $this->mock_manager = $this->getMockBuilder(get_class($this->manager))
            ->onlyMethods(
                array('getDemographicsElementForEvent', 'getClericalElementForEvent', 'getClinicalElementForEvent')
            )
            ->getMock();

        $mock_demographics = $this->getMockBuilder(Element_OphCoCvi_Demographics::class)->onlyMethods(array('validate')
        )->getMock();
        $mock_demographics->method('validate')->willReturn(true);

        $mock_clerical = $this->getMockBuilder(Element_OphCoCvi_ClericalInfo::class)->onlyMethods(array('validate')
        )->getMock();
        $mock_clerical->method('validate')->willReturn(true);

        $mock_clinical = $this->getMockBuilder(Element_OphCoCvi_ClinicalInfo::class)->onlyMethods(array('validate')
        )->getMock();
        $mock_clinical->method('validate')->willReturn(true);

        $this->mock_manager->method('getClinicalElementForEvent')->willReturn($mock_clinical);
        $this->mock_manager->method('getDemographicsElementForEvent')->willReturn($mock_demographics);
        $this->mock_manager->method('getClericalElementForEvent')->willReturn($mock_clerical);
    }

    protected function getEventTypeId()
    {
        return \EventType::model()->find("class_name = :cls_name", [':cls_name' => 'OphCoCvi'])->getPrimaryKey();
    }

    private function createElement_OphCoCvi_Esign()
    {
        $element = new Element_OphCoCvi_Esign();
        $element->event_id = $this->event->id;
        $element->save(false);

        return $element;
    }

    private function createElement_OphCoCvi_EventInfo()
    {
        $element = new Element_OphCoCvi_EventInfo();
        $element->event_id = $this->event->id;
        $element->save();
        return $element;
    }

    private function addConsultantSignature()
    {
        $signatory_user = \User::model()->findByPk(1);
        $esign = new OphCoCvi_Signature();
        $esign->element_id = $this->esign_element->id;
        $esign->signatory_name = 'Test signatory';
        $esign->signatory_role = 'Consultant';
        $esign->timestamp = mktime();
        $esign->type = $esign::TYPE_LOGGEDIN_USER;
        $esign->signed_user_id = $signatory_user->id;
        $esign->status = 1;
        $esign->signature_file_id = $signatory_user->signature_file_id;
        $esign->save(false);
        return $esign;
    }

    private function addPatientSignature()
    {
        // for signature file
        $signatory_user = \User::model()->findByPk(1);

        $esign = new OphCoCvi_Signature();
        $esign->element_id = $this->esign_element->id;
        $esign->signatory_name = $this->patient->getFullName();
        $esign->signatory_role = 'Patient';
        $esign->timestamp = mktime();
        $esign->type = $esign::TYPE_PATIENT;
        $esign->signed_user_id = null;
        $esign->status = 1;
        $esign->signature_file_id = $signatory_user->signature_file_id;
        $esign->save(false);
        return $esign;
    }

    public function testcanIssueWithoutSignatures()
    {
        $can_issue = $this->mock_manager->signatureValidation($this->event);

        $this->assertFalse($can_issue);
    }

    public function testcanIssueWithConsultantSignature()
    {
        $this->addConsultantSignature();
        $can_issue = $this->mock_manager->signatureValidation($this->event);
        $this->assertFalse($can_issue);
    }

    public function testcanIssueWithConsultantAndPatientSignature()
    {
        $this->addConsultantSignature();
        $this->addPatientSignature();
        $can_issue = $this->mock_manager->signatureValidation($this->event);
        $this->assertTrue($can_issue);
    }

    public function testcanIssueCviWithoutSignatures()
    {
        $can_issue = $this->mock_manager->canIssueCvi($this->event);
        $this->assertFalse($can_issue);
    }

    public function testcanIssueCviWithSignatures()
    {
        $this->addConsultantSignature();
        $this->addPatientSignature();
        $can_issue = $this->mock_manager->canIssueCvi($this->event);
        $this->assertTrue($can_issue);
    }

    public function testSignatoryIsPatient()
    {
        $this->addPatientSignature();
        $result = $this->esign_element->isSignedByPatient();
        $this->assertTrue($result);
    }

    public function testSignatoryIsNotPatient()
    {
        $this->addConsultantSignature();
        $result = $this->esign_element->isSignedByPatient();
        $this->assertFalse($result);
    }
}
