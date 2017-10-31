<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Templates for generating or matching data structures.
 */
class DataTemplate
{
    /**
     * @param string $filename
     * @param array  $consts
     *
     * @return DataTemplate
     */
    public static function fromJsonFile($filename, array $consts = array())
    {
        $rawTemplate = json_decode(file_get_contents($filename));
        if (is_null($rawTemplate)) {
            throw new Exception("Failed to parse JSON file '{$filename}'");
        }
        if (is_scalar($rawTemplate)) {
            throw new Exception("Data templates must be objects or arrays, '{$filename}' is scalar");
        }

        return self::preprocess($rawTemplate, $consts);
    }

    /**
     * @param object|array $rawTemplate
     * @param array        $consts
     *
     * @return DataTemplate
     */
    protected static function preprocess($rawTemplate, array $consts)
    {
        $template = array();

        foreach ($rawTemplate as $key => $value) {
            switch (gettype($value)) {
                case 'object':
                    if (($directive = self::preprocessDirective($value, $consts))) {
                        $template[$key] = $directive;
                    } else {
                        $template[$key] = self::preprocess($value, $consts);
                    }
                    break;
                case 'array':
                    $template[$key] = self::preprocess($value, $consts);
                    break;
                case 'string':
                    $template[$key] = self::replaceConsts($value, $consts);
                    break;
                default:
                    $template[$key] = $value;
            }
        }

        if (is_object($rawTemplate)) {
            return new DataTemplateObject($template);
        } else {
            return new DataTemplateArray($template);
        }
    }

    /**
     * @param object $object
     * @param array  $consts
     *
     * @return DataTemplateDirective|null
     */
    protected static function preprocessDirective($object, array $consts)
    {
        $vars = get_object_vars($object);
        if (count($vars) != 1) {
            return;
        }

        $keys = array_keys($vars);
        $key = $keys[0];
        if (!preg_match('/^:(\w+)$/', $key, $m)) {
            return;
        }

        $class = 'DataTemplateDirective'.lcfirst($m[1]);
        if (!class_exists($class)) {
            return;
        }

        $value = $vars[$key];
        if (is_string($value)) {
            $value = self::replaceConsts($value, $consts);
        }

        return new $class($value);
    }

    /**
     * @param string $value
     * @param array  $consts
     *
     * @return string
     */
    protected static function replaceConsts($value, array $consts)
    {
        if (preg_match('/^{{(\w+)}}$/', $value, $m)) {
            if (!array_key_exists($m[1], $consts)) {
                throw new Exception("Missing template constant: '{$m[1]}'");
            }

            return $consts[$m[1]];
        }

        return $value;
    }
}

abstract class DataTemplateComponent
{
    protected $value;

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Generate a data structure from the template using the values supplied.
     *
     * @param array $values
     *
     * @return mixed
     */
    abstract public function generate(array $values);

    /**
     * Match the template against a structure and extract values.
     *
     * @param mixed $structure
     * @param array &$warnings Populated with warnings to debug match failures
     *
     * @return array|null Extracted values, or null if matching failed
     */
    abstract public function match($structure, &$warnings = array());
}

class DataTemplateObject extends DataTemplateComponent
{
    public function generate(array $values)
    {
        $result = new StdClass();

        $has_props = false;
        foreach ($this->value as $key => $value) {
            if ($value instanceof DataTemplateComponent) {
                $value = $value->generate($values);
            }
            if (!is_null($value)) {
                $result->$key = $value;
                $has_props = true;
            }
        }

        return $has_props ? $result : null;
    }

    public function match($structure, &$warnings = array())
    {
        if (is_null($structure)) {
            return array();
        }

        if (!is_object($structure)) {
            $warnings[] = 'expected object, found '.gettype($structure);

            return;
        }

        $values = array();

        foreach ($this->value as $key => $value) {
            if ($value instanceof DataTemplateComponent) {
                $new_values = $value->match(@$structure->$key, $new_warnings);
                if (is_null($new_values)) {
                    foreach ($new_warnings as $warning) {
                        $warnings[] = "{$key}: $warning";
                    }

                    return;
                }
                $values += $new_values;
            }
        }

        return $values;
    }
}

class DataTemplateArray extends DataTemplateComponent
{
    public function generate(array $values)
    {
        $result = array();

        foreach ($this->value as $value) {
            if ($value instanceof DataTemplateComponent) {
                $value = $value->generate($values);
            }
            if (!is_null($value)) {
                $result[] = $value;
            }
        }

        return $result;
    }

    public function match($structure, &$warnings = array())
    {
        if (is_null($structure)) {
            return array();
        }

        if (!is_array($structure)) {
            $warnings[] = 'expected array, found '.gettype($structure);

            return;
        }

        $values = array();

        foreach ($this->value as $value) {
            foreach ($structure as $n => $item) {
                $found = false;
                if ($value instanceof DataTemplateComponent) {
                    $new_values = $value->match($item);
                    if (!is_null($new_values)) {
                        $values += $new_values;
                        $found = true;
                    }
                } else {
                    if ($value == $item) {
                        $found = true;
                    }
                }
                if ($found) {
                    unset($structure[$n]);
                    break;
                }
            }
        }

        return $values;
    }
}

class DataTemplateDirectiveMatch extends DataTemplateComponent
{
    public function generate(array $values)
    {
        return $this->value;
    }

    public function match($structure, &$warnings = array())
    {
        if ($structure == $this->value) {
            return array();
        } else {
            if (is_null($structure)) {
                $warnings[] = "missing, expected '{$this->value}'";
            } else {
                $warnings[] = "match failed: '{$structure}' != '{$this->value}'";
            }

            return;
        }
    }
}

class DataTemplateDirectiveSubst extends DataTemplateComponent
{
    public function generate(array $values)
    {
        return @$values[$this->value];
    }

    public function match($structure, &$warnings = array())
    {
        return is_null($structure) ? array() :  array($this->value => $structure);
    }
}
