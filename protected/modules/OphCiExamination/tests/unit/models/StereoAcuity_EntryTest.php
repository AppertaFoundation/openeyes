<?php


namespace OEModule\OphCiExamination\tests\unit\models;

use OEModule\OphCiExamination\models\CorrectionType;
use OEModule\OphCiExamination\models\StereoAcuity_Entry;
use OEModule\OphCiExamination\models\StereoAcuity_Method;
use OEModule\OphCiExamination\tests\unit\models\testingtraits\HasCorrectionTypeAttributeToTest;
use OEModule\OphCiExamination\tests\unit\models\testingtraits\HasWithHeadPostureAttributesToTest;

/**
 * Class StereoAcuity_EntryTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\StereoAcuity_Entry
 * @group sample-data
 * @group strabismus
 * @group stereo-acuity
 */
class StereoAcuity_EntryTest extends \ModelTestCase
{
    use \HasStandardRelationsTests;
    use \HasRelationOptionsToTest;
    use \WithFaker;

    use HasCorrectionTypeAttributeToTest;
    use HasWithHeadPostureAttributesToTest;

    protected $element_cls = StereoAcuity_Entry::class;

    /** @test */
    public function check_stereoacuity_method_relation_defined()
    {
        $instance = $this->getElementInstance();

        $this->assertArrayHasKey('method', $instance->relations());
    }

    /** @test */
    public function check_stereoacuity_method_rules_defined()
    {
        $instance = $this->getElementInstance();

        $this->assertRelationRuleDefined($instance, 'method_id', StereoAcuity_Method::class);
        $this->assertContains('method_id', $instance->getSafeAttributeNames());
    }

    /** @test */
    public function check_stereoacuity_method_options()
    {
        $instance = $this->getElementInstance();

        $this->assertOptionsAreRetrievable($instance, 'method', StereoAcuity_Method::class);
    }

    public function inconclusive_validation_provider()
    {
        return [
            [StereoAcuity_Entry::INCONCLUSIVE, true],
            [StereoAcuity_Entry::NOT_INCONCLUSIVE, true],
            ['', true],
            ['foo', false]
        ];
    }

    /**
     * @param $value
     * @param $expected
     * @test
     * @dataProvider inconclusive_validation_provider
     */
    public function check_inconclusive_validation($value, $expected)
    {
        $instance = $this->getElementInstance();
        $instance->inconclusive = $value;
        $this->assertEquals($expected, $instance->validate(['inconclusive']));
    }

    public function result_validation_provider()
    {
        return [
            [
                [
                    'inconclusive' => StereoAcuity_Entry::NOT_INCONCLUSIVE
                ],
                false
            ],
            [
                [
                    'inconclusive' => StereoAcuity_Entry::NOT_INCONCLUSIVE,
                    'result' => 'foo bar'
                ],
                true
            ],
            [
                [
                    'inconclusive' => StereoAcuity_Entry::INCONCLUSIVE
                ],
                true
            ],
            [
                [
                    'inconclusive' => StereoAcuity_Entry::INCONCLUSIVE,
                    'result' => 'foo bar'
                ],
                false
            ],
        ];
    }

    /**
     * @param $value
     * @param $expected
     * @test
     * @dataProvider result_validation_provider
     */
    public function check_result_validation($attrs, $expected)
    {
        $instance = $this->getElementInstance();
        $instance->setAttributes($attrs);
        $this->assertEquals($expected, $instance->validate(['result']));
    }

    public function letter_string_provider()
    {
        $method = $this->getRandomLookup(StereoAcuity_Method::class);
        $correctiontype = $this->getRandomLookup(CorrectionType::class);

        return [
            [
                [
                    'method_id' => $method->getPrimaryKey(),
                    'inconclusive' => StereoAcuity_Entry::INCONCLUSIVE
                ],
                $method . " - " . StereoAcuity_Entry::DISPLAY_INCONCLUSIVE
            ],
            [
                [
                    'method_id' => $method->getPrimaryKey(),
                    'inconclusive' => StereoAcuity_Entry::NOT_INCONCLUSIVE,
                    'result' => "foo bar"
                ],
                $method . " - foo bar"
            ],
            [
                [
                    'method_id' => $method->getPrimaryKey(),
                    'inconclusive' => StereoAcuity_Entry::NOT_INCONCLUSIVE,
                    'result' => "foo bar",
                    'correctiontype_id' => $correctiontype->getPrimaryKey(),
                ],
                $method . " - foo bar (" . $correctiontype->name . ")"
            ],
            [
                [
                    'method_id' => $method->getPrimaryKey(),
                    'inconclusive' => StereoAcuity_Entry::NOT_INCONCLUSIVE,
                    'result' => "foo bar",
                    'with_head_posture' => StereoAcuity_Entry::$WITH_HEAD_POSTURE,
                ],
                $method . " - foo bar (CHP " . StereoAcuity_Entry::$DISPLAY_WITH_HEAD_POSTURE . ")"
            ],
            [
                [
                    'method_id' => $method->getPrimaryKey(),
                    'inconclusive' => StereoAcuity_Entry::NOT_INCONCLUSIVE,
                    'result' => "foo bar",
                    'correctiontype_id' => $correctiontype->getPrimaryKey(),
                    'with_head_posture' => StereoAcuity_Entry::$WITHOUT_HEAD_POSTURE,
                ],
                $method . " - foo bar (" . $correctiontype->name . ", CHP " . StereoAcuity_Entry::$DISPLAY_WITHOUT_HEAD_POSTURE . ")"
            ]
        ];
    }

    /**
     * @param $value
     * @param $expected
     * @test
     * @dataProvider letter_string_provider
     */
    public function check_to_string_for_letter($attrs, $expected){

        $instance = $this->getElementInstance();
        $instance->setAttributes($attrs);
        $this->assertEquals($expected, (string)$instance); // explicit type casting may not be nesc, but added for readability

    }

    /** @test */
    public function check_inconclusive_options_available_as_attribute()
    {
        $options = $this->getElementInstance()->inconclusive_options;
        $this->assertCount(2, $options);
        $this->assertDropdownOptionsHasCorrectKeys($options);
    }

    /** @test */
    public function inconclusive_is_null_for_new_entries()
    {
        $instance = $this->getElementInstance();
        $this->assertEmpty((string) $instance->inconclusive);
    }

    public function results_provider()
    {
        return [
            [StereoAcuity_Entry::INCONCLUSIVE, 'foo', StereoAcuity_Entry::DISPLAY_INCONCLUSIVE],
            [StereoAcuity_Entry::NOT_INCONCLUSIVE, 'foo', 'foo'],
            [StereoAcuity_Entry::NOT_INCONCLUSIVE, null, '-'],
        ];
    }

    /**
     * @test
     * @dataProvider results_provider
     */
    public function result_display_varies_based_inconclusive($inconclusive, $result, $expected)
    {
        $instance = $this->getElementInstance();
        $instance->inconclusive = $inconclusive;
        $instance->result = $result;

        $this->assertEquals($expected, $instance->display_result);

    }
}