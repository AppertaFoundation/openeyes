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
    const NHS_DATE_FORMAT = 'j M Y';
    const NHS_DATE_FORMAT_JS = 'd M yy';
    const NHS_DATE_REGEX = '/^\d{1,2} \w{3} \d{4}$/';
    const NHS_DATE_EXAMPLE = '5 Dec 2011';

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
        if ($time) {
            return date(self::NHS_DATE_FORMAT, $time);
        } else {
            return $empty_string;
        }
    }

    public static function convertDate2HTML($value, $empty_string = '-')
    {
        $time = strtotime($value);
        if ($time) {
            return '<span class="day">'.date('j', $time).'</span><span class="mth">'.date('M', $time).'</span><span class="yr">'.date('Y', $time).'</span>';
        } else {
            return $empty_string;
        }
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

        return $time ? $time * 1000 : null;
    }

    /**
     * Calculate age from dob.
     *
     * If date of death provided, then returns age at point of death
     *
     * @param string $dob
     * @param string $date_of_death
     * @param string $check_date    Optional date to check age at (default is today)
     *
     * @return string $age
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
     */
    public static function getDateForAge($dob, $age, $date_of_death = null)
    {
        if (!$dob) {
            return;
        }

        $dob_datetime = new DateTime($dob);
        $age_date = $dob_datetime->add(new DateInterval('P'.$age.'Y'));

        if ($date_of_death) {
            $dod_datetime = new DateTime($date_of_death);
            if ($dod_datetime < $age_date) {
                return;
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

        if (preg_match('/^([\d\.]+)('.$regexp.')$/', strtoupper($val), $matches)) {
            return $matches[1] * pow(1024, $units[$matches[2]]);
        }
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
     * @param string $items
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

    public static function getNSShortname($instance)
    {
        $r = new ReflectionClass($instance);

        return $r->getShortName();
    }

    /**
     * @param $string
     * @param integer $line_count
     * @param integer $protect
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
     * @param string $pattern
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
     */
    public static function combineMySQLDateAndDateTime($date, $datetime)
    {
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $date)
            && preg_match('/\d{2}:\d{2}:\d{2}$/', $datetime)
        ) {
            return substr($date, 0, 10) . ' ' . substr($datetime, -8);
        }
    }
}
