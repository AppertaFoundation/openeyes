<?php

/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use Disorder;
use OE\factories\ModelFactory;

 /**
 * Class DisorderWithSampleDataTest
 *
 * @package \tests\unit\models
 * @covers Disorder
 * @group sample-data
 * @group disorder
 */
class DisorderWithSampleDataTest extends \ModelTestCase
{
    use \WithFaker;
    use \WithTransactions;

    protected $element_cls = Disorder::class;

    /**
     * @return array
     */
    public function attribute_values()
    {
        return [
            'empty should fail with required keys' =>[
                function ($testCase) {
                    return [
                        [],
                        false,
                        [
                            'id' => 'cannot be',
                            'fully_specified_name' => 'cannot be',
                            'term' => 'cannot be',
                        ]
                    ];
                }
            ],
            'required attributes should pass' => [
                function ($testCase) {
                    return [
                        [
                            'id' => 1,
                            'fully_specified_name' => 'Foo',
                            'term' => 'Bar',
                        ],
                        true,
                        []
                    ];
                }
            ],
            'string id should fail' => [
                function ($testCase) {
                    return [
                        [
                            'id' => 'Foo',
                            'fully_specified_name' => 'Foo',
                            'term' => 'Bar',
                        ],
                        false,
                        [
                            'id' => 'a number'
                        ]
                    ];
                }
            ],
            'too long required attributes will fail' => [
                function ($testCase) {
                    return [
                        [
                            'id' => 2,
                            'fully_specified_name' => $testCase->faker->sentence(255),
                            'term' => $testCase->faker->sentence(255),
                        ],
                        false,
                        [
                            'fully_specified_name' => 'too long',
                            'term' => 'too long'
                        ]
                    ];
                }
            ],
            'valid optional attributes should pass' => [
                function ($testCase) {
                    return [
                        [
                            'id' => 3,
                            'fully_specified_name' => $testCase->faker->word(),
                            'term' => $testCase->faker->word(),
                            'aliases' => $testCase->faker->word(),
                            'specialty_id' => $testCase->faker->word(),
                            'ecds_code' => $testCase->faker->regexify('[A-Za-z0-9]{20}'),
                            'ecds_term' => $testCase->faker->word(),
                            'icd10_code' => $testCase->faker->regexify('[A-Za-z0-9]{10}'),
                            'icd10_term' => $testCase->faker->word()
                        ],
                        true,
                        []
                    ];
                }
            ],
            'too long optional attrubutes should fail' => [
                function ($testCase) {
                    return [
                        [
                            'id' => 4,
                            'fully_specified_name' => $testCase->faker->word(),
                            'term' => $testCase->faker->word(),
                            'aliases' => $testCase->faker->sentence(255),
                            'specialty_id' => $testCase->faker->sentence(255),
                            'ecds_code' => $testCase->faker->sentence(255),
                            'ecds_term' => $testCase->faker->sentence(255),
                            'icd10_code' => $testCase->faker->sentence(255),
                            'icd10_term' => $testCase->faker->sentence(255)
                        ],
                        false,
                        [
                            'aliases' => 'too long',
                            'specialty_id' => 'too long',
                            'ecds_code' => 'too long',
                            'ecds_term' => 'too long',
                            'icd10_code' => 'too long',
                            'icd10_term' => 'too long'
                        ]
                    ];
                }
            ]
        ];
    }

    /**
     * @param $providerCallback
     * @test
     * @dataProvider attribute_values
     */
    public function disorder_rules_validates_as_expected(\Closure $providerCallback)
    {
        list($data, $should_validate, $expected_error_keys) = $providerCallback($this);

        $instance = new Disorder();

        foreach ($data as $attr => $value) {
            $instance->$attr = $value;
        }

        $this->assertEquals($should_validate, $instance->validate());

        foreach ($expected_error_keys as $attr => $message_partial) {
            $this->assertAttributeHasError($instance, $attr, $message_partial);
        }
    }

    /** @test */
    public function cannot_create_new_disorder_with_existing_id()
    {
        $existingDisorder = ModelFactory::factoryFor(Disorder::class)->create();

        $instance = new Disorder();

        foreach ([
            'id' => $existingDisorder->id,
            'fully_specified_name' => 'Foo',
            'term' => 'Bar',
        ] as $attr => $value) {
            $instance->$attr = $value;
        }

        $this->assertFalse($instance->validate());
        $this->assertAttributeHasError($instance, 'id', 'already exists');
    }
}
