<?php

namespace OEModule\OphCiExamination\tests\unit\models;

use OEModule\OphCiExamination\models\ContrastSensitivity_Type;
use OEModule\OphCiExamination\models\ContrastSensitivity_Result;
use OEModule\OphCiExamination\models\ContrastSensitivity;
use OEModule\OphCiExamination\models\CorrectionType;

/**
 * @covers \OEModule\OphCiExamination\models\ContrastSensitivity
 * @group sample-data
 * @group strabismus
 * @group contrast-sensitivity
 */
class ContrastSensitivityTest extends \ModelTestCase
{
    use \HasCoreEventElementTests;
    use \HasRelationOptionsToTest;
    use \WithFaker;

    protected $element_cls = ContrastSensitivity::class;

    /** @test */
    public function results_relation()
    {
        $instance = $this->getElementInstance();
        $relations = $instance->relations();

        $this->assertArrayHasKey('results', $relations);
        $this->assertEquals(\CHasManyRelation::class, $relations['results'][0]);
        $this->assertEquals(ContrastSensitivity_Result::class, $relations['results'][1]);
    }

    /** @test */
    public function attribute_safety()
    {
        $instance = $this->getElementInstance();
        $safe = $instance->getSafeAttributeNames();

        $this->assertContains('event_id', $safe);
        $this->assertContains('results', $safe);
    }

    public function entries_validation_provider()
    {
        $type = $this->getRandomLookup(ContrastSensitivity_Type::class);
        $alttype = $this->getRandomLookup(ContrastSensitivity_Type::class);
        while ($type->id === $alttype->id) {
            $alttype = $this->getRandomLookup(ContrastSensitivity_Type::class);
        }

        $value = random_int(0, 9);

        $eye = $this->getValidEyeIDs()[random_int(0, 2)];
        $alteye = $this->getValidEyeIDs()[random_int(0, 2)];
        while ($eye === $alteye) {
            $alteye = $this->getValidEyeIDs()[random_int(0, 2)];
        }

        $correctiontype = $this->getRandomLookup(CorrectionType::class);

        return [
            [
                [
                ],
                false,
                "no results and no comment should fail"
            ],
            [
                [
                    'comments' => 'Foo bar'
                ],
                true,
                "comment only should pass"
            ],
            [
                [
                    'results' => [
                        $this->createValidResult([
                            'contrastsensitivity_type_id' => $type->getPrimaryKey(),
                            'value' => $value,
                            'eye_id' => $eye,
                            'correctiontype_id' => $correctiontype->getPrimaryKey()
                        ])
                    ],
                ],
                true,
                "valid result should pass"
            ],
            [
                [
                    'results' => [
                        $this->createValidResult([
                            'contrastsensitivity_type_id' => $type->getPrimaryKey(),
                            'value' => $value,
                            'eye_id' => $eye,
                            'correctiontype_id' => $correctiontype->getPrimaryKey()
                        ])
                    ],
                    'comments' => 'Foo bar'
                ],
                true,
                "valid result and comment should pass"
            ],
            [
                [
                    'results' => [
                        $this->createValidResult([
                            'contrastsensitivity_type_id' => $type->getPrimaryKey(),
                            'value' => $value,
                            'eye_id' => $eye,
                            'correctiontype_id' => $correctiontype->getPrimaryKey()
                        ]),
                        $this->createValidResult([
                            'contrastsensitivity_type_id' => $type->getPrimaryKey(),
                            'value' => $value,
                            'eye_id' => $alteye,
                            'correctiontype_id' => $correctiontype->getPrimaryKey()
                        ]),
                    ]
                ],
                true,
                "results should pass as different laterality even though same test type"
            ],
            [
                [
                    'results' => [
                        $this->createValidResult([
                            'contrastsensitivity_type_id' => $type->getPrimaryKey(),
                            'value' => $value,
                            'eye_id' => $eye,
                            'correctiontype_id' => $correctiontype->getPrimaryKey()
                        ]),
                        $this->createValidResult([
                            'contrastsensitivity_type_id' => $alttype->getPrimaryKey(),
                            'value' => $value,
                            'eye_id' => $eye,
                            'correctiontype_id' => $correctiontype->getPrimaryKey()
                        ]),
                    ]
                ],
                true,
                "results should pass as different test type even though same laterality"
            ],
            [
                [
                    'results' => [
                        $this->createValidResult([
                            'contrastsensitivity_type_id' => $type->getPrimaryKey(),
                            'value' => $value,
                            'eye_id' => $eye,
                            'correctiontype_id' => $correctiontype->getPrimaryKey()
                        ]),
                        $this->createValidResult([
                            'contrastsensitivity_type_id' => $type->getPrimaryKey(),
                            'value' => $value,
                            'eye_id' => $eye,
                            'correctiontype_id' => $correctiontype->getPrimaryKey()
                        ]),
                    ]
                ],
                false,
                "results should be invalid with duplicate laterality for same type"
            ],
        ];
    }

    /**
     * @param $attrs
     * @param $expected
     * @param $message
     * @test
     * @dataProvider entries_validation_provider
     */
    public function entries_validation($attrs, $expected, $message)
    {
        $instance = $this->getElementInstance();
        $instance->setAttributes($attrs);

        $this->assertEquals($expected, $instance->validate(), $message);
    }

    public function letter_string_provider()
    {
        $type = $this->getRandomLookup(ContrastSensitivity_Type::class);
        $type2 = $this->getRandomLookup(ContrastSensitivity_Type::class);
        while ($type->getPrimaryKey() === $type2->getPrimaryKey()) {
            $type2 = $this->getRandomLookup(ContrastSensitivity_Type::class);
        }
        $value = random_int(0, 9);
        $value2 = random_int(0, 9);
        $value3 = random_int(0, 9);
        $correctiontype = $this->getRandomLookup(CorrectionType::class);
        $correctiontype2 = $this->getRandomLookup(CorrectionType::class);

        return [
            [
                [
                    [
                        'contrastsensitivity_type_id' => $type->getPrimaryKey(),
                        'value' => $value,
                        'eye_id' => ContrastSensitivity_Result::LEFT,
                        'correctiontype_id' => $correctiontype->getPrimaryKey()
                    ]
                ],
                null,
                '<tr><td>' . $type->name . '</td><td style="text-align: center">-</td>' .
                '<td style="text-align: center">-</td>' .
                '<td style="text-align: center">' . $value . ' (' . $correctiontype->name . ')' . '</td></tr>'
            ],
            [
                [
                    [
                        'contrastsensitivity_type_id' => $type->getPrimaryKey(),
                        'value' => $value,
                        'eye_id' => ContrastSensitivity_Result::LEFT,
                        'correctiontype_id' => $correctiontype->getPrimaryKey()
                    ]
                ],
                "foo bar",
                '<tr><td>' . $type->name . '</td><td style="text-align: center">-</td>' .
                '<td style="text-align: center">-</td>' .
                '<td style="text-align: center">' . $value . ' (' . $correctiontype->name . ')' . '</td></tr>'
            ],
            [
                [
                    [
                        'contrastsensitivity_type_id' => $type->getPrimaryKey(),
                        'value' => $value,
                        'eye_id' => ContrastSensitivity_Result::LEFT,
                        'correctiontype_id' => $correctiontype->getPrimaryKey()
                    ],
                    [
                        'contrastsensitivity_type_id' => $type->getPrimaryKey(),
                        'value' => $value2,
                        'eye_id' => ContrastSensitivity_Result::RIGHT,
                        'correctiontype_id' => $correctiontype2->getPrimaryKey()
                    ]
                ],
                null,
                '<tr><td>' . $type->name . '</td><td style="text-align: center">' . $value2 . ' (' . $correctiontype2->name . ')' . '</td>' .
                '<td style="text-align: center">-</td>' .
                '<td style="text-align: center">' . $value . ' (' . $correctiontype->name . ')' . '</td></tr>'
            ],
            [
                [
                    [
                        'contrastsensitivity_type_id' => $type->getPrimaryKey(),
                        'value' => $value,
                        'eye_id' => ContrastSensitivity_Result::LEFT,
                        'correctiontype_id' => $correctiontype->getPrimaryKey()
                    ],
                    [
                        'contrastsensitivity_type_id' => $type->getPrimaryKey(),
                        'value' => $value2,
                        'eye_id' => ContrastSensitivity_Result::RIGHT,
                        'correctiontype_id' => $correctiontype2->getPrimaryKey()
                    ],
                    [
                        'contrastsensitivity_type_id' => $type->getPrimaryKey(),
                        'value' => $value3,
                        'eye_id' => ContrastSensitivity_Result::BEO
                    ]
                ],
                null,
                '<tr><td>' . $type->name . '</td><td style="text-align: center">' . $value2 . ' (' . $correctiontype2->name . ')' . '</td>' .
                '<td style="text-align: center">' . $value3 . '</td>' .
                '<td style="text-align: center">' . $value . ' (' . $correctiontype->name . ')' . '</td></tr>'
            ],
            [
                [
                    [
                        'contrastsensitivity_type_id' => $type->getPrimaryKey(),
                        'value' => $value,
                        'eye_id' => ContrastSensitivity_Result::LEFT,
                        'correctiontype_id' => $correctiontype->getPrimaryKey()
                    ],
                    [
                        'contrastsensitivity_type_id' => $type2->getPrimaryKey(),
                        'value' => $value2,
                        'eye_id' => ContrastSensitivity_Result::RIGHT,
                        'correctiontype_id' => $correctiontype2->getPrimaryKey()
                    ]
                ],
                null,
                '<tr><td>' . $type->name . '</td><td style="text-align: center">-</td>' .
                '<td style="text-align: center">-</td>' .
                '<td style="text-align: center">' . $value . ' (' . $correctiontype->name . ')' . '</td></tr>',
                '<tr><td>' . $type2->name . '</td><td style="text-align: center">' . $value2 . ' (' . $correctiontype2->name . ')' . '</td>' .
                '<td style="text-align: center">-</td>' .
                '<td style="text-align: center">-</td></tr>'
            ],
        ];
    }

    /**
     * @test
     * @dataProvider letter_string_provider
     */
    public function letter_string($result_attrs, $comment, $expected)
    {
        $instance = $this->getElementInstance();
        $results = [];
        foreach ($result_attrs as $result_key => $result_attr) {
            $result = $this->createValidResult($result_attr);
            $results[] = $result;
        }
        $instance->results = $results;

        if ($comment) {
            $instance->comments = $comment;
        }

        $controller = $this->getMockBuilder(\CController::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        \Yii::app()->setController($controller);

        $my_letter_string = $instance->letter_string;

        $this->assertStringContainsString($this->removeWhiteSpaceAndLineBreaks((string)$expected), $this->removeWhiteSpaceAndLineBreaks($my_letter_string));

        if ($comment) {
            $this->assertStringContainsString($comment, $my_letter_string);
        }
    }

    public function removeWhiteSpaceAndLineBreaks($string)
    {
        return preg_replace('/\s*/m', "", $string);
    }

    /**
     * @param $result_attr
     * */
    public function createValidResult($result_attr)
    {
        $result = new ContrastSensitivity_Result();
        $result->setAttributes($result_attr);
        return $result;
    }

    public function getValidEyeIDs()
    {
        return [
            ContrastSensitivity_Result::BEO,
            ContrastSensitivity_Result::LEFT,
            ContrastSensitivity_Result::RIGHT
        ];
    }
}
