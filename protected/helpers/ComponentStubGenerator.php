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
     * @param array $properties
     * @return PHPUnit\Framework\MockObject\MockObject
     * @throws ReflectionException
     */
    public static function generate($class_name, array $properties = array())
    {
        $stub = (new PHPUnit\Framework\MockObject\Generator())->getMock($class_name, array(), array(), '', false, false, true, false);


        self::propertiesSetAndMatch($stub, $properties);

        return $stub;
    }

    /**
     * Iterates through properties to set values on the stub that exists on the stub class. If $force is true,
     * will set the value regardless of whether or not the property exists on the element.
     *
     * @param $stub PHPUnit\Framework\MockObject\MockObject
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

class ComponentStubMatcher implements PHPUnit\Framework\MockObject\Matcher\Invocation
{
    protected array $properties;

    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }

    public function toString() : string
    {
        return 'Component stub matcher';
    }

    public function hasMatchers()
    {
        return true;
    }

    /**
     * @param PHPUnit\Framework\MockObject\Invocation $invocation
     * @return bool|mixed|void
     */
    public function matches(PHPUnit\Framework\MockObject\Invocation $invocation)
    {
        if ($invocation->getMethodName() === '__set') {
            $this->properties[$invocation->getParameters()[0]] = $invocation->getParameters()[1];
            return;
        }

        if ($invocation->getMethodName() === '__get' || $invocation->getMethodName() === '__isset') {
            return array_key_exists($invocation->getParameters()[0], $this->properties);
        }

        return $this->methodNameToProperty($invocation, true);
    }

    public function invoked(PHPUnit\Framework\MockObject\Invocation $invocation)
    {
        if ($invocation->getMethodName() === '__get') {
            return $this->properties[$invocation->getParameters()[0]];
        }

        if ($invocation->getMethodName() === '__isset') {
            return isset($this->properties[$invocation->getParameters()[0]]);
        }

        return $this->methodNameToProperty($invocation, false);
    }

    public function verify()
    {
    }

    protected function methodNameToProperty(PHPUnit\Framework\MockObject\Invocation $invocation, $return_bool)
    {
        if (preg_match('/^get(.*)$/', $invocation->getMethodName(), $matches) && count($invocation->getParameters()) === 0) {
            $search = strtolower($matches[1]);
            foreach ($this->properties as $name => $value) {
                if (strtolower($name) === $search) {
                    return $return_bool ? true : $value;
                }
            }
        }

        return $return_bool ? false : null;
    }
}
