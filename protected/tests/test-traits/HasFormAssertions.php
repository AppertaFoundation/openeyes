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

use Symfony\Component\DomCrawler\Crawler;

trait HasFormAssertions
{
    /**
     * assertFormFields
     *
     * Asserts that the crawled $result from e.g. an ApplicationResponseWrapper contains the form inputs and corresponding values
     * expressed in $expected_inputs. It matches the inputs based on their names, with $expected_inputs being fed through
     * HasFormAssertions::flattenFormInputNames first to convert them into the representation used in the crawled DOM.
     *
     * The values are then compared with assertContainsEquals, which uses a looser equality in line with the stringly typed
     * nature of the input values in the DOM.
     *
     * @param array $expected_inputs
     * @param Crawler $result
     * @param ?string $message
     */
    protected function assertFormFields(array $expected_inputs, Crawler $result, ?string $message = null)
    {
        $message = $message ? $message . "\n" : '';

        $expected_inputs = $this->flattenFormInputNames($expected_inputs);
        $actual_inputs = $this->gatherCrawledInputs($result, $message);

        $constraint = $this->callback(
            function ($parameters) use ($message) {
                [$expected_inputs, $actual_inputs] = $parameters;

                foreach ($expected_inputs as $name => $assumed_value) {
                    /*
                     * Array values are destructured inside flattenFormInputNames, with the exception of empty arrays
                     * Empty arrays are treated the same as null: they are represented as an empty string
                     */
                    $assumed_value = ($assumed_value === null || is_array($assumed_value)) ? '' : $assumed_value;

                    if (!array_key_exists($name, $actual_inputs)) {
                        $this->fail($message . 'Expected form to have an input with the name ' . $name);
                    } elseif (!in_array($assumed_value, $actual_inputs[$name])) {
                        $this->fail($message . 'Expected form to contain value of `' . $assumed_value . '`, which is not present in `' . implode(', ', $actual_inputs[$name]) . '`, for ' . $name);
                    }
                }

                return true;
            }
        );

        $this->assertThat([$expected_inputs, $actual_inputs], $constraint);
    }

    protected function flattenFormInputNames(array $form_data, bool $top_level = true): array
    {
        $flattened = [];

        foreach ($form_data as $key => $value) {
            if (!$top_level) {
                $key = '[' . $key . ']';
            }

            if (is_array($value) && !empty($value)) {
                foreach ($this->flattenFormInputNames($value, false) as $sub_key => $sub_value) {
                    $flattened[$key . $sub_key] = $sub_value;
                }
            } else {
                $flattened[$key] = $value;
            }
        }

        return $flattened;
    }

    protected function gatherCrawledInputs($result, $message = '')
    {
        return array_reduce(
            array_merge(
                $result->filter('input[name]:not([type="radio"]):not([type="checkbox"])')->extract(['name', 'value']),
                $result->filter('textarea[name]')->extract(['name', '_text']),
                $this->gatherSelectInputs($result, $message),
                $this->gatherRadioButtonInputs($result, $message),
                $this->gatherCheckboxInputs($result, $message)
            ),
            static function ($gathered, $from) {
                $gathered[$from[0]][] = $from[1];

                return $gathered;
            },
            []
        );
    }

    protected function gatherSelectInputs($result, $message = '')
    {
        $unflattened = $result->filter('select[name]')->each(function ($node, $index) {
            $name = $node->extract(['name'])[0];
            $multiple = !empty($node->extract(['multiple'])[0]);
            $values = $node->children('option[selected]')->extract(['value']);

            if (count($values) === 0) {
                $values = [''];
            } elseif (count($values) > 1) {
                $this->assertTrue($multiple, $message . 'Multiple options selected for ' . $name . ' <select> which does not have a multiple attribute');
            }

            return array_map(
                static function ($value) use ($name) {
                    return [$name, $value];
                },
                $values
            );
        });

        return array_reduce($unflattened, 'array_merge', []);
    }

    protected function gatherRadioButtonInputs($result, $message = '')
    {
        return array_values(array_reduce(
            $result->filter('input[type="radio"][name]')->extract(['name', 'value', 'checked']),
            static function ($gathered, $input) {
                if (!isset($gathered[$input[0]])) {
                    $gathered[$input[0]] = [$input[0], null];
                }

                if ($input[2]) {
                    $gathered[$input[0]] = [$input[0], $input[1]];
                }

                return $gathered;
            },
            []
        ));
    }

    protected function gatherCheckboxInputs($result, $message = '')
    {
        return array_reduce(
            $result->filter('input[type="checkbox"][name]')->extract(['name', 'value', 'checked']),
            static function ($gathered, $input) {
                if ($input[2]) {
                    $gathered[] = [$input[0], $input[1]];
                } else {
                    $gathered[] = [$input[0], null];
                }

                return $gathered;
            },
            []
        );
    }
}
