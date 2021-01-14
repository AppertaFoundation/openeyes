<?php


namespace OEModule\OphCiExamination\tests\unit\models;

use OEModule\OphCiExamination\models\ContrastSensitivity;
use OEModule\OphCiExamination\models\ContrastSensitivity_Result;
use OEModule\OphCiExamination\models\ContrastSensitivity_Type;
use OEModule\OphCiExamination\models\CorrectionType;
use OEModule\OphCiExamination\tests\unit\models\testingtraits\HasCorrectionTypeAttributeToTest;

/**
 * @covers OEModule\OphCiExamination\models\ContrastSensitivity_Result
 * @group sample-data
 * @group strabismus
 * @group contrast-sensitivity
 */
class ContrastSensitivity_ResultTest extends \ModelTestCase
{
    use \HasStandardRelationsTests;
    use \HasRelationOptionsToTest;
    use \InteractsWithEventTypeElements;
    use \WithFaker;
    use \WithTransactions;
    use HasCorrectionTypeAttributeToTest;

    protected $element_cls = ContrastSensitivity_Result::class;


    /** @test */
    public function relations_defined()
    {
        $instance = $this->getElementInstance();

        $this->assertArrayHasKey('contrastsensitivity_type', $instance->relations());
    }

    /** @test */
    public function type_rules_defined()
    {
        $instance = $this->getElementInstance();

        $this->assertRelationRuleDefined($instance, 'contrastsensitivity_type_id', ContrastSensitivity_Type::class);
        $this->assertContains('contrastsensitivity_type_id', $instance->getSafeAttributeNames());
    }

    /** @test */
    public function type_options()
    {
        $instance = $this->getElementInstance();

        $this->assertOptionsAreRetrievable($instance, 'contrastsensitivity_type', ContrastSensitivity_Type::class);
    }


    public function entry_validation_provider()
    {
        $type = $this->getRandomLookup(ContrastSensitivity_Type::class);
        $value = random_int(0, 9);
        $eye = [
            ContrastSensitivity_Result::BEO,
            ContrastSensitivity_Result::LEFT,
            ContrastSensitivity_Result::RIGHT
        ][random_int(0, 2)];
        $correctiontype = $this->getRandomLookup(CorrectionType::class);

        return [
            [
                [
                    'value' => $value
                ],
                false
            ],
            [
                [
                    'eye_id' => $eye,
                    'correction_id' => $correctiontype->getPrimaryKey(),
                ],
                false
            ],
            [
                [
                    'contrastsensitivity_type_id' => $type->getPrimaryKey()
                ],
                false
            ],
            [
                [
                    'value' => $value,
                    'eye_id' => $eye
                ],
                false
            ],
            [
                [
                    'value' => $value,
                    'contrastsensitivity_type_id' => $type->getPrimaryKey()
                ],
                false
            ],
            [
                [
                    'value' => $value,
                    'eye_id' => $eye,
                    'contrastsensitivity_type_id' => $type->getPrimaryKey()
                ],
                true
            ],
            [
                [
                    'value' => $value,
                    'eye_id' => $eye,
                    'correction_id' => $correctiontype->getPrimaryKey(),
                    'contrastsensitivity_type_id' => $type->getPrimaryKey()
                ],
                true
            ],
        ];
    }

    /**
     * @param $value
     * @param $expected
     * @test
     * @dataProvider entry_validation_provider
     */
    public function entry_validation($attrs, $expected)
    {
        $instance = $this->getElementInstance();
        $instance->setAttributes($attrs);
        $this->assertEquals($expected, $instance->validate());
    }

    public function letter_string_provider()
    {
        $type = $this->getRandomLookup(ContrastSensitivity_Type::class);
        $value = random_int(0, 9);
        $eye = [
            ContrastSensitivity_Result::BEO,
            ContrastSensitivity_Result::LEFT,
            ContrastSensitivity_Result::RIGHT
        ][random_int(0, 2)];
        $correctiontype = $this->getRandomLookup(CorrectionType::class);

        $instance = $this->getElementInstance();

        return [
            [
                [
                    'contrastsensitivity_type_id' => $type->getPrimaryKey(),
                    'value' => $value,
                    'eye_id' => $eye,
                    'correctiontype_id' => $correctiontype->getPrimaryKey()
                ],
                $value . " (" . $correctiontype->name . ")"
            ],
            [
                [
                    'contrastsensitivity_type_id' => $type->getPrimaryKey(),
                    'value' => $value,
                    'eye_id' => $eye
                ],
                $value
            ]
        ];
    }

    /**
     * @param $attrs
     * @param $expected
     * @test
     * @dataProvider letter_string_provider
     */
    public function to_string_for_letter($attrs, $expected)
    {
        $instance = $this->getElementInstance();
        $instance->setAttributes($attrs);
        $savedInstance = $this->saveResult($instance);
        $this->assertEquals($expected, (string)$savedInstance); // explicit type casting may not be nesc, but added for readability
    }

    protected function saveResult(ContrastSensitivity_Result $instance)
    {
        $element = new ContrastSensitivity();
        $element->results = [$instance];
        $this->saveElement($element);
        return ContrastSensitivity_Result::model()->findByPk($instance->getPrimaryKey());
    }
}
