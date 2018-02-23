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
class OEFuzzyDateValidatorNotFuture extends OEFuzzyDateValidator
{
    protected function validateCompleteDate()
    {
        if (strtotime($this->object->{$this->attribute}) > time()) {
            $this->addError($this->object, $this->attribute, 'The date cannot be in the future');
        }
        parent::validateCompleteDate();
    }

    protected function validateFuzzyYear()
    {
        if ($this->year > 0 && $this->year < 1000) {
            $this->addError($this->object, $this->attribute, 'Invalid year format. You can enter date format as yyyy-mm-dd, or yyyy-mm or yyyy');
        }
        if ($this->year > date('Y')) {
            $this->addError($this->object, $this->attribute, 'The date cannot be in the future');
        }
    }

    protected function validateFuzzyMonthYear()
    {
        if (strtotime($this->year.'-'.$this->month.'-01') > time()) {
            $this->addError($this->object, $this->attribute, 'The date cannot be in the future');
        }
        parent::validateFuzzyMonthYear();
    }
}
