<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

namespace Service;

class Address extends DataObject
{
	static public function fromModel(\Address $address)
	{
		return new static(
			array(
				'line1' => $address->address1,
				'line2' => $address->address2 ?: null,
				'city' => $address->city,
				'state' => $address->county ?: null,
				'zip' => $address->postcode,
				'country' => $address->country->name,
			)
		);
	}

	public $use = null;
	public $line1;
	public $line2 = null;
	public $city;
	public $state = null;
	public $zip;
	public $country;

	public function toModel(\Address $address)
	{
		$address->address1 = $this->line1;
		$address->address2 = $this->line2;
		$address->city = $this->city;
		$address->county = $this->state;
		$address->postcode = $this->zip;

		$crit = new \CDbCriteria;
		$crit->addColumnCondition(array('code' => $this->country, 'name' => $this->country), 'OR');
		$country = \Country::model()->find($crit);

		if (!$country) {
			throw new InvalidValue("Unrecognised country: '{$this->country}'");
		}

		$address->country_id = $country->id;
	}
}
