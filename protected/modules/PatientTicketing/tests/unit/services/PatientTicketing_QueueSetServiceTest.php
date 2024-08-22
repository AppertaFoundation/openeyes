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
class PatientTicketing_QueueSetService extends OEDbTestCase
{
    public $fixtures = array(
        'patients' => 'Patient',
        'queues' => 'OEModule\PatientTicketing\models\Queue',
        'queue_outcomes' => 'OEModule\PatientTicketing\models\QueueOutcome',
        'queuesets' => 'OEModule\PatientTicketing\models\QueueSet',
        'queuesetcategories' => 'OEModule\PatientTicketing\models\QueueSetCategory',
        'tickets' => 'OEModule\PatientTicketing\models\Ticket',
        'ticketassignments' => 'OEModule\PatientTicketing\models\TicketQueueAssignment',
    );

    public function testgetQueueSetsForFirm()
    {
        $service = $this->getMockBuilder('OEModule\PatientTicketing\services\PatientTicketing_QueueSetService')
            ->disableOriginalConstructor()
            ->setMethods(array('modelToResource'))
            ->getMock();

        $service->expects($this->at(0))
            ->method('modelToResource')
            ->with($this->queuesets('queueset1'));

        $service->expects($this->at(1))
            ->method('modelToResource')
            ->with($this->queuesets('queueset2'));

        $firm = new \Firm();

        $res = $service->getQueueSetsForFirm($firm);
        $this->assertEquals(2, count($res));
    }

    public function canAddPatientProvider()
    {
        return array(
            array('queueset1', 'patient1', false, 'Patient is active on queueset so should not be add-able'),
            array('queueset2', 'patient1', true, 'Patient has no ticket in queueset, so should be add-able'),
            array('queueset2', 'patient3', true, 'Patient is complete on queueset, so should be add-able'),
        );
    }

    /**
     * @dataProvider canAddPatientProvider
     */
    public function testcanAddPatientToQueueSet($qs_name, $patient_name, $res, $msg)
    {
        $queueset = $this->queuesets($qs_name);
        $service = Yii::app()->service->getService('PatientTicketing_QueueSet');
        $this->assertEquals($res, $service->canAddPatientToQueueSet($this->patients($patient_name), $queueset->id), $msg);
    }

    public function testGetFilterSettingsAllOn()
    {
        $service = Yii::app()->service->getService('PatientTicketing_QueueSet');
        $this->assertEquals($service->read(1)->filter_priority, 1);
        $this->assertEquals($service->read(1)->filter_subspecialty, 1);
        $this->assertEquals($service->read(1)->filter_firm, 1);
        $this->assertEquals($service->read(1)->filter_my_tickets, 1);
        $this->assertEquals($service->read(1)->filter_closed_tickets, 1);
    }

    public function testGetFilterSettingsAllOff()
    {
        $service = Yii::app()->service->getService('PatientTicketing_QueueSet');
        $this->assertEquals($service->read(2)->filter_priority, 0);
        $this->assertEquals($service->read(2)->filter_subspecialty, 0);
        $this->assertEquals($service->read(2)->filter_firm, 0);
        $this->assertEquals($service->read(2)->filter_my_tickets, 0);
        $this->assertEquals($service->read(2)->filter_closed_tickets, 0);
    }
}
