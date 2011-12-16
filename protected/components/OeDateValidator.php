<?php
/**
 * _____________________________________________________________________________
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 * _____________________________________________________________________________
 * http://www.openeyes.org.uk			 info@openeyes.org.uk
 * --
 */

/**
 * Validator for OpenEyes standard dates
 */
class OeDateValidator extends CValidator {
	/**
	 * Validate date attribute
	 * 
	 * Dates must be in the format d-MMM-yyyy. Blank strings will also pass so that this validator can be combined with required without generating two errors.
	 * @param CModel $object the object being validated
	 * @param string $attribute the attribute being validated
	 */
	protected function validateAttribute($object, $attribute) {
		$value = $object->$attribute;
		$valid = false;
		
		// Allow empty string
		if($this->isEmpty($value)) {
			$valid = true;
		}
		
		if(preg_match('/^\d{1,2}-\w{3}-\d{4}$/', $value) && strtotime($value)) {
			$valid = true;
		}
		
		if(!$valid) {
			$message = ($this->message !== null) ? $this->message : Yii::t('yii','{attribute} is not a valid date');
			$this->addError($object, $attribute, $message);
		}
	}

}