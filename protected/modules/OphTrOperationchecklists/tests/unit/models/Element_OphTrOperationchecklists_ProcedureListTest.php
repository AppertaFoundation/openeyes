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

class Element_OphTrOperationchecklists_ProcedureListTest extends ActiveRecordTestCase
{
    /**
     * @var Element_OphTrOperationchecklists_ProcedureList
     */
    protected Element_OphTrOperationchecklists_ProcedureList $model;
    public $fixtures = array(
        'event' => Event::class,
        'element_type' => ElementType::class,
        'event_type' => EventType::class,
        'proc' => Procedure::class,
        'eye' => Eye::class,
        'priority' => Priority::class,
        'disorder' => Disorder::class,
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
        $this->model = new Element_OphTrOperationchecklists_ProcedureList();
    }

    /**
     * @covers Element_OphTrOperationchecklists_ProcedureList
     */
    public function testModel()
    {
        $this->assertEquals('Element_OphTrOperationchecklists_ProcedureList', get_class($this->model), 'Class name should match model');
    }

    /**
     * @covers Element_OphTrOperationchecklists_ProcedureList
     */
    public function testTableName()
    {
        $this->assertEquals('et_ophtroperationchecklists_procedurelist', $this->model->tableName());
    }

    /**
     * @covers Element_OphTrOperationchecklists_ProcedureList
     */
    public function testUpdateProceduresAndAnaestheticType() {
        $procIds = [];
        foreach ($this->proc as $procedure) {
            $procIds[] = $procedure['id'];
        }
        $anaestheticTypeId = [];
        $anaestheticTypeId[] = AnaestheticType::model()->find('code = "Sed"')->id;
        $event = $this->event('event1');
        $this->model->event_id = $event->id;
        $this->model->eye_id = $this->eye['eyeLeft']['id'];
        $this->model->booking_event_id = null;
        $this->model->disorder_id = $this->disorder['disorder1']['id'];
        $this->model->priority_id = $this->priority['priority1']['id'];
        $this->model->procedures = $procIds;
        $this->model->anaesthetic_type_assignments = $anaestheticTypeId;

        if (!$this->model->save()) {
            throw new Exception('Unable to save assignment: ' . var_dump($this->model->getErrors(), true));
        }

        $this->elementIds[] = $this->model->id;

        $this->model->updateProcedures($procIds);
        $this->model->updateAnaestheticType($anaestheticTypeId);

        $criteria = new CDbCriteria;
        $criteria->addInCondition('proc_id', $procIds);
        $relation_models = OphTrOperationchecklists_ProcedurelistProcedureAssignment::model()->findAll($criteria);

        $this->assertEquals(count($procIds), count($relation_models));

        $criteria1 = new CDbCriteria;
        $criteria1->addInCondition('anaesthetic_type_id', $anaestheticTypeId);
        $relation_models1 = OphTrOperationchecklists_AnaestheticAnaestheticType::model()->findAll($criteria1);

        $this->assertEquals(count($anaestheticTypeId), count($relation_models1));
    }

    public function tearDown()
    {
        foreach ($this->elementIds as $id) {
            foreach (OphTrOperationchecklists_ProcedurelistProcedureAssignment::model()->findAll('procedurelist_id = :procedurelist_id', array(':procedurelist_id' => $id)) as $t) {
                $t->noVersion()->delete();
            }
            foreach (OphTrOperationchecklists_AnaestheticAnaestheticType::model()->findAll('procedurelist_id = :procedurelist_id', array(':procedurelist_id' => $id)) as $t2) {
                $t2->noVersion()->delete();
            }
            Element_OphTrOperationchecklists_ProcedureList::model()->noVersion()->deleteByPk($id);
        }
        $this->elementIds = array();
        parent::tearDown();
    }
}
