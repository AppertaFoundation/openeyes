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
use OE\factories\models\EventFactory;

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
    private $signatory_user;

    public function setUp(): void
    {
        parent::setUp();
        $this->signatory_user = \User::model()->findByAttributes(['first_name' => 'admin']);
        $this->manager = new OphCoCvi_Manager(\Yii::app());
    }

    protected function createValidationMock(
        bool $clericalValidation,
        bool $clinicalValidation,
        bool $demographicsValidation,
        bool $signatureValidation
    ) {
        $manager = $this->getMockBuilder(OphCoCvi_Manager::class)
            ->onlyMethods(['clericalValidation', 'clinicalValidation', 'demographicsValidation', 'signatureValidation'])
            ->getMock();
        $manager->method('clericalValidation')->willReturn($clericalValidation);
        $manager->method('clinicalValidation')->willReturn($clinicalValidation);
        $manager->method('demographicsValidation')->willReturn($demographicsValidation);
        $manager->method('signatureValidation')->willReturn($signatureValidation);

        return $manager;
    }

    /** @test */
    public function cannot_issue_when_clerical_is_not_valid()
    {
        $event = EventFactory::forModule('OphCoCvi')->create();
        $manager = $this->createValidationMock(false, true, true, true);
        $manager->expects($this->once())
            ->method('clericalValidation')
            ->with($event)
            ->willReturn(false);

        $this->assertFalse($manager->clericalValidation($event));
    }

    /** @test */
    public function cannot_issue_when_clinical_is_not_valid()
    {
        $event = EventFactory::forModule('OphCoCvi')->create();
        $manager = $this->createValidationMock(true, false, true, true);
        $manager->expects($this->once())
            ->method('clinicalValidation')
            ->with($event)
            ->willReturn(false);

        $this->assertFalse($manager->clinicalValidation($event));
    }

    /** @test */
    public function cannot_issue_when_demographics_is_not_valid()
    {
        $event = EventFactory::forModule('OphCoCvi')->create();
        $manager = $this->createValidationMock(true, true, false, true);
        $manager->expects($this->once())
            ->method('demographicsValidation')
            ->with($event)
            ->willReturn(false);

        $this->assertFalse($manager->demographicsValidation($event));
    }

    /** @test */
    public function cannot_issue_when_signature_is_missing()
    {
        $event = EventFactory::forModule('OphCoCvi')->create();
        $manager = $this->createValidationMock(true, true, true, false);
        $manager->expects($this->once())
            ->method('signatureValidation')
            ->with($event)
            ->willReturn(false);

        $this->assertFalse($manager->signatureValidation($event));
    }

    /** @test */
    public function signature_validation_fails_when_no_patient_signatures_recorded()
    {
        $element = Element_OphCoCvi_Esign::factory()->create();
        OphCoCvi_Signature::factory()->asConsultant($element->id, $this->signatory_user)->create();
        OphCoCvi_Signature::factory()->asPatient($element->id, $this->signatory_user)->create();

        $this->assertTrue($this->manager->signatureValidation($element->event));
    }

    /** @test */
    public function signature_validation_fails_when_only_consultant_signature_recorded()
    {
        $element = Element_OphCoCvi_Esign::factory()->create();
        OphCoCvi_Signature::factory()->asConsultant($element->id, $this->signatory_user)->create();

        $this->assertFalse($this->manager->signatureValidation($element->event));
    }

    /** @test */
    public function signature_validation_success_when_consultant_and_patient_signature_are_recorded()
    {
        $element = Element_OphCoCvi_Esign::factory()->create();
        OphCoCvi_Signature::factory()->asConsultant($element->id, $this->signatory_user)->create();
        OphCoCvi_Signature::factory()->asPatient($element->id, $this->signatory_user)->create();

        $this->assertTrue($this->manager->signatureValidation($element->event));
    }

}
