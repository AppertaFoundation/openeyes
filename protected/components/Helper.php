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
	
	/**
	 * Convert NHS dates (d-mmm-yyyy) to MySQL format.
	 * Strings that do not match the NHS format are returned unchanged.
	 * 
	 * @param array $data Data containing one or more NHS dates
	 * @param array $fields Fields (keys) to convert (optional, if empty then all fields are checked for dates)
	 */
	public static function convertNHS2MySQL($data, $fields = null) {
		$list = ($fields) ? $fields : array_keys($data);
		foreach($list as $key) {
			if(isset($data[$key]) && preg_match('/^\d{1,2}-\w{3}-\d{4}$/', $data[$key])) {
				$data[$key] = date('Y-m-d',strtotime($data[$key]));
			}
		}
		return $data;
	}
	
	/**
	 * Convert MySQL date(time) value to NHS format.
	 * Strings that do not match MySQL date(time) format are returned unchanged unless forced,
	 * in which case they are returned as null
	 * @param string $value
	 * @param boolean $force
	 * @return string
	 */
	public static function convertMySQL2NHS($value, $force = false) {
		if(preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
			return date('j-M-Y',strtotime($value));
		} else if($force) {
			return null;
		} else {
			return $value;
		}
	}
	
}