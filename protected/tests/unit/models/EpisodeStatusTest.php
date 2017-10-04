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
class EpisodeStatusTest extends CDbTestCase
{
    /**
     * @var EpisodeStatus
     */
    protected $model;
    public $fixtures = array(
        'episodestatus' => 'EpisodeStatus',
    );

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->model = new EpisodeStatus();
    }

    /**
     * @covers EpisodeStatus::model
     */
    public function testModel()
    {
        $this->assertEquals('EpisodeStatus', get_class(EpisodeStatus::model()), 'Class name should match model.');
    }

    /**
     * @covers EpisodeStatus::tableName
     */
    public function testTableName()
    {
        $this->assertEquals('episode_status', $this->model->tableName());
    }

    /**
     * @covers EpisodeStatus::rules
     */
    public function testRules()
    {
        $this->assertTrue($this->episodestatus('episodestatus1')->validate());
        $this->assertEmpty($this->episodestatus('episodestatus1')->errors);
    }

    /**
     * @covers EpisodeStatus::attributeLabels
     */
    public function testAttributeLabels()
    {
        $expected = array(
            'id' => 'ID',
            'name' => 'Name',
            'order' => 'Order',
        );

        $this->assertEquals($expected, $this->model->attributeLabels());
    }

    /**
     * @covers EpisodeStatus::search
     */
    public function testSearch()
    {
        $this->model->setAttributes($this->episodestatus('episodestatus1')->getAttributes());
        $this->model->setAttribute('id', 1);
        $results = $this->model->search();
        $data = $results->getData();

        $expectedKeys = array('episodestatus1');
        $expectedResults = array();
        if (!empty($expectedKeys)) {
            foreach ($expectedKeys as $key) {
                $expectedResults[] = $this->episodestatus($key);
            }
        }
        $this->assertEquals(1, $results->getItemCount());
        $this->assertEquals($expectedResults, $data);
    }

    /**
     * @covers EpisodeStatus::getList
     */
    public function testGetList()
    {
        $expected = array(
            1 => 'New',
            2 => 'Under investigation',
            3 => 'Listed/booked',
            4 => 'Post-op',
            5 => 'Follow-up',
            6 => 'Discharged',
        );

        $result = $this->model->getList();

        $this->assertEquals($expected, $result);
    }
}
