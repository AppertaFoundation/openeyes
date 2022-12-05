<?php
/**
 * (C) OpenEyes Foundation, 2014
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
class DefaultControllerTest extends OEDbTestCase
{
    public static function setUpBeforeClass(): void
    {
        Yii::import('application.modules.OphTrOperationbooking.controllers.*');
    }

    public $fixtures = array(
        'patients' => 'Patient',
        'referral_types' => 'ReferralType',
        'referrals' => 'Referral',
    );

    public function getDefaultController($methods = null)
    {
        return $this->getMockBuilder('DefaultController')
                ->setConstructorArgs(array('DefaultController', new BaseEventTypeModule('OphTrOperationbooking', null)))
                ->setMethods($methods)
                ->getMock();
    }

    public function testCalculateDefaultReferral_first()
    {
        $this->markTestIncomplete();

        $test = $this->getDefaultController(array('getReferralChoices'));

        $test->firm = ComponentStubGenerator::generate('Firm', array('id' => 3, 'service_subspecialty_assignment_id' => 1));

        $test->expects($this->once())
            ->method('getReferralChoices')
            ->will($this->returnValue(array(
                                        ComponentStubGenerator::generate('Referral', array('id' => 5)),
                                        ComponentStubGenerator::generate('Referral', array('id' => 9)),
                                )));

        $res = $test->calculateDefaultReferral();
        $this->assertEquals(5, $res->id);
    }

    public function testCalculateDefaultReferral_firm()
    {
        $this->markTestIncomplete();

        $test = $this->getDefaultController(array('getReferralChoices'));

        $firm = ComponentStubGenerator::generate('Firm', array('id' => 3, 'service_subspecialty_assignment_id' => 1));
        $test->firm = $firm;

        $test->expects($this->once())
                ->method('getReferralChoices')
                ->will($this->returnValue(array(
                                        ComponentStubGenerator::generate('Referral', array('id' => 5)),
                                        ComponentStubGenerator::generate('Referral', array('id' => 9, 'firm' => $firm, 'firm_id' => $firm->id)),
                                )));

        $res = $test->calculateDefaultReferral();
        $this->assertEquals(9, $res->id);
    }

    public function testCalculateDefaultReferral_ssa()
    {
        $this->markTestIncomplete();

        $test = $this->getDefaultController(array('getReferralChoices'));

        $firm = ComponentStubGenerator::generate('Firm', array('id' => 3, 'service_subspecialty_assignment_id' => 1));
        $test->firm = $firm;
        $firm2 = ComponentStubGenerator::generate('Firm', array('id' => 9, 'service_subspecialty_assignment_id' => 1));

        $test->expects($this->once())
                ->method('getReferralChoices')
                ->will($this->returnValue(array(
                                        ComponentStubGenerator::generate('Referral', array('id' => 5, 'service_subspecialty_assignment_id' => 3)),
                                        ComponentStubGenerator::generate('Referral', array('id' => 9, 'firm' => $firm2, 'service_subspecialty_assignment_id' => 7)),
                                        ComponentStubGenerator::generate('Referral', array('id' => 12, 'service_subspecialty_assignment_id' => 1)),
                                )));

        $res = $test->calculateDefaultReferral();
        $this->assertEquals(12, $res->id);
    }
}
