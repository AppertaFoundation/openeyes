<?php

use OEModule\PatientTicketing\models\Ticket;

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class TicketTest extends \ActiveRecordTestCase
{
    public $fixtures = array(
            'queues' => 'OEModule\PatientTicketing\models\Queue',
            'queue_outcomes' => 'OEModule\PatientTicketing\models\QueueOutcome',
            'queuesets' => 'OEModule\PatientTicketing\models\QueueSet',
            'tickets' => 'OEModule\PatientTicketing\models\Ticket',
            'queue_assignments' => 'OEModule\PatientTicketing\models\TicketQueueAssignment',
    );

    public function getModel()
    {
        return Ticket::model();
    }

    public function setUp(): void
    {
        $this->original_service_manager = Yii::app()->service;
        $this->service_manager = new ServiceManagerWrapper2();

        Yii::app()->setComponent('service', $this->service_manager);
        parent::setUp();
    }

    public function tearDown(): void
    {
        Yii::app()->setComponent('service', $this->original_service_manager);
        parent::tearDown();
    }

    public function testGetDisplayQueue_NoDefaultQueue()
    {
        $service = $this->getMockBuilder('OEModule\PatientTicketing\services\PatientTicketing_QueueSetService')
            ->disableOriginalConstructor()
            ->setMethods(array('getQueueSetForQueue'))
            ->getMock();

        $this->service_manager->mocked_services['PatientTicketing_QueueSet'] = $service;

        $queueset = new OEModule\PatientTicketing\models\QueueSet();

        $service->expects($this->once())
            ->method('getQueueSetForQueue')
            ->will($this->returnValue($queueset));

        $model = new OEModule\PatientTicketing\models\Ticket();

        $current_queue = new OEModule\PatientTicketing\models\Queue();
        $current_queue->id = 939393;

        $model->current_queue = $current_queue;

        $display_queue = $model->getDisplayQueue();

        $this->assertInstanceOf('OEModule\PatientTicketing\models\Queue', $display_queue);
        $this->assertEquals(939393, $display_queue->id);
    }

    public function testGetDisplayQueue_DefaultQueue_NoAssignment()
    {
        $service = $this->getMockBuilder('OEModule\PatientTicketing\services\PatientTicketing_QueueSetService')
            ->disableOriginalConstructor()
            ->setMethods(array('getQueueSetForQueue'))
            ->getMock();

        $this->service_manager->mocked_services['PatientTicketing_QueueSet'] = $service;

        $default_queue = new OEModule\PatientTicketing\services\PatientTicketing_Queue(array('id' => 7070707));

        $queueset = new OEModule\PatientTicketing\models\QueueSet();
        $queueset->default_queue = $default_queue;

        $service->expects($this->once())
            ->method('getQueueSetForQueue')
            ->will($this->returnValue($queueset));

        $model = new OEModule\PatientTicketing\models\Ticket();

        $current_queue = new OEModule\PatientTicketing\models\Queue();
        $current_queue->id = 939393;

        $model->current_queue = $current_queue;

        $assignment = new OEModule\PatientTicketing\models\TicketQueueAssignment();
        $assignment->queue_id = 7070706;

        $model->queue_assignments = array($assignment);

        $display_queue = $model->getDisplayQueue();

        $this->assertInstanceOf('OEModule\PatientTicketing\models\Queue', $display_queue);
        $this->assertEquals(939393, $display_queue->id);
    }

    public function testGetDisplayQueue_DefaultQueue_WithAssignment()
    {
        $service = $this->getMockBuilder('OEModule\PatientTicketing\services\PatientTicketing_QueueSetService')
            ->disableOriginalConstructor()
            ->setMethods(array('getQueueSetForQueue'))
            ->getMock();

        $this->service_manager->mocked_services['PatientTicketing_QueueSet'] = $service;

        $default_queue = new OEModule\PatientTicketing\services\PatientTicketing_Queue(array('id' => 7070707));

        $queueset = new OEModule\PatientTicketing\models\QueueSet();
        $queueset->default_queue = $default_queue;

        $service->expects($this->once())
            ->method('getQueueSetForQueue')
            ->will($this->returnValue($queueset));

        $model = new OEModule\PatientTicketing\models\Ticket();

        $current_queue = new OEModule\PatientTicketing\models\Queue();
        $current_queue->id = 939393;

        $model->current_queue = $current_queue;

        $assignment = new OEModule\PatientTicketing\models\TicketQueueAssignment();
        $assignment->queue_id = 7070707;

        $model->queue_assignments = array($assignment);

        $display_queue = $model->getDisplayQueue();

        $this->assertInstanceOf('OEModule\PatientTicketing\services\PatientTicketing_Queue', $display_queue);
        $this->assertEquals(7070707, $display_queue->getId());
    }

    public function testGetDisplayQueueAssignment_NoDefaultQueue()
    {
        $service = $this->getMockBuilder('OEModule\PatientTicketing\services\PatientTicketing_QueueSetService')
            ->disableOriginalConstructor()
            ->setMethods(array('getQueueSetForQueue'))
            ->getMock();

        $this->service_manager->mocked_services['PatientTicketing_QueueSet'] = $service;

        $queueset = new OEModule\PatientTicketing\models\QueueSet();

        $service->expects($this->once())
            ->method('getQueueSetForQueue')
            ->will($this->returnValue($queueset));

        $model = new OEModule\PatientTicketing\models\Ticket();

        $current_queue = new OEModule\PatientTicketing\models\Queue();
        $current_queue_assignment = new OEModule\PatientTicketing\models\TicketQueueAssignment();
        $current_queue_assignment->id = 939393;

        $model->current_queue = $current_queue;
        $model->current_queue_assignment = $current_queue_assignment;

        $display_queue_assignment = $model->getDisplayQueueAssignment();

        $this->assertInstanceOf('OEModule\PatientTicketing\models\TicketQueueAssignment', $display_queue_assignment);
        $this->assertEquals(939393, $display_queue_assignment->id);
    }

    public function testGetDisplayQueueAssignment_DefaultQueue_NoAssignment()
    {
        $service = $this->getMockBuilder('OEModule\PatientTicketing\services\PatientTicketing_QueueSetService')
            ->disableOriginalConstructor()
            ->setMethods(array('getQueueSetForQueue'))
            ->getMock();

        $this->service_manager->mocked_services['PatientTicketing_QueueSet'] = $service;

        $default_queue = new OEModule\PatientTicketing\services\PatientTicketing_Queue(array('id' => 7070707));

        $queueset = new OEModule\PatientTicketing\models\QueueSet();
        $queueset->default_queue = $default_queue;

        $service->expects($this->once())
            ->method('getQueueSetForQueue')
            ->will($this->returnValue($queueset));

        $model = new OEModule\PatientTicketing\models\Ticket();

        $current_queue = new OEModule\PatientTicketing\models\Queue();
        $current_queue_assignment = new OEModule\PatientTicketing\models\TicketQueueAssignment();
        $current_queue_assignment->id = 939393;

        $model->current_queue = $current_queue;
        $model->current_queue_assignment = $current_queue_assignment;

        $assignment = new OEModule\PatientTicketing\models\TicketQueueAssignment();
        $assignment->queue_id = 7070706;

        $model->queue_assignments = array($assignment);

        $display_queue_assignment = $model->getDisplayQueueAssignment();

        $this->assertInstanceOf('OEModule\PatientTicketing\models\TicketQueueAssignment', $display_queue_assignment);
        $this->assertEquals(939393, $display_queue_assignment->id);
    }

    public function testGetDisplayQueueAssignment_DefaultQueue_WithAssignment()
    {
        $service = $this->getMockBuilder('OEModule\PatientTicketing\services\PatientTicketing_QueueSetService')
            ->disableOriginalConstructor()
            ->setMethods(array('getQueueSetForQueue'))
            ->getMock();

        $this->service_manager->mocked_services['PatientTicketing_QueueSet'] = $service;

        $default_queue = new OEModule\PatientTicketing\services\PatientTicketing_Queue(array('id' => 7070707));

        $queueset = new OEModule\PatientTicketing\models\QueueSet();
        $queueset->default_queue = $default_queue;

        $service->expects($this->once())
            ->method('getQueueSetForQueue')
            ->will($this->returnValue($queueset));

        $model = new OEModule\PatientTicketing\models\Ticket();

        $current_queue = new OEModule\PatientTicketing\models\Queue();
        $current_queue_assignment = new OEModule\PatientTicketing\models\TicketQueueAssignment();
        $current_queue_assignment->id = 939393;

        $model->current_queue = $current_queue;
        $model->current_queue_assignment = $current_queue_assignment;

        $assignment = new OEModule\PatientTicketing\models\TicketQueueAssignment();
        $assignment->id = 7070707;
        $assignment->queue_id = 7070707;

        $model->queue_assignments = array($assignment);

        $display_queue_assignment = $model->getDisplayQueueAssignment();

        $this->assertInstanceOf('OEModule\PatientTicketing\models\TicketQueueAssignment', $display_queue_assignment);
        $this->assertEquals(7070707, $display_queue_assignment->id);
    }

    public function getReportProvider()
    {
        return array(
            array('ticket3', 'updated test report'),
            array('ticket4', 'test report'),
        );
    }

    /**
     * @dataProvider getReportProvider
     */
    public function testGetReport($ticket_name, $report)
    {
        $ticket = $this->tickets($ticket_name);
        $this->assertEquals($report, $ticket->getReport(), 'Did not get expected report value for ticket.');
    }
}

class ServiceManagerWrapper2 extends \services\ServiceManager
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
