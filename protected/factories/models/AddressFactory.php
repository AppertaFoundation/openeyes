<?php
/**
 * (C) Apperta Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OE\factories\models;

use OE\factories\ModelFactory;
use Contact;
use Country;

class AddressFactory extends ModelFactory
{

    /**
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'address1' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'postcode' => $this->faker->postcode(),
            'country_id' => ModelFactory::factoryFor(Country::class)->useExisting(['code' => 'GB']),
            'contact_id' => Contact::factory()
        ];
    }

    public function full(): self
    {
        return $this->state([
            'address1' => $this->faker->secondaryAddress(),
            'address2' => $this->faker->streetAddress(),
            'county' => $this->faker->county()
        ]);
    }
}
