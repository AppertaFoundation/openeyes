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

class SplitEventTypeElement extends BaseEventTypeElement {
	
	//used as data flags for indicating left and right in any models related to a SplitEventTypeElement
	const LEFT = 0;
	const RIGHT = 1;
	
	public function hasLeft() {
		return $this->eye && $this->eye->name != 'Right';
	}

	public function hasRight() {
		return $this->eye && $this->eye->name != 'Left';
	}

	/**
	 * An array of field suffixes that we should treat as "sided".
	 * e.g. 'example' would indicate 'left_example' and 'right_example'
	 * @return array:
	 */
	public function sidedFields() {
		return array();
	}

	/**
	 * An associative array of field suffixes and their default values.
	 * Used for initialising sided fields
	 * @return array 
	 */
	public function sidedDefaults() {
		return array();
	}
	protected function beforeSave() {

		// Need to clear any "sided" fields if that side isn't active to prevent
		if($this->eye->name != 'Both') {
			foreach($this->sidedFields() as $field_suffix) {
				if($this->eye->name == 'Left') {
						$clear_field = 'right_'.$field_suffix;
				} else { // Right
						$clear_field = 'left_'.$field_suffix;
				}
				$this->$clear_field = null;
			}
		}

		return parent::beforeSave();
	}

	/**
	 * Sided fields have the same defaults on left and right
	 */
	public function setDefaultOptions() {
		$this->setSideDefaultOptions('left');
		$this->setSideDefaultOptions('right');
	}
	
	protected function setSideDefaultOptions($side) {
		foreach($this->sidedDefaults() as $field => $default) {
			$this->{$side.'_'.$field} = $default;
		}
	}
	
	/**
	 * Used to initialise the missing side in an update form.
	 */
	public function setUpdateOptions() {
		if($this->eye->name == 'Left') {
			$this->setSideDefaultOptions('right');
		} else if($this->eye->name == 'Right') {
			$this->setSideDefaultOptions('left');
		}
	}
	
}

