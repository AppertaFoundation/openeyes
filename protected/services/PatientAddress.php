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

namespace services;

/**
 * Patient addresses have additional properties
 */
class PatientAddress extends Address
{
	static protected $fhir_type = 'Address';

	static public function fromModel(\Address $address)
	{
		$pa = parent::fromModel($address);
		if ($address->date_start) $pa->date_start = new Date($address->date_start);
		if ($address->date_end) $pa->date_end = new Date($address->date_end);
		if ($address->address_type_id == \AddressType::CORRESPOND) $pa->correspond = true;
		if ($address->address_type_id == \AddressType::TRANSPORT) $pa->transport = true;
		return $pa;
	}

	public $date_start = null;
	public $date_end = null;
	public $correspond = false;
	public $transport = false;

	public function toModel(\Address $address)
	{
		parent::toModel($address);
		$address->date_start = $this->date_start;
		$address->date_end = $this->date_end;

		if ($this->correspond) {
			$address->address_type_id = \AddressType::CORRESPOND;
		} elseif ($this->transport) {
			$address->address_type_id = \AddressType::TRANSPORT;
		} else {
			$address->address_type_id = \AddressType::HOME;
		}
	}
}
