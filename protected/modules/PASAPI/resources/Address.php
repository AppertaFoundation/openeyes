<?php

namespace OEModule\PASAPI\resources;

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class Address extends BaseResource
{
    protected static $resource_type = 'Address';
    public $_internal_id;

    public function saveModel(\Address $model)
    {
        $model->address1 = $this->getAssignedProperty('Line1');
        $model->address2 = $this->getAssignedProperty('Line2');
        $model->city = $this->getAssignedProperty('City');
        $model->county = $this->getAssignedProperty('County');

        $model->postcode = $this->getAssignedProperty('Postcode');

        $model->address_type_id = static::getAddressType($this->getAssignedProperty('Type'));

        $this->mapCountry($model);

        if (!$model->validate()) {
            $this->addModelErrors($model->getErrors());

            return;
        }

        return $model->save();
    }

    private static function getAddressType($addr_type)
    {
        switch ($addr_type) {
            case 'HOME':
            case 'H':
            case 'P':
                return \AddressType::HOME;
            case 'CORR':
            case 'M':
                return \AddressType::CORRESPOND;
            default:
                return null;
        }
    }

    private function mapCountry(\Address $address)
    {
        $country = null;
        if ($code = $this->getAssignedProperty('Country')) {
            if (!$country = \Country::model()->findByAttributes(array('code' => $code))) {
                $this->addWarning('Unrecognised country code '.$code);
            }
        }
        $address->country_id = $country ? $country->id : null;
    }
}
