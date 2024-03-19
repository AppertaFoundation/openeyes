<?php
/**
 * (C) Apperta Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

trait HasFormAssertions
{
    protected function gatherCrawledInputs($result)
    {
        return array_reduce(
            array_merge(
                $result->filter('input[name]')->extract(['name', 'value']),
                $result->filter('textarea[name]')->extract(['name', '_text']),
                $this->gatherSelectInputs($result)
            ),
            static function ($gathered, $from) {
                $gathered[$from[0]][] = $from[1];

                return $gathered;
            },
            []
        );
    }

    protected function gatherSelectInputs($result)
    {
        return $result->filter('select[name]')->each(static function ($node, $index) {
            $value = $node->children('option[selected]')->extract(['value']);

            if (count($value) === 0) {
                $value = [''];
            }

            return array_merge($node->extract(['name']), $value);
        });
    }

    protected function assertFormFields($expected_inputs, $result)
    {
        $actual_inputs = $this->gatherCrawledInputs($result);

        foreach ($expected_inputs as $name => $assumed_value) {
            // TODO Double check and provide correct conversions for these two types of values if required.
            $assumed_value = ($assumed_value === null || is_array($assumed_value)) ? '' : $assumed_value;

            $this->assertTrue(array_key_exists($name, $actual_inputs), 'Expected form to have an input with the name ' . $name);
            $this->assertContains($assumed_value, $actual_inputs[$name], 'Expected form to contain value of `' . $assumed_value . '`, which is not present in `' . implode(', ', $actual_inputs[$name]) . '`, for ' . $name);
        }
    }

    protected function flattenFormInputNames(array $form_data): array
    {
        $flattened = [];

        foreach ($form_data as $key => $value) {
            if (is_array($value) && !empty($value)) {
                foreach ($this->flattenFormInputNames($value) as $sub_key => $sub_value) {
                    $flattened[$key . '[' . $sub_key . ']'] = $sub_value;
                }
            } else {
                $flattened[$key] = $value;
            }
        }

        return $flattened;
    }
}
