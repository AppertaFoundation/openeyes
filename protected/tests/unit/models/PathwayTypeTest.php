<?php

/**
 * Class PathwayTypeTest
 * @covers PathwayType
 * @method Pathway pathways($fixtureID)
 * @method PathwayStep steps($fixtureID)
 * @method WorklistPatient patients($fixtureID)
 */
class PathwayTypeTest extends ActiveRecordTestCase
{
    protected $fixtures = array(
        'worklists' => Worklist::class,
        'patients' => WorklistPatient::class,
        'pathways' => Pathway::class,
        'pathway_type_steps' => PathwayTypeStep::class,
        'steps' => PathwayStep::class,
    );

    public function getModel()
    {
        return PathwayType::model();
    }

    /**
     * @throws Exception
     */
    public function testCreateNewPathway(): void
    {
        $new_pathway = Pathway::model()->find('worklist_patient_id = ?', [4]);
        self::assertNull($new_pathway);
        $type = PathwayType::model()->findByPk(1);
        $type->createNewPathway(4);
        $new_pathway = Pathway::model()->find('worklist_patient_id = ?', [4]);

        self::assertNotNull($new_pathway);
    }

    /**
     * @throws Exception
     */
    public function testDuplicateStepsForPathway(): void
    {
        // Test duplication at the end of the queue.
        $type = PathwayType::model()->findByPk(1);
        $pathway = $this->pathways('pathway1');
        $results = $type->duplicateStepsForPathway($pathway->id, 0);
        self::assertNotEmpty($results);

        // Test duplication near the start of the queue.
        $results = $type->duplicateStepsForPathway($pathway->id, 1);
        $pathway->refresh();

        self::assertNotEmpty($results);

        // Ensure that the steps have been correctly reordered after inserting at the front of the queue.
        self::assertEquals(5, $pathway->requested_steps[0]->step_type_id);
        self::assertEquals(10, $pathway->requested_steps[1]->step_type_id);
    }
}
