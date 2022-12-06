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
use OEModule\PatientTicketing\models;

class PatientTicketing_APITest extends OEDbTestCase
{
    private $api;

    public $fixtures = array(
        'patients' => 'Patient',
        'queues' => 'OEModule\PatientTicketing\models\Queue',
        'queue_outcomes' => 'OEModule\PatientTicketing\models\QueueOutcome',
        'queuesets' => 'OEModule\PatientTicketing\models\QueueSet',
        'tickets' => 'OEModule\PatientTicketing\models\Ticket',
        'ticketassignments' => 'OEModule\PatientTicketing\models\TicketQueueAssignment',
    );

    public static function setUpBeforeClass(): void
    {
        Yii::app()->session['selected_institution_id'] = 1;
        Yii::app()->getModule('PatientTicketing');
    }

    public static function tearDownAfterClass(): void
    {
        unset(Yii::app()->session['selected_institution_id']);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->orig_svcman = Yii::app()->service;
        $this->svcman = new ServiceManagerWrapper();

        Yii::app()->setComponent('service', $this->svcman);
        $this->api = Yii::app()->moduleAPI->get('PatientTicketing');
    }

    public function tearDown(): void
    {
        Yii::app()->setComponent('service', $this->orig_svcman);
        parent::tearDown();
    }

    public function testGetFollowUpHasFollowUp()
    {
        $this->assertNotNull($this->api->getFollowUp(5));
    }

    public function testGetFollowUpNoFollowUp()
    {
        $this->assertFalse($this->api->getFollowUp(4));
    }

    public function testcanAddPatientToQueue()
    {
        $patient = new \Patient();
        $queue = new models\Queue();
        $queue->id = 5;

        $qs_r = $this->getMockBuilder('OEModule\PatientTicketing\services\PatientTicketing_QueueSet')
                ->disableOriginalConstructor()
                ->setMethods(array('getId'))
                ->getMock();

        $qs_r->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(3));

        $qs_svc = $this->getMockBuilder('OEModule\PatientTicketing\services\PatientTicketing_QueueSetService')
                ->disableOriginalConstructor()
                ->setMethods(array('canAddPatientToQueueSet', 'getQueueSetForQueue'))
                ->getMock();

        $qs_svc->expects($this->once())
                ->method('getQueueSetForQueue')
                ->with(5)
                ->will($this->returnValue($qs_r));

        $qs_svc->expects($this->once())
            ->method('canAddPatientToQueueSet')
            ->with($patient, 3)
            ->will($this->returnValue('test return'));

        $this->svcman->mocked_services['PatientTicketing_QueueSet'] = $qs_svc;

        $this->assertEquals('test return', $this->api->canAddPatientToQueue($patient, $queue), 'Should be passing through return value from service method');
    }

    public function testUpdateTicketForEvent_NoReportDefinition()
    {
        $event = new \Event();
        $ticket = new models\Ticket();
        $ticket->patient_id = 1;
        $queue = new models\Queue();

        $assignment = $this->getMockBuilder('OEModule\PatientTicketing\models\TicketQueueAssignment')
            ->disableOriginalConstructor()
            ->setMethods(array('save'))
            ->getMock();
        $ticket->save();
        $assignment->ticket_id = $ticket->id;

        $assignment->queue = $queue;

        $assignment->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $ticket->initial_queue_assignment = $assignment;

        $api = $this->getMockBuilder('OEModule\PatientTicketing\components\PatientTicketing_API')
            ->disableOriginalConstructor()
            ->setMethods(array('getTicketForEvent'))
            ->getMock();

        $api->expects($this->once())
            ->method('getTicketForEvent')
            ->willReturn($ticket);

        $api->updateTicketForEvent($event);
    }

    public function testUpdateTicketForEvent_WithReportDefinition()
    {
        $event = new \Event();
        $ticket = new models\Ticket();
        $queue = new models\Queue();

        $queue->report_definition = 'test4141';

        $assignment = $this->getMockBuilder('OEModule\PatientTicketing\models\TicketQueueAssignment')
            ->disableOriginalConstructor()
            ->setMethods(array('generateReportText', 'save'))
            ->getMock();

        $assignment->queue = $queue;

        $assignment->expects($this->once())
            ->method('generateReportText');

        $assignment->expects($this->once())
            ->method('save')
            ->will($this->returnValue(true));

        $ticket->initial_queue_assignment = $assignment;

        $api = $this->getMockBuilder('OEModule\PatientTicketing\components\PatientTicketing_API')
            ->disableOriginalConstructor()
            ->setMethods(array('getTicketForEvent'))
            ->getMock();

        $api->expects($this->once())
            ->method('getTicketForEvent')
            ->will($this->returnValue($ticket));

        $api->updateTicketForEvent($event);
    }
}

class ServiceManagerWrapper extends \services\ServiceManager
{
    public $mocked_services = array();

    public function getService($name)
    {
        if (@$this->mocked_services[$name]) {
            return $this->mocked_services[$name];
        } else {
            return parent::getService($name);
        }
    }
}
