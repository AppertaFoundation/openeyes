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
 * Helper functions
 */
class Helper {
	
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
	public static function convertNHS2MySQL($data, $fields = null) {
		if($is_string = !is_array($data)) {
			$data = array('dummy' => $data);
		}
		$list = ($fields) ? $fields : array_keys($data);
		foreach($list as $key) {
			if(isset($data[$key]) && preg_match(self::NHS_DATE_REGEX, $data[$key])) {
				$data[$key] = date('Y-m-d',strtotime($data[$key]));
			}
		}
		if($is_string) {
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
	public static function convertMySQL2NHS($value, $empty_string = '-') {
		if(preg_match('/^\d{4}-\d{2}-\d{2}/', $value) && $value != '0000-00-00 00:00:00' && $value != '0000-00-00') {
			return self::convertDate2NHS($value, $empty_string);
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
	public static function convertDate2NHS($value, $empty_string = '-') {
		$time = strtotime($value);
		if($time) {
			return date(self::NHS_DATE_FORMAT, $time);
		} else {
			return $empty_string;
		}
	}
	
}