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
use OEModule\PatientTicketing\models;

class QueueTest extends \ActiveRecordTestCase
{
    public $fixtures = array(
            'queues' => 'OEModule\PatientTicketing\models\Queue',
            'queue_outcomes' => 'OEModule\PatientTicketing\models\QueueOutcome',
            'queuesets' => 'OEModule\PatientTicketing\models\QueueSet',
    );

    public function getModel()
    {
        return models\Queue::model();
    }

    protected array $columns_to_skip = [
        'is_initial'
    ];

    public function dependentQueueIdsProvider()
    {
        return array(
            array(1, array(2, 6, 7, 8, 9, 11)),
            array(2, array()),
            array(6, array(7, 8, 9, 11)),
            array(5, array()),
            array(10, array()),
            array(7, array(8)),
        );
    }

    /**
     * @dataProvider dependentQueueIdsProvider
     */
    public function testgetDependentQueueIds($id, $res)
    {
        $test = models\Queue::model()->findByPk($id);
        $output = $test->getDependentQueueIds();
        sort($output);
        $this->assertEquals($res, $output);
    }

    public function rootQueueProvider()
    {
        return array(
            array(6, 1),
            array(3, array(5, 1)),
            array(8, 1),
            array(10, 10),
            array(4, array(5, 1)),
            array(7, 1),
            array(11, 1),
        );
    }

    /**
     * @dataProvider rootQueueProvider
     */
    public function testgetRootQueue($id, $res)
    {
        $test = models\Queue::model()->findByPk($id);
        $output = $test->getRootQueue();

        if (is_array($res)) {
            $this->assertTrue(is_array($output), 'array output expected for multiple queue roots.');
            $this->assertEquals(count($res), count($output));
            foreach ($output as $q) {
                $this->assertInstanceOf('OEModule\PatientTicketing\models\Queue', $q);
                $this->assertTrue(in_array($q->id, $res));
            }
        } else {
            $this->assertInstanceOf('OEModule\PatientTicketing\models\Queue', $output);
            $this->assertEquals($res, $output->id);
        }
    }

    public function queueSetProvider()
    {
        return array(
            array(1, 1),
            array(6, 1),
            array(12, 2),
            array(13, 2),
        );
    }

    /**
     * @dataProvider queueSetProvider
     */
    public function testgetQueueSet($id, $res)
    {
        $test = models\Queue::model()->findByPk($id);
        $qs = $test->getQueueSet();
        $this->assertEquals($res, $qs->id, 'Incorrect QueueSet returned');
    }

    public function testGetRelatedEventTypes()
    {
        $queues = array();

        $queue1 = new models\Queue();
        $queue1->id = 1;
        $queue2 = new models\Queue();
        $queue2->id = 2;
        $queue3 = new models\Queue();
        $queue3->id = 3;

        $queues = array($queue1, $queue2, $queue3);

        $event_types = array();

        foreach ($queues as $i => $queue) {
            $event_types[$queue->id] = array();
            $queue_event_types = array();

            foreach (EventType::model()->findAll(array('order' => 'id asc')) as $event_type) {
                if (rand(0, 1) == 0) {
                    $event_types[$queue->id][] = array(
                        'name' => $event_type->name,
                        'class_name' => $event_type->class_name,
                    );
                    $queue_event_types[] = $event_type;
                }
            }

            $queues[$i]->auto_update_relations = false;
            $queues[$i]->event_types = $queue_event_types;
        }

        $queue = new models\Queue();
        $queue->auto_update_relations = false;
        $queue->outcome_queues = $queues;

        $this->assertEquals($event_types, $queue->getRelatedEventTypes(false));
    }

    public function assignmentFieldValidateProvider()
    {
        return array(
            array('[{"type":"widget"}]', false, array('ID required for assignment field 1')),
            array('[{"id": "test_widget", "type":"widget"}]', false, array('Widget Name missing for test_widget')),
            array('[{"id": "test_widget", "type":"widget", "widget_name": "missing_widget"}]', false, array('Widget with name missing_widget for test_widget not defined')),
            array('[{"id": "test_widget", "type":"widget", "widget_name": "BaseTicketAssignment"}]', true, null),
        );
    }

    /**
     * @dataProvider assignmentFieldValidateProvider
     */
    public function testAssignmentFieldValidate($ass_fields, $valid, $messages)
    {
        $model = new models\Queue();
        $model->name = 'Test Queue';
        $model->assignment_fields = $ass_fields;
        $res = $model->validate();
        $this->assertEquals($valid, $res, 'Unexpected validation response');
        if ($messages) {
            $errs = $model->getErrors('assignment_fields');
            $this->assertEquals(count($errs), count($messages), 'Error message count not matching expectation');
            foreach ($messages as $i => $msg) {
                $this->assertEquals($msg, $errs[$i]);
            }
        }
    }
}
