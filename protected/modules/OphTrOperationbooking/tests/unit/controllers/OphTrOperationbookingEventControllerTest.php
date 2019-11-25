<?php
/**
 * Created by PhpStorm.
 * User: msmith
 * Date: 22/03/2014
 * Time: 10:50.
 */
class OphTrOperationbookingEventControllerTest extends CDbTestCase
{
    public static function setupBeforeClass()
    {
        Yii::import('application.modules.OphTrOperationbooking.controllers.*');
    }

    public $fixtures = array(
        'patients' => 'Patient',
        'referral_types' => 'ReferralType',
        'referrals' => 'Referral',
        'events' => 'Event',
    );

    public function getOphTrOperationbookingEventController($methods = null)
    {
        return $this->getMockBuilder('OphTrOperationbookingEventController')
                ->setConstructorArgs(array('OphTrOperationbookingEventController', new BaseEventTypeModule('OphTrOperationbooking', null)))
                ->setMethods($methods)
                ->getMock();
    }

    public function testGetReferralChoices()
    {
        $test = $this->getOphTrOperationbookingEventController();
        $test->patient = $this->patients('patient1');

        $this->assertEquals(array($this->referrals('referral3'), $this->referrals('referral1')), $test->getReferralChoices());
    }

    public function testGetReferralChoices_forElement()
    {
        $test = $this->getOphTrOperationbookingEventController();
        $test->patient = $this->patients('patient1');

        $element = ComponentStubGenerator::generate('Element_OphTrOperationbooking_Operation', array('referral_id' => $this->referrals('referral4')->id, 'referral' => $this->referrals('referral4')));
        $referrals = $test->getReferralChoices($element);

        $this->assertEquals(array($this->referrals('referral3'), $this->referrals('referral1'), $this->referrals('referral4')), $referrals);
    }

    public function testCheckScheduleAccess_ExistingEvent_NoEditRights()
    {
        $c = $this->getMockBuilder('OphTrOperationbookingEventController')
            ->disableOriginalConstructor()
            ->setMethods(array('checkEditAccess'))
            ->getMock();

        $c->event = $this->events('event1');

        $c->expects($this->once())
            ->method('checkEditAccess')
            ->will($this->returnValue(false));

        $this->assertFalse($c->checkScheduleAccess());
    }

    public function testCheckScheduleAccess_NewEvent_NoEditRights()
    {
        $c = $this->getMockBuilder('OphTrOperationbookingEventController')
            ->disableOriginalConstructor()
            ->setMethods(array('checkAccess'))
            ->getMock();

        $e = new Event();
        $c->event = $e;

        $c->expects($this->once())
            ->method('checkAccess')
            ->with('Edit')
            ->will($this->returnValue('werp'));

        $this->assertEquals('werp', $c->checkScheduleAccess());
    }

    public function testCheckScheduleAccess_PassedPriority_CheckAccess()
    {
        $c = $this->getMockBuilder('OphTrOperationbookingEventController')
            ->disableOriginalConstructor()
            ->setMethods(array('checkEditAccess', 'getOpenElementByClassName', 'checkAccess'))
            ->getMock();

        $c->expects($this->never())
            ->method('getOpenElementByClassName');

        $c->expects($this->once())
            ->method('checkAccess')
            ->with('wubwubwub')
            ->will($this->returnValue('booblyboo'));

        $c->expects($this->never())
            ->method('checkEditAccess');

        $priority = new OphTrOperationbooking_Operation_Priority();
        $priority->schedule_authitem = 'wubwubwub';

        $this->assertEquals('booblyboo', $c->checkScheduleAccess($priority));
    }

    public function testCheckScheduleAccess_PriorityFromEO_CheckAccess()
    {
        $c = $this->getMockBuilder('OphTrOperationbookingEventController')
            ->disableOriginalConstructor()
            ->setMethods(array('checkEditAccess', 'getOpenElementByClassName', 'checkAccess'))
            ->getMock();

        $priority = new OphTrOperationbooking_Operation_Priority();
        $priority->schedule_authitem = 'wobwobwob';

        $eo = new Element_OphTrOperationbooking_Operation();
        $eo->priority = $priority;

        $c->expects($this->once())
            ->method('getOpenElementByClassName')
            ->with('Element_OphTrOperationbooking_Operation')
            ->will($this->returnValue($eo));

        $c->expects($this->never())
            ->method('checkEditAccess');

        $c->expects($this->once())
            ->method('checkAccess')
            ->with('wobwobwob')
            ->will($this->returnValue('woob'));

        $this->assertEquals('woob', $c->checkScheduleAccess());
    }
}
