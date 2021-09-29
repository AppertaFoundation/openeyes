<?php

namespace OEModule\OphCiExamination\tests\unit\models;

use OEModule\OphCiExamination\models\PrismReflex;
use OEModule\OphCiExamination\models\PrismReflex_Entry;
use OEModule\OphCiExamination\tests\unit\models\testingtraits\HasWithHeadPostureEntriesToTest;

/**
 * Class DioptrePrismTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\PrismReflex
 * @group sample-data
 * @group strabismus
 * @group prism-reflex
 */
class PrismReflexTest extends \ModelTestCase
{
    use \HasCoreEventElementTests;
    use \HasRelationOptionsToTest;
    use \WithFaker;
    use HasWithHeadPostureEntriesToTest;

    protected $element_cls = PrismReflex::class;

    /** @test */
    public function entries_relation()
    {
        $instance = $this->getElementInstance();
        $relations = $instance->relations();

        $this->assertArrayHasKey('entries', $relations);
        $this->assertEquals(\CHasManyRelation::class, $relations['entries'][0]);
        $this->assertEquals(PrismReflex_Entry::class, $relations['entries'][1]);
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
        return [
            [
                [
                    'foo'
                ],
                null,
                'Prism Reflex Test: foo'
            ],
            [
                [
                    'foo', 'bar', 'moo'
                ],
                null,
                'Prism Reflex Test: foo, bar, moo'
            ],
            [
                [

                ],
                null,
                'Prism Reflex Test: No entries'
            ],
            [
                [
                    'this', 'has', 'a', 'comment'
                ],
                "I am a comment",
                'Prism Reflex Test: this, has, a, comment I am a comment'
            ],
        ];
    }

    /**
     * @test
     * @dataProvider letter_string_provider
     */
    public function letter_string($entry_strings, $comment, $expected)
    {
        $instance = $this->getElementInstance();
        $entries = [];
        foreach ($entry_strings as $entry_string) {
            $entry = $this->createMock(PrismReflex_Entry::class);
            $entry->method('__toString')
                ->will($this->returnValue($entry_string));
            $entries[] = $entry;
        }
        $instance->entries = $entries;

        if ($comment) {
            $instance->comments = $comment;
        }

        $this->assertEquals($expected, $instance->letter_string);
    }

    /** @test */
    public function at_least_one_entry_required()
    {
        $instance = $this->getElementInstance();
        $this->assertAttributeInvalid($instance, 'entries', 'cannot be blank');
        $instance->entries = [$this->createValidatingModelMock(PrismReflex_Entry::class)];
        $this->assertTrue($instance->validate(['entries']));
    }

    protected function getElementInstanceWithHeadPostureEntry()
    {
        $instance = $this->getElementInstance();
        $entry = new PrismReflex_Entry();
        $entry->with_head_posture = PrismReflex_Entry::$WITH_HEAD_POSTURE;
        $instance->entries = [$entry];

        return [$instance, 'entries.0'];
    }
}
