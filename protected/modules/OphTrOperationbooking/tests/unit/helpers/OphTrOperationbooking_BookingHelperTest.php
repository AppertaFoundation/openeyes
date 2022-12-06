<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class OphTrOperationbooking_BookingHelperTest extends CTestCase
{
    public static function setUpBeforeClass(): void
    {
        Yii::import('application.modules.OphTrOperationbooking.helpers.*');
    }

    private $helper;

    private $session;
    private $op;

    public function setUp(): void
    {
        $this->helper = new OphTrOperationbooking_BookingHelper();

        $this->session = ComponentStubGenerator::generate(
            'OphTrOperationbooking_Operation_Session',
            array(
                'anaesthetist' => 1,
                'consultant' => 1,
                'paediatric' => 1,
                'general_anaesthetic' => 1,
            )
        );
        $this->op = ComponentStubGenerator::generate(
            'Element_OphTrOperationbooking_Operation',
            array(
                'anaesthetist_required' => 1,
                'consultant_required' => 1,
                'event' => ComponentStubGenerator::generate(
                    'Event',
                    array(
                        'episode' => ComponentStubGenerator::generate(
                            'Episode',
                            array('patient' => ComponentStubGenerator::generate('Patient'))
                        ),
                    )
                ),
                'anaesthetic_type' => ComponentStubGenerator::generate('AnaestheticType', array('name' => 'GA')),
            )
        );
    }

    public function testAllRequirementsMet()
    {
        $this->op->event->episode->patient->expects($this->any())->method('isChild')->will($this->returnValue(true));

        $this->assertEquals(
            array(),
            $this->helper->checkSessionCompatibleWithOperation($this->session, $this->op)
        );
    }

    public function testAnaesthetistRequired()
    {
        $this->session->anaesthetist = 0;

        $this->assertEquals(
            array(OphTrOperationbooking_BookingHelper::ANAESTHETIST_REQUIRED),
            $this->helper->checkSessionCompatibleWithOperation($this->session, $this->op)
        );
    }

    public function testConsultantRequired()
    {
        $this->session->consultant = 0;

        $this->assertEquals(
            array(OphTrOperationbooking_BookingHelper::CONSULTANT_REQUIRED),
            $this->helper->checkSessionCompatibleWithOperation($this->session, $this->op)
        );
    }

    public function testPaediatricSessionRequired()
    {
        $this->session->paediatric = 0;
        $this->op->event->episode->patient->expects($this->any())->method('isChild')->will($this->returnValue(true));

        $this->assertEquals(
            array(OphTrOperationbooking_BookingHelper::PAEDIATRIC_SESSION_REQUIRED),
            $this->helper->checkSessionCompatibleWithOperation($this->session, $this->op)
        );
    }

    public function testGeneralAnaestheticRequired()
    {
        $this->markTestIncomplete('Needs fixing');
        $this->session->general_anaesthetic = 0;

        $this->assertEquals(
            array(OphTrOperationbooking_BookingHelper::GENERAL_ANAESTHETIC_REQUIRED),
            $this->helper->checkSessionCompatibleWithOperation($this->session, $this->op)
        );
    }
}
