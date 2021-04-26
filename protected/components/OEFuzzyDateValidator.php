<?php
/**
 * OpenEyes.
 *
 * 
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Validator for OpenEyes fuzzy dates.
 */
class OEFuzzyDateValidator extends CValidator
{
    /**
     * Validate a fuzzy date attribute.
     *
     * Dates must be in the format (yyyy-mm-dd). mm and dd can be 00 to indicate the level of fuzziness of the date
     *
     * @param CModel $object    the object being validated
     * @param string $attribute the attribute being validated
     */
    protected $object;
    protected $attribute;
    protected int $year;
    protected int $month;
    protected int $day;

    public function validateAttribute($object, $attribute)
    {
        $dt = $object->$attribute;
        $dt = str_replace(' ', '-', $dt);
        $dt_separated = explode('-', $dt);
        list($this->year, $this->month, $this->day) = array((int)$dt_separated[0], array_key_exists(1, $dt_separated) ? (int)$dt_separated[1] : 0, array_key_exists(2, $dt_separated) ? (int)$dt_separated[2] : 0);
        if (isset($dt) && $dt !== ""  && (!is_numeric($this->year) || !is_numeric($this->month) || !is_numeric($this->day))) {
            $this->addError($object, $attribute, 'Date must be in the format YYYY-MM-DD, YYYY-MM or YYYY');
        }


        if ($this->year<13&&$this->day==0){
            $this->day = $this->month;
            $this->month = $this->year;
            $this->year = 0;
        }
        $this->object = $object;
        $this->attribute = $attribute;

        if ($this->day > 0 && !$this->month > 0) {
            $this->addError($object, $attribute, 'Month is required if day is provided');
        }

        if ($this->month > 0 && (!$this->year > 0)) {
            $this->addError($object, $attribute, 'Year is required if month is provided');
        }

        $this->validateFuzzyYear();
        if ($this->day > 0 && $this->month > 0 && $this->year > 0) {
            $this->validateCompleteDate();
        } elseif ($this->month > 0 && $this->year > 0) {
            $this->validateFuzzyMonthYear();
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
        if ($this->year > 0 && $this->year < 1000) {
            $this->addError($this->object, $this->attribute, 'Invalid year format. You can enter date format as yyyy-mm-dd, or yyyy-mm or yyyy.');
        }
    }
}
