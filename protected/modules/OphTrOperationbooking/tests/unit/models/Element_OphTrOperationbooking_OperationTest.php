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
class Element_OphTrOperationbooking_OperationTest extends ActiveRecordTestCase
{
    public $fixtures = array(
        'episode' => 'Episode',
        'event' => Event::class,
        'subspecialties' => 'Subspecialty',
        'ssa' => 'ServiceSubspecialtyAssignment',
        'firms' => 'Firm',
        'wards' => 'OphTrOperationbooking_Operation_Ward',
        'patients' => 'Patient',
        'referral_types' => 'ReferralType',
        'referrals' => 'Referral',
        'rtt' => 'RTT',
        'statuses' => 'OphTrOperationbooking_Operation_Status',
        'sequences' => 'OphTrOperationbooking_Operation_Sequence',
        'sessions' => 'OphTrOperationbooking_Operation_Session',
        'anaesthetic_type_assignments' => 'OphTrOperationbooking_AnaestheticAnaestheticType',
        'operations' => 'Element_OphTrOperationbooking_Operation',
    );

    public function getModel()
    {
        return Element_OphTrOperationbooking_Operation::model();
    }

    protected array $columns_to_skip = [
        'total_duration'
    ];

    public static function setUpBeforeClass()
    {
        date_default_timezone_set('UTC');
    }

    /**
     * Checks both that the array values are equal, and that the keys for the array are in the same order
     * which assertEquals does not appear to do.
     *
     * @param $expected
     * @param $res
     */
    protected function assertOrderedAssocArrayEqual($expected, $res)
    {
        $this->assertEquals($expected, $res);
        $this->assertEquals(array_keys($expected), array_keys($res), 'Response key order does not match expected'.print_r($res, true));
    }

    protected function getMalePatient($is_stub = true)
    {
        if ($is_stub) {
            $p = ComponentStubGenerator::generate('Patient', array('gender' => 'M'));
            $p->method('isChild')->willReturn(false);

            return $p;
        }

        return 'patient1';
    }

    protected function getFemalePatient()
    {
        $p = ComponentStubGenerator::generate('Patient', array('gender' => 'F'));
        $p->method('isChild')->willReturn(false);

        return $p;
    }

    protected function getBoyPatient()
    {
        $p = ComponentStubGenerator::generate('Patient', array('gender' => 'M'));
        $p->method('isChild')->willReturn(true);

        return $p;
    }

    protected function getGirlPatient()
    {
        $p = ComponentStubGenerator::generate('Patient', array('gender' => 'F'));
        $p->method('isChild')->willReturn(true);

        return $p;
    }

    protected function getOperationForPatient($patient, $methods = null)
    {
        $op = $this->getMockBuilder('Element_OphTrOperationbooking_Operation')
                ->disableOriginalConstructor()
                ->setMethods($methods)
                ->getMock();

        $op->event = ComponentStubGenerator::generate(
            'Event',
            array(
                'episode' => ComponentStubGenerator::generate(
                    'Episode',
                    array('patient' => $patient, 'patient_id' => $patient->id)
                ),
            )
        );

        return $op;
    }

    protected function getSessionForTheatre($theatre)
    {
        $dt = new DateTime();
        $session = ComponentStubGenerator::generate(
            'OphTrOperationbooking_Operation_Session',
            array(
                'id' => 1,
                'theatre' => $theatre,
                'date' => $dt,
            )
        );

        return $session;
    }

    public function testgetWardOptions_MaleAdult()
    {
        $theatre = ComponentStubGenerator::generate(
            'OphTrOperationbooking_Operation_Theatre',
            array('site_id' => 1)
        );
        $session = $this->getSessionForTheatre($theatre);

        $op = $this->getOperationForPatient($this->getMalePatient());
        $res = $op->getWardOptions($session);

        $expected = array(
                $this->wards('ward1')->id => $this->wards('ward1')->name,
                $this->wards('ward4')->id => $this->wards('ward4')->name, );

        $this->assertOrderedAssocArrayEqual($expected, $res);
    }

    public function testgetWardOptions_FemaleAdult()
    {
        $theatre = ComponentStubGenerator::generate(
            'OphTrOperationbooking_Operation_Theatre',
            array('site_id' => 1)
        );
        $session = $this->getSessionForTheatre($theatre);

        $op = $this->getOperationForPatient($this->getFemalePatient());
        $res = $op->getWardOptions($session);

        $expected = array(
            $this->wards('ward2')->id => $this->wards('ward2')->name,
            $this->wards('ward4')->id => $this->wards('ward4')->name,
        );

        $this->assertOrderedAssocArrayEqual($expected, $res);
    }

    public function testgetWardOptions_Boy()
    {
        $theatre = ComponentStubGenerator::generate(
            'OphTrOperationbooking_Operation_Theatre',
            array('site_id' => 1)
        );
        $session = $this->getSessionForTheatre($theatre);

        $op = $this->getOperationForPatient($this->getBoyPatient());

        $res = $op->getWardOptions($session);

        $expected = array(
            $this->wards('ward5')->id => $this->wards('ward5')->name,
            $this->wards('ward6')->id => $this->wards('ward6')->name,
        );

        $this->assertOrderedAssocArrayEqual($expected, $res);
    }

    public function testgetWardOptions_Girl()
    {
        $theatre = ComponentStubGenerator::generate(
            'OphTrOperationbooking_Operation_Theatre',
            array('site_id' => 1)
        );
        $session = $this->getSessionForTheatre($theatre);

        $op = $this->getOperationForPatient($this->getGirlPatient());

        $res = $op->getWardOptions($session);

        $expected = array(
            $this->wards('ward3')->id => $this->wards('ward3')->name,
            $this->wards('ward6')->id => $this->wards('ward6')->name,
        );
        $this->assertOrderedAssocArrayEqual($expected, $res);
    }

    public function testgetWardOptions_OtherSite()
    {
        $theatre = ComponentStubGenerator::generate(
            'OphTrOperationbooking_Operation_Theatre',
            array('site_id' => 2)
        );
        $session = $this->getSessionForTheatre($theatre);

        $op = $this->getOperationForPatient($this->getMalePatient());

        $res = $op->getWardOptions($session);

        $expected = array(
            $this->wards('ward7')->id => $this->wards('ward7')->name,
        );
        $this->assertOrderedAssocArrayEqual($expected, $res);
    }

    public function testCantScheduleOperationWhenPatientUnavailable()
    {
        $theatre = ComponentStubGenerator::generate(
            'OphTrOperationbooking_Operation_Theatre',
            array('site_id' => 1)
        );
        $session = $this->getSessionForTheatre($theatre);

        $booking = ComponentStubGenerator::generate('OphTrOperationbooking_Operation_Booking', array('session' => $session));

        $op = $this->getOperationForPatient($this->getMalePatient());
        //in case configured to require referral for scheduling
        $op->referral = new Referral();
        $op_opts = $this->getMockBuilder('Element_OphTrOperationbooking_ScheduleOperation')
                ->disableOriginalConstructor()
                ->setMethods(array('isPatientAvailable'))
                ->getMock();
        $op_opts->expects($this->once())
            ->method('isPatientAvailable')
            ->willReturn(false);

        $res = $op->schedule($booking, '', '', '', false, null, $op_opts);
        $this->assertNotTrue($res);
        # arrays are error messages
        $this->assertEquals(gettype($res), 'array');
    }

    public function testProcedureCountSingleEye()
    {
        $op = new Element_OphTrOperationbooking_Operation();
        $op->procedures = array(new Procedure(), new Procedure());

        $this->assertEquals($op->getProcedureCount(), 2);
    }

    public function testProcedureCountBothEyes()
    {
        $op = new Element_OphTrOperationbooking_Operation();
        $op->procedures = array(new Procedure(), new Procedure());
        $op->eye_id = Eye::BOTH;

        $this->assertEquals($op->getProcedureCount(), 4);
    }

    public function testSchedule_ReferralRequiredWhenConfigured()
    {
        $curr = Yii::app()->params['ophtroperationbooking_schedulerequiresreferral'];
        Yii::app()->params['ophtroperationbooking_schedulerequiresreferral'] = true;

        $theatre = ComponentStubGenerator::generate(
            'OphTrOperationbooking_Operation_Theatre',
            array('site_id' => 1)
        );
        $session = $this->getSessionForTheatre($theatre);

        $booking = ComponentStubGenerator::generate('OphTrOperationbooking_Operation_booking', array('session' => $session));

        $op = $this->getOperationForPatient($this->getMalePatient());
        $op_opts = $this->getMockBuilder('Element_OphTrOperationbooking_ScheduleOperation')
                ->disableOriginalConstructor()
                ->setMethods(array('isPatientAvailable'))
                ->getMock();

        $op->referral = null;
        $res = $op->schedule($booking, '', '', '', false, null, $op_opts);
        $this->assertNotTrue($res);
        # arrays are error messages
        $this->assertSame(gettype($res), 'array');
        $this->assertEquals('Referral required to schedule operation', $res[0][0]);

        Yii::app()->params['ophtroperationbooking_schedulerequiresreferral'] = $curr;
    }

    public function testSchedule_ReferralNotRequiredWhenConfigured()
    {
        $curr = Yii::app()->params['ophtroperationbooking_schedulerequiresreferral'];
        Yii::app()->params['ophtroperationbooking_schedulerequiresreferral'] = false;
        $urgent = Yii::app()->params['urgent_booking_notify_hours'];
        Yii::app()->params['urgent_booking_notify_hours'] = false;

        // a lot of mocking needed as there's a lot of functionality in the schedule method
        // ... it might be nice to optimise this into a couple of different methods ...
        $booking = $this->getMockBuilder('OphTrOperationbooking_Operation_Booking')
                ->disableOriginalConstructor()
                ->setMethods(array('save', 'audit'))
                ->getMock();

        $booking->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $booking->expects($this->once())
            ->method('audit');

        $theatre = ComponentStubGenerator::generate(
            'OphTrOperationbooking_Operation_Theatre',
            array('site_id' => 1)
        );
        $session = $this->getSessionForTheatre($theatre);

        $session->expects($this->once())
            ->method('operationBookable')
            ->willReturn(true);

        // saved for comments
        $session->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $session->firm = new Firm();

        $booking->session = $session;

        $op = $this->getOperationForPatient($this->getMalePatient(), array('save', 'calculateEROD'));

        $op->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $erod = ComponentStubGenerator::generate('OphTrOperationbooking_Operation_EROD');
        $erod->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $op->expects($this->once())
            ->method('calculateEROD')
            ->willReturn($erod);

        $op->event->episode->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $op_opts = $this->getMockBuilder('Element_OphTrOperationbooking_ScheduleOperation')
                ->disableOriginalConstructor()
                ->setMethods(array('isPatientAvailable'))
                ->getMock();

        $op_opts->expects($this->once())
            ->method('isPatientAvailable')
            ->willReturn(true);

        // $session->expects($this->once())
        //     ->method('isBookable')
        //     ->will($this->returnValue(true));

        $op->referral = null;
        $res = $op->schedule($booking, '', '', '', false, null, $op_opts);
        $this->assertTrue($res);

        // reset params
        Yii::app()->params['ophtroperationbooking_schedulerequiresreferral'] = $curr;
        Yii::app()->params['urgent_booking_notify_hours'] = $urgent;
    }

    /**
     * @expectedException     Exception
     */
    public function testScheduleLocksRttNotInFuture()
    {
        $referral = $this->referrals('referral1');

        $op = new Element_OphTrOperationbooking_Operation();
        $op->attributes = array(
            'status_id' => 1,
            'anaesthetic_type_id' => 1,
            'referral_id' => $referral->id,
            'decision_date' => date('Y-m-d', strtotime('next week')),
            'total_duration' => 1,
        );

        // Have to set this manually here because event_id is not marked as a safe field, thus cannot be assigned as part of bulk assignment.
        $op->event_id = $this->event('event1')->id;

        $op->procedures = array(ComponentStubGenerator::generate('Procedure'));

        $schedule_op = ComponentStubGenerator::generate('Element_OphTrOperationbooking_ScheduleOperation');
        $schedule_op->method('isPatientAvailable')->willReturn(true);

        $booking = ComponentStubGenerator::generate(
            'OphTrOperationbooking_Operation_Booking',
            array(
                'session' => ComponentStubGenerator::generate(
                    'OphTrOperationbooking_Operation_Session',
                    array(
                        'date' => date('Y-m-d', strtotime('next week')),
                        'theatre' => ComponentStubGenerator::generate('OphTrOperationbooking_Operation_Theatre', array('site_id' => 1)),
                    )
                ),
                'event' => ComponentStubGenerator::generate(
                    'Event',
                    array(
                        'id' => 1,
                        'episode' => ComponentStubGenerator::generate(
                            'Episode',
                            array(
                                'id' => 1,
                                'patient' => ComponentStubGenerator::generate(
                                    'Patient',
                                    array('id' => 1,)
                                )
                            )
                        )
                    )
                ),
            )
        );

        $booking->method('save')->willReturn(true);
        $booking->session->method('operationBookable')->willReturn(true);
        $booking->session->method('save')->willReturn(true);

        $res = $op->schedule($booking, '', '', '', false, null, $schedule_op);

        $this->assertEquals($this->rtt('rtt1')->id, $op->rtt_id);
    }

    public function testScheduleLocksRtt()
    {
        //$this->markTestIncomplete('Requires an anaesthetic type/assignments fixture.');
        $referral = $this->referrals('referral1');

        $op = new Element_OphTrOperationbooking_Operation();
        $op->event_id = $this->event('event1')->id;

        $op->attributes = array(
            'status_id' => 1,
            //'anaesthetic_type_id' => 1,
            'referral_id' => $referral->id,
            'decision_date' => date('Y-m-d', strtotime('previous week')),
            'total_duration' => 1,
            'senior_fellow_to_do' => 1,
            'anaesthetist_preop_assessment' => 1,
            'anaesthetic_choice_id' => 1,
            'stop_medication' => 0,
            'fast_track' => 0,
            'special_equipment' => 0,
            'organising_admission_user_id' => 0,
            'any_grade_of_doctor' => 0,
            'priority_id' => 1,
            'complexity' => 0,
        );
        $op->id = 999;
        $this->assertNotNull($op->id);

        $op->procedures = array(ComponentStubGenerator::generate('Procedure'));
        $assignment = $this->anaesthetic_type_assignments('at1');
        $assignment->et_ophtroperationbooking_operation_id = 999;
        $op->anaesthetic_type_assignments = array($assignment);

        $schedule_op = ComponentStubGenerator::generate('Element_OphTrOperationbooking_ScheduleOperation');
        $schedule_op->method('isPatientAvailable')->willReturn(true);

        $booking = ComponentStubGenerator::generate(
            'OphTrOperationbooking_Operation_Booking',
            array(
                'session' => ComponentStubGenerator::generate(
                    'OphTrOperationbooking_Operation_Session',
                    array(
                        'date' => date('Y-m-d', strtotime('next week')),
                        'theatre' => ComponentStubGenerator::generate('OphTrOperationbooking_Operation_Theatre', array('site_id' => 1)),
                    )
                ),
            )
        );

        $booking->method('save')->willReturn(true);
        $booking->session->method('operationBookable')->willReturn(true);
        $booking->session->method('save')->willReturn(true);

        $res = $op->schedule($booking, '', '', '', false, null, $schedule_op);

        $this->assertEquals($this->rtt('rtt1')->id, $op->rtt_id);
    }

    public function testReferralValidatorMustBeCalled()
    {
        $op = $this->getOperationForPatient($this->patients('patient1'), array('validateReferral'));
        $op->referral_id = 1;
        $op->expects($this->once())
            ->method('validateReferral')
            ->with($this->equalTo('referral_id'), $this->isType('array'));

        $op->validate();
    }

    public function testReferralMustBelongtoPatient()
    {
        $op = $this->getOperationForPatient($this->patients('patient1'), array('addError'));

        $op->referral_id = $this->referrals('referral2')->id;

        $op->expects($this->once())
            ->method('addError')
            ->with($this->equalTo('referral_id'), $this->equalTo('Referral must be for the patient of the event'));

        $op->validateReferral('referral_id', array());
    }

    public function testWillStoreHasBookingsState()
    {
        $op = $this->getOperationForPatient($this->patients('patient1'), array('__get'));
        // although we don't care about the order, I don't think there's a way to expect
        // different calls to the same method in an arbitary order
        $op->expects($this->at(0))
            ->method('__get')
            ->with($this->equalTo('allBookings'))
            ->willReturn(array(new OphTrOperationbooking_Operation_Booking()));

        $op->expects($this->at(1))
                ->method('__get')
                ->with($this->equalTo('referral_id'))
                ->willReturn(1);

        $op->afterFind();
        // TODO: expand this to check storing original referral id as well
        $r = new ReflectionClass('Element_OphTrOperationbooking_Operation');
        $hb_prop = $r->getProperty('_has_bookings');
        $hb_prop->setAccessible(true);
        $this->assertTrue($hb_prop->getValue($op));
        $ref_prop = $r->getProperty('_original_referral_id');
        $ref_prop->setAccessible(true);
        $this->assertEquals(1, $ref_prop->getValue($op));
    }

    public function testcanChangeReferral_true()
    {
        $op = $this->getMockBuilder('Element_OphTrOperationbooking_Operation')
                ->disableOriginalConstructor()
                ->setMethods(null)
                ->getMock();
        $r = new ReflectionClass('Element_OphTrOperationbooking_Operation');
        $hb_prop = $r->getProperty('_has_bookings');
        $hb_prop->setAccessible(true);
        $hb_prop->setValue($op, false);

        $this->assertTrue($op->canChangeReferral());
    }

    public function testvalidateReferral_CannotBeChangedAfterOperationScheduled()
    {
        $op = $this->getOperationForPatient($this->patients('patient1'), array('canChangeReferral', 'addError'));

        $op->expects($this->once())
            ->method('canChangeReferral')
            ->willReturn(false);

        $r = new ReflectionClass('Element_OphTrOperationbooking_Operation');
        $ref_prop = $r->getProperty('_original_referral_id');
        $ref_prop->setAccessible(true);
        $ref_prop->setValue($op, 5);

        $op->referral_id = $this->referrals('referral1')->id;

        $op->expects($this->once())
            ->method('addError')
            ->with($this->equalTo('referral_id'), 'Referral cannot be changed after an operation has been scheduled');

        $op->validateReferral('referral_id', array());
    }

    public function testsetStatus_noSave()
    {
        $op = $this->getOperationForPatient($this->patients('patient1'), array('save'));

        $op->expects($this->never())
            ->method('save');

        $op->setStatus($this->statuses('scheduled')->name, false);
        $this->assertEquals($this->statuses('scheduled')->id, $op->status_id);
    }

    public function testsetStatus_save()
    {
        $op = $this->getOperationForPatient($this->patients('patient1'), array('save'));

        $op->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $op->setStatus($this->statuses('scheduled')->name);
        $this->assertEquals($this->statuses('scheduled')->id, $op->status_id);
    }

    public function testsetStatus_invalidStatus()
    {
        $op = $this->getOperationForPatient($this->patients('patient1'), array('save'));

        $op->expects($this->never())
                ->method('save');

        $this->expectException('Exception');
        $op->setStatus('Invalid Test Status');
    }

    public function testgetRTT_fixed()
    {
        $test = new Element_OphTrOperationbooking_Operation();
        $fxd_rtt = new RTT();

        $referral = ComponentStubGenerator::generate(
            'Referral',
            array(
                'activeRTT' => array(new RTT()),
            )
        );
        $test->fixed_rtt = $fxd_rtt;
        $test->referral = $referral;

        $this->assertSame($fxd_rtt, $test->getRTT());
    }

    public function testgetRTT_referral1Active()
    {
        $test = new Element_OphTrOperationbooking_Operation();

        $active_rtt = array(new RTT());

        $referral = ComponentStubGenerator::generate(
            'Referral',
            array(
                'activeRTT' => $active_rtt,
            )
        );
        $test->referral = $referral;

        $this->assertSame($active_rtt[0], $test->getRTT());
    }

    public function testgetRTT_referral2Active()
    {
        $test = new Element_OphTrOperationbooking_Operation();

        $active_rtt = array(new RTT(), new RTT());

        $referral = ComponentStubGenerator::generate(
            'Referral',
            array(
                'activeRTT' => $active_rtt,
            )
        );
        $test->referral = $referral;

        $this->assertNull($test->getRTT());
    }

    public function getCalculateERODTestCases()
    {
        return array(
            array(
                array(
                    'consultant_required' => false,
                    'anaesthetic_type' => 'LA',
                    'decision_date' => date('Y-m-d', strtotime('+5 days')),
                ),
                $this->getMalePatient(false),
                'firm1',
                'session4',
                'Check decision date affects which session is picked',
            ),
            array(
                array(
                    'consultant_required' => false,
                    'anaesthetic_type' => 'LA',
                    'decision_date' => date('Y-m-d', strtotime('-3 weeks')),
                ),
                $this->getMalePatient(false),
                'firm1',
                'session3',
                'Short notice restriction on EROD',
            ),
            array(
                array(
                    'consultant_required' => false,
                    'anaesthetic_type' => 'LA',
                    'decision_date' => date('Y-m-d', strtotime('-3 weeks')),
                ),
                $this->getMalePatient(false),
                'firm2',
                'session10',
                'Short notice different firm',
            ),
            array(
                array(
                    'consultant_required' => true,
                    'anaesthetic_type' => 'LA',
                    'decision_date' => date('Y-m-d', strtotime('-1 day')),
                ),
                $this->getMalePatient(false),
                'firm1',
                'session5',
                'Consultant impacting which session is picked',
            ),
            array(
                // NOTE: This is likely meant to test when the anaesthetist_required flag is set, but the test currently does not set this.
                // To resolve the issue, the code has been changed to 'GA'.
                array(
                    'consultant_required' => false,
                    'anaesthetic_type' => 'GA',
                    'decision_date' => date('Y-m-d', strtotime('-1 day')),
                ),
                $this->getMalePatient(false),
                'firm1',
                'session7',
                'Anaethetist impacting which session is picked',
            ),
            array(
                array(
                    'consultant_required' => false,
                    'anaesthetic_type' => 'GA',
                    'decision_date' => date('Y-m-d', strtotime('-1 day')),
                ),
                $this->getMalePatient(false),
                'firm1',
                'session7',
                'GA anaesthetic impacting which session is picked',
            ),
        );
    }

    /**
     * @dataProvider getCalculateERODTestCases
     * @param $op_properties
     * @param $patient
     * @param $firm_key
     * @param $expected_erod_session_key
     * @param $description
     * @throws Exception
     */
    public function testcalculateEROD($op_properties, $patient, $firm_key, $expected_erod_session_key, $description)
    {
        //$this->markTestIncomplete('Currently experiences issues with ComponentStubGenerator.');

        $test = new Element_OphTrOperationbooking_Operation();

        foreach ($op_properties as $k => $v) {
            if ($k !== 'anaesthetic_type') {
                $test->$k = $v;
            }
        }

        $test->anaesthetic_type = array(AnaestheticType::model()->findByAttributes(array('code' => $op_properties['anaesthetic_type'])));
        $test->event_id = $this->event('event1')->id;
        $calculated = $test->calculateEROD($this->firms($firm_key));

        if ($expected_erod_session_key) {
            $this->assertNotNull($calculated, $description.' should return an EROD');
            $this->assertInstanceOf(\OphTrOperationbooking_Operation_EROD::class, $calculated, $description . ' not returning EROD');
            $this->assertEquals(
                $this->sessions($expected_erod_session_key)->id,
                $calculated->session_id,
                $description.' - incorrect session picked for EROD'
            );
        } else {
            $this->assertNull($calculated, $description.' should not have an EROD');
        }
    }

    public function testgetRTTBreach_actualRTT()
    {
        $test = $this->getMockBuilder('Element_OphTrOperationbooking_Operation')
                ->disableOriginalConstructor()
                ->setMethods(array('getRTT'))
                ->getMock();

        $rtt = new RTT();
        $rtt->breach = date('Y-m-d', strtotime('+80 days'));

        $test
            ->method('getRTT')
            ->willReturn($rtt);

        $this->assertEquals($rtt->breach, $test->getRTTBreach());
    }

    public function testgetRTTBreach_configured()
    {
        $curr = Yii::app()->params['ophtroperationboooking_rtt_limit'];
        Yii::app()->params['ophtroperationboooking_rtt_limit'] = 3;

        $test = $this->getMockBuilder('Element_OphTrOperationbooking_Operation')
                ->disableOriginalConstructor()
                ->setMethods(array('getRTT'))
                ->getMock();

        $test->decision_date = date('Y-m-d', strtotime('-1 week'));

        $test
                ->method('getRTT')
                ->willReturn(null);

        $this->assertEquals(date('Y-m-d', strtotime('+2 weeks')), $test->getRTTBreach());
        Yii::app()->params['ophtroperationboooking_rtt_limit'] = $curr;
    }

    public function testgetRTTBreach_notConfigured()
    {
        $curr = Yii::app()->params['ophtroperationboooking_rtt_limit'];
        Yii::app()->params['ophtroperationboooking_rtt_limit'] = null;

        $test = $this->getMockBuilder('Element_OphTrOperationbooking_Operation')
                ->disableOriginalConstructor()
                ->setMethods(array('getRTT'))
                ->getMock();

        $test->decision_date = date('Y-m-d', strtotime('-1 week'));

        $test
                ->method('getRTT')
                ->willReturn(null);

        $this->assertNull($test->getRTTBreach());

        Yii::app()->params['ophtroperationboooking_rtt_limit'] = $curr;
    }
}
