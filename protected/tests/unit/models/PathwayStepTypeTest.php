<?php

/**
 * Class PathwayStepTypeTest
 * @covers PathwayStepType
 * @method Pathway pathways($fixtureID)
 * @method PathwayStep steps($fixtureID)
 */
class PathwayStepTypeTest extends ActiveRecordTestCase
{
    protected $fixtures = array(
        'worklists' => Worklist::class,
        'worklist_patients' => WorklistPatient::class,
        'pathways' => Pathway::class,
        'pathway_steps' => PathwayStep::class,
    );

    public static function setUpBeforeClass()
    {
        Yii::app()->session['selected_institution_id'] = 1;
    }

    public static function tearDownAfterClass()
    {
        unset(Yii::app()->session['selected_institution_id']);
    }

    /**
     * @throws Exception
     */
    public function tearDown()
    {
        // Delete all custom step types created by these unit tests.
        parent::tearDown();
        PathwayStepType::model()->deleteAll('`group` IS NULL');
    }

    public function getModel()
    {
        return PathwayStepType::model();
    }

    public function testGetStandardTypes(): void
    {
        $standard_types = PathwayStepType::getStandardTypes();

        self::assertCount(8, $standard_types);
    }

    /**
     * @throws Exception
     */
    public function testCreateNewStepForPathway(): void
    {
        $pathway = $this->pathways('pathway1');
        self::assertCount(1, $pathway->requested_steps);
        $path_step = PathwayStepType::model()->find('short_name = \'fork\'');
        $standard_step = PathwayStepType::model()->find('short_name = \'bio\'');

        $new_step = $standard_step->createNewStepForPathway($pathway->id, []);
        $pathway->refresh();
        self::assertNotNull($new_step);
        self::assertCount(2, $pathway->requested_steps);

        $new_step = $path_step->createNewStepForPathway($pathway->id, []);
        $pathway->refresh();
        self::assertNotNull($new_step);
        self::assertCount(3, $pathway->requested_steps);

        $new_step = $standard_step->createNewStepForPathway(10, []);
        $pathway->refresh();
        self::assertFalse($new_step);
        self::assertCount(3, $pathway->requested_steps);
    }

    public function testGetPathTypes(): void
    {
        $standard_types = PathwayStepType::getPathTypes();

        self::assertCount(4, $standard_types);
    }

    /**
     * @throws Exception
     */
    public function testGetCustomTypes(): void
    {
        $custom_types = PathwayStepType::getCustomTypes();

        self::assertCount(0, $custom_types);

        // Create a custom step type and ensure the count increases.
        $new_custom_type = new PathwayStepType();
        $new_custom_type->default_state = PathwayStep::STEP_REQUESTED;
        $new_custom_type->type = 'process';
        $new_custom_type->user_can_create = true;
        $new_custom_type->short_name = 'New';
        $new_custom_type->long_name = 'New custom step';

        $new_custom_type->save();
        $new_custom_type->createMapping(ReferenceData::LEVEL_INSTITUTION, Yii::app()->session['selected_institution_id']);

        $custom_types = PathwayStepType::getCustomTypes();

        self::assertCount(1, $custom_types);

        $new_custom_type->deleteMapping(ReferenceData::LEVEL_INSTITUTION, Yii::app()->session['selected_institution_id']);
        $new_custom_type->delete();
        $custom_types = PathwayStepType::getCustomTypes();
        self::assertCount(0, $custom_types);
    }
}
