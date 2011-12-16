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
			if(preg_match('/^\d{1,2}-\w{3}-\d{4}$/', $date) && strtotime($date)) {
				$this->Owner->{$date_column} = date('Y-m-d',strtotime($date));
			}
		}
		return parent::beforeSave($event);
	}

	/**
	 * Converts ISO 9075 dates to OE (e.g. 5-Dec-2011) after read from database
	 */
	public function afterFind($event) {
		/*
		foreach($this->date_columns as $date_column) {
			$date = $this->Owner->{$date_column};
			Yii::log($date);
			$this->Owner->{$date_column} = date('j-M-Y', strtotime($date));
			Yii::log($this->Owner->{$date_column});
		}
		*/
		return parent::afterFind($event);
	}

}