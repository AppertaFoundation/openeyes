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
class CheckDigitGenerator
{
    private $string_for_conversion;
    private $salt;

    /*
     * @param string $string_for_conversion
     * @param string $salt
     */
    public function __construct($string_for_conversion, $salt)
    {
        $this->string_for_conversion = $string_for_conversion;
        $this->salt = $salt;
    }

    /**
     * @param string $checkdigit
     */
    public function generateCheckDigit()
    {
        $sum = 0;
        $string = $this->string_for_conversion.$this->salt;
        $string = strrev($string);
        for ($i = 0; $i < strlen($string); ++$i) {
            $char = str_replace(range('A', 'Z'), range('1', '26'), $string[$i]);

            //Prior to PHP 7.1 str_split would return false on a non-numeric value, and so hyphens in the patient's DOB and lowercase characters and symbols in portal client_id would be coerced to zeros.
            //However at time of writing, this throws an error. The following reintroduces the original behaviour.
            if(!is_numeric($char)) {
                $char = 0;
            }

            $sum += array_sum(str_split(($char * pow(2, (($i + 1) % 2)))));
        }

        return ($sum * 9) % 10;
    }
}
