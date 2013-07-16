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
 * Helper functions
 */
class Helper
{
	const NHS_DATE_FORMAT = 'j M Y';
	const NHS_DATE_FORMAT_JS = 'd M yy';
	const NHS_DATE_REGEX = '/^\d{1,2} \w{3} \d{4}$/';
	const NHS_DATE_EXAMPLE = '5 Dec 2011';

	/**
	 * Convert NHS dates to MySQL format.
	 * Strings that do not match the NHS format are returned unchanged.
	 *
	 * @param string|array $data Data containing one or more NHS dates
	 * @param array $fields Fields (keys) to convert (optional, if empty then all fields are checked for dates)
	 * @return string|array
	 */
	public static function convertNHS2MySQL($data, $fields = null)
	{
		if ($is_string = !is_array($data)) {
			$data = array('dummy' => $data);
		}
		$list = ($fields) ? $fields : array_keys($data);
		foreach ($list as $key) {
			if ( isset($data[$key]) ) {
				// traverse down arrays to convert nested structures
				if (is_array($data[$key])) {
					$data[$key] = Helper::convertNHS2MySQL($data[$key], $fields);
				} elseif (is_string($data[$key]) && preg_match(self::NHS_DATE_REGEX, $data[$key])) {
					$data[$key] = date('Y-m-d',strtotime($data[$key]));
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
			return '<span class="day">'.date('j',$time).'</span><span class="mth">'.date('M',$time).'</span><span class="yr">'.date('Y',$time).'</span>';
		} else {
			return $empty_string;
		}
	}

	/**
	 * Calculate age from dob
	 *
	 * If date of death provided, then returns age at point of death
	 * @param string $dob
	 * @param string $date_of_death
	 */
	public static function getAge($dob, $date_of_death = null)
	{
		if (!$dob) return 'Unknown';
		$date = date('Ymd', strtotime($dob));
		$end_date = ($date_of_death) ? strtotime($date_of_death) : time();
		$age = date('Y',$end_date) - substr($date, 0, 4);
		$birthDate = substr($date, 4, 2) . substr($date, 6, 2);
		if (date('md',$end_date) < $birthDate) {
			$age--; // birthday hasn't happened yet this year
		}
		return $age;
	}

	public static function getMonthText($month, $long=false)
	{
		return date($long?'F':'M',mktime(0,0,0,$month,1,date('Y')));
	}

	/**
	 * generate string representation of a fuzzy date (fuzzy dates are strings of the format
	 * yyyy-mm-dd, where mm and dd can be 00 to indicate not being set)
	 *
	 * @param string $value
	 * @return string
	 */
	public static function formatFuzzyDate($value)
	{
		$year = (integer) substr($value,0,4);
		$mon = (integer) substr($value,5,2);
		$day = (integer) substr($value,8,2);

		if ($year && $mon && $day) {
			return self::convertMySQL2NHS($value);
		}

		if ($year && $mon) {
			return date('M Y',strtotime($year.'-'.$mon.'-01 00:00:00'));
		}

		if ($year) {
			return (string) $year;
		}

		return 'Unknown';
	}

	/**
	 * generate string representation of timestamp for the database
	 *
	 * @param int $timestamp
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
}
