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
 * Model behavior for OpenEyes standard dates
 */
class OeDateFormat extends CActiveRecordBehavior {

	public $date_columns = array();

	/**
	 * Converts OE (e.g. 5-Dec-2011) dates to ISO 9075 before save
	 */
	public function beforeSave($event) {
		foreach($this->date_columns as $date_column) {
			$date = $this->Owner->{$date_column};
			if(preg_match(Helper::NHS_DATE_REGEX, $date) && strtotime($date)) {
				$this->Owner->{$date_column} = date('Y-m-d',strtotime($date));
			}
		}
	}

	/**
	 * Converts ISO 9075 dates to OE (e.g. 5-Dec-2011) after read from database
	 */
	public function afterFind($event) {
		foreach($this->date_columns as $date_column) {
			$date = $this->Owner->{$date_column};
			// Don't convert blank dates
			if($date && $date != '0000-00-00') {
				$this->Owner->{$date_column} = date(Helper::NHS_DATE_FORMAT, strtotime($date));
			} else {
				$this->Owner->{$date_column} = '';
			}
		}
	}

}