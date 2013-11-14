<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * Generates test stubs for Yii components
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
	 */
	static public function generate($class_name, array $properties)
	{
		$stub = PHPUnit_Framework_MockObject_Generator::getMock($class_name, array(), array(), '', false);

		$value_map = array();
		$rf_obj = new ReflectionObject($stub);
		foreach ($properties as $name => $value) {
			if ($rf_obj->hasProperty($name)) {
				$stub->$name = $value;
			} else {
				$value_map[] = array($name, $value);
			}

			$get_method = "get{$name}";
			if ($rf_obj->hasMethod($get_method) && $rf_obj->getMethod($get_method)->getNumberOfParameters() == 0) {
				$stub->expects(new PHPUnit_Framework_MockObject_Matcher_AnyInvokedCount)
					->method($get_method)->will(new PHPUnit_Framework_MockObject_Stub_Return($value));
			}
		}

		$stub->expects(new PHPUnit_Framework_MockObject_Matcher_AnyInvokedCount)
			->method('__get')->will(new PHPUnit_Framework_MockObject_Stub_ReturnValueMap($value_map));

		return $stub;
	}
}
