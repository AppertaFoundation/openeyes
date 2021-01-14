<?php


namespace OEModule\OphCiExamination\tests\unit\models;

use OEModule\OphCiExamination\models\CorrectionType;
use OEModule\OphCiExamination\models\CoverAndPrismCover;
use OEModule\OphCiExamination\models\CoverAndPrismCover_Entry;
use OEModule\OphCiExamination\models\CoverAndPrismCover_Distance;
use OEModule\OphCiExamination\models\CoverAndPrismCover_VerticalPrism;
use OEModule\OphCiExamination\models\CoverAndPrismCover_HorizontalPrism;
use OEModule\OphCiExamination\models\traits\HasWithHeadPosture as HasWithHeadPostureTrait;
use OEModule\OphCiExamination\tests\traits\InteractsWithCoverAndPrismCover;
use OEModule\OphCiExamination\tests\unit\models\testingtraits\HasCorrectionTypeAttributeToTest;
use OEModule\OphCiExamination\tests\unit\models\testingtraits\HasWithHeadPostureAttributesToTest;

/**
 * Class CoverAndPrismCover_EntryTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\CoverAndPrismCover_Entry
 * @group sample-data
 * @group strabismus
 * @group cover-test
 */
class CoverAndPrismCover_EntryTest extends \ModelTestCase
{
    use \HasStandardRelationsTests;
    use \HasRelationOptionsToTest;
    use InteractsWithCoverAndPrismCover;
    use \WithTransactions;

    use HasWithHeadPostureAttributesToTest;
    use HasCorrectionTypeAttributeToTest;

    protected $element_cls = CoverAndPrismCover_Entry::class;

    /** @test */
    public function cover_and_prism_cover_relations_defined()
    {
        $instance = $this->getElementInstance();

        $this->assertArrayHasKey('horizontal_prism', $instance->relations());
        $this->assertArrayHasKey('vertical_prism', $instance->relations());
    }

    /** @test */
    public function cover_and_prism_cover_horizontal_prism_rules_defined()
    {
        $instance = $this->getElementInstance();

        $this->assertRelationRuleDefined($instance, 'horizontal_prism_id', CoverAndPrismCover_HorizontalPrism::class);
        $this->assertContains('horizontal_prism_id', $instance->getSafeAttributeNames());
    }

    /** @test */
    public function cover_and_prism_cover_horizontal_prism_options()
    {
        $instance = $this->getElementInstance();

        $this->assertOptionsAreRetrievable($instance, 'horizontal_prism', CoverAndPrismCover_HorizontalPrism::class);
    }

    /** @test */
    public function cover_and_prism_cover_horizontal_prism_value_rules_defined()
    {
        $instance = $this->getElementInstance();

        $this->assertContains('horizontal_value', $instance->getSafeAttributeNames());
    }

    /** @test */
    public function cover_and_prism_cover_vertical_prism_rules_defined()
    {
        $instance = $this->getElementInstance();

        $this->assertRelationRuleDefined($instance, 'vertical_prism_id', CoverAndPrismCover_VerticalPrism::class);
        $this->assertContains('vertical_prism_id', $instance->getSafeAttributeNames());
    }

    /** @test */
    public function cover_and_prism_cover_vertical_prism_options()
    {
        $instance = $this->getElementInstance();

        $this->assertOptionsAreRetrievable($instance, 'vertical_prism', CoverAndPrismCover_VerticalPrism::class);
    }

    /** @test */
    public function cover_and_prism_cover_vertical_prism_value_rules_defined()
    {
        $instance = $this->getElementInstance();

        $this->assertContains('vertical_value', $instance->getSafeAttributeNames());
    }

    /** @test */
    public function cover_and_prism_cover_distance_rules_defined()
    {
        $instance = $this->getElementInstance();

        $this->assertRelationRuleDefined($instance, 'distance_id', CoverAndPrismCover_Distance::class);
        $this->assertContains('distance_id', $instance->getSafeAttributeNames());
    }

    /** @test */
    public function cover_and_prism_cover_distance_options()
    {
        $instance = $this->getElementInstance();

        $this->assertOptionsAreRetrievable($instance, 'distance', CoverAndPrismCover_Distance::class);
    }

    /** @test */
    public function cover_and_prism_cover_vertical_value_min_validation()
    {
        $instance = $this->getElementInstance();
        $instance->vertical_prism_id = $this->generateCoverAndPrismCoverEntryData()['vertical_prism_id'];
        $this->assertMinValidation($instance, 'vertical_value', 0);
    }

    /** @test */
    public function cover_and_prism_cover_vertical_value_max_validation()
    {
        $instance = $this->getElementInstance();
        $instance->vertical_prism_id = $this->generateCoverAndPrismCoverEntryData()['vertical_prism_id'];
        $this->assertMaxValidation($instance, 'vertical_value', 50);
    }

    /** @test */
    public function cover_and_prism_cover_horizontal_value_min_validation()
    {
        $instance = $this->getElementInstance();
        $instance->horizontal_prism_id = $this->generateCoverAndPrismCoverEntryData()['horizontal_prism_id'];
        $this->assertMinValidation($instance, 'horizontal_value', 0);
    }

    /** @test */
    public function cover_and_prism_cover_horizontal_value_max_validation()
    {
        $instance = $this->getElementInstance();
        $instance->horizontal_prism_id = $this->generateCoverAndPrismCoverEntryData()['horizontal_prism_id'];
        $this->assertMaxValidation($instance, 'horizontal_value', 90);
    }

    public function entry_validation_provider()
    {
        // distance, correction, and one of horizontal, vertical or comment values required
        return [
            [
                [
                    'vertical_prism_id'
                ],
                false
            ],
            [
                [
                    'vertical_value'
                ],
                false
            ],
            [
                [
                    'vertical_prism_id',
                    'vertical_value'
                ],
                false
            ],
            [
                [
                    'distance_id',
                    'correctiontype_id',
                    'vertical_prism_id',
                    'vertical_value'
                ],
                true
            ],
            [
                [
                    'horizontal_prism_id'
                ],
                false
            ],
            [
                [
                    'horizontal_value'
                ],
                false
            ],
            [
                [
                    'horizontal_prism_id',
                    'horizontal_value'
                ],
                false
            ],
            [
                [
                    'distance_id',
                    'correctiontype_id',
                    'horizontal_prism_id',
                    'horizontal_value'
                ],
                true
            ],
            [
                [
                    'comments'
                ],
                false
            ],
            [
                [
                    'distance_id',
                    'correctiontype_id',
                    'comments'
                ],
                true
            ],
            [
                [
                    'vertical_prism_id',
                    'vertical_value',
                    'horizontal_prism_id',
                    'horizontal_value',
                ],
                false
            ],
            [
                [
                    'vertical_prism_id',
                    'vertical_value',
                    'horizontal_prism_id',
                    'horizontal_value',
                    'correctiontype_id',
                    'distance_id',
                    'with_head_posture',
                    'comments'
                ],
                true
            ]
        ];
    }

    /**
     * @param $attrs
     * @param $expected
     * @test
     * @dataProvider entry_validation_provider
     */
    public function entry_validation($attrs, $expected)
    {
        $data = $this->generateCoverAndPrismCoverEntryData();

        $instance = $this->getElementInstance();
        foreach ($attrs as $attr) {
            $instance->$attr = $data[$attr];
        }
        $this->assertEquals($expected, $instance->validate(), $expected ? "Should be valid" . print_r($instance->getErrors(), true) : "Should NOT be valid");
    }

    public function direction_provider()
    {
        return [
            ['horizontal'],
            ['vertical']
        ];
    }

    /**
     * @test
     * @dataProvider direction_provider
     */
    public function power_value_required_with_prism($direction)
    {
        $data = $this->generateCoverAndPrismCoverEntryData();

        $instance = $this->getElementInstance();
        $instance->comments = $data['comments'];
        $instance->{"{$direction}_prism_id"} = $data["{$direction}_prism_id"];
        $this->assertAttributeInvalid($instance, "{$direction}_value", 'required');
    }

    /**
     * @test
     * @dataProvider direction_provider
     */
    public function prism_required_with_power_value($direction)
    {
        $data = $this->generateCoverAndPrismCoverEntryData();

        $instance = $this->getElementInstance();
        $instance->comments = $data['comments'];
        $instance->{"{$direction}_value"} = $data["{$direction}_value"];
        $this->assertAttributeInvalid($instance, "{$direction}_prism_id", 'required');
    }

    /**
     * Overriding standard validation provider (in trait) due to element requirement
     * differences with base behavior,
     * in this instance the third test should validate as false
     */
    public function head_posture_validation_provider()
    {
        return [
            [HasWithHeadPostureTrait::$WITH_HEAD_POSTURE, true],
            [HasWithHeadPostureTrait::$WITHOUT_HEAD_POSTURE, true],
            ['', true],
            ['foo', false]
        ];
    }

    public function letter_string_provider()
    {
        $distance = $this->getRandomLookup(CoverAndPrismCover_Distance::class);
        $correction_type = $this->getRandomLookup(CorrectionType::class);
        $horizontal_prism = $this->getRandomLookup(CoverAndPrismCover_HorizontalPrism::class);
        $horizontal_value = random_int(0, 90);
        $vertical_prism = $this->getRandomLookup(CoverAndPrismCover_VerticalPrism::class);
        $vertical_value = random_int(0, 50);

        return [
            [
                // BARE MIN TO PASS
                [
                    'distance_id' => $distance->getPrimaryKey(),
                    'correctiontype_id' => $correction_type->getPrimaryKey(),
                    'comments' => 'a small comment'
                ],
                $distance->name . ", " . $correction_type->name . ", a small comment"
            ],
            [
                // FULL ENTRY
                [
                    'distance_id' => $distance->getPrimaryKey(),
                    'correctiontype_id' => $correction_type->getPrimaryKey(),
                    'horizontal_prism_id' => $horizontal_prism->getPrimaryKey(),
                    'horizontal_value' => $horizontal_value,
                    'vertical_prism_id' => $vertical_prism->getPrimaryKey(),
                    'vertical_value' => $vertical_value,
                    'with_head_posture' => CoverAndPrismCover_Entry::$WITHOUT_HEAD_POSTURE,
                    'comments' => "I am an entry comment"
                ],
                $distance->name . ", " . $correction_type->name . ", CHP: " . CoverAndPrismCover_Entry::$DISPLAY_WITHOUT_HEAD_POSTURE . ", " .
                "I am an entry comment, " . $horizontal_value . " Δ " . $horizontal_prism->name . ", " . $vertical_value . " Δ " .
                $vertical_prism->name
            ]
        ];
    }

    /**
     * @param $value
     * @param $expected
     * @test
     * @dataProvider letter_string_provider
     */
    public function convert_to_string($attrs, $expected)
    {
        $instance = $this->getElementInstance();
        $instance->setAttributes($attrs);
        $savedInstance = $this->saveEntry($instance);
        $this->assertEquals($expected, (string)$savedInstance); // explicit type casting may not be nesc, but added for readability
    }

    protected function saveEntry(CoverAndPrismCover_Entry $instance)
    {
        $element = new CoverAndPrismCover();
        $element->entries = [$instance];
        $this->saveElement($element);
        return CoverAndPrismCover_Entry::model()->findByPk($instance->getPrimaryKey());
    }
}