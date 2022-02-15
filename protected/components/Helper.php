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
 * Helper functions.
 */
class Helper
{
    public const NHS_DATE_FORMAT = 'j M Y';
    public const NHS_DATE_FORMAT_JS = 'd M yy';
    public const NHS_DATE_REGEX = '/^\d{1,2} \w{3} \d{4}$/';
    public const NHS_DATE_EXAMPLE = '5 Dec 2011';
    public const SHORT_DATE_FORMAT = 'd/m/y';
    public const EPOCHDAY = 86400000;
    public const EPOCHWEEK = 604800000;
    public const EPOCHMONTH = 2629743000;
    public const FULL_YEAR_FORMAT = 'd/m/Y';


    /**
     * Convert NHS dates to MySQL format.
     * Strings that do not match the NHS format are returned unchanged or are not valid dates.
     *
     * @param string|array $data   Data containing one or more NHS dates
     * @param array        $fields Fields (keys) to convert (optional, if empty then all fields are checked for dates)
     *
     * @return string|array
     */
    public static function convertNHS2MySQL($data, $fields = null)
    {
        if ($is_string = !is_array($data)) {
            $data = array('dummy' => $data);
        }
        $list = ($fields) ? $fields : array_keys($data);
        foreach ($list as $key) {
            if (isset($data[$key])) {
                // traverse down arrays to convert nested structures
                if (is_array($data[$key])) {
                    $data[$key] = self::convertNHS2MySQL($data[$key], $fields);
                } elseif (is_string($data[$key]) && preg_match(self::NHS_DATE_REGEX, $data[$key])) {
                    $check_date = date_parse_from_format('j M Y', $data[$key]);
                    if (checkdate($check_date['month'], $check_date['day'], $check_date['year'])) {
                        $data[$key] = date('Y-m-d', strtotime($data[$key]));
                    }
                }
            }
        }
        if ($is_string) {
            return $data['dummy'];
        } else {
            return $data;
        }
    }

    /**
     * Convert MySQL date(time) value to NHS format.
     * Strings that do not match MySQL date(time) format return $empty_string.
     *
     * @param string $value
     * @param string $empty_string
     *
     * @return string
     */
    public static function convertMySQL2NHS($value, $empty_string = '-')
    {
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $value) && $value != '0000-00-00 00:00:00' && $value != '0000-00-00') {
            return self::convertDate2NHS($value, $empty_string);
        } else {
            return $empty_string;
        }
    }

    public static function convertMySQL2HTML($value, $empty_string = '-')
    {
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $value) && $value != '0000-00-00 00:00:00' && $value != '0000-00-00') {
            return self::convertDate2HTML($value, $empty_string);
        } else {
            return $empty_string;
        }
    }

    /**
     * Convert date(time) value to NHS format.
     * Strings that do not return a valid date return $empty_string.
     *
     * @param string $value
     * @param string $empty_string
     *
     * @return string
     */
    public static function convertDate2NHS($value, $empty_string = '-')
    {
        $time = strtotime($value);
        if ($time !== false) {
            return date(self::NHS_DATE_FORMAT, $time);
        } else {
            return $empty_string;
        }
    }

    public static function convertDate2Short($value, $empty_string = '-')
    {
        $time = strtotime($value);
        if ($time !== false) {
            return date(self::SHORT_DATE_FORMAT, $time);
        } else {
            return $empty_string;
        }
    }

	public static function convertDate2FullYear($value, $empty_string = '-')
	{
		$time = strtotime($value);
		if ($time !== false) {
			return date(self::FULL_YEAR_FORMAT, $time);
		}

		return $empty_string;
	}

    public static function convertDate2HTML($value, $empty_string = '-')
    {
        $time = strtotime($value);
        if ($time !== false) {
            return '<span class="day">'.date('j', $time).'</span><span class="mth">'.date('M', $time).'</span><span class="yr">'.date('Y', $time).'</span>';
        } else {
            return $empty_string;
        }
    }

    /**
     * Generates the string representation of a fuzzy date (fuzzy dates are strings of the format
     * yyyy-mm-dd, where mm and dd can be 00 to indicate not being set).
     *
     * @param string $value The fuzzy date to convert
     * @return string The output HTML
     */
    public static function convertFuzzyDate2HTML($value)
    {
        $year = (integer)substr($value, 0, 4) ?: '';
        $monthIndex = (integer)substr($value, 5, 2);
        $mon = $monthIndex !== 0 ? DateTime::createFromFormat('!m', $monthIndex)->format('M') : '';
        $day = (integer)substr($value, 8, 2) ?: '';

        return "<span class='day'>$day </span><span class='mth'>$mon </span><span class='yr'>$year</span>";
    }

    /**
     * Convert mysql format datetime to JS timestamp (milliseconds since unix epoch).
     *
     * @param string $value
     *
     * @return int
     */
    public static function mysqlDate2JsTimestamp($value)
    {
        $time = strtotime($value);

        return $time !== false ? $time * 1000 : null;
    }

    /**
     * Calculate age from dob.
     *
     * If date of death provided, then returns age at point of death
     *
     * @param string $dob
     * @param string $date_of_death
     * @param string $check_date Optional date to check age at (default is today)
     *
     * @return string $age
     * @throws Exception
     */
    public static function getAge($dob, $date_of_death = null, $check_date = null)
    {
        if (!$dob) {
            return 'Unknown';
        }

        $dob_datetime = new DateTime($dob);
        $check_datetime = new DateTime($check_date);

        if ($date_of_death) {
            $dod_datetime = new DateTime($date_of_death);
            if ($check_datetime->diff($dod_datetime)->invert) {
                $check_datetime = $dod_datetime;
            }
        }

        return $dob_datetime->diff($check_datetime)->y;
    }

    /**
     * Given a dob and an age (in years) returns the date at which the person would reach the given age.
     * If given a date of death, and they will never reach the age, returns null.
     *
     * @param $dob
     * @param $age
     * @param null $date_of_death
     *
     * @return null|string
     * @throws Exception
     */
    public static function getDateForAge($dob, $age, $date_of_death = null)
    {
        if (!$dob) {
            return null;
        }

        $dob_datetime = new DateTime($dob);
        $age_date = $dob_datetime->add(new DateInterval('P'.$age.'Y'));

        if ($date_of_death) {
            $dod_datetime = new DateTime($date_of_death);
            if ($dod_datetime < $age_date) {
                return null;
            }
        }

        return $age_date->format('Y-m-d');
    }

    public static function getMonthText($month, $long = false)
    {
        return date($long ? 'F' : 'M', mktime(0, 0, 0, $month, 1, date('Y')));
    }

    public static function padFuzzyDate($day, $month, $year)
    {
        return str_pad(@$day, 4, '0', STR_PAD_LEFT).'-'.str_pad(@$month, 2, '0', STR_PAD_LEFT).'-'.str_pad(@$year, 2, '0', STR_PAD_LEFT);
    }

    /**
     * generate string representation of a fuzzy date (fuzzy dates are strings of the format
     * yyyy-mm-dd, where mm and dd can be 00 to indicate not being set).
     *
     * @param string $value
     *
     * @return string
     */
    public static function formatFuzzyDate($value)
    {
        $year = (integer) substr($value, 0, 4);
        $mon = (integer) substr($value, 5, 2);
        $day = (integer) substr($value, 8, 2);

        if ($year && $mon && $day) {
            return self::convertMySQL2NHS($value);
        }

        if ($year && $mon) {
            return date('M Y', strtotime($year.'-'.$mon.'-01 00:00:00'));
        }

        if ($year) {
            return (string) $year;
        }

        return 'Undated';
    }

    /**
     * Formats a fuzzy day MONTH year format string to styled HTML
     *
     * @param string $dateStr the date to format (e.g. 01 Apr 1979)
     *
     * @return string
     */
    public static function oeDateAsStr($dateStr){
        if ($dateStr === "" || $dateStr === "Undated") return $dateStr;

        $arr = explode(' ',$dateStr);
        $day = '';
        $mth = '';
        $yr = '';
        switch (sizeof($arr)) {
            case 1:
                $yr = $arr[0];
                break;
            case 2:
                $mth = $arr[0];
                $yr = $arr[1];
                break;
            case 3:
                $day = $arr[0];
                $mth = $arr[1];
                $yr = $arr[2];
                break;
        }

        return '<span class="oe-date"><span class="day">'.$day.'</span><span class="mth">'.$mth.'</span><span class="yr">'.$yr.'</span></span>';
    }

    /**
     * generate string representation of timestamp for the database.
     *
     * @param int $timestamp
     *
     * @return string
     */
    public static function timestampToDB($timestamp)
    {
        return date('Y-m-d H:i:s', $timestamp);
    }

    public static function getWeekdayText($weekday)
    {
        switch ($weekday) {
            case 1: return 'Monday';
            case 2: return 'Tuesday';
            case 3: return 'Wednesday';
            case 4: return 'Thursday';
            case 5: return 'Friday';
            case 6: return 'Saturday';
            case 7: return 'Sunday';
        }
        return null;
    }

    /**
     * convert string of format n[units] to bytes
     * units can be one of B, KB, MB, GB, TB, PB, EB, ZB or YB (case-insensitive).
     *
     * @param $val
     *
     * @return mixed
     */
    public static function convertToBytes($val)
    {
        $units = array('B' => 0, 'KB' => 1, 'MB' => 2, 'GB' => 3, 'TB' => 4, 'PB' => 5, 'EB' => 6, 'ZB' => 7, 'YB' => 8);
        $regexp = implode('|', array_keys($units));
        if (intval($val) === $val) {
            // no units, so simply return
            return $val;
        }

        if (preg_match('/^([\d.]+)('.$regexp.')$/', strtoupper($val), $matches)) {
            return $matches[1] * pow(1024, $units[$matches[2]]);
        }
        return null;
    }

    /**
     * Generate a version 4 UUID.
     *
     * @return string
     */
    public static function generateUuid()
    {
        return sprintf(
            '%04x%04x-%04x-4%03x-%01x%03x-%04x%04x%04x',
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 4095),
            mt_rand(8, 11), mt_rand(0, 4095),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535)
        );
    }

    /**
     * Extract values from a list of objects or arrays using {@link CHtml value}.
     *
     * @param object[]|array[] $objects
     * @param string attribute
     * @param mixed $default
     *
     * @return array
     */
    public static function extractValues(array $objects, $attribute, $default = null)
    {
        $values = array();
        foreach ($objects as $object) {
            $values[] = CHtml::value($object, $attribute, $default);
        }

        return $values;
    }

    /**
     * Format a list of strings with comma separators and a final 'and'.
     *
     * @param array $items
     *
     * @return string
     */
    public static function formatList(array $items)
    {
        switch (count($items)) {
            case 0:
                return '';
                break;
            case 1:
                return reset($items);
                break;
            default:
                $last = array_pop($items);

                return implode(', ', $items).' and '.$last;
        }
    }

    /**
     * @param $instance
     * @return string
     * @throws ReflectionException
     */
    public static function getNSShortname($instance)
    {
        $r = new ReflectionClass($instance);

        return $r->getShortName();
    }

    /**
     * @param $string
     * @param int $line_count
     * @param int $protect
     * @param string $delimiter
     * @param string $joiner

     * @return string $result
     * @throws InvalidArgumentException
     */
    public static function lineLimit($string, $line_count, $protect = 0, $delimiter = "\n", $joiner = ', ')
    {
        if ($protect >= $line_count) {
            throw new InvalidArgumentException("protect must be less than the line_count");
        }

        $bits = explode($delimiter, $string);
        if (count($bits) <= $line_count) {
            return implode($delimiter, $bits);
        }
        $protected = array();
        for ($i = 0; $i < $protect; $i++) {
            array_unshift($protected, array_pop($bits));
        }

        while ((count($bits) + $protect) > $line_count) {
            $last = array_pop($bits);
            $bits[count($bits)-1] .= $joiner . $last;
        }

        return implode($delimiter, array_merge($bits, $protected));
    }

    /**
     * Find an element in a nested hasharray $haystack with a string $needle separated by $delimiter
     *
     * @param array $haystack
     * @param $needle
     * @param string $delimiter
     * @return null
     */
    public static function elementFinder($needle, $haystack, $delimiter = '.')
    {
        $break = strpos($needle, $delimiter);
        $key = $break === false ? $needle : substr($needle, 0, $break);
        if (isset($haystack[$key])) {
            if ($key == $needle) {
                return $haystack[$key];
            }
            return static::elementFinder(substr($needle, $break + 1), $haystack[$key]);
        } else {
            return null;
        }
    }

    /**
     * Extracts what should be the md5 from the end of a string to verify against the remainder
     * Returns the verified data if the md5 is correct, null otherwise.
     *
     * @param $data
     * @return null|string
     */
    public static function md5Verified($data)
    {
        $actual_data = substr($data, 0, -32);
        $checksum = substr($data, -32);
        if (md5($actual_data) === $checksum) {
            return $actual_data;
        }
        return null;

    }

    /**
     * Check if the given DateTime string is valid
     *
     * @param string $date_time
     * @return bool True if parsing. False if parsing fails.
     */
    public static function isValidDateTime($date_time)
    {
        //CDateTimeParser does not handle 1 Jan 2017 format
        //CDateTimeParser::parse($date_time, self::NHS_DATE_FORMAT) ? true : false;
        return strtotime($date_time);
    }

    /**
     * @param $date
     * @param $datetime
     * @return string
     * @throws Exception
     */
    public static function combineMySQLDateAndDateTime($date, $datetime)
    {
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $date)
            && preg_match('/\d{2}:\d{2}:\d{2}$/', $datetime)
        ) {
            return substr($date, 0, 10) . ' ' . substr($datetime, -8);
        }
        throw new Exception('Not a valid date and/or time string');
    }

    /**
     * Fetch Eye id from array - based on array keys: left_eye, right_eye, na_eye
     *
     * @param array $data
     * @return int|null
     */
    public static function getEyeIdFromArray(array $data)
    {
        $eye_id = null;
        $left_eye = Helper::elementFinder('left_eye', $data);
        $right_eye = Helper::elementFinder('right_eye', $data);
        $na_eye = Helper::elementFinder('na_eye', $data);

        if ($left_eye && $right_eye) {
            $eye_id = Eye::BOTH;
        } elseif ($left_eye) {
            $eye_id = Eye::LEFT;
        } elseif ($right_eye) {
            $eye_id = Eye::RIGHT;
        } elseif ($na_eye) {
            $eye_id = -9;
        }

        return $eye_id;
    }

    public static function array_dump_html(array $data){
        $return_info = "";
        foreach ($data as $key => $value){
            if (is_array($value)){
                $value = Helper::array_dump_html($value);
            }
            $return_info .= $value.'<br>';
        }
        return $return_info;
    }

    /**
     * Return bytes based on the ini_get returns value e.g. 2M
     * @param $val
     * @return int|string
     */
    public static function return_bytes($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);
        $val = (int) $val;
        switch($last) {
            case 'g':
                $val *= pow(1024, 3); //1073741824
                break;
            case '':
            case 'm':
                $val *= pow(1024, 2); //1048576
                break;
            case 'k':
                $val *= 1024;
                break;
        }

        return $val;

    }

    /**
     * Set post code format, if it wrong
     * @param type $postcode
     * @return string
     */
    public static function setPostCodeFormat($postcode)
    {
        $clean_postcode = preg_replace("/[^A-Za-z0-9]/", '', $postcode);
        $clean_postcode = strtoupper($clean_postcode);

        //if 5 charcters, insert space after the 2nd character
        if(strlen($clean_postcode) == 5)
        {
            $postcode = substr($clean_postcode,0,2) . " " . substr($clean_postcode,2,3);
        }

        //if 6 charcters, insert space after the 3rd character
        elseif(strlen($clean_postcode) == 6)
        {
            $postcode = substr($clean_postcode,0,3) . " " . substr($clean_postcode,3,3);
        }


        //if 7 charcters, insert space after the 4th character
        elseif(strlen($clean_postcode) == 7)
        {
            $postcode = substr($clean_postcode,0,4) . " " . substr($clean_postcode,4,3);
        }

        return $postcode;
    }

    /**
     * Returns a file size in human-readable form
     *
     * @param int $bytes
     * @return string
     */
    public static function fileSize(int $bytes): string
    {
        $units = ['B', 'kB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes, 1024));
        $unit = $units[$i];
        $precision = $i <= 1 ? 0 : 2;
        return round($bytes / pow(1024, $i), $precision) . $unit;
    }

    /**
     * Dose and unit grammar format
     * @param string $item_dose
     * @return string
     */
    public static function formatPluralForDose(string $item_dose): string
    {
        if( preg_match("/[,\.-]?[0-9]+[,.]?[0-9]*([\/][0-9]+[,.]?[0-9]*)*/",  $item_dose, $dose) && (!empty($dose) && (substr($item_dose, -3) == '(s)'))){
            preg_match_all("/[^0-9\s.,]+/",  $item_dose, $units);

            $unit = '';
            foreach($units[0] as $val){
                $unit .= " ".$val;
            }

            if($dose[0] <= "1"){
                $result = $dose[0].substr($unit, 0, -3);
            } else if($dose[0] > "1"){
                $grammarUnit = self::grammarCheckForPlural($unit);
                $result = $dose[0].$grammarUnit;
            } else {
                $result = $dose[0].$unit;
            }
        } else {
            $result = $item_dose;
        }

        return $result;
    }

    /**
     * Grammar check for plural
     * @param string $word
     * @return string
     */
    public static function grammarCheckForPlural(string $word): string
    {
        if(substr($word, -3) == '(s)') {
            $word = substr($word,0, -3);
        }

        if (in_array(substr($word, -2), ["ay", "ey", "oy"])) {
            return $word.'s';
        }

        if(substr($word, -1) == "y") {
            return substr($word,0, -1).'ies';
        }

        if(
            (substr($word, -1) == "s") ||
            (substr($word, -2) == "sh") ||
            (substr($word, -2) == "ch") ||
            (substr($word, -1) == "x") ||
            (substr($word, -1) == "z")
        )
        {
            return $word.'es';
        }

        return $word.'s';
    }
}
