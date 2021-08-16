<?php

/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Trait InteractsWithPatient
 *
 * Very basic trait that will need more work if testing is expanded to use generated Patient instances
 * more. Doesn't currently provide a lot of flexibility.
 */
trait InteractsWithPatient
{
    use InteractsWithEventTypeElements;
    use WithFaker;

    protected function generateSavedPatientWithEpisode()
    {
        $patient = $this->generateSavedPatient();
        $episode = new Episode();
        $episode->patient_id = $patient->id;
        $episode->firm_id = $this->getRandomLookup(Firm::class)->id;
        $episode->start_date = $this->faker->dateTime()->format('Y-m-d H:i:s');
        $episode->save();

        return $patient;
    }

    protected function generateSavedPatient()
    {
        $contact = new \Contact();
        $contact->setAttributes($this->generateContactData());
        $contact->save();

        $address = new \Address();
        $address->setAttributes($this->generateAddressData());
        $address->contact_id = $contact->id;
        $address->save();

        $patient = new \Patient();
        $patient->setAttributes($this->generatePatientData());
        $patient->contact_id = $contact->id;
        $patient->save();

        return $patient;
    }

    protected function generatePatientData()
    {
        return [
            'dob' => $this->faker->dateTimeBetween('-70 years', '-6 months')->format('Y-m-d'),
            'gender' => $this->faker->randomElement(['M', 'F', 'U']),
            'hos_num' => $this->faker->numerify('########'),
            'nhs_num' => $this->faker->numerify('##########'), // not necessarily valid
            'gp_id' => $this->getRandomLookup(Gp::class)->id,
            'practice_id' => $this->getRandomLookup(Practice::class)->id,
            'ethnic_group_id' => $this->getRandomLookup(EthnicGroup::class)->id,
            'is_local' => true
        ];
    }

    protected function generateContactData()
    {
        return [
            'title' => $this->faker->title,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'primary_phone' => $this->faker->phoneNumber,
            'created_institution_id' => 1
        ];
    }

    protected function generateAddressData()
    {
        return [
            'address1' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'postcode' => $this->faker->postcode,
            'county' => $this->faker->county,
            'country_id' => $this->getRandomLookup(Country::class)->id,
            'address_type_id' => $this->getRandomLookup(AddressType::class)->id,
            'date_start' => $this->faker->dateTimeBetween('-5 years', '-1 year')->format('Y-m-d H:i:s')
        ];
    }
}