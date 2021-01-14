<?php

namespace OEModule\OphCiExamination\tests\unit\models;

use OEModule\OphCiExamination\models\interfaces\SidedData;
use OEModule\OphCiExamination\models\Synoptophore;
use OEModule\OphCiExamination\models\Synoptophore_ReadingForGaze;
use OEModule\OphCiExamination\tests\traits\InteractsWithSynoptophore;

/**
 * Class SynoptophoreTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\Synoptophore
 * @group sample-data
 * @group strabismus
 * @group synoptophore
 */
class SynoptophoreTest extends \ModelTestCase
{
    use \HasCoreEventElementTests;
    use \HasRelationOptionsToTest;
    use InteractsWithSynoptophore;
    use \WithTransactions;

    protected $element_cls = Synoptophore::class;

    /** @test */
    public function readings_relation()
    {
        $instance = $this->getElementInstance();
        $relations = $instance->relations();

        foreach (['left', 'right'] as $prefix) {
            $relation_name = "{$prefix}_readings";
            $this->assertArrayHasKey($relation_name, $relations);
            $this->assertEquals(\CHasManyRelation::class, $relations[$relation_name][0]);
            $this->assertEquals(Synoptophore_ReadingForGaze::class, $relations[$relation_name][1]);
        }
    }

    /** @test */
    public function attribute_safety()
    {
        $instance = $this->getElementInstance();
        $safe = $instance->getSafeAttributeNames();

        $this->assertContains('event_id', $safe);
        $this->assertContains('comments', $safe);
        $this->assertContains('angle_from_primary', $safe);
        $this->assertContains('right_readings', $safe);
        $this->assertContains('left_readings', $safe);
    }

    public function side_provider()
    {
        return [
            ['right'],
            ['left']
        ];
    }

    /**
     * @test
     * @dataProvider side_provider
     */
    public function at_least_one_reading_required_for_side($side)
    {
        $readings_attr = "{$side}_readings";

        $instance = $this->getElementInstance();
        $instance->{"setHas" . ucfirst($side)}();
        $this->assertAttributeInvalid($instance, $readings_attr, 'cannot be blank');
        $instance->$readings_attr = [$this->createValidatingModelMock(Synoptophore_ReadingForGaze::class)];
        $this->assertTrue($instance->validate([$readings_attr]));
    }

    /**
     * @test
     * @dataProvider side_provider
     */
    public function cannot_have_duplicate_readings_for_gaze($invalid_side)
    {
        $gaze_type = $this->faker->randomElement(Synoptophore_ReadingForGaze::model()->getValidGazeTypes());
        $reading = $this->createValidatingModelMock(Synoptophore_ReadingForGaze::class);
        $reading->gaze_type = $gaze_type;

        $instance = $this->getElementInstance();
        $instance->{"setHas" . ucfirst($invalid_side)}(); // ensure side set

        $instance->{"{$invalid_side}_readings"} = [$reading, $reading];

        $this->assertAttributeInvalid($instance, "{$invalid_side}_readings", "Each gaze type can only be recorded once");
    }

    public function side_for_saving_provider()
    {
        return [
            ['right', SidedData::RIGHT],
            ['left', SidedData::LEFT]
        ];
    }

    /**
     * @test
     * @dataProvider side_for_saving_provider
     * @param $side
     * @param $eye_id
     */
    public function readings_are_saved_correctly($side, $eye_id)
    {
        $instance = $this->getElementInstance();
        $readings_attr = "{$side}_readings";
        $element_data = $this->generateSynoptophoreData();
        $instance->setAttributes($element_data);
        $reading_data = $this->generateSynoptophoreReadingData([
            'eye_id' => $eye_id
        ]);
        $instance->$readings_attr = [$reading_data];
        $instance->eye_id = $eye_id;

        $this->assertTrue($instance->validate(), "Invalid: " . print_r($instance->getErrors(), true));
        $this->assertTrue($instance->save(), "element should save successfully.");
        $savedInstance = Synoptophore::model()->findByPk($instance->getPrimaryKey());

        $this->assertCount(1, $savedInstance->$readings_attr);
        foreach ($reading_data as $attr => $val) {
            $this->assertEquals($val, $savedInstance->$readings_attr[0]->$attr);
        }
        foreach ($element_data as $attr => $val) {
            $this->assertEquals($val, $instance->$attr);
        }
    }

    /** @test */
    public function retrieving_specific_reading_is_null_when_has_no_reading()
    {
        $instance = $this->getElementInstance();

        $this->assertNull($instance->getReadingForSideByGazeType(
            $this->faker->randomElement(['right', 'left']),
            $this->getRandomGazeType()
        ));
    }

    /** @test */
    public function retrieves_specific_reading()
    {
        $instance = $this->getElementInstance();
        $side = $this->faker->randomElement(['right', 'left']);
        $reading = $this->generateSynoptophoreReading();
        $gaze_type = $reading->gaze_type;

        $instance->{"setHas" . ucfirst($side)}();
        $instance->{"{$side}_readings"} = [$reading];

        $this->assertEquals($reading, $instance->getReadingForSideByGazeType($side, $gaze_type));

        $other_gaze_type = $this->getRandomGazeType();
        while ($other_gaze_type === $gaze_type) {
            $other_gaze_type = $this->getRandomGazeType();
        }

        $this->assertNull($instance->getReadingForSideByGazeType($side, $other_gaze_type));
    }
}
