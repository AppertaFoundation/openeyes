<?php
/**
 * (C) OpenEyes Foundation, 2024
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2024, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace components;

use ArrayHelper;
use PHPUnit\Framework\TestCase;

class ArrayHelperTest extends TestCase
{
    /**
     * @test
     * @covers ArrayHelper
     */
    public function insertAtKey(): void
    {
        $initial_array = $this->getInitialArray();
        $tests = $this->getTestCases();

        foreach ($tests as $test) {
            $result = ArrayHelper::insertAtKey($initial_array, $test['new_item'], $test['attribute'], $test['target_key']);
            $this->assertEquals($test['expected'], $result);
        }
    }

    private function getInitialArray()
    {
        return [
            10 => (object)['attribute' => 'A'],
            20 => (object)['attribute' => 'B'],
            30 => (object)['attribute' => 'D'],
            31 => (object)['attribute' => 'X'],
            32 => (object)['attribute' => 'XXXII'],
            40 => (object)['attribute' => 'E'],
            50 => (object)['attribute' => 'G']
        ];
    }

    private function getTestCases()
    {
        return [
            // Insert new element after the specified key
            [
                'new_item' => (object)['attribute' => 'C'],
                'attribute' => 'attribute',
                'target_key' => 30,
                'expected' => [
                    10 => (object)['attribute' => 'A'],
                    20 => (object)['attribute' => 'B'],
                    30 => (object)['attribute' => 'C'],
                    31 => (object)['attribute' => 'D'],
                    32 => (object)['attribute' => 'X'],
                    33 => (object)['attribute' => 'XXXII'],
                    40 => (object)['attribute' => 'E'],
                    50 => (object)['attribute' => 'G']
                ]
            ],
            // Insert new element after the specified key where the new element is lexically greater than the element after the key
            [
                'new_item' => (object)['attribute' => 'Y'],
                'attribute' => 'attribute',
                'target_key' => 30,

                /**
                 * This maybe confusing here. As we want to insert Y to the key 30
                 * we only check if the key D is greater or less than Y.
                 * In this case, D is less than Y, so Y should be inserted after D.
                 * But we do not care about the other items.
                 */
                'expected' => [
                    10 => (object)['attribute' => 'A'],
                    20 => (object)['attribute' => 'B'],
                    30 => (object)['attribute' => 'D'],
                    31 => (object)['attribute' => 'Y'],
                    32 => (object)['attribute' => 'X'],
                    33 => (object)['attribute' => 'XXXII'],
                    40 => (object)['attribute' => 'E'],
                    50 => (object)['attribute' => 'G']
                ]
            ],
            // Insert new element after the specified key where the new element is lexically smaller than the element after the key
            [
                'new_item' => (object)['attribute' => 'B'],
                'attribute' => 'attribute',
                'target_key' => 30,
                'expected' => [
                    10 => (object)['attribute' => 'A'],
                    20 => (object)['attribute' => 'B'],
                    30 => (object)['attribute' => 'B'],
                    31 => (object)['attribute' => 'D'],
                    32 => (object)['attribute' => 'X'],
                    33 => (object)['attribute' => 'XXXII'],
                    40 => (object)['attribute' => 'E'],
                    50 => (object)['attribute' => 'G']
                ]
            ],
            // Insert new element after the specified key where the key doesn't exist
            [
                'new_item' => (object)['attribute' => 'Z'],
                'attribute' => 'attribute',
                'target_key' => 35,
                'expected' => [
                    10 => (object)['attribute' => 'A'],
                    20 => (object)['attribute' => 'B'],
                    30 => (object)['attribute' => 'D'],
                    31 => (object)['attribute' => 'X'],
                    32 => (object)['attribute' => 'XXXII'],
                    35 => (object)['attribute' => 'Z'],
                    40 => (object)['attribute' => 'E'],
                    50 => (object)['attribute' => 'G']
                ]
            ],
        ];
    }
}
