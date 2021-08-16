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
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class OphCoTherapyapplication_EmailTest extends ActiveRecordTestCase
{
    public static function setupBeforeClass()
    {
        Yii::app()->getModule('OphCoTherapyapplication');
        Yii::app()->session['selected_institution_id'] = 1;
    }

    public $fixtures = array(
        'ep' => 'Episode',
        'ev' => 'Event',
        'email' => 'OphCoTherapyapplication_Email',
    );

    private $event_type;

    public function getModel()
    {
        return OphCoTherapyapplication_Email::model();
    }

    protected array $columns_to_skip = [
        'eye_id'
    ];

    public static function tearDownAfterClass()
    {
        unset(Yii::app()->session['selected_institution_id']);
    }

    public function setUp()
    {
        parent::setUp();

        $this->event_type = EventType::model()->find();
    }

    public function testGetStatusForEvent_Pending()
    {
        $event = $this->createEvent();
        $this->assertEquals(OphCoTherapyapplication_Processor::STATUS_PENDING, OphCoTherapyapplication_Email::model()->getStatusForEvent($event));
    }

    public function testGetStatusForEvent_Sent()
    {
        $event = $this->createEvent();
        $this->createEmail($event);
        $this->assertEquals(OphCoTherapyapplication_Processor::STATUS_SENT, OphCoTherapyapplication_Email::model()->getStatusForEvent($event));
    }

    public function testGetStatusForEvent_Reopened()
    {
        $event = $this->createEvent();
        $email = $this->createEmail($event);
        $email->archived = 1;
        $email->save();
        $this->assertEquals(OphCoTherapyapplication_Processor::STATUS_REOPENED, OphCoTherapyapplication_Email::model()->getStatusForEvent($event));
    }

    public function testGetStatusForEvent_Resent()
    {
        $event = $this->createEvent();
        $email = $this->createEmail($event);
        $email->archived = 1;
        $email->save();
        $this->createEmail($event);
        $this->assertEquals(OphCoTherapyapplication_Processor::STATUS_SENT, OphCoTherapyapplication_Email::model()->getStatusForEvent($event));
    }

    public function testGetStatusForEvent_Reopened_WithSentInDatabase()
    {
        $this->createEmail($this->createEvent());
        $event = $this->createEvent();
        $email = $this->createEmail($event);
        $email->archived = 1;
        $email->save();
        $this->assertEquals(OphCoTherapyapplication_Processor::STATUS_REOPENED, OphCoTherapyapplication_Email::model()->getStatusForEvent($event));
    }

    public function testArchiveForEvent()
    {
        $event = $this->createEvent();
        $em1 = $this->createEmail($event);
        $em2 = $this->createEmail($event);

        OphCoTherapyapplication_Email::model()->archiveForEvent($event);

        $em1->refresh();
        $em2->refresh();

        $this->assertEquals(1, $em1->archived);
        $this->assertEquals(1, $em2->archived);
    }

    public function testArchiveForEvent_OtherEventsUnaffected()
    {
        $ev1 = $this->createEvent();
        $ev1_em1 = $this->createEmail($ev1);
        $ev1_em2 = $this->createEmail($ev1);

        $ev2 = $this->createEvent();
        $ev2_em1 = $this->createEmail($ev2);
        $ev2_em2 = $this->createEmail($ev2);

        OphCoTherapyapplication_Email::model()->archiveForEvent($ev1);

        $ev2_em1->refresh();
        $ev2_em2->refresh();

        $this->assertEquals(0, $ev2_em1->archived);
        $this->assertEquals(0, $ev2_em2->archived);
    }

    /**
     * @return Event
     * @throws Exception
     */
    private function createEvent()
    {
        $event = new Event();
        $event->episode_id = $this->ep('episode1')->id;
        $event->event_type_id = $this->event_type->id;
        $event->institution_id = 1;
        $event->delete_pending = 0;
        $event->save();

        return $event;
    }

    private function createEmail(Event $event)
    {
        $email = new OphCoTherapyapplication_Email();
        $email->event_id = $event->id;
        $email->eye_id = Eye::LEFT;
        $email->save();

        return $email;
    }
}
