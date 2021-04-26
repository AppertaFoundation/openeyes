<?php

namespace OEModule\OphCiExamination\tests\unit\models;

use OEModule\OphCiExamination\models\CorrectionType;
use OEModule\OphCiExamination\models\CoverAndPrismCover;
use OEModule\OphCiExamination\models\CoverAndPrismCover_Entry;
use OEModule\OphCiExamination\models\CoverAndPrismCover_HorizontalPrism;
use OEModule\OphCiExamination\models\CoverAndPrismCover_Distance;
use OEModule\OphCiExamination\models\CoverAndPrismCover_VerticalPrism;
use OEModule\OphCiExamination\tests\traits\InteractsWithCoverAndPrismCover;
use OEModule\OphCiExamination\tests\unit\models\testingtraits\HasWithHeadPostureEntriesToTest;

/**
 * Class CoverAndPrismCoverTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\CoverAndPrismCover
 * @group sample-data
 * @group strabismus
 * @group cover-test
 */
class CoverAndPrismCoverTest extends \ModelTestCase
{
    use \HasCoreEventElementTests;
    use \HasRelationOptionsToTest;
    use HasWithHeadPostureEntriesToTest;
    use InteractsWithCoverAndPrismCover;

    protected $element_cls = CoverAndPrismCover::class;

    /** @test */
    public function entries_relation()
    {
        $instance = $this->getElementInstance();
        $relations = $instance->relations();

        $this->assertArrayHasKey('entries', $relations);
        $this->assertEquals(\CHasManyRelation::class, $relations['entries'][0]);
        $this->assertEquals(CoverAndPrismCover_Entry::class, $relations['entries'][1]);
    }

    /** @test */
    public function attribute_safety()
    {
        $instance = $this->getElementInstance();
        $safe = $instance->getSafeAttributeNames();

        $this->assertContains('event_id', $safe);
        $this->assertContains('entries', $safe);
    }

    public function letter_string_provider()
    {
        $distance = $this->getRandomLookup(CoverAndPrismCover_Distance::class);
        $distance2 = $this->getRandomLookup(CoverAndPrismCover_Distance::class);
        $correctiontype = $this->getRandomLookup(CorrectionType::class);
        $correctiontype2 = $this->getRandomLookup(CorrectionType::class);
        $horizontal_prism = $this->getRandomLookup(CoverAndPrismCover_HorizontalPrism::class);
        $horizontal_value = random_int(0, 45);
        $horizontal_prism2 = $this->getRandomLookup(CoverAndPrismCover_HorizontalPrism::class);
        $horizontal_value2 = random_int(46, 90);
        $vertical_prism = $this->getRandomLookup(CoverAndPrismCover_VerticalPrism::class);
        $vertical_value = random_int(0, 25);
        $vertical_prism2 = $this->getRandomLookup(CoverAndPrismCover_VerticalPrism::class);
        $vertical_value2 = random_int(26, 50);

        return [
            [
                // BARE MIN TO PASS
                [
                    [
                        'horizontal_prism_id' => $horizontal_prism->getPrimaryKey(),
                        'horizontal_value' => $horizontal_value,
                        'vertical_prism_id' => $vertical_prism->getPrimaryKey(),
                        'vertical_value' => $vertical_value,
                        'with_head_posture' => CoverAndPrismCover_Entry::$WITH_HEAD_POSTURE
                    ],
                ],
                null,
                [
                    $horizontal_prism->name,
                    $horizontal_value,
                    $vertical_prism->name,
                    $vertical_value,
                    CoverAndPrismCover_Entry::$DISPLAY_WITH_HEAD_POSTURE
                ]
            ],
            [
                // FULL ENTRY
                [
                    [
                        'distance_id' => $distance->getPrimaryKey(),
                        'correctiontype_id' => $correctiontype->getPrimaryKey(),
                        'horizontal_prism_id' => $horizontal_prism->getPrimaryKey(),
                        'horizontal_value' => $horizontal_value,
                        'vertical_prism_id' => $vertical_prism->getPrimaryKey(),
                        'vertical_value' => $vertical_value,
                        'with_head_posture' => CoverAndPrismCover_Entry::$WITH_HEAD_POSTURE,
                        'comments' => "I am an entry comment"
                    ],
                ],
                null,
                [
                    $distance->name,
                    $correctiontype->name,
                    $horizontal_prism->name,
                    $horizontal_value,
                    $vertical_prism->name,
                    $vertical_value,
                    CoverAndPrismCover_Entry::$DISPLAY_WITH_HEAD_POSTURE,
                    "I am an entry comment"
                ]
            ],
            [
                // TWO BARE ENTRIES
                [
                    [
                        'horizontal_prism_id' => $horizontal_prism->getPrimaryKey(),
                        'horizontal_value' => $horizontal_value,
                        'vertical_prism_id' => $vertical_prism->getPrimaryKey(),
                        'vertical_value' => $vertical_value,
                        'with_head_posture' => CoverAndPrismCover_Entry::$WITHOUT_HEAD_POSTURE,
                        'comments' => "I am an entry comment"
                    ],
                    [
                        'distance_id' => $distance2->getPrimaryKey(),
                        'correctiontype_id' => $correctiontype2->getPrimaryKey(),
                        'horizontal_prism_id' => $horizontal_prism2->getPrimaryKey(),
                        'horizontal_value' => $horizontal_value2,
                        'vertical_prism_id' => $vertical_prism2->getPrimaryKey(),
                        'vertical_value' => $vertical_value2,
                        'with_head_posture' => CoverAndPrismCover_Entry::$WITH_HEAD_POSTURE
                    ]
                ],
                null,
                [
                    $horizontal_prism->name,
                    $horizontal_value,
                    $vertical_prism->name,
                    $vertical_value,
                    CoverAndPrismCover_Entry::$DISPLAY_WITHOUT_HEAD_POSTURE,
                    "I am an entry comment",
                    $distance2->name,
                    $correctiontype2->name,
                    $horizontal_prism2->name,
                    $horizontal_value2,
                    $vertical_prism2->name,
                    $vertical_value2,
                    CoverAndPrismCover_Entry::$DISPLAY_WITH_HEAD_POSTURE,
                ]
            ],
            [
                // NO ENTRIES
                [

                ],
                null,
                [
                    "No entries"
                ]
            ],
            [
                // FULL ENTRY AND COMMENT
                [
                    [
                        'distance_id' => $distance->getPrimaryKey(),
                        'correctiontype_id' => $correctiontype->getPrimaryKey(),
                        'horizontal_prism_id' => $horizontal_prism->getPrimaryKey(),
                        'horizontal_value' => $horizontal_value,
                        'vertical_prism_id' => $vertical_prism->getPrimaryKey(),
                        'vertical_value' => $vertical_value,
                        'with_head_posture' => CoverAndPrismCover_Entry::$WITH_HEAD_POSTURE,
                        'comments' => "I am an entry comment"
                    ],
                ],
                "I am a comment",
                [
                    $distance->name,
                    $correctiontype->name,
                    $horizontal_prism->name,
                    $horizontal_value,
                    $vertical_prism->name,
                    $vertical_value,
                    CoverAndPrismCover_Entry::$DISPLAY_WITH_HEAD_POSTURE,
                    "I am an entry comment",
                    "I am a comment"
                ]
            ],
        ];
    }

    /**
     * @test
     * @dataProvider letter_string_provider
     */
    public function letter_string($entry_attrs, $comment, $expected)
    {
        $instance = $this->getElementInstance();
        $entries = [];
        foreach ($entry_attrs as $entry_attr) {
            $entry = new CoverAndPrismCover_Entry();
            $entry->setAttributes($entry_attr);
            $entries[] = $entry;
        }
        $instance->entries = $entries;

        if ($comment) {
            $instance->comments = $comment;
        }

        $controller = $this->getMockBuilder(\CController::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        \Yii::app()->setController($controller);

        $my_letter_string = $instance->letter_string;

        foreach ($expected as $key => $value) {
            $this->assertContains((string)$value, $my_letter_string);
        }
    }


    public function entries_validation_provider()
    {
        return [
            [
                [
                ],
                false
            ],
            [
                [
                    'comments' => 'Foo bar'
                ],
                true
            ],
            [
                [
                    'entries' => [
                        $this->createValidatingModelMock(CoverAndPrismCover_Entry::class)
                    ],
                    'comments' => 'Foo bar'
                ],
                true
            ],
            [
                [
                    'entries' => [
                        $this->createValidatingModelMock(CoverAndPrismCover_Entry::class),
                        $this->createValidatingModelMock(CoverAndPrismCover_Entry::class)
                    ]
                ],
                true
            ],
        ];
    }

    /**
     * @param $attrs
     * @param $expected
     * @test
     * @dataProvider entries_validation_provider
     */
    public function entries_validation($attrs, $expected)
    {
        $instance = $this->getElementInstance();
        $instance->setAttributes($attrs);
        $this->assertEquals($expected, $instance->validate());
    }

    protected function getElementInstanceWithHeadPostureEntry()
    {
        $instance = $this->getElementInstance();
        $instance->entries = [
            $this->generateCoverAndPrismCoverEntryData([
                'with_head_posture' => CoverAndPrismCover_Entry::$WITH_HEAD_POSTURE
            ])
        ];

        return [$instance, 'entries.0'];
    }
}