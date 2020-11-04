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

class Element_OphTrOperationchecklists_NursingAssessmentTest extends ActiveRecordTestCase
{
    /**
     * @var Element_OphTrOperationchecklists_NursingAssessment
     */
    protected Element_OphTrOperationchecklists_NursingAssessment $model;
    public $fixtures = array(
        'event' => Event::class,
        'element_type' => ElementType::class,
        'event_type' => EventType::class,
        'ophtroperationchecklists_questions' => OphTrOperationchecklists_Questions::class,
        'ophtroperationchecklists_answers' => OphTrOperationchecklists_Answers::class,
        'ophtroperationchecklists_question_answer_assignment' => OphTrOperationchecklists_QuestionAnswerAssignment::class,
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
        $this->model = new Element_OphTrOperationchecklists_NursingAssessment();
    }

    /**
     * @covers Element_OphTrOperationchecklists_NursingAssessment
     */
    public function testModel()
    {
        $this->assertEquals('Element_OphTrOperationchecklists_NursingAssessment', get_class($this->model), 'Class name should match model');
    }

    /**
     * @covers Element_OphTrOperationchecklists_NursingAssessment
     */
    public function testTableName()
    {
        $this->assertEquals('et_ophtroperationchecklists_nursing_assessment', $this->model->tableName());
    }

    /**
     * @covers Element_OphTrOperationchecklists_NursingAssessment
     */
    public function testSaveNursingData()
    {
        $event = $this->event('event1');

        $element = new Element_OphTrOperationchecklists_NursingAssessment();
        $element->event_id = $event->id;
        $element->save();

        $elementType = $this->element_type('admission');
        // get the first radio button
        $question = OphTrOperationchecklists_Questions::model()->find('element_type_id = :element_type_id AND type = :type', array(':element_type_id' => $elementType->id, ':type' => 'RADIO'));
        $answerIds = [];
        $questionAnswerAssignments = OphTrOperationchecklists_QuestionAnswerAssignment::model()->findAll('question_id = :question_id', array(':question_id' => $question->id));
        foreach ($questionAnswerAssignments as $questionAnswerAssignment) {
            $answerIds[] = $questionAnswerAssignment->answer_id;
        }
        $randomAnswerId = rand($answerIds[0], $answerIds[sizeof($answerIds)-1]);

        if (isset($question)) {
            $result = new OphTrOperationchecklists_NursingResults();
            $result->setAttributes(array(
                'question_id' => $question->id,
                'answer_id' => $randomAnswerId,
                'answer' => '',
                'comment' => '',
            ), false);

            $this->elementIds[] = $element->id;

            $element->checklistResults = array($result);
            $element->saveNursingData();

            $checklist_results = OphTrOperationchecklists_NursingResults::model()->findAll('element_id = :element_id AND question_id = :question_id', array(':element_id' => $element->id, ':question_id' => $question->id));

            // check the table contains a single row with the correct data.
            $this->assertCount(1, $checklist_results);
        }
    }

    public function tearDown()
    {
        foreach ($this->elementIds as $id) {
            foreach (OphTrOperationchecklists_NursingResults::model()->findAll('element_id = :element_id', array(':element_id' => $id)) as $t) {
                $t->noVersion()->delete();
            }
            Element_OphTrOperationchecklists_NursingAssessment::model()->noVersion()->deleteByPk($id);
        }
        $this->elementIds = array();
        parent::tearDown();
    }
}
