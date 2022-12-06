<?php
/**
 * (C) Copyright Apperta Foundation 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class Element_OphTrOperationchecklists_NoteTest extends ActiveRecordTestCase
{
    /**
     * @var Element_OphTrOperationchecklists_Note
     */
    protected Element_OphTrOperationchecklists_Note $model;
    public $fixtures = array(
        'event' => Event::class,
        'element_type' => ElementType::class,
        'event_type' => EventType::class
    );
    public $elementIds = array();

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
        $this->model = new Element_OphTrOperationchecklists_Note();
    }

    /**
     * @covers Element_OphTrOperationchecklists_Note
     */
    public function testModel()
    {
        $this->assertEquals('Element_OphTrOperationchecklists_Note', get_class($this->model), 'Class name should match model');
    }

    /**
     * @covers Element_OphTrOperationchecklists_Note
     */
    public function testTableName()
    {
        $this->assertEquals('et_ophtroperationchecklists_note', $this->model->tableName());
    }

    /**
     * @covers Element_OphTrOperationchecklists_Note
     */
    public function testSaveCaseNote()
    {
        $event = $this->event('event1');

        $element = new Element_OphTrOperationchecklists_Note();
        $element->event_id = $event->id;
        $element->save();

        $this->elementIds[] = $element->id;

        $element->saveNote('Test1');
        $element->saveNote('Test2');

        $case_notes = OphTrOperationchecklists_Notes::model()->findAll('element_id = :element_id', array(':element_id' => $element->id));

        // check the table contains a single row with the correct data.
        $this->assertCount(2, $case_notes);
    }

    public function tearDown(): void
    {
        foreach ($this->elementIds as $id) {
            foreach (OphTrOperationchecklists_Notes::model()->findAll('element_id = :element_id', array(':element_id' => $id)) as $t) {
                $t->noVersion()->delete();
            }
            Element_OphTrOperationchecklists_Note::model()->noVersion()->deleteByPk($id);
        }
        $this->elementIds = array();

        parent::tearDown();
    }
}
