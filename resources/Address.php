<?php namespace OEModule\PASAPI\resources;

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class Address extends BaseResource
{

    static protected $resource_type = 'Address';
    public $_internal_id;

    public function saveModel(\Address $model)
    {
        $model->address1 = $this->Line1;
        $model->address2 = $this->Line2;
        $model->city = $this->City;
        $model->county = $this->County;
        if (!$country = \Country::model()->findByAttributes(array('code' => strtoupper($this->Country)))) {
            throw new \Exception("Unrecognised country code " . $this->Country);
        };
        $model->postcode = $this->Postcode;

        $model->country_id = $country->id;
        $model->address_type_id = static::getAddressType($this->Type);

        $model->save();
    }

    static private function getAddressType($addr_type)
    {
        switch ($addr_type) {
            case 'HOME': return \AddressType::model()->find('name=?',array('Home'))->id;
        }

        return null;
    }
}