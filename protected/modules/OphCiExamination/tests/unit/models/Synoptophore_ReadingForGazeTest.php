<?php


namespace OEModule\OphCiExamination\tests\unit\models;

use OEModule\OphCiExamination\models\interfaces\SidedData;
use OEModule\OphCiExamination\models\Synoptophore;
use OEModule\OphCiExamination\models\Synoptophore_ReadingForGaze;
use OEModule\OphCiExamination\models\Synoptophore_Direction;
use OEModule\OphCiExamination\models\Synoptophore_Deviation;
use OEModule\OphCiExamination\tests\traits\InteractsWithSynoptophore;

/**
 * Class Synoptophore_ReadingForGazeTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\Synoptophore_ReadingForGaze
 * @group sample-data
 * @group strabismus
 * @group synoptophore
 */
class Synoptophore_ReadingForGazeTest extends \ModelTestCase
{
    use \HasStandardRelationsTests;
    use \HasRelationOptionsToTest;
    use InteractsWithSynoptophore;
    use \WithFaker;
    use \WithTransactions;

    protected $element_cls = Synoptophore_ReadingForGaze::class;

    /** @test */
    public function relations_defined()
    {
        $instance = $this->getElementInstance();

        $this->assertArrayHasKey('direction', $instance->relations());
        $this->assertArrayHasKey('deviation', $instance->relations());
    }

    /** @test */
    public function direction_rules_defined()
    {
        $instance = $this->getElementInstance();

        $this->assertRelationRuleDefined($instance, 'direction_id', Synoptophore_Direction::class);
        $this->assertContains('direction_id', $instance->getSafeAttributeNames());
    }

    /** @test */
    public function direction_options()
    {
        $instance = $this->getElementInstance();

        $this->assertOptionsAreRetrievable($instance, 'direction', Synoptophore_Direction::class);
    }

    /** @test */
    public function deviation_rules_defined()
    {
        $instance = $this->getElementInstance();

        $this->assertRelationRuleDefined($instance, 'deviation_id', Synoptophore_Deviation::class);
        $this->assertContains('deviation_id', $instance->getSafeAttributeNames());
    }

    /** @test */
    public function deviation_options()
    {
        $instance = $this->getElementInstance();

        $this->assertOptionsAreRetrievable($instance, 'deviation', Synoptophore_Deviation::class);
    }

    public function entry_validation_provider()
    {

        // gaze type and eye_id and one meaningful value required for an entry
        return [
            [
                [
                    'direction_id',
                ],
                false
            ],
            [
                [
                    'deviation_id',
                ],
                false
            ],
            [
                [
                    'horizontal_angle',
                ],
                false
            ],
            [
                [
                    'vertical_power',
                ],
                false
            ],
            [
                [
                    'torsion',
                ],
                false
            ],
            [
                [
                    'gaze_type',
                ],
                false
            ],
            [
                [
                    'eye_id',
                ],
                false
            ],
            [
                [
                    'direction_id',
                    'gaze_type'
                ],
                false
            ],
            [
                [
                    'deviation_id',
                    'gaze_type'
                ],
                false
            ],
            [
                [
                    'horizontal_angle',
                    'gaze_type'
                ],
                false
            ],
            [
                [
                    'vertical_power',
                    'gaze_type'
                ],
                false
            ],
            [
                [
                    'torsion',
                    'gaze_type'
                ],
                false
            ],
            [
                [
                    'gaze_type',
                    'eye_id'
                ],
                false
            ],
            [
                [
                    'direction_id',
                    'gaze_type',
                    'eye_id'
                ],
                true
            ],
            [
                [
                    'deviation_id',
                    'gaze_type',
                    'eye_id'
                ],
                true
            ],
            [
                [
                    'horizontal_angle',
                    'gaze_type',
                    'eye_id'
                ],
                true
            ],
            [
                [
                    'vertical_power',
                    'gaze_type',
                    'eye_id'
                ],
                true
            ],
            [
                [
                    'torsion',
                    'gaze_type',
                    'eye_id'
                ],
                true
            ],
        ];
    }

    /**
     * @param $attrs
     * @param $expected
     * @test
     * @dataProvider entry_validation_provider
     */
    public function entry_is_only_valid_when_all_required_attributes_set($attrs, $expected)
    {
        $instance = $this->getElementInstance();

        $data = $this->generateSynoptophoreReadingData();

        foreach ($attrs as $attr) {
            $instance->$attr = $data[$attr];
        }

        $this->assertEquals($expected, $instance->validate());
    }

    public function side_provider()
    {
        return [
            ['right', SidedData::RIGHT],
            ['left', SidedData::LEFT]
        ];
    }

    /**
     * @param $side
     * @param $eye_id
     * @test
     * @dataProvider side_provider
     */
    public function reading_to_string($side, $eye_id)
    {
        $direction = $this->getRandomLookup(Synoptophore_Direction::class);
        $deviation = $this->getRandomLookup(Synoptophore_Deviation::class);
        $data = $this->generateSynoptophoreReadingData([
            'direction_id' => $direction->id,
            'deviation_id' => $deviation->id,
            'eye_id' => $eye_id
        ]);

        $instance = $this->getElementInstance();


        $instance->setAttributes($data);

        $savedInstance = $this->saveEntry($instance, $side, $eye_id);
        $expected = "+" . $data['horizontal_angle'] . "° " . $data['vertical_power'] . "Δ " .
            $direction->name . " " . $data['torsion'] . " " . $deviation->abbreviation;

        $this->assertEquals($expected, (string) $savedInstance); // explicit type casting may not be nesc, but added for readability
    }

    protected function saveEntry(Synoptophore_ReadingForGaze $instance, $side, $eye_id)
    {
        $sided_attr = "{$side}_readings";

        $element = new Synoptophore();
        $element->setAttributes($this->generateSynoptophoreData());
        $element->$sided_attr = [$instance];
        $element->eye_id = $eye_id;

        $this->saveElement($element);

        return Synoptophore_ReadingForGaze::model()->findByPk($instance->getPrimaryKey());
    }
}
