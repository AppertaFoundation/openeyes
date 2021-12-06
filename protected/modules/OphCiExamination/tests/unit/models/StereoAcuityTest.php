<?php

namespace OEModule\OphCiExamination\tests\unit\models;

use OEModule\OphCiExamination\models\StereoAcuity;
use OEModule\OphCiExamination\models\StereoAcuity_Entry;
use OEModule\OphCiExamination\tests\unit\models\testingtraits\HasWithHeadPostureEntriesToTest;

/**
 * Class StereoAcuityTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\StereoAcuity
 * @group sample-data
 * @group strabismus
 * @group stereo-acuity
 */
class StereoAcuityTest extends \ModelTestCase
{
    use \HasCoreEventElementTests;
    use \HasRelationOptionsToTest;
    use \WithFaker;
    use HasWithHeadPostureEntriesToTest;

    protected $element_cls = StereoAcuity::class;

    /** @test */
    public function check_entries_relation()
    {
        $instance = $this->getElementInstance();
        $relations = $instance->relations();

        $this->assertArrayHasKey('entries', $relations);
        $this->assertEquals(\CHasManyRelation::class, $relations['entries'][0]);
        $this->assertEquals(StereoAcuity_Entry::class, $relations['entries'][1]);
    }

    /** @test */
    public function check_attribute_safety()
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
                'Stereo Acuity: foo'
            ],
            [
                [
                    'foo', 'bar', 'moo'
                ],
                'Stereo Acuity: foo, bar, moo'
            ],
            [
                [

                ],
                'Stereo Acuity: No entries'
            ],
        ];
    }

    /**
     * @test
     * @dataProvider letter_string_provider
     */
    public function test_letter_string($entry_strings, $expected)
    {
        $instance = $this->getElementInstance();
        $entries = [];
        foreach ($entry_strings as $entry_string) {
            $entry = $this->createMock(StereoAcuity_Entry::class);
            $entry->method('__toString')
                ->will($this->returnValue($entry_string));
            $entries[] = $entry;
        }
        $instance->entries = $entries;

        $this->assertEquals($expected, $instance->letter_string);
    }

    /** @test */
    public function at_least_one_entry_required()
    {
        $instance = $this->getElementInstance();
        $this->assertAttributeInvalid($instance, 'entries', 'cannot be blank');
        $instance->entries = [$this->createValidatingModelMock(StereoAcuity_Entry::class)];
        $this->assertTrue($instance->validate('entries'));
    }

    protected function getElementInstanceWithHeadPostureEntry()
    {
        $instance = $this->getElementInstance();
        $entry = new StereoAcuity_Entry();
        $entry->with_head_posture = StereoAcuity_Entry::$WITH_HEAD_POSTURE;
        $instance->entries = [$entry];
        return [$instance, 'entries.0'];
    }
}
