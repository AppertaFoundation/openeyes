<?php
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
class PatientTicketing_TicketServiceTest extends OEDbTestCase
{
    public $fixtures = array(
            'patients' => 'Patient',
            'episodes' => 'Episode',
            'events' => 'Event',
            'queuesetcategorys' => 'OEModule\PatientTicketing\models\QueueSetCategory',
            'queues' => 'OEModule\PatientTicketing\models\Queue',
            'queue_outcomes' => 'OEModule\PatientTicketing\models\QueueOutcome',
            'queuesets' => 'OEModule\PatientTicketing\models\QueueSet',
            'tickets' => 'OEModule\PatientTicketing\models\Ticket',
            'ticketassignments' => 'OEModule\PatientTicketing\models\TicketQueueAssignment',
    );

    public function testgetTicketActionLabel()
    {
        $no_label = $this->getMockBuilder('OEModule\PatientTicketing\models\Ticket')
                ->disableOriginalConstructor()
                ->setMethods(array('is_complete'))
                ->getMock();

        $no_label->expects($this->any())
            ->method('is_complete')
            ->will($this->returnValue(true));

        //$svc = $this->manager->getService('PatientTicketing_Ticket');
        $svc = Yii::app()->service->getService('PatientTicketing_Ticket');

        $this->assertEquals(null, $svc->getTicketActionLabel($no_label));

        $label_queue = ComponentStubGenerator::generate('OEModule\PatientTicketing\models\Queue');

        $label_ticket = $this->getMockBuilder('OEModule\PatientTicketing\models\Ticket')
                ->disableOriginalConstructor()
                ->setMethods(array('is_complete'))
                ->getMock();
        $label_ticket->current_queue = $label_queue;
        $label_ticket->expects($this->any())
            ->method('is_complete')
            ->will($this->returnValue(false));

        // default label when not customised
        $this->assertEquals('Move', $svc->getTicketActionLabel($label_ticket));

        // custom current queue label
        $label_queue->action_label = 'Test Label';

        $this->assertEquals('Test Label', $svc->getTicketActionLabel($label_ticket));
    }

    public function getTicketsForPatientProvider()
    {
        return array(
                array('patient1', array(1), array(1, 3)),
                array('patient2', array(2), array(2)),
                array('patient3', array(), array(4)),

        );
    }

    /**
     * @dataProvider getTicketsForPatientProvider
     */
    public function testgetTicketsForPatient($patient_id, $active_ticket_ids, $all_ticket_ids)
    {
        //$svc = $this->manager->getService('PatientTicketing_Ticket');
        $svc = Yii::app()->service->getService('PatientTicketing_Ticket');

        $patient = $this->patients($patient_id);
        $def_active_tickets = $svc->getTicketsForPatient($patient);
        $active_tickets = $svc->getTicketsForPatient($patient, true);
        $all_tickets = $svc->getTicketsForPatient($patient, false);
        // just map the ids for comparison with expected results
        $test_active_ticket_ids = array_map(function ($t) {return $t->id;
        }, $active_tickets);
        sort($test_active_ticket_ids);
        $test_all_ticket_ids = array_map(function ($t) {return $t->id;
        }, $all_tickets);
        sort($test_all_ticket_ids);

        $this->assertEquals(count($def_active_tickets), count($active_tickets), 'Default method call and active call matches');
        $this->assertEquals(count($active_ticket_ids), count($active_tickets), 'Active tickets count matches expected'.print_r($active_tickets, true));
        foreach ($active_tickets as $i => $ticket) {
            $this->assertEquals($ticket->id, $active_tickets[$i]->id, "Default ticket {$i} matches active call matches");
        }
        foreach ($active_ticket_ids as $i => $id) {
            $this->assertEquals($id, $test_active_ticket_ids[$i], 'All tickets matches expected id');
        }
        foreach ($all_ticket_ids as $i => $id) {
            $this->assertEquals($id, $test_all_ticket_ids[$i], 'All tickets matches expected id');
        }
    }

    public function getCategoryForTicketProvider()
    {
        return array(
            array('ticket1', 'queuesetcategory1'),
            array('ticket2', 'queuesetcategory2'),
        );
    }

    /**
     * @dataProvider getCategoryForTicketProvider
     */
    public function testgetCategoryForTicket($ticket_name, $queuesetcategory_name)
    {
        $svc = Yii::app()->service->getService('PatientTicketing_Ticket');
        $t = $this->tickets($ticket_name);
        $qsc = $this->queuesetcategorys($queuesetcategory_name);

        $this->assertEquals($qsc->id, $svc->getCategoryForTicket($t)->id);
    }

    public function getTicketEpisodeProvider()
    {
        return array(
            array('ticket1', 'episode1'),
            array('ticket2', null),
        );
    }

    /**
     * @dataProvider getTicketEpisodeProvider
     */
    public function testgetTicketEpisode($ticket_name, $episode_name)
    {
        $svc = Yii::app()->service->getService('PatientTicketing_Ticket');
        $ticket = $this->tickets($ticket_name);
        if ($episode_name) {
            $episode = $this->episodes($episode_name);
            $this->assertEquals($episode->id, $svc->getTicketEpisode($ticket)->id);
        } else {
            $this->assertNull($svc->getTicketEpisode($ticket));
        }
    }
}
