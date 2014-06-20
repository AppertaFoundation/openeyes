<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * Validator for OpenEyes fuzzy dates
 */
class OEFuzzyDateValidator extends CValidator
{
	/**
	 * Validate a fuzzy date attribute
	 *
	 * Dates must be in the format (yyyy-mm-dd). mm and dd can be 00 to indicate the level of fuzziness of the date
	 *
	 * @param CModel $object the object being validated
	 * @param string $attribute the attribute being validated
	 */

	protected $object;
	protected $attribute;
	protected $year;
	protected $month;
	protected $day;

	public function validateAttribute($object, $attribute)
	{
		$dt = $object->$attribute;

		$this->year = (integer) substr($dt,0,4);
		$this->month = (integer) substr($dt,5,2);
		$this->day = (integer) substr($dt,8,2);
		$this->object = $object;
		$this->attribute = $attribute;


		if ($this->day > 0 && !$this->month > 0)  {
			$this->addError($object, $attribute, 'Month is required if day is provided');
		}

		if($this->month > 0 && (!$this->year > 0)) {
			$this->addError($object, $attribute, 'Year is required if month is provided');
		}

		if ($this->day > 0 && $this->month > 0 && $this->year > 0) {
			$this->validateCompleteDate();
		} else if ($this->month > 0 && $this->year > 0) {
			$this->validateFuzzyMonthYear();
		}	else if ($this->year > 0) {
			$this->validateFuzzyYear();
		}
	}

	protected function validateCompleteDate()
	{
		if (!checkdate($this->month, $this->day, $this->year)) {
			$this->addError($this->object, $this->attribute, 'This is not a valid date');
		}
	}

	protected function validateFuzzyMonthYear()
	{
		if ($this->month > 12) {
			$this->addError($this->object, $this->attribute, 'Invalid month value');
		}
	}

	protected function validateFuzzyYear()
	{
	}
}
