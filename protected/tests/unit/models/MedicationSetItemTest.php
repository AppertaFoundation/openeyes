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
class MedicationSetItemTest extends ActiveRecordTestCase
{
    /**
     * @var MedicationSetItem
     */
    protected $model;
    public $fixtures = array(
        'drugsetitems' => 'MedicationSetItem',
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
        $this->model = new MedicationSetItem();
    }

    /**
     * @covers MedicationSetItem
     */
    public function testModel()
    {
        $this->assertEquals('MedicationSetItem', get_class(MedicationSetItem::model()), 'Class name should match model.');
    }

    /**
     * @covers MedicationSetItem
     */
    public function testTableName()
    {
        $this->assertEquals('medication_set_item', $this->model->tableName());
    }

    /**
     * @covers MedicationSetItem
     * @throws CException
     */
    public function testRules()
    {
        parent::testRules();
        $this->assertTrue($this->drugsetitems('drugsetitem1')->validate());
        $this->assertEmpty($this->drugsetitems('drugsetitem2')->errors);
    }

    /**
     * @covers MedicationSetItem
     */
    public function testSearch()
    {
        $this->model->setAttributes($this->drugsetitems('drugsetitem1')->getAttributes());
        $results = $this->model->search();
        $data = $results->getData();

        $expectedKeys = array('drugsetitem1');
        $expectedResults = array();
        if (!empty($expectedKeys)) {
            foreach ($expectedKeys as $key) {
                $expectedResults[] = $this->drugsetitems($key);
            }
        }
        $this->assertEquals(1, $results->getItemCount());
        $this->assertEquals($expectedResults, $data);
    }
}
