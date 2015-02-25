<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * Validator to make a field conditionally required based on the value of another field
 */
class RequiredIfFieldValidator extends CValidator
{
	public $field;
	public $value;

	public $relation = null;

	public $message = "{attribute} cannot be blank";

	public function validateAttribute($object, $attribute)
	{
		if ($this->relation) {
			$related = $object->{$this->relation};
			if (is_array($related)) {
				$required = false;
				foreach ($related as $item) {
					if ($item->{$this->field} == $this->value) {
						$required = true;
						break;
					}
				}
			} else {
				$required = ($this->expand($related,$this->field) == $this->value);
			}
		} else {
			$required = ($this->expand($object,$this->field) == $this->value);
		}

		if ($required && $this->isEmpty($object->$attribute,true)) {
			$this->addError($object, $attribute, $this->message);
		}
	}

	public function expand($object, $attribute)
	{
		if ($pos = strpos($attribute,'.')) {
			$first = substr($attribute,0,$pos);
			$second = substr($attribute,$pos+1,strlen($attribute));

			return $this->expand($object->$first, $second);
		}

		return $object->$attribute;
	}
}
