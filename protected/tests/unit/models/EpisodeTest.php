<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class EpisodeTest extends ActiveRecordTestCase
{
    /**
     * @var Episode
     */
    protected $model;

    public $fixtures = array(
        'episode' => 'Episode',
        'event' => 'Event',
    );

    public function getModel()
    {
        return $this->model;
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->model = new Episode();
    }

    /**
     * @covers Episode
     */
    public function testModel()
    {
        $this->assertEquals('Episode', get_class(Episode::model()), 'Class name should match model.');
    }

    /**
     * @covers Episode
     *
     */
    public function testRelations()
    {
        //test events relation sorting
        $events = $this->episode('episode2')->events;
        $events[0]->save();//only when saving the event date is set to created_date when null
        $this->assertNotNull($events[0]->event_date);
        $this->assertLessThanOrEqual(
            $events[1]->event_date,
            $events[0]->event_date,
            'EventDate 0 : '.$events[0]->event_date.' > EventDate 1 :'.$events[1]->event_date
        );
    }

    /**
     * @covers Episode
     */
    public function testTableName()
    {
        $this->assertEquals('episode', $this->model->tableName());
    }

    /**
     * @covers Episode
     */
    public function testAttributeLabels()
    {
        $expected = array(
            'id' => 'ID',
            'patient_id' => 'Patient',
            'firm_id' => 'Firm',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'episode_status_id' => 'Current Status',
        );

        $this->assertEquals($expected, $this->model->attributeLabels());
    }


}
