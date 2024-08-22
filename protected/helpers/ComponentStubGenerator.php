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
    protected static $properties_cache = [];

    /**
     * @param string $class_name
     * @param array $properties
     * @return PHPUnit\Framework\MockObject\MockObject
     * @throws ReflectionException
     */
    public static function generate($class_name, array $properties = [])
    {
        $stub = (new \PHPUnit\Framework\MockObject\Generator())->getMock($class_name, array(), array(), '', false, false, true, false);

        self::propertiesSetAndMatch($stub, $properties);

        return $stub;
    }

    /**
     * Iterates through properties to set values on the stub that exists on the stub class. If $force is true,
     * will set the value regardless of whether or not the property exists on the element.
     *
     * @param $stub \PHPUnit\Framework\MockObject\MockObject
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

        static::$properties_cache[spl_object_id($stub)] = $properties;
        if (method_exists($stub, '__set')) {
            $stub->method('__set')
                ->willReturnCallback(function (...$args) use ($stub) {
                    static::$properties_cache[spl_object_id($stub)][$args[0]] = $args[1];
                });
        }

        if (method_exists($stub, '__isset')) {
            $stub->method('__isset')
                ->willReturnCallback(function (...$args) use ($stub) {
                    return isset(static::$properties_cache[spl_object_id($stub)][$args[0]]);
                });
        }

        if (method_exists($stub, '__get')) {
            $stub->method('__get')
                ->willReturnCallback(function (...$args) use ($stub) {
                    return static::$properties_cache[spl_object_id($stub)][$args[0]] ?? null;
                });
        }
    }
}
