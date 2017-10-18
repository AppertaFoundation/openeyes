<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Generates test stubs for Yii components.
 *
 * Yii components' properties can come from many sources and be
 * accessed either directly or via get method calls.  This class,
 * given a set of property values, will generate a stub that will
 * return those values under any circumstances.
 */
class ComponentStubGenerator
{
    /**
     * @param string $class_name
     * @param array  $properties
     */
    public static function generate($class_name, array $properties = array())
    {
        $stub = PHPUnit_Framework_MockObject_Generator::getMock($class_name, array(), array(), '', false, false, true, false);

        self::propertiesSetAndMatch($stub, $properties);

        return $stub;
    }

    /**
     * iteratest through properties to set values on the stub that exists on the stub class. If $force is true,
     * will set the value regardless of whether or not the property exists on the element.
     *
     * @param $stub
     * @param array $properties
     * @param bool  $force
     */
    public static function propertiesSetAndMatch($stub, array $properties = array(), $force = false)
    {
        $rf_obj = new ReflectionObject($stub);
        foreach ($properties as $name => $value) {
            if ($force || $rf_obj->hasProperty($name)) {
                $stub->$name = $value;
            }
        }

        $stub->__phpunit_getInvocationMocker()->addMatcher(new ComponentStubMatcher($properties));
    }
}

class ComponentStubMatcher implements PHPUnit_Framework_MockObject_Matcher_Invocation
{
    protected $properties;

    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }

    public function toString()
    {
        echo 'Component stub matcher';
    }

    public function matches(PHPUnit_Framework_MockObject_Invocation $invocation)
    {
        if ($invocation->methodName == '__set') {
            $this->properties[$invocation->parameters[0]] = $invocation->parameters[1];
        } elseif ($invocation->methodName == '__get' || $invocation->methodName == '__isset') {
            return array_key_exists($invocation->parameters[0], $this->properties);
        } else {
            return $this->methodNameToProperty($invocation, true);
        }
    }

    public function invoked(PHPUnit_Framework_MockObject_Invocation $invocation)
    {
        if ($invocation->methodName == '__get') {
            return $this->properties[$invocation->parameters[0]];
        } elseif ($invocation->methodName == '__isset') {
            return isset($this->properties[$invocation->parameters[0]]);
        } else {
            return $this->methodNameToProperty($invocation, false);
        }
    }

    public function verify()
    {
    }

    protected function methodNameToProperty(PHPUnit_Framework_MockObject_Invocation $invocation, $return_bool)
    {
        if (preg_match('/^get(.*)$/', $invocation->methodName, $matches) && count($invocation->parameters) == 0) {
            $search = strtolower($matches[1]);
            foreach ($this->properties as $name => $value) {
                if (strtolower($name) == $search) {
                    return $return_bool ? true : $value;
                }
            }
        }

        return $return_bool ? false : null;
    }
}
