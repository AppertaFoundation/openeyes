<?php
/**
 * (C) Copyright Apperta Foundation 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
    * @covers OphDrPGDPSD_PGDPSD
    *
    * @group sample-data
    * @group pgdpsd
 */
class OphDrPGDPSD_PDGPSDTest extends \ActiveRecordTestCase
{
    public function getModel()
    {
        return OphDrPGDPSD_PGDPSD::model();
    }

    public function data_provider()
    {
        return [
            'empty post fails validation' => [
                [],
                null,
                [],
                false,
                [
                    'name', 'type', 'Users', 'Medications'
                ]
            ],
            'too long name fail validation' => [
                [
                    'name' => "Foo Bar Baz Lorem Ipsum Said Amet Bingo Bango Bongo"
                ],
                null,
                [],
                false,
                [
                    'name'
                ]
            ],
            'description is required for pgd type' => [
                [
                    'name' => "Foo bar",
                    'type' => 'pgd'
                ],
                null,
                [],
                false,
                [
                    'description'
                ]
            ],
            'valid psd passes validation' => [
                [
                    'name' => "Foo bar",
                    'type' => 'psd'
                ],
                true,
                [
                    $this->getValidPSDMed()
                ],
                true,
                []
            ],
            'empty psd meds fails validation' => [
                [
                    'name' => "Foo bar",
                    'type' => 'psd'
                ],
                true,
                [
                    []
                ],
                false,
                [
                    'Medications'
                ]
            ],
            'mix of valid and invalid psd meds fails validation 1' => [
                [
                    'name' => "Foo bar",
                    'type' => 'psd'
                ],
                true,
                [
                    $this->getInvalidMed('psd'),
                    $this->getValidPSDMed()
                ],
                false,
                [
                    'Medications'
                ]
            ],
            'mix of valid and invalid psd meds fails validation 2' => [
                [
                    'name' => "Foo bar",
                    'type' => 'psd'
                ],
                true,
                [
                    $this->getValidPSDMed(),
                    $this->getInvalidMed('psd')
                ],
                false,
                [
                    'Medications'
                ]
            ],
            'mix of valid and invalid psd meds fails validation 3' => [
                [
                    'name' => "Foo bar",
                    'type' => 'psd'
                ],
                true,
                [
                    $this->getValidPSDMed(),
                    $this->getInvalidMed('psd'),
                    $this->getValidPSDMed()
                ],
                false,
                [
                    'Medications'
                ]
            ],
            'multiple valid psd meds passes validation' => [
                [
                    'name' => "Foo bar",
                    'type' => 'psd'
                ],
                true,
                [
                    $this->getValidPSDMed(),
                    $this->getValidPSDMed()
                ],
                true,
                []
            ],
            'valid pgd passes validation' => [
                [
                    'name' => "Foo bar",
                    'type' => 'pgd',
                    'description' => 'Lorem ipsum said amet'
                ],
                true,
                [
                    $this->getValidPGDMed()
                ],
                true,
                []
            ],
            'empty pgd meds fails validation' => [
                [
                    'name' => "Foo bar",
                    'type' => 'pgd',
                    'description' => 'Lorem ipsum said amet'
                ],
                true,
                [
                    []
                ],
                false,
                [
                    'Medications'
                ]
            ],
            'mix of valid and invalid pgd meds fails validation 1' => [
                [
                    'name' => "Foo bar",
                    'type' => 'pgd',
                    'description' => 'Lorem ipsum said amet'
                ],
                true,
                [
                    $this->getInvalidMed('pgd'),
                    $this->getValidPGDMed()
                ],
                false,
                [
                    'Medications'
                ]
            ],
            'mix of valid and invalid pgd meds fails validation 2' => [
                [
                    'name' => "Foo bar",
                    'type' => 'pgd',
                    'description' => 'Lorem ipsum said amet'
                ],
                true,
                [
                    $this->getValidPGDMed(),
                    $this->getInvalidMed('pgd')
                ],
                false,
                [
                    'Medications'
                ]
            ],
            'multiple valid pgd meds passses validation' => [
                [
                    'name' => "Foo bar",
                    'type' => 'pgd',
                    'description' => 'Lorem ipsum said amet'
                ],
                true,
                [
                    $this->getValidPGDMed(),
                    $this->getValidPGDMed()
                ],
                true,
                []
            ]
        ];
    }

    /**
     * @dataProvider data_provider
     *
     * @test
     * */
    public function validation_rules_are_applied_correctly($attributes, $user_or_team, $meds, $valid, $expected_errors)
    {
        $this_pgdpsd = new OphDrPGDPSD_PGDPSD();
        $this_pgdpsd->attributes = $attributes;

        if ($user_or_team) {
            if (rand(0, 1)) {
                $this_pgdpsd->temp_team_ids = [Team::factory()->create()->id];
            } else {
                $this_pgdpsd->temp_user_ids = [User::factory()->create()->id];
            }
        }

        if (count($meds)) {
            $this_pgdpsd->temp_meds_info = $meds;
        }

        $this->assertEquals($this_pgdpsd->validate(), $valid);

        if (count($expected_errors)) {
            foreach ($expected_errors as $expected_error) {
                $this->assertArrayHasKey($expected_error, $this_pgdpsd->getErrors());
            }
        }
    }

    protected function getValidPSDMed()
    {
        return [
            'medication_id' => rand(1, 50),
            'pgdpsd_id' => rand(1, 2),
            'route_id' => rand(1, 50),
            'dose' => rand(1, 50),
            'dose_unit_term' => 'Foo'
        ];
    }

    protected function getValidPGDMed()
    {
        return [
            'medication_id' => rand(1, 50),
            'pgdpsd_id' => rand(1, 2),
            'route_id' => rand(1, 50),
            'dose' => rand(1, 50),
            'dose_unit_term' => 'Baz',
            'frequency_id' => rand(1, 50),
            'duration_id' => rand(1, 50),
            'dispense_condition_id' => rand(1, 50),
            'dispense_location_id' => rand(1, 50)
        ];
    }

    protected function getInvalidMed($type)
    {
        $med = $type === 'psd' ? $this->getValidPSDMed() : $this->getValidPGDMed();
        shuffle($med);
        $numToRemove = rand(1, count($med));
        for ($i = 0; $i < $numToRemove; $i++) {
            array_pop($med);
        }
        return $med;
    }
}
