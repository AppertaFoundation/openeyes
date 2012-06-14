<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * Base class for all elements
 *
 * The followings are the available columns in table 'base_element':
 * @property integer $id
 * @property integer $event_id
 * @property string $element_class
 */
class BaseElement extends BaseActiveRecord {

	/**
	 * Returns the static model of the specified AR class.
	 * @return DrugDuration the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'base_element';
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
				'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
		);
	}

	public function getElement() {
		$element_class = $this->element_class;
		return $element_class::model()->find('base_id = ?', array($this->id));
	}

}
