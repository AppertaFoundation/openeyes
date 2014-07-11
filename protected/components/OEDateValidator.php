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


class OEDateValidator extends CValidator
{
	public function validateAttribute($object, $attribute)
	{
		if(isset($object->{$attribute}) && !empty($object->{$attribute})){
			$check_date = null;

			if ($m = preg_match('/^\d\d\d\d-\d\d{0,1}-\d\d{0,1}( \d\d:\d\d:\d\d){0,1}$/', $object->$attribute)) {
				$check_date = date_parse_from_format('Y-m-d',$object->{$attribute});
			}

			if (!$check_date || !checkdate($check_date['month'], $check_date['day'], $check_date['year'])) {
				if(strtotime($object->{$attribute})!=false){
					$this->addError($object, $attribute,$object->getAttributeLabel($attribute).' is not in valid format: ' . $object->$attribute);
				}
				else {
					$this->addError($object, $attribute,$object->getAttributeLabel($attribute).' is not a valid date: ' . $object->$attribute);
				}
				return false;
			}

			return true;
		}
	}
}
